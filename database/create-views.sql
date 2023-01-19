CREATE OR REPLACE VIEW view_parteien AS
SELECT u.id AS user_id, u.name AS user_name, u.email AS user_mail, IF(p.nummer IS NULL, NULL, 'game') AS game, p.nummer AS partei_nummer, p.id AS partei_id, p.name AS partei_name, p.email AS partei_mail
FROM user u
LEFT JOIN fantasyasql1.partei p ON p.user_id = u.id
UNION
SELECT u.id AS user_id, u.name AS user_name, u.email AS user_mail, IF(p.nummer IS NULL, NULL, 'game') AS game, p.nummer AS partei_nummer, p.id AS partei_id, p.name AS partei_name, p.email AS partei_mail
FROM user u
RIGHT JOIN fantasyasql1.partei p ON p.user_id = u.id
UNION
SELECT u.id AS user_id, u.name AS user_name, u.email AS user_mail, 'lemuria' AS game, NULL AS partei_nummer, NULL AS partei_id, a.uuid AS partei_name, u.email AS partei_email
FROM user u
RIGHT JOIN assignment a ON a.user_id = u.id
ORDER BY game, partei_name;
