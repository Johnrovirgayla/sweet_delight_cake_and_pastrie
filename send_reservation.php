<?php
/**
 * Sweet Delights — Reservation Email Handler
 * Place in ROOT of your website (same level as index.html)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ============================================================
// ✏️  CONFIGURE THESE — YOUR SETTINGS (same as send_order.php)
// ============================================================
$OWNER_EMAIL        = 'joca.gayla.coc@phinmaed.com';
$OWNER_NAME         = 'Sweet Delights Owner';
$FROM_EMAIL         = 'noreply@sweetdelights.com';
$FROM_NAME          = 'Sweet Delights';
$GMAIL_USER         = 'YOUR_EMAIL@gmail.com';
$GMAIL_APP_PASSWORD = 'ecuo xbnc tzoh fvlk';
// ============================================================

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$name   = htmlspecialchars(trim($data['name']   ?? ''));
$email  = filter_var(trim($data['email']  ?? ''), FILTER_VALIDATE_EMAIL);
$phone  = htmlspecialchars(trim($data['phone']  ?? ''));
$date   = htmlspecialchars(trim($data['date']   ?? ''));
$time   = htmlspecialchars(trim($data['time']   ?? ''));
$guests = htmlspecialchars(trim($data['guests'] ?? ''));
$notes  = htmlspecialchars(trim($data['notes']  ?? 'None'));

if (!$name || !$email || !$phone || !$date || !$time || !$guests) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$dateObj = new DateTime($date);
$dateFormatted = $dateObj->format('l, F j, Y');

// ============================
// EMAIL TO STORE OWNER
// ============================
$ownerSubject = "🍰 New Reservation from {$name} — {$dateFormatted} at {$time}";
$ownerHtml = "
<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>
  body{font-family:Arial,sans-serif;background:#fdf4fb;margin:0;padding:0;}
  .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(233,30,140,0.1);}
  .header{background:linear-gradient(135deg,#e91e8c,#f06ab0);padding:36px 40px;text-align:center;}
  .header h1{color:white;margin:0;font-size:1.5rem;}
  .header p{color:rgba(255,255,255,0.85);margin:8px 0 0;}
  .body{padding:32px 40px;}
  .section{background:#fdf4fb;border-radius:12px;padding:20px 24px;margin-bottom:20px;}
  .section h3{color:#e91e8c;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;}
  .info-row{display:flex;margin-bottom:10px;gap:12px;}
  .info-label{font-weight:700;color:#555;min-width:100px;font-size:0.88rem;flex-shrink:0;}
  .info-val{color:#111;font-size:0.88rem;}
  .highlight{background:#e91e8c;color:white;padding:16px 24px;border-radius:12px;text-align:center;margin-bottom:20px;}
  .highlight .date{font-size:1.3rem;font-weight:800;}
  .highlight .meta{font-size:0.9rem;opacity:0.9;margin-top:4px;}
  .footer{background:#1a1a2e;padding:20px 40px;text-align:center;color:#999;font-size:0.78rem;}
</style></head><body>
<div class='wrap'>
  <div class='header'>
    <h1>🍰 New Reservation Request!</h1>
    <p>Sweet Delights — Reservation Notification</p>
  </div>
  <div class='body'>
    <div class='highlight'>
      <div class='date'>📅 {$dateFormatted}</div>
      <div class='meta'>⏰ {$time} &nbsp;·&nbsp; 👥 {$guests}</div>
    </div>
    <div class='section'>
      <h3>👤 Guest Details</h3>
      <div class='info-row'><span class='info-label'>Name</span><span class='info-val'><strong>{$name}</strong></span></div>
      <div class='info-row'><span class='info-label'>Email</span><span class='info-val'><a href='mailto:{$email}' style='color:#e91e8c;'>{$email}</a></span></div>
      <div class='info-row'><span class='info-label'>Phone</span><span class='info-val'><a href='tel:{$phone}' style='color:#e91e8c;'>{$phone}</a></span></div>
    </div>
    " . ($notes !== 'None' ? "<div class='section'><h3>📝 Special Requests</h3><p style='color:#555;font-size:0.9rem;margin:0;'>{$notes}</p></div>" : '') . "
    <div style='background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:16px 20px;font-size:0.85rem;color:#92400e;'>
      <strong>⚡ Action Required:</strong> Please confirm this reservation by calling <a href='tel:{$phone}' style='color:#92400e;'>{$phone}</a> or emailing <a href='mailto:{$email}' style='color:#92400e;'>{$email}</a> within 1 hour.
    </div>
  </div>
  <div class='footer'>Sweet Delights Website — Reservation Notification System</div>
</div>
</body></html>";

// ============================
// CONFIRMATION EMAIL TO CUSTOMER
// ============================
$custSubject = "✅ Reservation Request Received — Sweet Delights";
$custHtml = "
<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>
  body{font-family:Arial,sans-serif;background:#fdf4fb;margin:0;padding:0;}
  .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;}
  .header{background:linear-gradient(135deg,#e91e8c,#f06ab0);padding:36px 40px;text-align:center;}
  .header h1{color:white;margin:0;font-size:1.5rem;}
  .header p{color:rgba(255,255,255,0.85);margin:8px 0 0;}
  .body{padding:32px 40px;}
  .card{background:#fdf4fb;border-radius:12px;padding:20px 24px;margin-bottom:20px;text-align:center;}
  .card .big{font-size:1.2rem;font-weight:800;color:#e91e8c;}
  .card .sub{color:#777;font-size:0.88rem;margin-top:4px;}
  .info-row{display:flex;gap:12px;margin-bottom:8px;}
  .info-label{font-weight:700;color:#555;min-width:90px;font-size:0.85rem;}
  .info-val{color:#111;font-size:0.85rem;}
  .footer{background:#1a1a2e;padding:20px 40px;text-align:center;color:#999;font-size:0.78rem;}
</style></head><body>
<div class='wrap'>
  <div class='header'>
    <h1>🎂 We'd love to see you!</h1>
    <p>Your reservation at Sweet Delights is being prepared</p>
  </div>
  <div class='body'>
    <p style='color:#555;margin-bottom:24px;line-height:1.6;'>Hi <strong>{$name}</strong>! We've received your reservation request. Here's a summary:</p>
    <div class='card'>
      <div class='big'>📅 {$dateFormatted}</div>
      <div class='sub'>⏰ {$time} &nbsp;·&nbsp; 👥 {$guests}</div>
    </div>
    " . ($notes !== 'None' ? "<div style='background:#fff3cd;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:0.85rem;color:#856404;'><strong>Your special requests:</strong> {$notes}</div>" : '') . "
    <div style='background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;font-size:0.85rem;color:#166534;'>
      We'll confirm your reservation via phone (<strong>{$phone}</strong>) or email within 1 hour of your request.
    </div>
    <p style='color:#999;font-size:0.82rem;text-align:center;'>Questions? Call us at <a href='tel:5551234567' style='color:#e91e8c;'>(555) 123-4567</a></p>
  </div>
  <div class='footer'>© 2024 Sweet Delights · 123 Baker Street, Cagayan de Oro</div>
</div>
</body></html>";

require_once __DIR__ . '/phpmailer-master/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer-master/src/SMTP.php';
require_once __DIR__ . '/phpmailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $toName, $subject, $htmlBody, $fromEmail, $fromName, $gmailUser, $gmailPass) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmailUser;
        $mail->Password   = $gmailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to, $toName);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$sent1 = sendMail($OWNER_EMAIL, $OWNER_NAME, $ownerSubject, $ownerHtml, $FROM_EMAIL, $FROM_NAME, $GMAIL_USER, $GMAIL_APP_PASSWORD);
$sent2 = sendMail($email, $name, $custSubject, $custHtml, $FROM_EMAIL, $FROM_NAME, $GMAIL_USER, $GMAIL_APP_PASSWORD);

if ($sent1 || $sent2) {
    echo json_encode(['success' => true, 'message' => 'We\'ll confirm your reservation within 1 hour.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not send email. Please call us at (555) 123-4567.']);
}
