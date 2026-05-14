<?php
session_start();
header('Content-Type: application/json');

// Auth guard — admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'db.php'; // your existing DB connection file
$admin_id = $_SESSION['user_id'];

function logAdminAction($pdo, $admin_id, $action) {
    $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action) VALUES (?, ?)");
    $stmt->execute([$admin_id, $action]);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ══════════════════════════════════════════
// GET ACTIONS
// ══════════════════════════════════════════

// ── Admin Stats ──
if ($action === 'admin_stats') {
    $stats = [];

    $r = $pdo->query("SELECT COUNT(*) FROM claims WHERE claim_status = 'Pending'")->fetchColumn();
    $stats['pending_claims'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM claims WHERE claim_status = 'Approved'")->fetchColumn();
    $stats['approved_claims'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM claims WHERE claim_status = 'Rejected'")->fetchColumn();
    $stats['rejected_claims'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['total_users'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
    $stats['student_count'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM users WHERE role='staff'")->fetchColumn();
    $stats['staff_count'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
    $stats['admin_count'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM lost_items WHERE status='Open'")->fetchColumn();
    $stats['open_lost'] = (int)$r;

    $r = $pdo->query("SELECT COUNT(*) FROM found_items WHERE status='Available'")->fetchColumn();
    $stats['available_found'] = (int)$r;

    echo json_encode($stats);
    exit;
}

// ── All Claims (with optional status filter) ──
if ($action === 'admin_claims') {
    $status = $_GET['status'] ?? '';
    $sql = "
        SELECT c.*, 
               fi.item_name AS found_name, fi.category AS found_category,
               fi.location_found, fi.date_found,
               u.full_name AS claimant_name, u.email AS claimant_email
        FROM claims c
        JOIN found_items fi ON c.found_id = fi.found_id
        JOIN users u ON c.claimant_user_id = u.user_id
    ";
    $params = [];
    if ($status) {
        $sql .= " WHERE c.claim_status = ?";
        $params[] = $status;
    }
    $sql .= " ORDER BY c.claim_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ── All Lost Items ──
if ($action === 'admin_lost') {
    $status   = $_GET['status'] ?? '';
    $category = $_GET['category'] ?? '';
    $sql = "
        SELECT li.*, u.full_name AS reporter_name
        FROM lost_items li
        JOIN users u ON li.user_id = u.user_id
        WHERE 1=1
    ";
    $params = [];
    if ($status)   { $sql .= " AND li.status = ?";   $params[] = $status; }
    if ($category) { $sql .= " AND li.category = ?"; $params[] = $category; }
    $sql .= " ORDER BY li.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ── All Found Items ──
if ($action === 'admin_found') {
    $status = $_GET['status'] ?? '';
    $sql = "
        SELECT fi.*, u.full_name AS reporter_name
        FROM found_items fi
        JOIN users u ON fi.user_id = u.user_id
        WHERE 1=1
    ";
    $params = [];
    if ($status) { $sql .= " AND fi.status = ?"; $params[] = $status; }
    $sql .= " ORDER BY fi.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ── All Matches ──
if ($action === 'admin_matches') {
    $sql = "
        SELECT mr.*,
               li.item_name AS lost_name, u.full_name AS reporter_name,
               fi.item_name AS found_name, fi.location_found
        FROM match_results mr
        JOIN lost_items li ON mr.lost_id = li.lost_id
        JOIN found_items fi ON mr.found_id = fi.found_id
        JOIN users u ON li.user_id = u.user_id
        ORDER BY mr.matched_at DESC
    ";
    $stmt = $pdo->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ── All Users ──
if ($action === 'admin_users') {
    $role = $_GET['role'] ?? '';
    $dept = $_GET['department'] ?? '';
    $sql = "
        SELECT u.*,
               (SELECT COUNT(*) FROM lost_items  WHERE user_id = u.user_id) AS lost_count,
               (SELECT COUNT(*) FROM found_items WHERE user_id = u.user_id) AS found_count,
               (SELECT COUNT(*) FROM claims WHERE claimant_user_id = u.user_id) AS claim_count
        FROM users u
        WHERE 1=1
    ";
    $params = [];
    if ($role) { $sql .= " AND u.role = ?";       $params[] = $role; }
    if ($dept) { $sql .= " AND u.department = ?"; $params[] = $dept; }
    $sql .= " ORDER BY u.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Never expose password hashes
    foreach ($rows as &$row) unset($row['password']);
    echo json_encode($rows);
    exit;
}

// ── All Notifications (for the send panel) ──
if ($action === 'admin_notifications') {
    $sql = "
        SELECT n.*, u.full_name AS recipient_name
        FROM notifications n
        JOIN users u ON n.user_id = u.user_id
        ORDER BY n.created_at DESC
        LIMIT 50
    ";
    $stmt = $pdo->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ── Admin Logs ──
if ($action === 'admin_logs') {
    $sql = "
        SELECT al.*, u.full_name AS admin_name
        FROM admin_logs al
        JOIN users u ON al.admin_id = u.user_id
        ORDER BY al.created_at DESC
        LIMIT 100
    ";
    $stmt = $pdo->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ── Analytics (reuse existing logic + admin extras) ──
if ($action === 'get_analytics') {
    $data = [];

    // Totals
    $total_lost    = (int)$pdo->query("SELECT COUNT(*) FROM lost_items")->fetchColumn();
    $total_found   = (int)$pdo->query("SELECT COUNT(*) FROM found_items")->fetchColumn();
    $total_claimed = (int)$pdo->query("SELECT COUNT(*) FROM claims WHERE claim_status='Approved'")->fetchColumn();
    $recovery_rate = $total_lost > 0 ? round(($total_claimed / $total_lost) * 100) : 0;

    $data['totals'] = [
        'total_lost'    => $total_lost,
        'total_found'   => $total_found,
        'total_claimed' => $total_claimed,
        'recovery_rate' => $recovery_rate,
    ];

    // Top categories
    $stmt = $pdo->query("
        SELECT COALESCE(category,'Unknown') AS category, COUNT(*) AS count
        FROM lost_items GROUP BY category ORDER BY count DESC LIMIT 6
    ");
    $data['top_categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monthly trend (last 6 months) — lost + found
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at,'%b %Y') AS month,
               YEAR(created_at) AS yr, MONTH(created_at) AS mo,
               COUNT(*) AS lost_count
        FROM lost_items
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY yr, mo ORDER BY yr, mo
    ");
    $lostByMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at,'%b %Y') AS month,
               YEAR(created_at) AS yr, MONTH(created_at) AS mo,
               COUNT(*) AS found_count
        FROM found_items
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY yr, mo ORDER BY yr, mo
    ");
    $foundByMonth = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $foundByMonth[$row['yr'].'-'.$row['mo']] = $row['found_count'];
    }
    foreach ($lostByMonth as &$row) {
        $key = $row['yr'].'-'.$row['mo'];
        $row['found_count'] = (int)($foundByMonth[$key] ?? 0);
    }
    $data['monthly_trend'] = $lostByMonth;

    // Hotspot locations
    $stmt = $pdo->query("
        SELECT COALESCE(location_lost,'Unknown') AS location, COUNT(*) AS count
        FROM lost_items GROUP BY location_lost ORDER BY count DESC LIMIT 6
    ");
    $data['hotspot_locations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Peak days
    $stmt = $pdo->query("
        SELECT DAYNAME(created_at) AS day_name, COUNT(*) AS count
        FROM lost_items GROUP BY day_name ORDER BY count DESC
    ");
    $data['peak_days'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top items
    $stmt = $pdo->query("
        SELECT item_name, COUNT(*) AS count
        FROM lost_items GROUP BY item_name ORDER BY count DESC LIMIT 5
    ");
    $data['top_items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Found locations
    $stmt = $pdo->query("
        SELECT COALESCE(location_found,'Unknown') AS location, COUNT(*) AS count
        FROM found_items GROUP BY location_found ORDER BY count DESC LIMIT 6
    ");
    $data['found_locations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
    exit;
}


// ══════════════════════════════════════════
// POST ACTIONS
// ══════════════════════════════════════════

// ── Approve / Reject Claim ──
if ($action === 'update_claim') {
    $claim_id     = (int)($_POST['claim_id'] ?? 0);
    $verb         = $_POST['action'] ?? '';   // 'approve' or 'reject'
    $admin_remark = trim($_POST['admin_remark'] ?? '');

    if (!in_array($verb, ['approve','reject'])) {
        echo json_encode(['success'=>false,'message'=>'Invalid action.']); exit;
    }
    if (!$claim_id) { echo json_encode(['success'=>false,'message'=>'Invalid claim ID.']); exit; }

    $claim_status = $verb === 'approve' ? 'Approved' : 'Rejected';

    $stmt = $pdo->prepare("
        SELECT c.claimant_user_id, c.found_id, fi.item_name
        FROM claims c JOIN found_items fi ON c.found_id=fi.found_id
        WHERE c.claim_id=?");
    $stmt->execute([$claim_id]);
    $claim = $stmt->fetch();
    if (!$claim) { echo json_encode(['success'=>false,'message'=>'Claim not found.']); exit; }

    $pdo->prepare("UPDATE claims SET claim_status=?,admin_remark=? WHERE claim_id=?")
        ->execute([$claim_status, $admin_remark, $claim_id]);

    if ($claim_status === 'Approved') {
        $pdo->prepare("UPDATE found_items SET status='Returned' WHERE found_id=?")
            ->execute([$claim['found_id']]);
        $pdo->prepare("UPDATE lost_items li
            JOIN match_results mr ON li.lost_id=mr.lost_id
            SET li.status='Claimed'
            WHERE mr.found_id=?")
            ->execute([$claim['found_id']]);
    }

    $msg = $claim_status === 'Approved'
        ? "Your claim for \"{$claim['item_name']}\" was APPROVED! Please collect it from the admin office."
        : "Your claim for \"{$claim['item_name']}\" was not approved." . ($admin_remark ? " Admin note: $admin_remark" : '');

    $pdo->prepare("INSERT INTO notifications (user_id,message) VALUES (?,?)")
        ->execute([$claim['claimant_user_id'], $msg]);

    logAdminAction($pdo, $admin_id, "$claim_status claim #$claim_id for \"{$claim['item_name']}\"" . ($admin_remark ? " — $admin_remark" : ''));
    echo json_encode(['success' => true]);
    exit;
}

// ── Update Item Status ──
if ($action === 'update_item_status') {
    $item_id   = (int)($_POST['item_id'] ?? 0);
    $item_type = $_POST['item_type'] ?? '';
    $status    = $_POST['status'] ?? '';

    if (!$item_id || !in_array($item_type, ['lost','found'])) {
        echo json_encode(['success'=>false,'message'=>'Invalid input.']); exit;
    }

    if ($item_type === 'lost') {
        $allowed = ['Open','Matched','Claimed','Closed'];
        if (!in_array($status, $allowed)) { echo json_encode(['success'=>false,'message'=>'Invalid status.']); exit; }
        $stmt = $pdo->prepare("UPDATE lost_items SET status=? WHERE lost_id=?");
        $stmt->execute([$status, $item_id]);
        $stmt = $pdo->prepare("SELECT item_name FROM lost_items WHERE lost_id=?");
        $stmt->execute([$item_id]);
        $name = $stmt->fetchColumn();
    } else {
        $allowed = ['Available','Matched','Returned'];
        if (!in_array($status, $allowed)) { echo json_encode(['success'=>false,'message'=>'Invalid status.']); exit; }
        $stmt = $pdo->prepare("UPDATE found_items SET status=? WHERE found_id=?");
        $stmt->execute([$status, $item_id]);
        $stmt = $pdo->prepare("SELECT item_name FROM found_items WHERE found_id=?");
        $stmt->execute([$item_id]);
        $name = $stmt->fetchColumn();
    }

    logAdminAction($pdo, $admin_id, "Updated $item_type item #$item_id (\"{$name}\") status to \"$status\"");
    echo json_encode(['success' => true]);
    exit;
}

// ── Send Notification ──
if ($action === 'send_notification') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if (!$user_id || !$message) {
        echo json_encode(['success'=>false,'message'=>'User ID and message required.']); exit;
    }

    // Verify user exists
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE user_id=?");
    $stmt->execute([$user_id]);
    $recipient = $stmt->fetchColumn();
    if (!$recipient) { echo json_encode(['success'=>false,'message'=>'User not found.']); exit; }

    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?,?)");
    $stmt->execute([$user_id, $message]);

    logAdminAction($pdo, $admin_id, "Sent notification to user #$user_id ($recipient): \"$message\"");
    echo json_encode(['success' => true]);
    exit;
}

// Fallback
echo json_encode(['error' => 'Unknown action: ' . $action]);