<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT id, otp, otp_expiry FROM users WHERE email = ? AND is_verified = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['otp'] == $otp && strtotime($user['otp_expiry']) > time()) {
            $update_stmt = $conn->prepare("UPDATE users SET is_verified = 1, otp = NULL, otp_expiry = NULL WHERE id = ?");
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            $update_stmt->close();

            $success = "Email verified successfully. <br> You can now log in.";
            unset($_SESSION['email']);
        } else {
            $error = "Invalid or expired OTP. Please try again.";
        }
    } else {
        $error = "User not found or already verified.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
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

        .form-container {
            padding: 30px 25px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
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

        .input-group input {
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 50px;
            font-size: 14px;
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(108, 99, 255, 0.2);
        }

        .input-group input:focus + i {
            color: var(--primary-color);
        }

        button {
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

        button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h2>Verify Your Email</h2>
        </div>
        <div class="form-container">
            <?php
            if (!empty($error)) {
                echo "<p class='error'>$error</p>";
            }
            if (!empty($success)) {
                echo "<p class='success'>$success</p>";
                echo "<button onclick=\"window.location.href='login.php'\">Back to Login</button>";
            } else {
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="input-group">
                    <i class="fas fa-key"></i>
                    <input type="text" name="otp" id="otp" placeholder="Enter OTP" required>
                </div>
                <button type="submit">Verify</button>
            </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>