# Project & Task Management System

This application is developed by **Aikaterini Eirini Sfakianou** (`katsfak12@gmail.com`) as part of an internship project. It is a full-featured web application designed to help users manage their projects and tasks efficiently with a clean, user-friendly interface.

## Features

- **User Authentication System**

  - User registration with validation
  - Secure login/logout functionality
  - Password hashing for security
  - Session management

- **User Profile Management**

  - Edit personal information (name, username, email)
  - Change password with current password verification
  - Profile validation to prevent duplicate usernames/emails

- **Project Management**

  - Create new projects
  - Edit project names
  - Delete projects (with confirmation)
  - View all user projects

- **Task Management**

  - Add tasks to specific projects
  - Mark tasks as complete/incomplete
  - Edit task descriptions
  - Delete tasks (with confirmation)
  - Visual indication of completed tasks

- **Responsive Design**
  - Clean, modern CSS styling
  - Consistent color scheme (#4CAF50 green theme)
  - Mobile-friendly interface

## Prerequisites

- **XAMPP** installed (includes Apache, PHP, and MySQL)
- PHP >= 7.4
- MySQL/MariaDB
- Web browser

## Database Structure

The application uses a MySQL database named `todolist_app` with the following tables:

- `users` - Stores user account information
- `projects` - Stores user projects
- `tasks` - Stores tasks associated with projects

## Installation

1. **Clone or copy the repository** into your XAMPP `htdocs` directory:

   ```bash
   git clone https://github.com/katsfak/To-Do-App
   ```

   ```bash
   cp -r [your-app-folder] /opt/lampp/htdocs/Intership
   ```

2. **Start XAMPP**:

   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services

3. **Database Setup**:

   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Create a new database named `todolist_app`
   - Create the required tables: [`todolist_app.sql`](todolist_app.sql)

4. **Configure Database Connection**:
   - The database configuration is already set in [`config.php`](config.php):
     ```php
     $host = "localhost";
     $user = "root";
     $password = "";
     $dbname = "todolist_app";
     ```

## Running the Application

1. Open your browser and navigate to:

   ```
   http://localhost/Intership/LogIn/index.php
   ```

2. **First Time Setup**:
   - Click "Sign up here" to create a new account
   - Fill in all required fields (Full Name, Username, Email, Password)
   - After successful registration, log in with your credentials

## Usage

### Getting Started

1. **Register**: Create your account via [Register/signup.php](Register/signup.php)
2. **Login**: Access the system via [LogIn/index.php](LogIn/index.php)
3. **Dashboard**: After login, you'll be redirected to [Home/home.php](Home/home.php)

### Managing Projects

- **Create Project**: Use the "Add New Project" form on the home page
- **Edit Project**: Use the inline edit form next to each project name
- **Delete Project**: Click the red "Delete" button (requires confirmation)

### Managing Tasks

- **Add Task**: Use the "Add Task" form within each project
- **Complete Task**: Click "Complete" to mark as done (click "Undo" to revert)
- **Edit Task**: Use the inline edit form to modify task descriptions
- **Delete Task**: Click the red "Delete" button (requires confirmation)

### Profile Management

- **Edit Profile**: Click "[Profile Settings]" in the header
- **Update Information**: Modify your name, username, or email
- **Change Password**: Enter current password and new password

### Logout

- Click "[Logout]" in the header to securely end your session

## File Structure

```
/opt/lampp/htdocs/Intership/
├── config.php              # Database configuration
├── logout.php              # Logout functionality
├── Readme.md               # This file
├── todolist_app.sql        # Database file
├── Home/
│   ├── home.php            # Main dashboard
│   └── style.css           # Home page styling
├── LogIn/
│   ├── index.php           # Login page
│   └── style.css           # Login page styling
├── Profile/
│   ├── profile.php         # User profile management
│   └── style.css           # Profile page styling
└── Register/
    ├── signup.php          # User registration
    └── style.css           # Registration page styling
```

## Security Features

- **SQL Injection Protection**: Uses prepared statements throughout
- **Password Security**: Passwords are hashed using PHP's `password_hash()`
- **Session Management**: Proper session handling for user authentication
- **Input Validation**: Server-side validation for all user inputs
- **XSS Protection**: Uses `htmlspecialchars()` for output sanitization

## Troubleshooting

### Common Issues

- **Blank page or errors**:

  - Check PHP error logs in `/opt/lampp/logs/php_error_log`
  - Ensure all files have proper PHP opening tags

- **Database connection issues**:

  - Verify credentials in [`config.php`](config.php)
  - Ensure MySQL service is running in XAMPP
  - Check if database `todolist_app` exists

- **Permission issues**:

  - Ensure files in `/opt/lampp/htdocs/Intership` are readable by the web server
  - On Linux/Mac: `chmod -R 755 /opt/lampp/htdocs/Intership`

- **Session issues**:

  - Clear browser cookies and cache
  - Restart Apache service in XAMPP

- **Registration/Login problems**:
  - Verify database tables exist with correct structure
  - Check for duplicate usernames/emails in database

### Error Messages

- **"All fields are required"**: Fill in all form fields
- **"Username or email already taken"**: Choose different credentials
- **"Passwords do not match"**: Ensure password confirmation matches
- **"Password must be at least 6 characters"**: Use longer password

## Contact

For questions or support, contact **Aikaterini Eirini Sfakianou** at [katsfak12@gmail.com](mailto:katsfak12@gmail.com).
