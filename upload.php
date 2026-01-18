<?php
require_once 'config.php';
requireLogin();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Check if user can upload
if (!canUploadMore($userId)) {
    $error = 'You have reached the maximum number of free uploads (' . MAX_FREE_FILES . ' files). Please upgrade to Premium for unlimited uploads!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && canUploadMore($userId)) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $originalName = basename($file['name']);
        $fileSize = $file['size'];
        
        // Generate unique filename
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('file_', true) . '.' . $extension;
        $uploadPath = UPLOAD_DIR . $uniqueName;
        
        // Create user directory if not exists
        $userDir = UPLOAD_DIR . $userId . '/';
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }
        $uploadPath = $userDir . $uniqueName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            try {
                $db = getDB();
                $stmt = $db->prepare("INSERT INTO files (user_id, filename, original_name, file_size) VALUES (?, ?, ?, ?)");
                $stmt->execute([$userId, $uniqueName, $originalName, $fileSize]);
                
                $success = 'File uploaded successfully!';
            } catch (PDOException $e) {
                $error = 'Database error. Please try again.';
                unlink($uploadPath); // Remove uploaded file if db insert fails
            }
        } else {
            $error = 'Failed to upload file. Please try again.';
        }
    } else {
        $error = 'Please select a file to upload.';
    }
}

$fileCount = getUserFileCount($userId);
$isPremium = isUserPremium($userId);
$canUpload = canUploadMore($userId);

require_once 'header.php';
?>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-cloud-upload-alt fa-3x" style="color: #667eea;"></i>
                    </div>
                    <h2 class="fw-bold">Upload Files</h2>
                    <p class="text-muted">
                        <?php if ($isPremium): ?>
                            <span class="premium-badge me-2"><i class="fas fa-crown me-1"></i>Premium</span>
                            Unlimited uploads available
                        <?php else: ?>
                            <?php echo $fileCount; ?> / <?php echo MAX_FREE_FILES; ?> files used
                        <?php endif; ?>
                    </p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <?php if (!$canUpload): ?>
                            <br><a href="premium.php" class="alert-link">Click here to upgrade to Premium</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        <br><a href="files.php" class="alert-link">View your files</a>
                    </div>
                <?php endif; ?>
                
                <?php if ($canUpload): ?>
                    <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                        <div class="upload-zone mb-4" onclick="document.getElementById('fileInput').click();">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h4>Drag & Drop your file here</h4>
                            <p class="text-muted mb-3">or click to browse</p>
                            <input type="file" id="fileInput" name="file" class="d-none" onchange="showFileName(this)">
                            <p class="text-muted mb-0" id="selectedFile">No file selected</p>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-lock fa-4x text-muted"></i>
                        </div>
                        <h4>Upload Limit Reached</h4>
                        <p class="text-muted">You've used all your free uploads. Upgrade to Premium for unlimited storage!</p>
                        <a href="premium.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-crown me-2"></i>Upgrade to Premium - <?php echo PREMIUM_PRICE; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!$isPremium): ?>
                <div class="card p-4 mt-4 text-center">
                    <h5 class="mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Free Plan Limits</h5>
                    <p class="mb-0">
                        You can upload up to <strong><?php echo MAX_FREE_FILES; ?> files</strong> for free. 
                        For unlimited uploads, upgrade to Premium for just <strong><?php echo PREMIUM_PRICE; ?></strong>!
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('selectedFile').innerHTML = 
            '<i class="fas fa-file me-2"></i><strong>' + input.files[0].name + '</strong> (' + 
            formatFileSize(input.files[0].size) + ')';
    }
}

function formatFileSize(bytes) {
    if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' bytes';
}

// Drag and drop functionality
const uploadZone = document.querySelector('.upload-zone');
if (uploadZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, () => uploadZone.style.borderColor = '#764ba2', false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, () => uploadZone.style.borderColor = '#667eea', false);
    });
    
    uploadZone.addEventListener('drop', function(e) {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('fileInput').files = files;
            showFileName(document.getElementById('fileInput'));
        }
    }, false);
}
</script>

<?php require_once 'footer.php'; ?>
