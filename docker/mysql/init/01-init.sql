-- Database initialization script
-- This script runs automatically when the MySQL container starts for the first time

-- Ensure UTF8 encoding is used
ALTER DATABASE `app_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create any additional database objects needed for initial setup
-- For example, you might want to create additional users or roles

-- Grant permissions
GRANT ALL PRIVILEGES ON `app_db`.* TO 'user'@'%';
FLUSH PRIVILEGES;

-- You can add more initialization SQL here as needed
