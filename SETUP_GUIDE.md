# Sweet Delights — Complete Setup Guide

## Folder Structure
```
sweet-delights/
├── index.html              ← Homepage
├── css/
│   └── style.css
├── js/
│   ├── products.js
│   ├── cart.js
│   └── main.js
├── pages/
│   ├── shop.html
│   ├── cart.html
│   ├── checkout.html
│   └── reservations.html
├── phpmailer-master/
│   └── src/
│       ├── PHPMailer.php   ← Real PHPMailer (included)
│       ├── SMTP.php
│       └── Exception.php
├── send_order.php          ← Handles order emails
├── send_reservation.php    ← Handles reservation emails
└── SETUP_GUIDE.md          ← This file
```

---

## STEP 1 — Edit Your Email Settings

Open both `send_order.php` AND `send_reservation.php` and change:

```php
$OWNER_EMAIL        = 'YOUR_EMAIL@gmail.com';   // ← Your Gmail
$GMAIL_USER         = 'YOUR_EMAIL@gmail.com';   // ← Same Gmail
$GMAIL_APP_PASSWORD = 'xxxx xxxx xxxx xxxx';    // ← See Step 2 below
```

Also update the Gmail compose fallback in `pages/checkout.html` and
`pages/reservations.html` — search for `YOUR_EMAIL@gmail.com` and replace.

---

## STEP 2 — Create a Gmail App Password

Your regular Gmail password won't work with SMTP. You need an App Password:

1. Go to: https://myaccount.google.com/security
2. Turn ON "2-Step Verification" (required)
3. Go to: https://myaccount.google.com/apppasswords
4. Select app: "Mail", device: "Other" → type "Sweet Delights"
5. Click Generate → Copy the 16-character password
6. Paste it into `$GMAIL_APP_PASSWORD` in both PHP files

---

## STEP 3 — Upload to Your Web Hosting

Upload the ENTIRE `sweet-delights/` folder to your hosting's `public_html/` directory.

Your PHP hosting must support:
- PHP 7.4 or higher
- SMTP / outgoing mail (most shared hosts like cPanel, InMotion, etc.)

**Free PHP hosting options:**
- InfinityFree.net (free, supports PHP + SMTP)
- 000webhost.com (free)
- Any cPanel shared hosting

---

## STEP 4 — Test It

1. Visit your site at `yourdomain.com/index.html`
2. Add items to cart → checkout → place an order
3. Check your Gmail inbox — you should receive:
   - ✅ An order alert email (to you as owner)
   - ✅ A confirmation email (to the customer)
4. Also test the Reservations page

---

## How Orders Reach You (Gmail)

When a customer places an order, you'll receive an email like this:

```
Subject: 🎂 New Order from Juan dela Cruz — ₱1,250.00

Customer: Juan dela Cruz
Phone: 09171234567  ← tap to call directly
Email: juan@email.com
Payment: GCash

Items:
  • Chocolate Decadence Cake x1 = ₱45.99
  • Vanilla Cupcakes x2 = ₱37.98

TOTAL: ₱83.97

Action Required: Contact customer to confirm delivery.
```

The customer also gets a beautiful confirmation email automatically.

---

## Payment Methods

Edit `pages/checkout.html` to update your GCash/Maya numbers:
Search for `09XX-XXX-XXXX` and replace with your actual number.

---

## Customizing Products

Edit `js/products.js` — add your real cakes/pastries with:
- Name, price, category (Cake / Pastry), description, image URL
- Set `featured: true` for items shown on the homepage

---

## Troubleshooting

**Emails not sending?**
- Double-check your App Password (no spaces when pasting)
- Make sure 2FA is ON in your Google account
- Check your hosting's error logs

**Gmail compose opens instead of sending?**
- This is the fallback when PHP is not available (e.g., opening from a local file)
- Upload to a PHP server and it will send automatically

---

Need help? The Gmail fallback in checkout.html always works as a backup!
