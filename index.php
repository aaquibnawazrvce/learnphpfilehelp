<?php
require_once 'config.php';
require_once 'header.php';
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">
            <i class="fas fa-cloud-upload-alt me-3"></i>CloudDrive
        </h1>
        <p class="lead mb-4">Your Personal Cloud Storage Solution</p>
        <p class="mb-4">Store, access, and share your files securely from anywhere in the world.</p>
        <?php if (!isLoggedIn()): ?>
            <a href="signup.php" class="btn btn-light btn-lg me-3 rounded-pill px-4">
                <i class="fas fa-user-plus me-2"></i>Get Started Free
            </a>
            <a href="login.php" class="btn btn-outline-light btn-lg rounded-pill px-4">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-light btn-lg rounded-pill px-4">
                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="container main-content">
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-4">
            <div class="stat-card h-100">
                <i class="fas fa-shield-alt"></i>
                <h4 class="mt-3">Secure Storage</h4>
                <p class="text-muted">Your files are stored securely with industry-standard protection.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stat-card h-100">
                <i class="fas fa-bolt"></i>
                <h4 class="mt-3">Fast Uploads</h4>
                <p class="text-muted">Lightning-fast upload speeds to get your files stored quickly.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stat-card h-100">
                <i class="fas fa-mobile-alt"></i>
                <h4 class="mt-3">Access Anywhere</h4>
                <p class="text-muted">Access your files from any device, anywhere in the world.</p>
            </div>
        </div>
    </div>
    
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4">
            <div class="card p-4">
                <h3 class="mb-4"><i class="fas fa-rocket text-primary me-2"></i>Get Started in 3 Easy Steps</h3>
                <div class="d-flex mb-3">
                    <div class="me-3">
                        <span class="badge bg-primary rounded-circle p-3">1</span>
                    </div>
                    <div>
                        <h5>Create Account</h5>
                        <p class="text-muted mb-0">Sign up for free in seconds</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-3">
                        <span class="badge bg-primary rounded-circle p-3">2</span>
                    </div>
                    <div>
                        <h5>Upload Files</h5>
                        <p class="text-muted mb-0">Drag and drop or click to upload</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="me-3">
                        <span class="badge bg-primary rounded-circle p-3">3</span>
                    </div>
                    <div>
                        <h5>Access Anywhere</h5>
                        <p class="text-muted mb-0">Download your files from any device</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card p-4 text-center" style="background: var(--primary-gradient); color: white;">
                <h3 class="mb-3"><i class="fas fa-crown me-2"></i>Free Plan</h3>
                <p class="display-4 fw-bold">10 Files</p>
                <p class="mb-4">Upload up to 10 files completely free!</p>
                <hr class="bg-white">
                <h4 class="mb-3">Need More?</h4>
                <p class="h2 text-warning"><?php echo PREMIUM_PRICE; ?></p>
                <p>Unlock unlimited uploads with Premium!</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="signup.php" class="btn btn-light btn-lg rounded-pill">Start Free Trial</a>
                <?php else: ?>
                    <a href="premium.php" class="btn btn-light btn-lg rounded-pill">Upgrade Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
