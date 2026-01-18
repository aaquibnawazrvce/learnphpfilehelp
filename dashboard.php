<?php
require_once 'config.php';
requireLogin();

$userId = $_SESSION['user_id'];
$fileCount = getUserFileCount($userId);
$isPremium = isUserPremium($userId);
$canUpload = canUploadMore($userId);

require_once 'header.php';
?>

<div class="hero-section text-center" style="padding: 2rem 0; border-radius: 0 0 30px 30px;">
    <div class="container">
        <h2 class="mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
            <?php if ($isPremium): ?>
                <span class="premium-badge"><i class="fas fa-crown me-1"></i>Premium</span>
            <?php endif; ?>
        </h2>
        <p class="mb-0">Manage your files from your personal dashboard</p>
    </div>
</div>

<div class="container main-content">
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <i class="fas fa-file"></i>
                <div class="stat-number"><?php echo $fileCount; ?></div>
                <p class="text-muted mb-0">Total Files</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <i class="fas fa-cloud-upload-alt"></i>
                <div class="stat-number">
                    <?php if ($isPremium): ?>
                        <span class="text-success">∞</span>
                    <?php else: ?>
                        <?php echo MAX_FREE_FILES - $fileCount; ?>
                    <?php endif; ?>
                </div>
                <p class="text-muted mb-0">Uploads Remaining</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <i class="fas fa-user-shield"></i>
                <div class="stat-number">
                    <?php if ($isPremium): ?>
                        <span class="text-success">Premium</span>
                    <?php else: ?>
                        <span class="text-warning">Free</span>
                    <?php endif; ?>
                </div>
                <p class="text-muted mb-0">Account Type</p>
            </div>
        </div>
    </div>
    
    <?php if (!$canUpload && !$isPremium): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Upload Limit Reached!</strong> You've used all <?php echo MAX_FREE_FILES; ?> free uploads. 
            <a href="premium.php" class="alert-link">Upgrade to Premium</a> for unlimited uploads at just <?php echo PREMIUM_PRICE; ?>!
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-4 h-100">
                <h4 class="mb-3"><i class="fas fa-upload text-primary me-2"></i>Quick Upload</h4>
                <p class="text-muted">Upload your files quickly and securely.</p>
                <a href="upload.php" class="btn btn-primary <?php echo (!$canUpload) ? 'disabled' : ''; ?>">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload Files
                </a>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card p-4 h-100">
                <h4 class="mb-3"><i class="fas fa-folder-open text-primary me-2"></i>My Files</h4>
                <p class="text-muted">View and manage all your uploaded files.</p>
                <a href="files.php" class="btn btn-primary">
                    <i class="fas fa-eye me-2"></i>View Files
                </a>
            </div>
        </div>
    </div>
    
    <?php if (!$isPremium): ?>
        <div class="card p-4 mt-4" style="background: var(--primary-gradient); color: white;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4><i class="fas fa-crown me-2"></i>Upgrade to Premium</h4>
                    <p class="mb-md-0">Get unlimited file uploads and priority support for just <?php echo PREMIUM_PRICE; ?>!</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="premium.php" class="btn btn-light btn-lg rounded-pill">
                        <i class="fas fa-rocket me-2"></i>Upgrade Now
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
