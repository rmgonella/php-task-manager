PHP Task Manager
Task Management System in PHP with MVC Architecture

PHP Task Manager is a complete task management system built in PHP, using the Model-View-Controller (MVC) architecture and MySQL as the database.
The project includes secure user authentication (registration and login with password_hash() and sessions), full CRUD functionality (Create, Read, Update, Delete) for tasks, and filtering by status.

Main Features
Feature	Description
Authentication	User registration and login with encrypted passwords (password_hash()) and session management.
Task CRUD	Create, view, edit, and delete tasks.
Filters	Filter tasks by status (Pending, Completed) and priority (Low, Medium, High).
Simple Interface	Clean and responsive design using only pure HTML and CSS (no frameworks).
MVC Architecture	Organized code structure with /app, /controllers, /models, /views, /config for easy maintenance and scalability.
Project Structure

The project follows the directory structure below:

php-task-manager/
├── app/
│   ├── Router.php         # Main routing class
│   └── routes.php         # Application route definitions
├── assets/
│   ├── css/
│   │   └── style.css      # CSS styles
│   └── js/
│       └── main.js        # JavaScript scripts
├── config/
│   ├── config.php         # General settings and utility functions
│   └── database.php       # Database connection configuration
├── controllers/
│   ├── AuthController.php # Authentication logic (Login, Register, Logout)
│   ├── HomeController.php # General page logic (Home, 404, etc.)
│   └── TaskController.php # Task management logic (CRUD, Dashboard)
├── models/
│   ├── Task.php           # Model for interacting with the 'tasks' table
│   └── User.php           # Model for interacting with the 'users' table
├── public/
│   ├── .htaccess          # Apache rewrite rules
│   └── index.php          # Application entry point (Front Controller)
├── views/
│   ├── auth/              # Authentication views (login, register, profile)
│   ├── errors/            # Error views (404, 500)
│   ├── home/              # General page views (index, about, contact)
│   ├── layouts/           # Layouts (header, footer)
│   └── tasks/             # Task views (dashboard, create, edit, show)
├── .htaccess              # Main rewrite rules
└── database.sql           # SQL script for table creation

Installation and Local Setup

To run PHP Task Manager on your local machine, you’ll need a web server environment that supports PHP and MySQL (such as XAMPP, WAMP, MAMP, or Docker).

1. Web Server Setup

Download and Configure the Project:

Clone this repository or download the ZIP file.

Move the php-task-manager folder to your web server root directory (e.g., htdocs for XAMPP).

Apache Configuration (Important):

Ensure that the mod_rewrite module is enabled.

The .htaccess files in the root and public folders are essential for MVC routing.
If you’re using a different server (e.g., Nginx), configure equivalent rewrite rules to route all requests to public/index.php.

2. Database Configuration

Create the Database:

Access your database manager (phpMyAdmin, MySQL Workbench, etc.).

Create a new database named php_task_manager.

Import the Tables:

Import the database.sql script into the newly created database.
This script will create the users and tasks tables and insert a demo user.

Demo User:

Email: teste@exemplo.com

Password: 123456

Adjust Credentials (if needed):

Edit the config/database.php file if your MySQL credentials differ from the default (host: localhost, username: root, password: '').

// config/database.php
private $host = 'localhost';
private $db_name = 'php_task_manager';
private $username = 'root';
private $password = ''; // Your MySQL password

3. Accessing the Application

Open the URL:

Open your browser and access the URL where the project is hosted.
If you placed the folder in your local server root, the URL will be:

http://localhost/php-task-manager/


Login:

Click “Login” and use the demo credentials or create a new account.

Note: If you encounter routing (404) errors, check the Apache mod_rewrite configuration and your base path.

4. Development Mode

The public/index.php file defines a DEBUG constant:

// public/index.php
define('DEBUG', true); // Change to false in production


When DEBUG is true, detailed errors are shown, and development routes (like /info) are enabled.

In production, set DEBUG to false to hide errors and log them in logs/error.log.

Best Practices and Security

Passwords: Uses password_hash() and password_verify() to securely store user passwords.

CSRF Protection: CSRF tokens are generated and verified for all POST forms to prevent cross-site request forgery.

Sanitization: The sanitizeInput() function cleans user input to prevent XSS attacks.

PDO: All database interactions use PDO with prepared statements to prevent SQL injection.

Contributions

Feel free to contribute improvements, bug fixes, or new features.

Developed by Rodrigo Marchi Gonella