CREATE DATABASE IF NOT EXISTS php_docker;

USE php_docker;

CREATE TABLE IF NOT EXISTS student (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Age INT NOT NULL CHECK (Age >= 18),
    Gender ENUM('male', 'female', 'others') NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Phone VARCHAR(15) NOT NULL UNIQUE, -- Increased length for international numbers
    New_Password VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Track when the record was created
   ModifiedAt TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);