ALTER DATABASE DEFAULT CHARACTER SET utf8;
ALTER DATABASE DEFAULT COLLATE utf8mb4_bin;

SET NAMES utf8;

DROP TABLE IF EXISTS planning_entry_followers;
DROP TABLE IF EXISTS planning_entry;
DROP TABLE IF EXISTS planning_status;
DROP TABLE IF EXISTS plannings;
DROP TABLE IF EXISTS roles_resources;
DROP TABLE IF EXISTS users_options;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

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


CREATE TABLE planning_status (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(256),
    color VARCHAR(30) NOT NULL DEFAULT '#999999',
    closed TINYINT NOT NULL DEFAULT 0,
    PRIMARY KEY(id)
) ENGINE=InnoDB;

INSERT INTO planning_status(id,title,color,closed) VALUES (0,'Awaiting','#F2C511',0);
INSERT INTO planning_status(id,title,color,closed) VALUES (0,'Planned','#3D556E',0);
INSERT INTO planning_status(id,title,color,closed) VALUES (0,'Ready','#F39C19',0);
INSERT INTO planning_status(id,title,color,closed) VALUES (0,'Ongoing','#3398DB',0);
INSERT INTO planning_status(id,title,color,closed) VALUES (0,'Done','#2ECC70',1);


CREATE TABLE plannings (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(256),
    PRIMARY KEY(id)
) ENGINE=InnoDB;

INSERT INTO plannings VALUES (0,'Global planning');


CREATE TABLE planning_entry (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_creator INTEGER UNSIGNED NOT NULL,
    title VARCHAR(256),
    description TEXT,
    id_status INTEGER UNSIGNED NOT NULL,
    id_planning INTEGER UNSIGNED NOT NULL,
    date_start DATETIME DEFAULT NULL,
    date_end DATETIME DEFAULT NULL,
    id_assigned_to INTEGER UNSIGNED DEFAULT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (id_status) REFERENCES planning_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_planning) REFERENCES plannings(id) ON DELETE CASCADE,
    FOREIGN KEY (id_creator) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX(date_start),
    INDEX(date_end)
) ENGINE=InnoDB;

CREATE TABLE planning_entry_followers (
    id_planning_entry BIGINT UNSIGNED NOT NULL,
    id_user INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id_planning_entry,id_user),
    FOREIGN KEY (id_planning_entry) REFERENCES planning_entry(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


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