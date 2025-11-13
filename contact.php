<?php
// === CONFIG ===
$toEmail   = "contact@debeacon.in"; 

$bccEmail  = "developer.illforddigital@gmail.com"; 

$bccEmail  = "edb@illforddigital.com"; 

$subject   = "New Contact Form Submission";
$recaptchaSecret = "6LfPy7MrAAAAAPKzXXKTOvCthZuL8zC8lUvBrgVi"; 

// === HELPER: Sanitize ===
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// === HONEYPOT CHECK ===
if (!empty($_POST['website'])) { // fake hidden field
    http_response_code(400);
    echo "Spam detected.";
    exit;
}

// === RECAPTCHA CHECK ===
if (empty($_POST['g-recaptcha-response'])) {
    http_response_code(400);
    echo "reCAPTCHA not completed.";
    exit;
}

$recaptchaResponse = $_POST['g-recaptcha-response'];
$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptchaSecret . "&response=" . $recaptchaResponse);
$captchaSuccess = json_decode($verify);

if (!$captchaSuccess->success) {
    http_response_code(400);
    echo "reCAPTCHA verification failed.";
    exit;
}

// === GET AND VALIDATE FORM FIELDS ===
$name    = isset($_POST['Name']) ? sanitize_input($_POST['Name']) : '';
$email   = isset($_POST['Email']) ? filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL) : '';
$phone   = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
$message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';

if (empty($name) || empty($email) || empty($phone) || empty($message)) {
    http_response_code(400);
    echo "All fields are required.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Invalid email address.";
    exit;
}

// === EMAIL HEADERS ===
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: ".$name." <".$email.">" . "\r\n";
$headers .= "Reply-To: ".$email. "\r\n";
$headers .= "BCC: ".$bccEmail. "\r\n"; // 

// === STYLED EMAIL BODY (HTML + CSS) ===
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
    <h2>📩 New Contact Form Submission</h2>
    <div class='field'><span class='label'>Name:</span> <span class='value'>$name</span></div>
    <div class='field'><span class='label'>Email:</span> <span class='value'>$email</span></div>
    <div class='field'><span class='label'>Phone:</span> <span class='value'>$phone</span></div>
    <div class='field'><span class='label'>Message:</span><br>
      <div class='value' style='white-space:pre-line; margin-top:5px;'>$message</div>
    </div>
    <div class='footer'>This email was sent from your website contact form.</div>
  </div>
</body>
</html>
";

// === SEND EMAIL ===
if (mail($toEmail, $subject, $body, $headers)) {
    header("Location: thankyou.html"); // ✅ redirect after success
    exit;
} else {
    http_response_code(500);
    echo "Mailer Error: Unable to send message.";
}
?>
