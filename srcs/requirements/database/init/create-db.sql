DROP DATABASE IF EXISTS camagru;

CREATE DATABASE camagru;

USE camagru;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  username varchar(30) NOT NULL DEFAULT '',
  email varchar(100) NOT NULL DEFAULT '',
  password varchar(255) NOT NULL DEFAULT ''
);