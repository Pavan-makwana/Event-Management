# 🏛️ Event Management System (PHP & MySQL)

> A comprehensive, full-stack web application built with PHP for managing event halls, services, packages, and client bookings. It features a secure administrative dashboard and a user-friendly client portal.

---

## 🌟 Features

### Admin Portal
* **Secure Authentication:** Admin Login and Signup implemented with secure `password_hash()` and prepared statements (PDO).
* **Dashboard Overview:** Real-time statistics for total Halls, Services, Packages, and a summary of Pending/Approved Bookings.
* **Recent Activity:** Displays a list of the 10 most recent bookings.
* **Access Control:** Uses `requireAdmin()` and `isAdmin()` functions to protect administrative routes.

### Client Portal (Homepage)
* **Featured Packages:** Dynamically displays the latest event packages with venue details (Hall Name, Capacity, Location).
* **Value Proposition:** Highlights key features like Premium Venues, Complete Packages, Easy Booking, and Best Prices.
* **Call-to-Action (CTA):** Clear links for browsing packages and contacting the team.

### Backend & Security
* **PDO Database:** Uses PHP Data Objects (PDO) for secure, parameterized queries to prevent SQL injection.
* **Helper Functions:** Includes reusable functions like `sanitize()`, `redirect()`, and `formatCurrency()`.

---

## ⚙️ Installation & Setup

### Prerequisites

You must have a local server environment (like XAMPP, MAMP, or Docker) with the following components:

* **PHP (v7.4 or higher recommended)**
* **MySQL or MariaDB**
* **Composer** (if you use it for dependency management, though not explicitly shown, it's good practice)

### Step-by-Step Guide

1.  **Clone the Repository:**
    ```bash
    git clone [https://github.com/YourUsername/Event-Management-System.git](https://github.com/YourUsername/Event-Management-System.git)
    cd Event-Management-System
    ```

2.  **Database Configuration:**
    * Create a new MySQL database (e.g., `event_db`).
    * Update the database connection details in `config/database.php`.

    *Example `config/database.php` (for reference):*
    ```php
    // ...
    $host = 'localhost';
    $db   = 'event_db';
    $user = 'root';
    $pass = 'your_strong_password'; // CHANGE THIS!
    // ...
    ```



3.  **Access the Application:**
    * **Client Homepage:** `http://localhost/Event-Management-System/index.php` (or similar URL)
    * **Admin Signup:** `http://localhost/Event-Management-System/admin/signup.php`
    * **Admin Login:** `http://localhost/Event-Management-System/admin/login.php`

---

## 💻 Tech Stack

* **Language:** PHP
* **Database:** MySQL / MariaDB (via PDO)
* **Front-end:** HTML5, CSS3, Vanilla JavaScript
* **Styling:** Custom CSS (using classes like `btn`, `form-control`, `alert`)

---

## 🤝 Contribution

Contributions are always welcome! If you find a bug or have a suggestion, please open an issue or a pull request.

---


## 📞 Contact

\[Pavan Makwana ] - \[https://github.com/Pavan-makwana/]