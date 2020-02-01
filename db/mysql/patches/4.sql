CREATE table modules(
  name VARCHAR(128) NOT NULL,
  active TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY(name)
)