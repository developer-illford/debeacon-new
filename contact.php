<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ========================
   INCLUDE PHPMailer FILES
======================== */
require __DIR__ . '/src/Exception.php';
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';

/* ========================
   BASIC RESPONSE TYPE
======================== */
header('Content-Type: application/json');

/* ========================
   HONEYPOT CHECK
======================== */
if (!empty($_POST['website'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

/* ========================
   SANITIZE INPUTS (XSS)
======================== */
function clean($value) {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

$name    = clean($_POST['Name'] ?? '');
$email   = filter_var($_POST['Email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone   = clean($_POST['phone'] ?? '');
$message = clean($_POST['message'] ?? '');

/* ========================
   VALIDATION
======================== */
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error']);
    exit;
}

/* ========================
   GOOGLE reCAPTCHA CHECK
======================== */
$recaptchaSecret = '6LfFaE8sAAAAADvLbQD0zGK8A3mLBmA1WpXB50zs';
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

$verify = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}"
);

$captcha = json_decode($verify);

if (!$captcha || !$captcha->success) {
    echo json_encode(['status' => 'captcha_error']);
    exit;
}

/* ========================
   STYLED EMAIL BODY
======================== */
$body = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f9f9f9;
  padding: 20px;
  color: #333;
}
.container {
  background: #ffffff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  max-width: 600px;
  margin: auto;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
h2 {
  color: #004aad;
  margin-bottom: 20px;
  text-align: center;
}
.field {
  margin-bottom: 12px;
}
.label {
  font-weight: bold;
  color: #555;
}
.value {
  margin-left: 5px;
  color: #222;
}
.footer {
  margin-top: 20px;
  font-size: 13px;
  text-align: center;
  color: #777;
}
</style>
</head>
<body>
<div class='container'>
<h2> New Contact Form Submission</h2>

<div class='field'><span class='label'>Name:</span> <span class='value'>{$name}</span></div>
<div class='field'><span class='label'>Email:</span> <span class='value'>{$email}</span></div>
<div class='field'><span class='label'>Phone:</span> <span class='value'>{$phone}</span></div>

<div class='field'>
<span class='label'>Message:</span><br>
<div class='value' style='white-space:pre-line;margin-top:5px;'>{$message}</div>
</div>

<div class='footer'>This email was sent from your website contact form.</div>
</div>
</body>
</html>
";

/* ========================
   SEND MAIL USING GMAIL SMTP
======================== */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'de.beacon.international@gmail.com';       //  replace
    $mail->Password   = 'blxb toqw scmp hkit';   //  replace
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('de.beacon.international@gmail.com');
    $mail->addAddress('contact@debeacon.in'); // receive here
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body    = $body;

    $mail->send();

   header("Location: thankyou.html");
   exit;

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $mail->ErrorInfo
    ]);
}
