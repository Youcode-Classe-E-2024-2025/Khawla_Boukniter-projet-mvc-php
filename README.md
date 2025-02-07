# PHP MVC Framework

A lightweight, secure and modern MVC framework built with PHP, featuring a robust architecture and essential components for web application development.

## Features

- MVC Architecture
- Secure Authentication System
- Database Abstraction Layer
- Form Validation
- Session Management
- CSRF Protection
- Logging System
- Twig Template Engine
- Security Utilities
- Request Routing

## Requirements

- PHP 8.0 or higher
- PostgreSQL
- Composer
- Apache/Nginx

## Installation

1. Clone the repository:
```bash
git clone https://github.com/Youcode-Classe-E-2024-2025/Khawla_Boukniter-projet-mvc-php.git
```
2. Install dependencies:
```bash
composer install
```
3. Configure environment:
```bash
cp .env.example .env
```
4. Update .env with your database credentials:
```bash
DB_HOST=your_host
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password
```

## Project Structure
```bash
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Core/
│   ├── Models/
│   └── Views/
├── public/
├── logs/
└── vendor/
```

## Core Components
* Router: Handles URL routing and request dispatching
* Controller: Base controller with common functionality
* Model: Database interaction layer
* View: Template rendering using Twig
* Auth: Authentication and authorization
* Validator: Form and data validation
* Security: CSRF protection and security utilities
* Session: Session management
* Logger: Application logging
* Database: PDO database wrapper

## Security
* CSRF Protection: Prevents CSRF attacks
* Input Validation: Sanitizes and validates user input
* Password Hashing: Securely stores user passwords
* XSS Prevention: Escapes HTML output to prevent XSS attacks
* Prepared Statements: Prevents SQL injection

## Contributing
- Fork the repository
- Create a new branch: `git checkout -b feature/your-feature`
- Commit your changes: `git commit -am 'Add some feature'`
- Push to the branch: `git push origin feature/your-feature`
- Submit a pull request
