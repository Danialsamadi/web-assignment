# Blog Platform

## Overview
The Blog Platform is a web application developed for a college web programming course. It allows users to create, edit, and delete blog posts. It supports user authentication, categorization of posts, and image uploads. Users can also search for posts based on keywords, categories, or authors.

## Features
- User authentication (login, logout, registration)
- Create, edit, and delete blog posts
- Add categories to posts
- Upload images to posts
- Search and filter posts by keywords, categories, or authors
- View all posts by a specific user
- View account details (username, email, creation date)

## Technologies Used
- PHP
- MySQL
- HTML/CSS
- JavaScript

## Setup Instructions

### Prerequisites
- PHP 7.x or higher
- MySQL 5.x or higher
- Web server (Apache, Nginx, etc.)

### Database Setup
1. Create a new MySQL database.
2. Import the following SQL schema to create the necessary tables:
    ```sql
    -- Create users table
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Create posts table
    CREATE TABLE posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        image BLOB,
        keywords VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- Create categories table
    CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE
    );

    -- Create post_categories table
    CREATE TABLE post_categories (
        post_id INT NOT NULL,
        category_id INT NOT NULL,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    );

    -- Create comments table
    CREATE TABLE comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ```

### Configuration
1. Clone the repository to your web server's root directory:
    ```sh
    git clone https://github.com/Danialsamadi/web-assignment
    ```

2. Update the database connection details in `abstractDAO.php`

### Running the Application
1. Start your web server and navigate to the application in your browser


## Security Considerations
- Ensure proper validation and sanitization of user inputs to prevent SQL injection and XSS attacks.
- Use prepared statements for database queries.
- Store passwords securely using hashing algorithms like bcrypt.
