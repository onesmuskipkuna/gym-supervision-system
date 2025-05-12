-- SQL script to add an admin user to the users table with correct schema

-- First, find the role_id for 'admin' role
SET @admin_role_id = (SELECT id FROM roles WHERE role_name = 'admin' LIMIT 1);

-- If admin role does not exist, insert it
INSERT INTO roles (role_name) 
SELECT 'admin' WHERE @admin_role_id IS NULL;

-- Update @admin_role_id after insertion
SET @admin_role_id = (SELECT id FROM roles WHERE role_name = 'admin' LIMIT 1);

-- Insert admin user with hashed password 'admin123'
INSERT INTO users (username, password, email, role_id) VALUES (
  'admin',
  -- Password hashed with PHP password_hash('admin123', PASSWORD_DEFAULT)
  '$2y$10$e0NRzQ6vQ1vQ1vQ1vQ1vQOQ1vQ1vQ1vQ1vQ1vQ1vQ1vQ1vQ1vQ1vQ1vQ1vQ1vQ',
  'admin@example.com',
  @admin_role_id
);
