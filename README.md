# 🎓 Valiente Student Management System

A student registry web app built with **PHP, MySQL, HTML, CSS, and Vanilla JavaScript** — focused on Negros Oriental · Region VII, Philippines.

---

## ✨ Features

- 🔐 **Authentication** — Login, signup, and session-protected pages
- 📊 **Admin Dashboard** — Stats overview, students by school/course charts, recent enrollments
- ➕ **Add Student** — Form with cascading municipality → barangay address selector
- 📋 **View Table** — Full student records with Edit and Delete actions
- 📄 **Simple List** — Card-based view of all students
- ✏️ **Edit Record** — Pre-filled form with smart address parsing
- 🗑️ **Delete Record** — Confirm-before-delete with prepared statements
- 🌙 **Dark / Light Mode** — Toggle that persists via `localStorage`
- 🏘️ **Address Cascade** — Barangay dropdown auto-populates based on selected municipality (covers all towns in Negros Oriental)
- 🛡️ **Security** — Password hashing (bcrypt), prepared statements throughout, session guard on all protected pages

---

## 🛠️ Tech Stack

| Layer | Tech |
|---|---|
| Backend | PHP 8+ |
| Database | MySQL (via MySQLi) |
| Frontend | HTML5, CSS3, Vanilla JS |
| Auth | PHP Sessions + `password_hash()` bcrypt |
| Fonts | Google Fonts — Syne, DM Sans |

---

## 📁 Project Structure

```
valiente/
├── index.php             # Add student form (protected)
├── dashboard.php         # Admin dashboard with stats & charts
├── read.php              # Simple card list of students (protected)
├── readtable.php         # Full table view with edit/delete (protected)
├── edit.php              # Edit a student record (protected)
├── delete.php            # Delete a record (protected)
├── receiver.php          # Handles add-student form POST
├── login.php             # Login page
├── signup.php            # Registration page
├── logout.php            # Destroys session & redirects
├── auth.php              # Session guard (include on protected pages)
├── connection.php        # MySQLi database connection
├── form_options.php      # Courses, schools, barangay data & address helpers
├── address_cascade.js    # Cascading municipality → barangay dropdown
├── theme.js              # Dark/light theme toggle & persistence
├── style.css             # Global styles
├── seed_admin.php        # One-time admin account seeder (auto-deletes)
└── valiente.sql          # Database schema & seed SQL
```

---

## 🚀 Setup & Installation

### 1. Requirements
- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache or Nginx (XAMPP / Laragon recommended locally)

### 2. Clone the repository
```bash
git clone https://github.com/your-username/valiente-sms.git
```

### 3. Import the database
```bash
mysql -u root -p < valiente.sql
```
Or import `valiente.sql` via **phpMyAdmin**.

### 4. Configure the connection
Open `connection.php` and update your credentials:
```php
$connection = mysqli_connect("localhost", "root", "", "data");
```

### 5. Seed the admin account
Open this URL in your browser **once**:
```
http://localhost/valiente/seed_admin.php
```
This creates the admin user and **self-deletes** the file.

**Default credentials:**
| Field | Value |
|---|---|
| Email | `admin@valiente.com` |
| Password | `Admin@2025` |

### 6. Open the app
```
http://localhost/valiente/login.php
```

---

## 🗺️ Covered Municipalities (Negros Oriental)

The address cascade covers all major cities and municipalities including:

Dumaguete City, Bacong, Dauin, Sibulan, Valencia, Bais City, Bayawan City, Guihulngan City, Canlaon City, Santa Catalina, Mabinay, Manjuyod, Zamboanguita, and more.

---

## 👥 Team

| Name | Role |
|---|---|
| **Khian Valiente** | Frontend — UI/UX, HTML, CSS, JavaScript |
| **Aljen Driech Marcillana** | Backend — PHP, Database, Server Logic |
| **Chris Anthony Daugdaug** | Backend — PHP, Database, Server Logic |

---

## 📬 Contact

- 📧 Email: khianvaliente13@email.com
- 📍 Dumaguete City, Negros Oriental, Philippines

---

## ⚠️ Security Notes

> This project is intended for **local / academic use**. Before deploying to production:
> - Change default admin credentials immediately
> - Move `connection.php` credentials to environment variables
> - Enable HTTPS
> - Add CSRF protection to all forms

---

© 2025 Khian Valiente, Aljen Driech Marcillana & Chris Anthony Daugdaug. All rights reserved.
