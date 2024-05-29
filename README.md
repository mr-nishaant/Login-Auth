# React & WordPress User Authentication

## Overview

This project is a simple user authentication system built using React for the frontend and WordPress for the backend. Users can sign up, log in, and view their profile information after logging in. Authentication is handled using tokens stored in cookies.

## Features

- User signup
- User login
- User logout
- Viewing user profile information (username and email)

## Prerequisites

- Node.js (>= 12.x)
- npm or yarn
- WordPress instance

## Setup Instructions

### Backend (WordPress)

1. **Set Up WordPress**

   Ensure you have a running WordPress instance. You can set it up locally using tools like XAMPP, MAMP, or use a hosted solution.

2. **Create Custom Database Table**

   Create a custom table to store user tokens. You can use phpMyAdmin or any other database management tool to execute the following SQL:

   ```sql
   CREATE TABLE IF NOT EXISTS `wp_user_tokens` (
       `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
       `user_id` bigint(20) unsigned NOT NULL,
       `token` varchar(255) NOT NULL,
       `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
       PRIMARY KEY (`id`),
       UNIQUE KEY `token` (`token`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
