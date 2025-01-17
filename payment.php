<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user has already made a payment
$stmt = $conn->prepare("SELECT has_paid, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Error: User not found.");
}

if (isset($user['has_paid']) && $user['has_paid']) {
    header("Location: index.php");
    exit();
}

// Razorpay API credentials (replace with your actual test credentials)
$razorpay_key_id = "rzp_test_sC9wQzWpja3MGt";
$razorpay_key_secret = "h6DAMnUJo2QrP9Xl4lmzBDIG";

// Payment amount (in paise)
$amount = 10000; // 100 INR

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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

        .form-container p {
            margin-bottom: 20px;
            text-align: center;
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
            width: 100%;
        }

        button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #6c63ff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader p {
            color: #fff;
            margin-top: 10px;
        }

        .error-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
            background-color: #ff6b6b;
            color: #fff;
        }

        .error-message i {
            font-size: 24px;
            margin-right: 10px;
        }

        .error-message.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div id="payment-container" class="container">
        <div class="form-header">
            <h2>Make Payment</h2>
        </div>
        <div class="form-container">
            <p>Please make a one-time payment of 100 INR to access the Typing Tutor.</p>
            <button id="pay-button">Pay Now</button>
        </div>
    </div>

    <div id="success-container" class="container" style="display: none;">
        <div class="form-header">
            <h2>Payment Successful</h2>
        </div>
        <div class="form-container">
            <p>Your payment has been processed successfully. You will be redirected shortly.</p>
        </div>
    </div>

    <div id="loader" class="loader" style="display: none;">
        <div class="spinner"></div>
        <p>Processing payment...</p>
    </div>

    <script>
        var options = {
            "key": "<?php echo $razorpay_key_id; ?>",
            "amount": "<?php echo $amount; ?>",
            "currency": "INR",
            "name": "Typing Tutor",
            "description": "One-time access fee",
            "handler": function (response){
                document.getElementById('loader').style.display = 'flex';

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "verify_payment.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        document.getElementById('loader').style.display = 'none';

                        if (xhr.status === 200) {
                            var result = JSON.parse(xhr.responseText);
                            if (result.success) {
                                showSuccessMessage();
                            } else {
                                showErrorMessage();
                            }
                        } else {
                            showErrorMessage();
                        }
                    }
                };
                xhr.send("payment_id=" + response.razorpay_payment_id);
            },
            "prefill": {
                "name": "<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>",
                "email": "<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>"
            },
            "theme": {
                "color": "#6c63ff"
            }
        };

        var rzp = new Razorpay(options);
        document.getElementById('pay-button').onclick = function(e){
            rzp.open();
            e.preventDefault();
        }

        function showSuccessMessage() {
            document.getElementById('payment-container').style.display = 'none';
            document.getElementById('success-container').style.display = 'block';

            setTimeout(function() {
                window.location.href = "index.php";
            }, 3000);
        }

        function showErrorMessage() {
            var errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.innerHTML = '<i class="fas fa-times-circle"></i><p>Payment failed. Please try again.</p>';
            document.body.appendChild(errorMessage);

            setTimeout(function() {
                errorMessage.classList.add('show');
            }, 100);

            setTimeout(function() {
                errorMessage.remove();
            }, 3000);
        }
    </script>
</body>
</html>