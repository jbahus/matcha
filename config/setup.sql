Create DATABASE IF NOT EXISTS matcha;

USE matcha;

CREATE TABLE IF NOT EXISTS users
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    age INT DEFAULT NULL,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    gender INT DEFAULT 0,
    orientation INT DEFAULT 0,
    active INT DEFAULT 0,
    hidden INT DEFAULT 0,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bio TEXT(2048) DEFAULT NULL,
    lon FLOAT DEFAULT 0,
    lat FLOAT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS tags
(
    id_user INT NOT NULL,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS scores
(
  id_user INT NOT NULL,
  score INT DEFAULT 0
);

CREATE TABLE if NOT EXISTS matching
(
  id1 INT NOT NULL,
  id2 INT NOT NULL,
  matched boolean DEFAULT NULL
);

-- INSERT INTO users(firstname, lastname, age, email, username, password, gender, orientation, active, hidden, bio, lon, lat) VALUES ('Sophie', 'Laffé', 24, 'test@test.test.fr','Sosodu93', '5ad9e0749efcefa2de23ab60b4111392adea9e7b9fad27480ce8f1a052e9dc14b66e1edee1a86fba2b458cb55f5949231415941bfc7cbd2e15aa8a3dbd9ea869', 2, 2, 1, 1, 'Coucou', '2.350751', '48.932683');
-- INSERT INTO tags(id_user, name) VALUES (1, 'geek'), (1, 'tatouage');
-- INSERT INTO scores(id_user, score) VALUES (1, 20);
-- INSERT INTO users(firstname, lastname, age, email, username, password, gender, orientation, active, hidden, bio, lon, lat) VALUES ('Jean', 'Larue', 20, 'toto@test.test.fr','PoubelleModerne', '5ad9e0749efcefa2de23ab60b4111392adea9e7b9fad27480ce8f1a052e9dc14b66e1edee1a86fba2b458cb55f5949231415941bfc7cbd2e15aa8a3dbd9ea869', 1, 2, 1, 1, 'Coucou', '2.350751', '48.932683');
-- INSERT INTO tags(id_user, name) VALUES (2, 'geek'), (2, 'tatouage');
-- INSERT INTO scores(id_user, score) VALUES (2, 10);
-- INSERT INTO users(firstname, lastname, age, email, username, password, gender, orientation, active, hidden, bio, lon, lat) VALUES ('Remi', 'Fassol', 18, 'tata@test.test.fr','Lasido', '5ad9e0749efcefa2de23ab60b4111392adea9e7b9fad27480ce8f1a052e9dc14b66e1edee1a86fba2b458cb55f5949231415941bfc7cbd2e15aa8a3dbd9ea869', 1, 1, 1, 1, 'Coucou', '2.350751', '48.932683');
-- INSERT INTO tags(id_user, name) VALUES (3, 'geek'), (3, 'tatouage');
-- INSERT INTO scores(id_user, score) VALUES (3, 3000);
-- INSERT INTO users(firstname, lastname, age, email, username, password, gender, orientation, active, hidden, bio, lon, lat) VALUES ('George', 'Abitbol', 35, 'titi@test.test.fr','LHommeLePlusClasseDuMonde', '5ad9e0749efcefa2de23ab60b4111392adea9e7b9fad27480ce8f1a052e9dc14b66e1edee1a86fba2b458cb55f5949231415941bfc7cbd2e15aa8a3dbd9ea869', 1, 0, 1, 1, 'Coucou', '2.350751', '48.932683');
-- INSERT INTO tags(id_user, name) VALUES (4, 'geek'), (4, 'tatouage');
-- INSERT INTO scores(id_user, score) VALUES (4, 42);
-- INSERT INTO users(firstname, lastname, age, email, username, password, gender, orientation, active, hidden, bio, lon, lat) VALUES ('Laura', 'Tatta', 22, 'tutu@test.test.fr','AttaqueEclair', '5ad9e0749efcefa2de23ab60b4111392adea9e7b9fad27480ce8f1a052e9dc14b66e1edee1a86fba2b458cb55f5949231415941bfc7cbd2e15aa8a3dbd9ea869', 2, 2, 1, 1, 'Coucou', '2.350751', '48.932683');
-- INSERT INTO tags(id_user, name) VALUES (5, 'geek'), (5, 'tatouage'), (5, 'ville');
-- INSERT INTO scores(id_user, score) VALUES (5, 150);
-- INSERT INTO users(firstname, lastname, age, email, username, password, gender, orientation, active, hidden, bio, lon, lat) VALUES ('Miranda', 'Abricot', 32, 'gogo@test.test.fr','DansLarbre', '5ad9e0749efcefa2de23ab60b4111392adea9e7b9fad27480ce8f1a052e9dc14b66e1edee1a86fba2b458cb55f5949231415941bfc7cbd2e15aa8a3dbd9ea869', 2, 1, 1, 1, 'Coucou', '2.350751', '48.932683');
-- INSERT INTO tags(id_user, name) VALUES (6, 'geek'), (6, 'tatouage'), (6, 'ville'), (6, 'poilu');
-- INSERT INTO scores(id_user, score) VALUES (6, 40);
-- INSERT INTO users(firstname, lastname, age, email, username, password, gender, orientation, active, hidden, bio, lon, lat) VALUES ('Roxane', 'Carpe', 40, 'pfiou@test.test.fr','LaPecheTrk', '5ad9e0749efcefa2de23ab60b4111392adea9e7b9fad27480ce8f1a052e9dc14b66e1edee1a86fba2b458cb55f5949231415941bfc7cbd2e15aa8a3dbd9ea869', 2, 1, 1, 1, 'Coucou', '2.350751', '48.932683');
-- INSERT INTO tags(id_user, name) VALUES (7, 'geek'), (7, 'tatouage'), (7, 'ville'), (7, 'poilu');
-- INSERT INTO scores(id_user, score) VALUES (7, 80);
