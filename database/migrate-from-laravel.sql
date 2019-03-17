TRUNCATE TABLE symfony.game;
INSERT INTO symfony.game (name, description, db, alias, is_active, start_day, start_hour)
SELECT name, description, `database`, alias, 1, adddays, addhours FROM fantasyasql5.games;
INSERT INTO symfony.game (name, description, db, alias, is_active, start_day, start_hour)
SELECT name, description, `database`, alias, 0, adddays, addhours FROM fantasyasql5.games_old;
UPDATE symfony.game SET start_day = 0 WHERE alias != 'spiel';

TRUNCATE TABLE symfony.myth;
INSERT INTO symfony.myth (myth)
SELECT myth FROM fantasyasql5.myth;

TRUNCATE TABLE symfony.news;
INSERT IGNORE INTO symfony.news (created_at, title, content)
SELECT created_at, title, content FROM fantasyasql5.news;

TRUNCATE TABLE symfony.user;
INSERT INTO symfony.user (id, name, roles, password, email)
SELECT id, name, '["ROLE_USER"]', '', email FROM fantasyasql5.users;
UPDATE symfony.user SET roles = '["ROLE_USER", "ROLE_ADMIN", "ROLE_BETA_TESTER", "ROLE_MULTI_PLAYER", "ROLE_NEWS_CREATOR"]' WHERE name = 'Thalian';
