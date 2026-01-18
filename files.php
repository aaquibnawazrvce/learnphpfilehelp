<?php
require_once 'config.php';
requireLogin();

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $fileId = intval($_POST['file_id']);
    
    try {
        $db = getDB();
        
        // Get file info
        $stmt = $db->prepare("SELECT filename FROM files WHERE id = ? AND user_id = ?");
        $stmt->execute([$fileId, $userId]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($file) {
            // Delete from filesystem
            $filePath = UPLOAD_DIR . $userId . '/' . $file['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete from database
            $stmt = $db->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
            $stmt->execute([$fileId, $userId]);
            
            $success = 'File deleted successfully!';
        } else {
            $error = 'File not found.';
        }
    } catch (PDOException $e) {
        $error = 'An error occurred. Please try again.';
    }
}

// Get user's files
$db = getDB();
$stmt = $db->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$userId]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

$fileCount = count($files);
$isPremium = isUserPremium($userId);

require_once 'header.php';
?>

<div class="container main-content">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-folder-open text-primary me-2"></i>My Files
                    </h2>
                    <p class="text-muted mb-0">
                        <?php echo $fileCount; ?> file<?php echo $fileCount !== 1 ? 's' : ''; ?> stored
                        <?php if ($isPremium): ?>
                            <span class="premium-badge ms-2"><i class="fas fa-crown me-1"></i>Premium</span>
                        <?php else: ?>
                            <span class="text-muted">(<?php echo MAX_FREE_FILES - $fileCount; ?> uploads remaining)</span>
                        <?php endif; ?>
                    </p>
                </div>
                <a href="upload.php" class="btn btn-primary <?php echo (!canUploadMore($userId)) ? 'disabled' : ''; ?>">
                    <i class="fas fa-plus me-2"></i>Upload New
                </a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$isPremium && $fileCount >= MAX_FREE_FILES): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Upload Limit Reached!</strong> 
                    <a href="premium.php" class="alert-link">Upgrade to Premium</a> for unlimited uploads at just <?php echo PREMIUM_PRICE; ?>!
                </div>
            <?php endif; ?>
            
            <?php if (empty($files)): ?>
                <div class="card p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-cloud-upload-alt fa-4x text-muted"></i>
                    </div>
                    <h4>No Files Yet</h4>
                    <p class="text-muted mb-4">You haven't uploaded any files. Start by uploading your first file!</p>
                    <div>
                        <a href="upload.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-upload me-2"></i>Upload Your First File
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#ID</th>
                                    <th>File Name</th>
                                    <th style="width: 120px;">Size</th>
                                    <th style="width: 150px;">Uploaded</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($files as $file): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $file['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="file-icon me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                                    <i class="fas fa-file"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($file['original_name']); ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo formatFileSize($file['file_size']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($file['uploaded_at'])); ?></td>
                                        <td>
                                            <a href="download.php?id=<?php echo $file['id']; ?>" 
                                               class="btn btn-success btn-sm me-1" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete(<?php echo $file['id']; ?>, '<?php echo htmlspecialchars(addslashes($file['original_name'])); ?>')"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Mobile view cards -->
                <div class="d-md-none mt-4">
                    <?php foreach ($files as $file): ?>
                        <div class="file-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <div class="file-icon me-3">
                                        <i class="fas fa-file"></i>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($file['original_name']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            ID: <?php echo $file['id']; ?> • 
                                            <?php echo formatFileSize($file['file_size']); ?> • 
                                            <?php echo date('M d, Y', strtotime($file['uploaded_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <a href="download.php?id=<?php echo $file['id']; ?>" class="btn btn-success btn-sm flex-fill">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="confirmDelete(<?php echo $file['id']; ?>, '<?php echo htmlspecialchars(addslashes($file['original_name'])); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteFileName"></strong>?</p>
                <p class="text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="file_id" id="deleteFileId">
                    <button type="submit" name="delete_file" class="btn btn-danger rounded-pill">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(fileId, fileName) {
    document.getElementById('deleteFileId').value = fileId;
    document.getElementById('deleteFileName').textContent = fileName;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<style>
@media (min-width: 768px) {
    .d-md-none {
        display: none !important;
    }
}
@media (max-width: 767px) {
    .table-responsive {
        display: none;
    }
}
</style>

<?php require_once 'footer.php'; ?>
