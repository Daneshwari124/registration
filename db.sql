CREATE DATABASE IF NOT EXISTS registration_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE registration_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  email VARCHAR(255),
  password VARCHAR(255),
  dob DATE,
  gender VARCHAR(20),
  phone VARCHAR(50),
  address1 VARCHAR(255),
  address2 VARCHAR(255),
  city VARCHAR(100),
  state VARCHAR(100),
  country VARCHAR(100),
  zipcode VARCHAR(20),
  education VARCHAR(150),
  occupation VARCHAR(150),
  company VARCHAR(150),
  industry VARCHAR(150),
  experience VARCHAR(50),
  skills TEXT,
  hobbies TEXT,
  marital_status VARCHAR(50),
  religion VARCHAR(50),
  blood_group VARCHAR(10),
  emergency_contact VARCHAR(100),
  website VARCHAR(255),
  linkedin VARCHAR(255),
  photo VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
