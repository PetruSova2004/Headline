# Nette Web Project - Registration and User Management

## Overview

Hello, I am Șova Petru, and I have implemented a Full Stack Developer Candidate's Technical Task using the Nette Framework. This task involves creating a complete registration flow with user CRUD operations and integration with email verification. Below is a detailed guide on how to set up and run the project.

### Project Features:
- **Registration Page**: Users can register with their username, email, and password.
- **Validation**: Both client-side and server-side validation ensure data integrity.
- **User Management (CRUD)**: Administrators can manage registered users, including creating, updating, and deleting accounts.
- **User Interface**: A responsive and intuitive UI for both the registration page and user management.
- **Security**: Passwords are hashed and protected against common vulnerabilities like CSRF attacks.

## Installation Instructions

### Requirements

To run this project, you need to have the following installed:

- **PHP 8.1 or higher**: You can download and install PHP from [here](https://www.php.net/downloads).
- **Composer**: A tool for managing PHP dependencies. You can install Composer from [here](https://getcomposer.org/download/).

### Setup

1. **Clone the repository**:

   Run the following command to clone the project and install the dependencies:

   ```bash
   git clone https://github.com/PetruSova2004/Headline.git
   cd Headline
   composer install
   ```

2. **Set Permissions**:
   Ensure the following directories are writable:
- temp/
- log/

3. **Configure Database**:
   After installing the project, configure the database connection by editing the `config/common.neon` file.

Update the database connection settings based on your database. For example:

   ```bash
   database:
  dsn: 'mysql:host=localhost;dbname=my_database'
  user: 'root'
  password: 'root_password'
```
Make sure to replace the values with your own database credentials.

4. **Configure SMTP Settings**:
   To enable email functionality, update the SMTP configuration in the `config/services.neon` file. Replace `username` and `password` with your email credentials:
 ```bash 
parameters:
    smtp:
        host: 'smtp.gmail.com'
        port: 465
        username: 'The sender email address.'
        password: 'The app-specific password or the account password used to authenticate the email-sending process via the SMTP server'
        secure: 'ssl'
```
You can learn how to generate an app-specific password for Gmail by following these instructions: https://support.google.com/mail/answer/185833?hl=en

5. **Update `.env` File:**:
   Edit the `.env` file and set the `APP_URL` to match your domain:
 ```bash 
APP_URL=http://localhost:8000
```

6. **Run Migrations**:
   Navigate to the `app/Core/Database` folder and run the following command to apply migrations:
 ```bash 
cd app/Core/Database 
php run_migrations.php
```
The console should output:
 ```bash 
Migrations completed successfully!
```

7. **Start the Web Server**:
   You can now start the project using PHP's built-in server. Run the following command:
 ```bash 
php -S localhost:8000 -t www
```
Open your browser and visit http://localhost:8000 to view the login page.


## Accessing the Project

### Authentication
To access the resources of this project, you need to authenticate.

#### Default Admin Credentials
After running the migrations, an admin user is already created in the database. You can log in using the following credentials:
- Username: `admin`
- Email: `admin@example.com`
- Password: `admin123`

#### Registering a New User
Alternatively, you can register a new user by visiting the registration page. The form will ask for the following details:

- Username
- Email
- Password

## User Management (Admin Panel)
Once logged in as an admin, you can manage registered users via the user management page. Here, administrators can:

- View the list of registered users.
- Add, update, or delete users.

These features are protected by proper access control to ensure only admins can modify user data.


## Conclusion
With this setup, you'll have a fully functional registration system, along with an admin panel for managing users. This task demonstrates proficiency in working with the Nette Framework, implementing secure registration flows, handling user CRUD operations, and includes email verification for account confirmation during the registration process.

Good luck, and feel free to reach out for any assistance! :)





