# 🎓 Alumni Management System

A simple web-based **Alumni Management System** developed using **PHP, MySQL, HTML, and CSS**.  
This system helps manage alumni records, events, and announcements with role-based access for **Public Users**, **Alumni**, and **Administrators**.

---

## ✨ Features Overview

### 👥 Public Users
- View alumni directory (read-only)
- View announcements and events
- Register as an alumni

### 🎓 Alumni Users
- Secure login & logout
- View and update personal profile
- Browse alumni directory
- View events and announcements

### 🛠️ Admin Users
- Admin dashboard
- Approve or reject alumni registrations
- Manage alumni records (CRUD)
- Manage events and announcements

---

## 🗂️ Project Structure

```plaintext
alumni_system/
├── index.php                # Home / Public landing page
│
├── config/
│   └── db_connection.php    # Database connection configuration
│
├── public/
│   └── register.php         # Alumni registration page
│
├── alumni/
│   ├── login.php            # Alumni login
│   ├── logout.php           # Alumni logout
│   ├── dashboard.php        # Alumni dashboard
│   ├── profile.php          # View & edit alumni profile
│   ├── directory.php        # View alumni directory
│   └── events.php           # View events
│
├── admin/
│   ├── dashboard.php        # Admin dashboard
│   ├── manage_users.php     # Manage alumni (CRUD)
│   ├── manage_events.php    # Manage events
│   ├── manage_announcements.php  # Manage announcements
│   └── approvals.php        # Approve / reject alumni registrations
│
└── css/
│    └── style.css            # Application styling
│── images/
   └── logo.jpg,image_1.png   # Add image and logo for home/index.jpg page
## 📊 User Flow Summary 

### 👤 Public User
🏠 Home ➡️ 👀 View Alumni (Read Only) ➡️ 📝 Register ➡️ ⏳ Wait for Approval


### 🎓 Alumni User
🔐 Login ➡️ 📊 Dashboard ➡️ 🧑‍💼 View / Edit Profile ➡️ 📂 Alumni Directory ➡️ 📅 Events ➡️ 🚪 Logout


### 🛠️ Admin User
🔐 Login ➡️ 🖥️ Dashboard ➡️ ✅ Approve Registrations ➡️ 🗃️ Manage Alumni (CRUD) ➡️ 📅 Manage Events ➡️ 🚪 Logout


