ALTER TABLE users ADD hash VARCHAR(256);
UPDATE users SET hash = SHA2(UUID(), 256);