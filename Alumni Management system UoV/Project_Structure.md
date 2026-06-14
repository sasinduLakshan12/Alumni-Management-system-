🗂️ Project Structure

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
    └── style.css            # Application styling
