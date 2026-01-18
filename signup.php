<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $country_code = trim($_POST['country_code'] ?? '');
    $phone_no = trim($_POST['phone_no'] ?? '');
    
    // Validation
    if (empty($username) || empty($password) || empty($email) || empty($country_code) || empty($phone_no)) {
        $error = 'All fields are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $db = getDB();
            
            // Check if username or email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error = 'Username or email already exists.';
            } else {
                // Create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password, email, country_code, phone_no) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $hashedPassword, $email, $country_code, $phone_no]);
                
                $success = 'Account created successfully! You can now login.';
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
        <div class="col-md-6 col-lg-5">
            <div class="card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-user-plus fa-3x" style="color: #667eea;"></i>
                    </div>
                    <h2 class="fw-bold">Create Account</h2>
                    <p class="text-muted">Join CloudDrive today - it's free!</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        <br><a href="login.php" class="alert-link">Click here to login</a>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-1"></i>Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               placeholder="Choose a username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email Address
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               placeholder="Enter your email" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="country_code" class="form-label">
                                <i class="fas fa-globe me-1"></i>Code
                            </label>
                            <select class="form-control" id="country_code" name="country_code" required>
                                <option value="">Select</option>
                                <option value="+1" <?php echo (($_POST['country_code'] ?? '') === '+1') ? 'selected' : ''; ?>>+1 (US)</option>
                                <option value="+44" <?php echo (($_POST['country_code'] ?? '') === '+44') ? 'selected' : ''; ?>>+44 (UK)</option>
                                <option value="+91" <?php echo (($_POST['country_code'] ?? '') === '+91') ? 'selected' : ''; ?>>+91 (IN)</option>
                                <option value="+86" <?php echo (($_POST['country_code'] ?? '') === '+86') ? 'selected' : ''; ?>>+86 (CN)</option>
                                <option value="+81" <?php echo (($_POST['country_code'] ?? '') === '+81') ? 'selected' : ''; ?>>+81 (JP)</option>
                                <option value="+49" <?php echo (($_POST['country_code'] ?? '') === '+49') ? 'selected' : ''; ?>>+49 (DE)</option>
                                <option value="+33" <?php echo (($_POST['country_code'] ?? '') === '+33') ? 'selected' : ''; ?>>+33 (FR)</option>
                                <option value="+61" <?php echo (($_POST['country_code'] ?? '') === '+61') ? 'selected' : ''; ?>>+61 (AU)</option>
                                <option value="+55" <?php echo (($_POST['country_code'] ?? '') === '+55') ? 'selected' : ''; ?>>+55 (BR)</option>
                                <option value="+7" <?php echo (($_POST['country_code'] ?? '') === '+7') ? 'selected' : ''; ?>>+7 (RU)</option>
                            </select>
                        </div>
                        <div class="col-8">
                            <label for="phone_no" class="form-label">
                                <i class="fas fa-phone me-1"></i>Phone Number
                            </label>
                            <input type="tel" class="form-control" id="phone_no" name="phone_no" 
                                   value="<?php echo htmlspecialchars($_POST['phone_no'] ?? ''); ?>" 
                                   placeholder="Enter phone number" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Create a password" required>
                        <small class="text-muted">At least 6 characters</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Confirm Password
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm your password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                    
                    <p class="text-center text-muted mb-0">
                        Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
