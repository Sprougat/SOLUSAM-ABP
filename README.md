# SOLUSAM Backend - Setup Guide

## Requirements

- PHP 8.1+
- MySQL 5.7+
- Composer

## 1. Clone & Install

```bash
git clone <repo-url> SOLUSAM
cd SOLUSAM
composer install
```

## 2. Database

Create database `solusam` di MySQL. Import schema dari migration:

```bash
php spark migrate
```

## 3. Konfigurasi .env

Copy `env` ke `.env` dan isi:

```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

# Database
database.default.hostname = localhost
database.default.database = solusam
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi

# JWT
JWT_SECRET = ganti_dengan_string_random_panjang

# Midtrans (Sandbox)
MIDTRANS_SERVER_KEY = SB-Mid-server-XXXXXXXXXXXXXXXX
MIDTRANS_CLIENT_KEY = SB-Mid-client-XXXXXXXXXXXXXXXX
MIDTRANS_IS_PRODUCTION = false

# Google OAuth
GOOGLE_CLIENT_ID = xxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET = GOCSPX-xxxx
GOOGLE_REDIRECT_URI = http://localhost:8080/auth/google/callback

# Email SMTP (Gmail)
email.protocol = smtp
email.SMTPHost = smtp.gmail.com
email.SMTPUser = email@gmail.com
email.SMTPPass = app_password
email.SMTPPort = 587
email.SMTPCrypto = tls
email.mailType = html
```

### Cara Dapatkan Keys

**Midtrans:**
1. Buka https://dashboard.sandbox.midtrans.com/
2. Settings -> Access Keys
3. Copy Server Key & Client Key

**Google OAuth:**
1. Buka https://console.cloud.google.com/
2. Buat project baru
3. APIs & Services -> Credentials -> Create OAuth 2.0 Client ID
4. Authorized redirect URI: `http://localhost:8080/auth/google/callback`
5. Copy Client ID & Client Secret

**SMTP Gmail:**
1. Aktifkan 2FA di akun Google
2. Buka https://myaccount.google.com/apppasswords
3. Buat App Password untuk "Mail"
4. Copy 16-char password ke `email.SMTPPass`

## 4. Jalankan Server

```bash
php spark serve
```

Default: http://localhost:8080

## 5. Ngrok (Untuk Midtrans Webhook)

Midtrans butuh public URL untuk webhook. Localhost tidak bisa diakses dari internet.

### Install

Download dari https://ngrok.com/download, extract, login:

```bash
ngrok config add-authtoken <token_kamu>
```

### Jalankan

```bash
ngrok http 8080
```

Copy URL HTTPS yang muncul, contoh: `https://abc123.ngrok-free.app`

### Update .env

```env
app.baseURL = 'https://abc123.ngrok-free.app/'
```

Restart server: `Ctrl+C` lalu `php spark serve`

### Set Webhook di Midtrans

1. Buka https://dashboard.sandbox.midtrans.com/
2. Settings -> Configuration
3. Payment Notification URL:
   ```
   https://abc123.ngrok-free.app/penjualan/midtrans-notification
   ```
4. Klik Update

**Catatan:** Ngrok URL berubah tiap restart. Kalau pakai ngrok free, URL berubah setiap run. Untuk development lama, pertimbangkan upgrade ke ngrok paid untuk domain tetap.

## Troubleshooting

**Error 500 setelah ubah .env:**
Restart server: `Ctrl+C`, lalu `php spark serve`

**Midtrans 401 Unauthorized:**
1. Cek Server Key benar (tanpa spasi)
2. Restart server
3. Clear cache: `rm -rf writable/cache/*`

**Google OAuth redirect mismatch:**
Pastikan `GOOGLE_REDIRECT_URI` sama persis dengan yang didaftarkan di Google Cloud Console (termasuk trailing slash).
