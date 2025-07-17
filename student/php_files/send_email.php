<?php
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// use Dotenv\Dotenv;

// require_once __DIR__ . '/../../vendor/autoload.php';
// require_once __DIR__ . '/../../config/config.php';

// $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
// $dotenv->load();

// function sendVerificationEmail($toEmail, $toName, $token) {
//     $mail = new PHPMailer(true);
//     try {
//         // SMTP settings
//         $mail->isSMTP();
//         $mail->Host       = $_ENV['SMTP_HOST'];
//         $mail->SMTPAuth   = true;
//         $mail->Username   = $_ENV['SMTP_USER'];
//         $mail->Password   = $_ENV['SMTP_PASS'];
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port       = $_ENV['SMTP_PORT'];

//         // Recipients
//         $mail->setFrom($_ENV['FROM_EMAIL'], $_ENV['FROM_NAME']);
//         $mail->addAddress($toEmail, $toName);

//         // Content
//         $mail->isHTML(true);
//         $mail->Subject = 'Verify Your Email';
//         $verifyLink = BASE_URL . "/student/php_files/verify_email.php?token=$token";
//         $mail->Body    = "Hi $toName,<br><br>Please verify your email:<br><a href='$verifyLink'>$verifyLink</a>";

//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         error_log("Mailer Error: " . $mail->ErrorInfo); // Server log
//         echo "Mailer Error: " . $mail->ErrorInfo;        // Direct output for debugging
//         return false;
//     }
// }




use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function sendVerificationEmail($toEmail, $toName, $token) {
    $verifyLink = BASE_URL . "/student/php_files/verify_email.php?token=$token";
    $subject = 'Verify Your Email';
    $body = "Hi $toName,<br><br>Please verify your email:<br><a href='$verifyLink'>$verifyLink</a>";
    return sendEmail($toEmail, $subject, $body);
}

function sendEmail($toEmail, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['FROM_EMAIL'], $_ENV['FROM_NAME']);
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
