<?php
require_once 'config.php';
requireLogin();

$userId = $_SESSION['user_id'];
$fileId = intval($_GET['id'] ?? 0);

if ($fileId <= 0) {
    header('Location: files.php');
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$fileId, $userId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        header('Location: files.php');
        exit;
    }
    
    $filePath = UPLOAD_DIR . $userId . '/' . $file['filename'];
    
    if (!file_exists($filePath)) {
        header('Location: files.php?error=notfound');
        exit;
    }
    
    // Set headers for download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    
    // Clear output buffer
    ob_clean();
    flush();
    
    // Read and output file
    readfile($filePath);
    exit;
    
} catch (PDOException $e) {
    header('Location: files.php?error=db');
    exit;
}
?>
