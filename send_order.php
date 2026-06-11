<?php
/**
 * Sweet Delights — Order Email Handler
 * Place this file in the ROOT of your website (same level as index.html)
 * 
 * SETUP:
 *  1. Edit the $OWNER_EMAIL below to your real Gmail address
 *  2. Upload this file + the phpmailer-master folder to your server
 *  3. If using Gmail SMTP: create an App Password at myaccount.google.com/apppasswords
 *     then fill in $GMAIL_USER and $GMAIL_APP_PASSWORD below
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ============================================================
// ✏️  CONFIGURE THESE — YOUR SETTINGS
// ============================================================
$OWNER_EMAIL      = 'joca.gayla.coc@phinmaed.com';   // ← Your Gmail that receives order alerts
$OWNER_NAME       = 'Sweet Delights Owner';
$FROM_EMAIL       = 'noreply@sweetdelights.com'; // ← Your domain email (or same as above)
$FROM_NAME        = 'Sweet Delights';
$GMAIL_USER       = 'YOUR_EMAIL@gmail.com';   // ← Gmail for SMTP sending
$GMAIL_APP_PASSWORD = 'ecuo xbnc tzoh fvlk';  // ← Gmail App Password (not your main password)
// ============================================================

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$name    = htmlspecialchars(trim($data['name'] ?? ''));
$email   = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone   = htmlspecialchars(trim($data['phone'] ?? ''));
$address = htmlspecialchars(trim($data['address'] ?? ''));
$notes   = htmlspecialchars(trim($data['notes'] ?? 'None'));
$payment = htmlspecialchars(trim($data['payment'] ?? 'Cash on Delivery'));
$cart    = $data['cart'] ?? [];
$total   = number_format(floatval($data['total'] ?? 0), 2);

if (!$name || !$email || !$phone || !$address || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Build order items HTML
$itemsHtml = '';
$itemsText = '';
foreach ($cart as $item) {
    $iName  = htmlspecialchars($item['name'] ?? '');
    $iQty   = intval($item['qty'] ?? 1);
    $iPrice = number_format(floatval($item['price'] ?? 0) * $iQty, 2);
    $itemsHtml .= "<tr>
        <td style='padding:10px 16px;border-bottom:1px solid #f3e8f7;'>{$iName}</td>
        <td style='padding:10px 16px;border-bottom:1px solid #f3e8f7;text-align:center;'>{$iQty}</td>
        <td style='padding:10px 16px;border-bottom:1px solid #f3e8f7;text-align:right;font-weight:700;color:#e91e8c;'>&#8369;{$iPrice}</td>
    </tr>";
    $itemsText .= "  • {$iName} x{$iQty} = ₱{$iPrice}\n";
}

// ============================
// EMAIL TO STORE OWNER (YOU)
// ============================
$ownerSubject = "🎂 New Order from {$name} — ₱{$total}";
$ownerHtml = "
<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>
  body{font-family:Arial,sans-serif;background:#fdf4fb;margin:0;padding:0;}
  .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(233,30,140,0.1);}
  .header{background:linear-gradient(135deg,#e91e8c,#f06ab0);padding:36px 40px;text-align:center;}
  .header h1{color:white;margin:0;font-size:1.6rem;}
  .header p{color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:0.95rem;}
  .body{padding:32px 40px;}
  .section{background:#fdf4fb;border-radius:12px;padding:20px 24px;margin-bottom:20px;}
  .section h3{color:#e91e8c;font-size:0.85rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;}
  .info-row{display:flex;margin-bottom:8px;}
  .info-label{font-weight:700;color:#555;min-width:90px;font-size:0.88rem;}
  .info-val{color:#111;font-size:0.88rem;}
  table{width:100%;border-collapse:collapse;margin-top:8px;}
  th{background:#f9e8f5;padding:10px 16px;text-align:left;font-size:0.8rem;font-weight:700;color:#e91e8c;text-transform:uppercase;letter-spacing:0.5px;}
  th:last-child{text-align:right;}
  th:nth-child(2){text-align:center;}
  .total-row td{padding:14px 16px;font-weight:700;font-size:1rem;background:#fdf4fb;}
  .badge{display:inline-block;background:#e91e8c;color:white;padding:4px 14px;border-radius:20px;font-size:0.8rem;font-weight:700;}
  .footer{background:#1a1a2e;padding:20px 40px;text-align:center;color:#999;font-size:0.78rem;}
</style></head><body>
<div class='wrap'>
  <div class='header'>
    <h1>🎂 New Order Received!</h1>
    <p>Sweet Delights — Order Notification</p>
  </div>
  <div class='body'>
    <div class='section'>
      <h3>👤 Customer Details</h3>
      <div class='info-row'><span class='info-label'>Name</span><span class='info-val'><strong>{$name}</strong></span></div>
      <div class='info-row'><span class='info-label'>Email</span><span class='info-val'><a href='mailto:{$email}' style='color:#e91e8c;'>{$email}</a></span></div>
      <div class='info-row'><span class='info-label'>Phone</span><span class='info-val'><a href='tel:{$phone}' style='color:#e91e8c;'>{$phone}</a></span></div>
      <div class='info-row'><span class='info-label'>Address</span><span class='info-val'>{$address}</span></div>
      <div class='info-row'><span class='info-label'>Payment</span><span class='info-val'><span class='badge'>{$payment}</span></span></div>
    </div>
    <div class='section'>
      <h3>🛍️ Order Items</h3>
      <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>{$itemsHtml}</tbody>
        <tfoot><tr class='total-row'>
          <td colspan='2'>TOTAL</td>
          <td style='text-align:right;color:#e91e8c;font-size:1.1rem;'>&#8369;{$total}</td>
        </tr></tfoot>
      </table>
    </div>
    " . ($notes !== 'None' ? "<div class='section'><h3>📝 Special Notes</h3><p style='color:#555;font-size:0.9rem;margin:0;'>{$notes}</p></div>" : "") . "
    <div style='background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:16px 20px;font-size:0.85rem;color:#92400e;'>
      <strong>⚡ Action Required:</strong> Please contact the customer to confirm their order and arrange delivery.
      " . ($payment !== 'Cash on Delivery' ? "<br><strong>💳 {$payment} payment</strong> — verify payment before preparing the order." : '') . "
    </div>
  </div>
  <div class='footer'>Sweet Delights Website — Order Notification System</div>
</div>
</body></html>";

// ============================
// CONFIRMATION EMAIL TO CUSTOMER
// ============================
$custSubject = "✅ Order Confirmed — Sweet Delights";
$custHtml = "
<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>
  body{font-family:Arial,sans-serif;background:#fdf4fb;margin:0;padding:0;}
  .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(233,30,140,0.1);}
  .header{background:linear-gradient(135deg,#e91e8c,#f06ab0);padding:36px 40px;text-align:center;}
  .header h1{color:white;margin:0;font-size:1.6rem;}
  .header p{color:rgba(255,255,255,0.85);margin:8px 0 0;}
  .body{padding:32px 40px;}
  .section{background:#fdf4fb;border-radius:12px;padding:20px 24px;margin-bottom:20px;}
  table{width:100%;border-collapse:collapse;}
  th{background:#f9e8f5;padding:10px 16px;text-align:left;font-size:0.8rem;font-weight:700;color:#e91e8c;text-transform:uppercase;}
  th:last-child,td:last-child{text-align:right;}
  td{padding:10px 16px;border-bottom:1px solid #f3e8f7;font-size:0.88rem;}
  .total-row td{font-weight:700;background:#fdf4fb;}
  .steps{counter-reset:step;}
  .step{display:flex;gap:14px;margin-bottom:16px;align-items:flex-start;}
  .step-num{background:#e91e8c;color:white;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.82rem;flex-shrink:0;}
  .footer{background:#1a1a2e;padding:20px 40px;text-align:center;color:#999;font-size:0.78rem;}
</style></head><body>
<div class='wrap'>
  <div class='header'>
    <h1>🎉 Thank you, {$name}!</h1>
    <p>Your order has been received by Sweet Delights</p>
  </div>
  <div class='body'>
    <p style='color:#555;margin-bottom:24px;line-height:1.6;'>We're so excited to prepare your order! Here's a summary of what you ordered:</p>
    <div class='section'>
      <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>{$itemsHtml}</tbody>
        <tfoot><tr class='total-row'>
          <td colspan='2'><strong>TOTAL</strong></td>
          <td style='color:#e91e8c;font-size:1.05rem;'><strong>&#8369;{$total}</strong></td>
        </tr></tfoot>
      </table>
    </div>
    <div class='section'>
      <p style='font-weight:700;margin-bottom:14px;color:#111;'>📦 What happens next:</p>
      <div class='step'><div class='step-num'>1</div><div><strong>Order Received</strong><br><span style='color:#777;font-size:0.85rem;'>We have your order and are reviewing it now.</span></div></div>
      <div class='step'><div class='step-num'>2</div><div><strong>We'll contact you</strong><br><span style='color:#777;font-size:0.85rem;'>Our team will call or text you at {$phone} to confirm details.</span></div></div>
      <div class='step'><div class='step-num'>3</div><div><strong>Preparation</strong><br><span style='color:#777;font-size:0.85rem;'>Your treats are freshly baked and prepared with love.</span></div></div>
      <div class='step'><div class='step-num'>4</div><div><strong>Delivery</strong><br><span style='color:#777;font-size:0.85rem;'>Your order is delivered to {$address}.</span></div></div>
    </div>
    " . ($payment !== 'Cash on Delivery' ? "
    <div style='background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:16px 20px;font-size:0.85rem;color:#92400e;margin-bottom:20px;'>
      <strong>💳 Payment Reminder ({$payment}):</strong><br>
      Please send &#8369;{$total} and use your name (<strong>{$name}</strong>) as your payment reference.
    </div>" : '') . "
    <p style='color:#999;font-size:0.82rem;text-align:center;'>Questions? Call us at <a href='tel:5551234567' style='color:#e91e8c;'>(555) 123-4567</a> or reply to this email.</p>
  </div>
  <div class='footer'>© 2024 Sweet Delights · 123 Baker Street, Cagayan de Oro · info@sweetdelights.com</div>
</div>
</body></html>";

// ============================
// SEND EMAILS VIA PHPMAILER
// ============================
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
    echo json_encode(['success' => true, 'message' => 'Order confirmed! Check your email.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not send email. Please call us directly.']);
}
