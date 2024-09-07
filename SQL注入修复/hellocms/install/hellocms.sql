CREATE DATABASE ctf;
use ctf;

CREATE TABLE metadata (
    id INT PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(300),
    value_text VARCHAR(300)
);

INSERT INTO metadata (key_name, value_text) VALUES ('author', '探姬');
INSERT INTO metadata (key_name, value_text) VALUES ('date', '2024-08-26 14:34');
INSERT INTO metadata (key_name, value_text) VALUES ('repo', 'github.com/ProbiusOfficial/ctf2awd');
INSERT INTO metadata (key_name, value_text) VALUES ('email', 'admin@hello-ctf.com');
INSERT INTO metadata (key_name, value_text) VALUES ('link', 'hello-ctf.com');

CREATE DATABASE IF NOT EXISTS filemanager;

USE filemanager;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password) VALUES 
('admin', MD5('admin')),
('user1', MD5('123456')),
('user2', MD5('jaga123'));

