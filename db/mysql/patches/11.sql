INSERT INTO settings(name,val) VALUES('default_timezone' , 'Europe/Brussels');
INSERT INTO settings(name,val) VALUES('default_language' , 'en');
INSERT IGNORE INTO users_options (id_user,`key`,`value`) SELECT id,'lang','en' FROM users;