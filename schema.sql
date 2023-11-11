DROP DATABASE IF EXISTS yeticave;

CREATE DATABASE yeticave CHARACTER SET utf8 COLLATE UTF8_UNICODE_CI;

USE yeticave;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name_category VARCHAR(128) NOT NULL UNIQUE,
    character_code VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date_registration DATETIME DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL UNIQUE,
    user_name VARCHAR(128) NOT NULL,
    user_password CHAR(12) NOT NULL,
    contacts TEXT
);

CREATE TABLE lots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(255),
    lot_description TEXT,
    img VARCHAR(255),
    start_price INT UNSIGNED,
    date_end DATE,
    step INT UNSIGNED,
    user_id INT,
    winner_id INT,
    category_id INT,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (winner_id) REFERENCES users (id),
    FOREIGN KEY (category_id) REFERENCES categories (id)
);

CREATE TABLE bets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date_bet DATETIME DEFAULT CURRENT_TIMESTAMP,
    price_bet INT UNSIGNED,
    user_id INT,
    lot_id INT,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (lot_id) REFERENCES lots (id)
);
