# School Portal

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)

A role-based academic records and enrollment portal for administrators, teachers, and students.

## About the Project

This project centralizes common school operations in one web application: student records management, subject scheduling, enrollment, grading, collections, and reporting.  
It is designed to reduce manual record handling and give each role (admin, teacher, student) a focused workflow.

### Key Features
- Multi-role login (admin, teacher, student)
- Student, course, subject, semester, room, and teacher management
- Enrollment and grade entry workflows (midterm/final)
- Collection tracking and audit trail
- PDF report generation using FPDF

## Tech Stack / Built With

- **Language:** PHP
- **Database:** MySQL
- **Frontend:** HTML, Bootstrap/Bootswatch, Bootstrap Icons
- **PDF Reporting:** FPDF
- **Server (local):** Apache/Nginx with PHP or PHP built-in server

## Getting Started

### Prerequisites

- PHP 8.x+ with PDO MySQL extension enabled
- MySQL 8.x+
- A local web server (Apache/Nginx) or PHP CLI for local serving

### Installation

1. Clone the repository:

```bash
git clone https://github.com/Ricky-zzz/school_portal.git
cd school_portal
```

2. Create the database and import schema/data:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS school;"
mysql -u root -p school < school.sql
```

3. Configure database connection in:

`partials/conn.php`

Update these values as needed:
- `$host`
- `$dbname`
- `$username`
- `$password`

4. Start the project locally (example using PHP built-in server):

```bash
php -S localhost:8000
```

Then open:
- `http://localhost:8000/login.php` (Admin)
- `http://localhost:8000/index.php` (Student)
- `http://localhost:8000/login2.php` (Teacher)

### Configuration Notes

- Session-based authentication is used across all roles.
- Initial sample records and credentials are provided by `school.sql`.
- No `.env` file is currently used; configuration is file-based in `partials/conn.php`.

## Usage

### Basic Flow

1. Log in as Admin, Teacher, or Student.
2. Navigate to role-specific menu pages:
   - Admin: `/menu.php`
   - Teacher: `/teacher/menu.php`
   - Student: `/student/menu.php`
3. Perform actions such as managing records, grading, enrollment, and generating reports.

### Example Commands

```bash
# Start local server
php -S localhost:8000

# Re-import database if you need a reset
mysql -u root -p school < school.sql
```

### Screenshot Placeholders

- `[Login Screen Screenshot]`
- `[Admin Dashboard Screenshot]`
- `[Teacher Grading Screen Screenshot]`
- `[Student Menu Screenshot]`

## Contributing

Contributions are welcome. To contribute:

1. Fork the repository
2. Create a feature branch (`feature/your-change`)
3. Commit your changes with clear messages
4. Push your branch and open a pull request

Please keep changes focused, documented, and tested manually against the affected workflow.

## License

This project currently has no declared license.  
Add your preferred license (for example, MIT) and update this section accordingly.
