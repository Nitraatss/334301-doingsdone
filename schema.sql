CREATE DATABASE schema_db;

USE schema_db;

CREATE TABLE projects(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
category CHAR
);

CREATE TABLE users(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
email CHAR,
username CHAR,
pass CHAR
);

CREATE TABLE tasks(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
title CHAR,
deadline_date DATE,
is_done BOOLEAN,
project_id INT,
user_id INT
);