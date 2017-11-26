CREATE DATABASE schema_db;

USE schema_db;

CREATE TABLE projects(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
category CHAR(50)
);

CREATE TABLE users(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
email CHAR(255),
username CHAR(50),
pass CHAR(60)
);

CREATE TABLE tasks(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
title CHAR(255),
deadline_date DATE,
is_done BOOLEAN,
project_id INT,
user_id INT
);