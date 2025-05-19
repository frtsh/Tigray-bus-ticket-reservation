
-- Drop existing tables if they exist
DROP TABLE IF EXISTS admins, buses, users, seats, tickets, booking;

-- Create admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin
INSERT INTO admins (username, password) VALUES ('fluency', 'fluency');

-- Create buses table
CREATE TABLE buses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    route VARCHAR(100) NOT NULL
);

-- Insert sample bus data
INSERT INTO buses (name, route) VALUES
('Selam Bus', 'Mekelle - Addis Ababa'),
('Habesha Bus', 'Axum - Addis Ababa'),
('Gonder Bus', 'Mekelle - Gonder');

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20)
);

-- Create seats table
CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id INT,
    seat_number VARCHAR(10),
    is_booked BOOLEAN DEFAULT 0,
    FOREIGN KEY (bus_id) REFERENCES buses(id)
);

-- Create tickets table
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    seat_id INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (seat_id) REFERENCES seats(id)
);

-- Create booking table
CREATE TABLE booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    bus_id INT,
    seat_id INT,
    booking_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (bus_id) REFERENCES buses(id),
    FOREIGN KEY (seat_id) REFERENCES seats(id)
);
