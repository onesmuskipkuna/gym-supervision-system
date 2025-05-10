-- Database schema for Gym Supervision System

CREATE DATABASE IF NOT EXISTS gym_supervision;
USE gym_supervision;

-- Users and roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Cleanliness management
CREATE TABLE cleanliness_areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE cleanliness_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shift_name VARCHAR(50) NOT NULL UNIQUE,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL
);

CREATE TABLE cleaning_staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE cleanliness_timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_id INT NOT NULL,
    shift_id INT NOT NULL,
    staff_id INT NOT NULL,
    cleaning_date DATE NOT NULL,
    FOREIGN KEY (area_id) REFERENCES cleanliness_areas(id),
    FOREIGN KEY (shift_id) REFERENCES cleanliness_shifts(id),
    FOREIGN KEY (staff_id) REFERENCES cleaning_staff(id)
);

CREATE TABLE cleanliness_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timetable_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    rating_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (timetable_id) REFERENCES cleanliness_timetable(id)
);

-- Gym classes management
CREATE TABLE coaches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE gym_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    coach_id INT NOT NULL,
    studio_location VARCHAR(100) NOT NULL,
    class_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (coach_id) REFERENCES coaches(id)
);

CREATE TABLE class_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    user_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    FOREIGN KEY (class_id) REFERENCES gym_classes(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE coach_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    rating_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id)
);

-- Gym area management
CREATE TABLE gym_areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE gym_area_timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_id INT NOT NULL,
    staff_id INT NOT NULL,
    shift_id INT NOT NULL,
    timetable_date DATE NOT NULL,
    FOREIGN KEY (area_id) REFERENCES gym_areas(id),
    FOREIGN KEY (staff_id) REFERENCES cleaning_staff(id),
    FOREIGN KEY (shift_id) REFERENCES cleanliness_shifts(id)
);

-- Machine maintenance
CREATE TABLE machine_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE machines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    machine_name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    location ENUM('main gym', 'ladies gym') NOT NULL,
    status ENUM('good condition', 'under maintenance', 'fault') NOT NULL DEFAULT 'good condition',
    FOREIGN KEY (category_id) REFERENCES machine_categories(id)
);

CREATE TABLE machine_status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    machine_id INT NOT NULL,
    status ENUM('good condition', 'under maintenance', 'fault') NOT NULL,
    remarks TEXT,
    changed_by INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (machine_id) REFERENCES machines(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);
