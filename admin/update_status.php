<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$type = $_POST['type'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

// Whitelist the table name up front — it must never be built from user input directly.
$table = $type === 'tribute' ? 'tributes' : ($type === 'media' ? 'media' : null);
$status = $action === 'approve' ? 'approved' : ($action === 'reject' ? 'rejected' : null);

if ($table && $status && $id > 0) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("UPDATE {$table} SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);

    // The dashboard only shows an approve/reject decision for the tribute
    // itself — any photo/video attached to it (previewed as a thumbnail
    // alongside the tribute text) never gets its own control, so it must
    // follow the same decision or it stays pending forever.
    if ($table === 'tributes') {
        $stmt = $pdo->prepare('UPDATE media SET status = :status WHERE tribute_id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }
}

header('Location: dashboard.php');
exit;
