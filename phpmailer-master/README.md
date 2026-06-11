# PHPMailer — Included with Sweet Delights

This folder contains the real PHPMailer library (fetched from the official GitHub).

## Files included
- `src/PHPMailer.php` — Main class
- `src/SMTP.php` — SMTP transport
- `src/Exception.php` — Exception handler

## How it works
Your `send_order.php` and `send_reservation.php` load these files directly.
No Composer needed — just upload this whole folder to your server.

## Official source
https://github.com/PHPMailer/PHPMailer

## License
LGPL 2.1 — free for commercial use.
