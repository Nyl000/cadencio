ALTER TABLE planning_status ADD closed TINYINT NOT NULL DEFAULT 0;

UPDATE planning_status SET closed = 1 WHERE title = 'done' OR title = 'Done';