<?php
require_once 'config.php';
requireLogin();

$userId = $_SESSION['user_id'];
$isPremium = isUserPremium($userId);
$fileCount = getUserFileCount($userId);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionId = trim($_POST['transaction_id'] ?? '');
    $paymentType = trim($_POST['payment_type'] ?? '');
    $paymentDate = trim($_POST['payment_date'] ?? '');
    
    if (empty($transactionId) || empty($paymentType) || empty($paymentDate)) {
        $error = 'All fields are required.';
    } else {
        try {
            $db = getDB();
            
            // Check if transaction ID already exists
            $stmt = $db->prepare("SELECT id FROM payments WHERE transaction_id = ?");
            $stmt->execute([$transactionId]);
            
            if ($stmt->fetch()) {
                $error = 'This transaction ID has already been used.';
            } else {
                // Insert payment record
                $stmt = $db->prepare("INSERT INTO payments (user_id, transaction_id, payment_type, payment_date) VALUES (?, ?, ?, ?)");
                $stmt->execute([$userId, $transactionId, $paymentType, $paymentDate]);
                
                // Update user to premium
                $stmt = $db->prepare("UPDATE users SET is_premium = 1 WHERE id = ?");
                $stmt->execute([$userId]);
                
                $_SESSION['is_premium'] = 1;
                $isPremium = true;
                
                $success = 'Payment submitted successfully! Your account has been upgraded to Premium.';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}

require_once 'header.php';
?>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if ($isPremium): ?>
                <div class="card p-5 text-center" style="background: var(--primary-gradient); color: white;">
                    <div class="mb-4">
                        <i class="fas fa-crown fa-4x text-warning"></i>
                    </div>
                    <h2 class="fw-bold mb-3">You're a Premium Member!</h2>
                    <p class="lead mb-4">Enjoy unlimited file uploads and all premium features.</p>
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <div class="bg-white bg-opacity-10 rounded p-3">
                                <i class="fas fa-infinity fa-2x mb-2"></i>
                                <h5>Unlimited Uploads</h5>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="bg-white bg-opacity-10 rounded p-3">
                                <i class="fas fa-headset fa-2x mb-2"></i>
                                <h5>Priority Support</h5>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="bg-white bg-opacity-10 rounded p-3">
                                <i class="fas fa-rocket fa-2x mb-2"></i>
                                <h5>Fast Uploads</h5>
                            </div>
                        </div>
                    </div>
                    <a href="upload.php" class="btn btn-light btn-lg rounded-pill mt-3">
                        <i class="fas fa-upload me-2"></i>Start Uploading
                    </a>
                </div>
            <?php else: ?>
                <div class="card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-crown fa-3x text-warning"></i>
                        </div>
                        <h2 class="fw-bold">Upgrade to Premium</h2>
                        <p class="text-muted">Unlock unlimited uploads for just <?php echo PREMIUM_PRICE; ?></p>
                    </div>
                    
                    <?php if ($fileCount < MAX_FREE_FILES): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You've used <strong><?php echo $fileCount; ?></strong> of your <strong><?php echo MAX_FREE_FILES; ?></strong> free uploads. 
                            Upgrade anytime to unlock unlimited storage!
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You've reached your free upload limit! Submit your payment details below to upgrade.
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-2">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Free Plan</h5>
                                    <p class="display-6 fw-bold">$0</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i><?php echo MAX_FREE_FILES; ?> Files</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Basic Support</li>
                                        <li class="text-muted"><i class="fas fa-times me-2"></i>Limited Storage</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-primary border-2" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                                <div class="card-body text-center">
                                    <span class="badge bg-warning text-dark mb-2">RECOMMENDED</span>
                                    <h5 class="text-primary">Premium Plan</h5>
                                    <p class="display-6 fw-bold text-primary"><?php echo PREMIUM_PRICE; ?></p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Unlimited Files</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Priority Support</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Larger File Sizes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card bg-light p-4 mb-4">
                        <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Instructions</h5>
                        <p class="mb-2">1. Make a payment of <strong><?php echo PREMIUM_PRICE; ?></strong> using one of the following methods:</p>
                        <ul>
                            <li>PayPal: cloudrive@example.com</li>
                            <li>Bank Transfer: Contact support for details</li>
                            <li>Credit/Debit Card: Use our payment gateway</li>
                        </ul>
                        <p class="mb-0">2. Fill in the form below with your payment details.</p>
                    </div>
                    
                    <h4 class="mb-3"><i class="fas fa-file-invoice me-2"></i>Submit Payment Details</h4>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">
                                <i class="fas fa-hashtag me-1"></i>Transaction ID
                            </label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                                   placeholder="Enter your payment transaction ID" required
                                   value="<?php echo htmlspecialchars($_POST['transaction_id'] ?? ''); ?>">
                            <small class="text-muted">The unique ID from your payment confirmation</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_type" class="form-label">
                                <i class="fas fa-wallet me-1"></i>Payment Type
                            </label>
                            <select class="form-control" id="payment_type" name="payment_type" required>
                                <option value="">Select payment method</option>
                                <option value="paypal" <?php echo (($_POST['payment_type'] ?? '') === 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                                <option value="credit_card" <?php echo (($_POST['payment_type'] ?? '') === 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
                                <option value="debit_card" <?php echo (($_POST['payment_type'] ?? '') === 'debit_card') ? 'selected' : ''; ?>>Debit Card</option>
                                <option value="bank_transfer" <?php echo (($_POST['payment_type'] ?? '') === 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                <option value="upi" <?php echo (($_POST['payment_type'] ?? '') === 'upi') ? 'selected' : ''; ?>>UPI</option>
                                <option value="other" <?php echo (($_POST['payment_type'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="payment_date" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Payment Date
                            </label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                   required value="<?php echo htmlspecialchars($_POST['payment_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Submit Payment Details
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
