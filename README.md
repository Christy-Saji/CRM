# CRM Platform

A simple CRM (Customer Relationship Management) system built with PHP and MySQL for study and demonstration purposes.

## Features

- Admin, client, and employee management
- Complaint tracking and resolution
- Product management
- Chat system between clients and employees
- Database migrations and schema management

## Project Structure

```
crm_final/
│
├── admin/
│   ├── assign.php
│   ├── dashboard.php
│   ├── emp_login.php
│   ├── emp_manage.php
│   ├── login.php
│   ├── logout.php
│   ├── management.php
│   ├── product.php
│   ├── report.php
│   ├── update_client_status.php
│   └── update_client_status copy.php
│
├── assets/
│   ├── bac.png
│   ├── backgorund.png
│   ├── style.css
│   └── whiteBack.png
│
├── chat/
│   ├── chat_handler.php
│   ├── check_new_messages.php
│   ├── client_chat.php
│   ├── employee_chat.php
│   └── get_messages.php
│
├── client/
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   └── status.php
│
├── config/
│   └── db.php
│
├── database/
│   ├── crm_db.sql
│   ├── run_migration.php
│   └── migrations/
│       └── 20240505_add_inactive_to_complaints.sql
│
├── employee/
│   ├── emp_dash.php
│   ├── emp_login.php
│   ├── logout.php
│   └── resolve.php
│
├── index.php
└── README.md
```

## Getting Started

1. **Clone the repository:**
   ```sh
   git clone https://github.com/Christy-Saji/CRM.git
   ```

2. **Set up the database:**
   - Import `database/crm_db.sql` into your local MySQL server (e.g., using phpMyAdmin).

3. **Configure database connection:**
   - Edit `config/db.php` with your local database credentials.

4. **Run the project:**
   - Place the project folder in your web server directory (e.g., `c:/xampp/htdocs/` for XAMPP).
   - Access `http://localhost/crm_final/` in your browser.

## Notes

- For security, do **not** use real credentials in `db.php` if sharing publicly.
- The project is for educational use and may lack production-level security.

## License

This project is for study/demo purposes. Use and modify as you wish.