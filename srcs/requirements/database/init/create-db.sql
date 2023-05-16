DROP DATABASE IF EXISTS camagru;

CREATE DATABASE camagru;

USE camagru;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  username varchar(30) NOT NULL DEFAULT '',
  email varchar(100) NOT NULL DEFAULT '',
  password varchar(255) NOT NULL DEFAULT '',
  active tinyint(1) NOT NULL DEFAULT 0,
  activation_code varchar(255) NOT NULL DEFAULT '',
  activation_expiration DATETIME NOT NULL,
  activated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  update_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS password_resets;
CREATE TABLE password_resets (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  email varchar(100) NOT NULL DEFAULT '',
  token varchar(255) NOT NULL DEFAULT '',
  expiration DATETIME NOT NULL,
  creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS stickers;
CREATE TABLE stickers (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL DEFAULT '',
  image_path varchar(255) NOT NULL DEFAULT '',
  creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- POPULATE STICKERS TABLE
INSERT INTO stickers (title, image_path) VALUES
('bunny', 'uploads/stickers/bunny.png'),
('mask', 'uploads/stickers/mask.png'),
('star', 'uploads/stickers/star.png'),
('cat', 'uploads/stickers/cat.png');

DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  title varchar(255) NOT NULL DEFAULT '',
  image_path varchar(255) NOT NULL DEFAULT '',
  creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  comment varchar(255) NOT NULL DEFAULT '',
  creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (post_id) REFERENCES posts(id)
);
