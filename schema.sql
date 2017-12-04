CREATE DATABASE schema_db;

USE schema_db;

CREATE TABLE projects(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
category CHAR(50),
user_id INT
);

CREATE TABLE users(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
email CHAR(255),
username CHAR(50),
pass CHAR(60),
registration_date DATE,
contacts CHAR(255)
);

CREATE TABLE tasks(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
title CHAR(255),
creation_date DATE,
deadline_date DATE,
file_path CHAR(255),
is_done BOOLEAN,
project_id INT,
user_id INT
);