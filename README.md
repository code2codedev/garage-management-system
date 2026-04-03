# Garage Management System (GMS)

[![PHP](https://img.shields.io/badge/PHP-5.6%2B-8892B0?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)

## 📋 Overview
GMS is a **full-stack PHP/MySQL web application** designed for garage management workflows. It supports multi-role access (Admin, Customer, Mechanic, Receptionist) to handle vehicle registration, job assignments, status tracking, internal messaging, and history logging. The system features modern UI with video backgrounds, searchable tables, popups, and responsive design.

Built for efficiency in automotive repair shops, streamlining operations from customer intake to job completion.

## Installation
1. Clone the repo
2. Import `gms.sql` into MySQL
3. Configure `db_connect.php` with your credentials
4. Run on localhost or deploy to InfinityFree


## ✨ Key Features
- **Role-Based Dashboards**: Separate interfaces for Admin, Customer, Mechanic, and Receptionist.
- **Vehicle Management**: Register vehicles by reg number/phone, view personal fleet (customer).
- **Job Tracking**: Assign jobs, update status (In Progress/Completed), add notes/recommendations.
- **Internal Messaging**: Real-time communication between Mechanics and Receptionists.
- **History Logs**: Track all actions with timestamps.
- **Search & Filters**: Client-side search on all tables.
- **User Authentication**: Secure login/register with password hashing (`pass_hash.php`).
- **Responsive UI**: Custom CSS per role, video backgrounds, popups.
- Secure login system
- Reports exportable to CSV

## 🏗️ Tech Stack
- **Backend**: PHP (includes sessions, forms, MySQLi)
- **Database**: MySQL (`alphagarage_db.sql`)
- **Frontend**: HTML5, CSS3, Vanilla JavaScript (AJAX for fetches)
- **Assets**: Images/videos in `public/Images/`, global JS/CSS in `public/`

## 📁 Project Structure
```
GMS_TEST-final presentaion/
├── admin/              # Admin dashboard & tools
├── customer/           # Customer vehicle view/register
├── database/           # SQL schemas: alphagarage_db.sql, admin_detail.sql
│   ├── alphagarage_db.sql
│   └── admin_detail.sql
├── mechanic/           # Jobs, history, messages for mechanics
│   ├── mechanic_dashboard.php
│   ├── mechanic_jobs.php
│   ├── mechanic_history.php
│   └── mechanic_messages.php
├── public/             # Entry point & shared assets
│   ├── index.php          # Main entry
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   └── pass_hash.php
│   ├── css/
│   ├── Images/
│   ├── includes/       # Shared: db_connect.php, header.php, sidebar.php, footer.php, popup.php
│   └── js/
└── receptionist/      # Vehicle reg, services, conversations
    ├── receptionist_dashboard.php
    ├── receptionist_vehicle_registration.php
    └── fetch_*.php (AJAX)
```

## 🚀 Quick Setup & Run
Since the system is already working:

1. **Database Setup** (if not done):
   ```
   mysql -u root -p < database/alphagarage_db.sql
   ```
   Update `public/includes/db_connect.php` with your DB credentials:
   ```php
   $host = 'localhost';
   $user = 'root';
   $pass = '';
   $db = 'gms_db';
   ```

2. **Run Locally** (XAMPP/WAMP/Apache + PHP + MySQL):
   - Place in `htdocs/gms/` (or equivalent).
   - Start Apache/MySQL.
   - Visit: `http://localhost/gms/public/index.php`

3. **Test Users**:
   - Register new users via `register.php`.
   - Login at `login.php`.

## 📖 Usage Guide
1. **Login/Register**: At `public/index.php` → Role-specific dashboard.
2. **Customer**: Register/view vehicles (`customer_vehicles.php`).
3. **Receptionist**: Register vehicles, assign jobs/services (`receptionist_vehicle_registration.php`).
4. **Mechanic**: View/update jobs, send messages (`mechanic_jobs.php`, `mechanic_messages.php`).
5. **Admin**: Oversight (details in `admin/`).

**Demo Flow**:
- Customer registers vehicle.
- Admin assigns job.
- Mechanic updates status → messages rec.
- History logged automatically.

## 🔧 Known Issues / TODOs
- No explicit "Garage" in code → Consider renaming DB/project.
- Add image uploads for vehicles.
- Email notifications.
- API for mobile app.

## 🤝 Contributing
1. Fork the repo.
2. Create branch: `git checkout -b feature/xyz`.
3. Commit: `git commit -m 'Add feature'`.
4. Push & PR.

## 📄 License
MIT License - Feel free to use/modify.

## 🙏 Credits
Developed for university project presentation.

---

**Ready to run!** Open `public/index.php` in your browser.

