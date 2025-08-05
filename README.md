# 🗳️ Online Voting System

A secure, user-friendly web-based voting platform developed for coursework in Ethical Hacking and Cyber Security. Built with PHP, MySQL, HTML, CSS, and JavaScript, this project simulates a real-world voting system with encryption, authentication, and admin control.

---

## 📌 Features

### 👥 Voter Side
- Voter registration with validation
- Secure login using hashed passwords
- One vote per user enforcement
- AES-256-GCM encryption of votes
- Vote confirmation page

### 🛡️ Admin Side
- Admin login with OTP (email-based 2FA)
- Admin dashboard to view/manage elections
- View encrypted votes
- Decrypt votes to view results securely

### 🔐 Security Features
- AES-256-GCM for vote encryption
- `password_hash()` for password storage
- Session-based login tracking
- Duplicate vote prevention
- CAPTCHA and OTP protection (optional)

---

## 🧰 Tools and Technologies Used

- **Frontend:** HTML, CSS, JavaScript, TailwindCSS/Bootstrap
- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Security:** AES-256-GCM, password_hash(), sessions
- **Dev Tools:** VS Code, XAMPP, phpMyAdmin
- **Utilities:** PHPMailer (for OTP)

---
## INSTRUCTIONS

DOWNLOAD "Online Voting Management System Project"

Install XAMPP

Download the zip file/ download winrar

Extract the file and copy "Online Voting System" folder

Paste inside root directory/ where you install xammp local disk C: drive D: drive E: paste: (for xampp/htdocs)

Open PHPMyAdmin: Localhost:8080/phpmyadmin

Create a database with name Online Voting System

Import .sql file(given inside the zip package in db file folder)

Run the script

<img width="967" height="471" alt="image" src="https://github.com/user-attachments/assets/442fbcd9-4151-4448-b8f5-08fab3c53f6d" />

