-- SQL script to create maintenance_staff table for Gym Supervision System

CREATE TABLE maintenance_staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100)
);
