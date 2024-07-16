<?php
session_start();
require_once 'db_connection.php';

$error = $success = '';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, username, password, is_verified FROM users WHERE username = ?");
        
        if ($stmt === false) {
            $error = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        if ($user['is_verified'] == 1) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            header("Location: index.php");
                            exit();
                        } else {
                            $error = "Please verify your email before logging in.";
                        }
                    } else {
                        $error = "Invalid username or password";
                    }
                } else {
                    $error = "Invalid username or password";
                }
            } else {
                $error = "Error executing statement: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c63ff;
            --secondary-color: #f5f5f5;
            --text-color: #333;
            --error-color: #ff6b6b;
            --success-color: #51cf66;
            --gradient: linear-gradient(135deg, #6c63ff, #4834d4);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--secondary-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-color);
            background-image: 
                radial-gradient(circle at top left, rgba(108, 99, 255, 0.1) 0%, transparent 30%),
                radial-gradient(circle at bottom right, rgba(72, 52, 212, 0.1) 0%, transparent 30%);
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }
       
        .container {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 90%;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .form-header {
            background: var(--gradient);
            color: #fff;
            padding: 30px 20px;
            text-align: center;
        }

        .form-header h2 {
            margin-bottom: 10px;
            font-weight: 600;
        }

        .form-header p {
            opacity: 0.8;
            font-size: 0.9em;
        }

        .form-container {
            padding: 30px 25px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container input {
            padding: 12px 15px 12px 40px;
            margin-bottom: 0;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 50px;
            font-size: 14px;
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .form-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(108, 99, 255, 0.2);
        }

        .form-container button {
            background: var(--gradient);
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
            width: 100%;
        }

        .form-container button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .form-switcher {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .form-switcher button {
            background: none;
            border: none;
            color: #777;
            font-size: 16px;
            cursor: pointer;
            padding: 5px 15px;
            margin: 0 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .form-switcher button::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .form-switcher button.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-switcher button.active::after {
            transform: scaleX(1);
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            opacity: 0.8;
        }

        .error, .success {
            padding: 10px 15px;
            border-radius: 50px;
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }

        .error {
            background-color: var(--error-color);
            color: #fff;
        }

        .success {
            background-color: var(--success-color);
            color: #fff;
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            color: #777;
            font-size: 16px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .input-group input:focus + i {
            color: var(--primary-color);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-footer a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
<div class="container">
        <div class="form-header">
            <h2>Welcome</h2>
        </div>
        <div class="form-container">
            <div class="form-switcher">
                <button id="loginBtn" class="active">Login</button>
                <button id="signupBtn">Sign Up</button>
            </div>
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="loginUsername" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <form id="signupForm" action="signup.php" method="post" style="display: none;">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="signupUsername" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="signupEmail" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-mobile-alt"></i>
                    <input type="tel" name="mobile" id="signupMobile" placeholder="Mobile Number (10 digits)" 
                           pattern="[0-9]{10}" maxlength="10" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                           title="Please enter exactly 10 digits" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="signupPassword" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" id="signupConfirmPassword" placeholder="Confirm Password" required>
                </div>
                <button type="submit">Sign Up</button>
            </form>
            <div id="noAccountMessage" class="form-footer">
                <p>Don't have an account ? <a href="#" id="switchToSignup">Sign up</a></p>
            </div>
            <div class="forgot-password">
                <a href="forgot_password.php">Forgot your password?</a>
            </div>
            <?php
            if (!empty($error)) {
                echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
            }
            if (!empty($success)) {
                echo "<p class='success'>" . htmlspecialchars($success) . "</p>";
            }
            ?>
        </div>
    </div>
    <script>
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');
        const loginBtn = document.getElementById('loginBtn');
        const signupBtn = document.getElementById('signupBtn');
        const switchToSignupLink = document.getElementById('switchToSignup');
        const noAccountMessage = document.getElementById('noAccountMessage');

        function showLoginForm() {
            loginForm.style.display = 'flex';
            signupForm.style.display = 'none';
            loginBtn.classList.add('active');
            signupBtn.classList.remove('active');
            noAccountMessage.style.display = 'block';
        }

        function showSignupForm() {
            loginForm.style.display = 'none';
            signupForm.style.display = 'flex';
            loginBtn.classList.remove('active');
            signupBtn.classList.add('active');
            noAccountMessage.style.display = 'none';
        }

        loginBtn.addEventListener('click', showLoginForm);
        signupBtn.addEventListener('click', showSignupForm);
        switchToSignupLink.addEventListener('click', (e) => {
            e.preventDefault();
            showSignupForm();
        });

        document.getElementById('signupMobile').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });

        // Initialize the correct view
        if (loginForm.style.display !== 'none') {
            showLoginForm();
        } else {
            showSignupForm();
        }
    </script>
</body>
</html>