<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

require_once 'db_connect.php';

$user_id = (int)$_SESSION['user_id'];
// Read action from GET first, then POST — avoids the FormData overwrite bug
$action = $_GET['action'] ?? '';
if (!$action) {
    // For POST requests, FormData appends action= twice when we call post('submit_lost',{...})
    // We read it cleanly from the raw input instead
    $action = $_POST['action'] ?? '';
}

switch ($action) {

    case 'get_stats':
        $lost_count = $pdo->prepare("SELECT COUNT(*) FROM lost_items WHERE user_id=? AND status IN ('Open','Matched')");
        $lost_count->execute([$user_id]);
        $found_count = $pdo->prepare("SELECT COUNT(*) FROM found_items WHERE user_id=?");
        $found_count->execute([$user_id]);
        $match_count = $pdo->prepare("SELECT COUNT(*) FROM match_results mr JOIN lost_items li ON mr.lost_id=li.lost_id WHERE li.user_id=?");
        $match_count->execute([$user_id]);
        $notif_count = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND status='Unread'");
        $notif_count->execute([$user_id]);
        echo json_encode([
            'lost'    => (int)$lost_count->fetchColumn(),
            'found'   => (int)$found_count->fetchColumn(),
            'matches' => (int)$match_count->fetchColumn(),
            'notifs'  => (int)$notif_count->fetchColumn(),
        ]);
        break;

    case 'get_lost':
        $stmt = $pdo->prepare("SELECT * FROM lost_items WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'get_found':
        $stmt = $pdo->prepare("
            SELECT fi.*, u.full_name AS reporter_name
            FROM found_items fi
            JOIN users u ON fi.user_id = u.user_id
            ORDER BY fi.created_at DESC");
        $stmt->execute();
        echo json_encode($stmt->fetchAll());
        break;

    case 'get_matches':
        $stmt = $pdo->prepare("
            SELECT mr.*, li.item_name AS lost_name, fi.item_name AS found_name,
                   fi.location_found, fi.date_found, fi.status AS found_status
            FROM match_results mr
            JOIN lost_items li ON mr.lost_id = li.lost_id
            JOIN found_items fi ON mr.found_id = fi.found_id
            WHERE li.user_id = ?
            ORDER BY mr.matched_at DESC");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'get_claims':
        $stmt = $pdo->prepare("
            SELECT c.*, fi.item_name AS found_name
            FROM claims c
            JOIN found_items fi ON c.found_id = fi.found_id
            WHERE c.claimant_user_id = ?
            ORDER BY c.claim_date DESC");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'get_notifications':
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'mark_read':
        $notif_id = (int)($_POST['notification_id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE notifications SET status='Read' WHERE notification_id=? AND user_id=?");
        $stmt->execute([$notif_id, $user_id]);
        echo json_encode(['success' => true]);
        break;

    case 'mark_all_read':
        $stmt = $pdo->prepare("UPDATE notifications SET status='Read' WHERE user_id=?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true]);
        break;

    case 'submit_lost':
        $item_name   = trim($_POST['item_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $color       = trim($_POST['color'] ?? '');
        $brand       = trim($_POST['brand'] ?? '');
        $location    = trim($_POST['location_lost'] ?? '');
        $date_lost   = $_POST['date_lost'] ?: null;

        if (!$item_name) { echo json_encode(['success'=>false,'message'=>'Item name is required.']); break; }

        $stmt = $pdo->prepare("INSERT INTO lost_items (user_id,item_name,description,category,color,brand,location_lost,date_lost) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$user_id,$item_name,$description,$category,$color,$brand,$location,$date_lost]);
        $lost_id = (int)$pdo->lastInsertId();

        runMatchCheck($pdo, $lost_id, $item_name, $category, $color, $user_id);
        echo json_encode(['success' => true, 'lost_id' => $lost_id]);
        break;

    case 'submit_found':
        $item_name   = trim($_POST['item_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $color       = trim($_POST['color'] ?? '');
        $brand       = trim($_POST['brand'] ?? '');
        $location    = trim($_POST['location_found'] ?? '');
        $date_found  = $_POST['date_found'] ?: null;

        if (!$item_name) { echo json_encode(['success'=>false,'message'=>'Item name is required.']); break; }

        $stmt = $pdo->prepare("INSERT INTO found_items (user_id,item_name,description,category,color,brand,location_found,date_found) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$user_id,$item_name,$description,$category,$color,$brand,$location,$date_found]);
        echo json_encode(['success' => true]);
        break;

    case 'submit_claim':
        $found_id     = (int)($_POST['found_id'] ?? 0);
        $claim_reason = trim($_POST['claim_reason'] ?? '');

        if (!$found_id || !$claim_reason) {
            echo json_encode(['success'=>false,'message'=>'All fields required.']); break;
        }
        $check = $pdo->prepare("SELECT claim_id FROM claims WHERE found_id=? AND claimant_user_id=?");
        $check->execute([$found_id, $user_id]);
        if ($check->fetch()) {
            echo json_encode(['success'=>false,'message'=>'You have already claimed this item.']); break;
        }
        $stmt = $pdo->prepare("INSERT INTO claims (found_id,claimant_user_id,claim_reason) VALUES (?,?,?)");
        $stmt->execute([$found_id, $user_id, $claim_reason]);

        $item = $pdo->prepare("SELECT item_name FROM found_items WHERE found_id=?");
        $item->execute([$found_id]);
        $item_name = $item->fetchColumn() ?: 'item';

        $notif = $pdo->prepare("INSERT INTO notifications (user_id,message) VALUES (?,?)");
        $notif->execute([$user_id, "Your claim for \"$item_name\" has been submitted and is under admin review."]);
        echo json_encode(['success' => true]);
        break;

    case 'get_analytics':
        $data = [];
        $stmt = $pdo->query("SELECT COALESCE(category,'Unknown') AS category, COUNT(*) AS count FROM lost_items WHERE category!='' GROUP BY category ORDER BY count DESC LIMIT 6");
        $data['top_categories'] = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT COALESCE(location_lost,'Unknown') AS location, COUNT(*) AS count FROM lost_items WHERE location_lost!='' GROUP BY location_lost ORDER BY count DESC LIMIT 6");
        $data['hotspot_locations'] = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT DAYNAME(date_lost) AS day_name, DAYOFWEEK(date_lost) AS day_num, COUNT(*) AS count FROM lost_items WHERE date_lost IS NOT NULL GROUP BY day_name,day_num ORDER BY day_num");
        $data['peak_days'] = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT DATE_FORMAT(created_at,'%b %Y') AS month, DATE_FORMAT(created_at,'%Y-%m') AS sort_key, COUNT(*) AS lost_count FROM lost_items WHERE created_at>=DATE_SUB(NOW(),INTERVAL 6 MONTH) GROUP BY month,sort_key ORDER BY sort_key ASC");
        $data['monthly_trend'] = $stmt->fetchAll();

        $lost_total  = (int)$pdo->query("SELECT COUNT(*) FROM lost_items")->fetchColumn();
        $found_total = (int)$pdo->query("SELECT COUNT(*) FROM found_items")->fetchColumn();
        $claimed     = (int)$pdo->query("SELECT COUNT(*) FROM claims WHERE claim_status='Approved'")->fetchColumn();
        $data['totals'] = [
            'total_lost'    => $lost_total,
            'total_found'   => $found_total,
            'total_claimed' => $claimed,
            'recovery_rate' => $lost_total > 0 ? round(($claimed/$lost_total)*100) : 0,
        ];
        $stmt = $pdo->query("SELECT COALESCE(location_found,'Unknown') AS location, COUNT(*) AS count FROM found_items WHERE location_found!='' GROUP BY location_found ORDER BY count DESC LIMIT 5");
        $data['found_locations'] = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT item_name, COUNT(*) AS count FROM lost_items GROUP BY item_name ORDER BY count DESC LIMIT 5");
        $data['top_items'] = $stmt->fetchAll();

        echo json_encode($data);
        break;

    default:
        echo json_encode(['error' => 'Unknown action: ' . htmlspecialchars($action)]);
}

function runMatchCheck($pdo, $lost_id, $item_name, $category, $color, $user_id) {
    $stmt = $pdo->query("SELECT * FROM found_items WHERE status='Available'");
    $found_items = $stmt->fetchAll();
    foreach ($found_items as $fi) {
        $score = 0;
        if (!empty($category) && strtolower($fi['category']) === strtolower($category)) $score++;
        if (!empty($color)    && strtolower($fi['color'])    === strtolower($color))    $score++;
        $lost_words  = array_filter(explode(' ', strtolower($item_name)));
        $found_words = array_filter(explode(' ', strtolower($fi['item_name'])));
        if (count(array_intersect($lost_words, $found_words)) > 0) $score++;
        $similarity = $score / 3;
        if ($similarity >= 0.34) { // at least 1 of 3 criteria
            $check = $pdo->prepare("SELECT match_id FROM match_results WHERE lost_id=? AND found_id=?");
            $check->execute([$lost_id, $fi['found_id']]);
            if (!$check->fetch()) {
                $pdo->prepare("INSERT INTO match_results (lost_id,found_id,similarity_score) VALUES (?,?,?)")
                    ->execute([$lost_id, $fi['found_id'], $similarity]);
                if ($similarity >= 0.67) {
                    $pdo->prepare("UPDATE lost_items  SET status='Matched' WHERE lost_id=?") ->execute([$lost_id]);
                    $pdo->prepare("UPDATE found_items SET status='Matched' WHERE found_id=?")->execute([$fi['found_id']]);
                    // Notify the user of the match
                    $pdo->prepare("INSERT INTO notifications (user_id,message) VALUES (?,?)")
                        ->execute([$user_id, "Your lost item \"$item_name\" has been matched with a found item! Check your Matches tab."]);
                }
            }
        }
    }
}