-- Users Table (Handles authentication, typically 'admins' and/or clients)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    name VARCHAR(100), 
    username VARCHAR(50) UNIQUE, 
    email VARCHAR(100) UNIQUE, 
    password VARCHAR(255), 
    role ENUM('admin', 'client') DEFAULT 'user', -- Added role for clarification
    created_at DATETIME
);

-- Events Table (Might define general event details or venues)
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100), -- e.g., 'Grand Hall', 'Conference Room A'
    -- Add columns like capacity, location, image, etc.
    capacity INT,
    location VARCHAR(255)
);

-- Event_Packages Table (The products offered)
CREATE TABLE event_packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    event_id INT, -- Links to the venue/event type
    total_price DECIMAL(10, 2),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Booking Table (Client Orders)
CREATE TABLE booking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT, -- Links to the client/user who booked
    package_id INT,
    event_date DATE,
    status ENUM('pending', 'approved', 'cancelled') DEFAULT 'pending',
    created_at DATETIME,
    FOREIGN KEY (package_id) REFERENCES event_packages(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);