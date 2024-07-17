<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure you've installed PHPMailer via Composer

function sendVerificationEmail($to, $verificationLink) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gajjarkartik74@gmail.com'; // Replace with your email
        $mail->Password   = 'plyq bzzh wesg bhlf'; // Replace with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('gajjarkartik74@gmail.com', 'typing tutor');
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body    = "Please click the following link to verify your email address: <a href='$verificationLink'>Verify Email</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error sending verification email: {$mail->ErrorInfo}");
        return false;
    }
}

function sendPasswordResetEmail($to, $resetLink) {
    $mail = new PHPMailer(true);

    try {
        // Server settings (same as above)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gajjarkartik74@gmail.com'; // Replace with your email
        $mail->Password   = 'plyq bzzh wesg bhlf'; // Replace with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('gajjarkartik74@gmail.com', 'typing tutor');
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body    = "Please click the following link to reset your password: <a href='$resetLink'>Reset Password</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error sending password reset email: {$mail->ErrorInfo}");
        return false;
    }
}