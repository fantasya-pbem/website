TRUNCATE TABLE symfony.game;
INSERT INTO symfony.game
SELECT id, name, description, `database`, alias, 1, 0, 0 FROM fantasyasql5.games;

TRUNCATE TABLE symfony.myth;
INSERT INTO symfony.myth (myth)
SELECT myth FROM fantasyasql5.myth;

TRUNCATE TABLE symfony.news;
INSERT IGNORE INTO symfony.news (created_at, title, content)
SELECT created_at, title, content FROM fantasyasql5.news;

TRUNCATE TABLE symfony.user;
INSERT INTO symfony.user
SELECT id, name, '["ROLE_USER"]', '', email FROM fantasyasql5.users;
UPDATE symfony.user SET roles = '["ROLE_USER", "ROLE_ADMIN", "ROLE_BETA_TESTER", "ROLE_MULTI_PLAYER", "ROLE_NEWS_CREATOR"]' WHERE name = 'Thalian';
