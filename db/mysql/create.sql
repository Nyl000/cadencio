ALTER DATABASE DEFAULT CHARACTER SET utf8;
ALTER DATABASE DEFAULT COLLATE utf8mb4_bin;

SET NAMES utf8;

DROP TABLE IF EXISTS roles_resources;
DROP TABLE IF EXISTS users_options;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS modules;
DROP TABLE IF EXISTS settings;


CREATE TABLE settings(
    name VARCHAR(128) NOT NULL,
    val TEXT DEFAULT NULL,
    PRIMARY KEY(name)
) ENGINE=InnoDB;

CREATE TABLE roles(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    label VARCHAR(128) NOT NULL,
    UNIQUE KEY(name),
    PRIMARY KEY(id)
) ENGINE=InnoDB;

INSERT INTO roles (id, name,label) VALUES(1, 'admin','Administrators');
INSERT INTO roles (id, name,label) VALUES(2, 'default','Users');


CREATE TABLE users (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(128) DEFAULT NULL,
    firstname VARCHAR(128) DEFAULT NULL,
    nickname VARCHAR(128) DEFAULT NULL,
    email VARCHAR(128) NOT NULL,
    password VARCHAR(256) NOT NULL,
    date_register DATETIME NOT NULL,
    id_role INT UNSIGNED NOT NULL DEFAULT 2,
    PRIMARY KEY(id),
    UNIQUE KEY(email),
    FOREIGN KEY(id_role) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;


--  For local tests
-- INSERT INTO users(id,email,password,date_register,id_role) VALUES (0,'loic.boucha@gmail.com', SHA2('loic01',256),NOW(),1);
-- INSERT INTO users(id,email,password,date_register,id_role) VALUES (0,'funnylity@gmail.com', SHA2('loic01',256),NOW(),2);

CREATE TABLE users_options (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    id_user INTEGER UNSIGNED NOT NULL,
    `key` VARCHAR(128) NOT NULL,
    `value` VARCHAR(256) DEFAULT NULL,
    PRIMARY KEY(id),
    UNIQUE KEY(id_user,`key`),
    FOREIGN KEY(id_user) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE roles_resources(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_role INT UNSIGNED NOT NULL,
    resource_name VARCHAR(128),
    resource_right VARCHAR(128),
    PRIMARY KEY(id),
    FOREIGN KEY(id_role) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO roles_resources VALUES(0,1,'*','*');




CREATE table notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  date DATETIME NOT NULL,
  id_user INTEGER UNSIGNED NOT NULL,
  title VARCHAR(128) NOT NULL,
  content TEXT,
  seen TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY(id),
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE table modules(
  name VARCHAR(128) NOT NULL,
  active TINYINT NOT NULL DEFAULT 0,
  version INT UNSIGNED NOT NULL DEFAULT 1,
  db_version INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(name)
)