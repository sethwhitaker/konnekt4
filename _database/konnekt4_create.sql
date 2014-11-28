-- Created by Vertabelo (http://vertabelo.com)
-- Script type: drop
-- Scope: [tables, references]
-- Generated at Sat Nov 22 21:34:13 UTC 2014



-- foreign keys
ALTER TABLE chat DROP FOREIGN KEY chat_users;
ALTER TABLE game DROP FOREIGN KEY games_end_type;
ALTER TABLE game_user DROP FOREIGN KEY games_users_games;
ALTER TABLE game_user DROP FOREIGN KEY games_users_stat_type;
ALTER TABLE game_user DROP FOREIGN KEY games_users_users;
ALTER TABLE stat DROP FOREIGN KEY stats_users;

-- tables
-- Table chat
DROP TABLE chat;
-- Table end_type
DROP TABLE end_type;
-- Table game
DROP TABLE game;
-- Table game_user
DROP TABLE game_user;
-- Table stat
DROP TABLE stat;
-- Table stat_type
DROP TABLE stat_type;
-- Table user
DROP TABLE user;



-- End of file.

-- Created by Vertabelo (http://vertabelo.com)
-- Script type: create
-- Scope: [tables, references]
-- Generated at Sat Nov 22 22:03:52 UTC 2014




-- tables
-- Table: challenge_type
CREATE TABLE challenge_type (
    id int    NOT NULL  AUTO_INCREMENT,
    type varchar(16)    NOT NULL ,
    CONSTRAINT challenge_type_pk PRIMARY KEY (id)
);

-- Table: chat
CREATE TABLE chat (
    id int    NOT NULL  AUTO_INCREMENT,
    message varchar(256)    NOT NULL ,
    time int    NOT NULL ,
    user_id int    NOT NULL ,
    CONSTRAINT chat_pk PRIMARY KEY (id)
);

-- Table: end_type
CREATE TABLE end_type (
    id int    NOT NULL  AUTO_INCREMENT,
    type varchar(64)    NOT NULL ,
    CONSTRAINT end_type_pk PRIMARY KEY (id)
);

-- Table: game
CREATE TABLE game (
    id int    NOT NULL  AUTO_INCREMENT,
    whose_turn int    NOT NULL ,
    board varchar(2048)    NULL ,
    last_move varchar(128)    NULL ,
    last_updated int    NOT NULL ,
    active int    NOT NULL ,
    end_type_id int    NULL ,
    CONSTRAINT game_pk PRIMARY KEY (id)
);

-- Table: game_user
CREATE TABLE game_user (
    id int    NOT NULL  AUTO_INCREMENT,
    game_id int    NOT NULL ,
    user_id int    NOT NULL ,
    stat_type_id int    NULL ,
    challenge_type_id int    NOT NULL ,
    CONSTRAINT game_user_pk PRIMARY KEY (id)
);

-- Table: stat
CREATE TABLE stat (
    id int    NOT NULL  AUTO_INCREMENT,
    user_id int    NOT NULL ,
    wins int    NOT NULL DEFAULT 0 ,
    losses int    NOT NULL DEFAULT 0 ,
    ties int    NOT NULL DEFAULT 0 ,
    CONSTRAINT stat_pk PRIMARY KEY (id)
);

-- Table: stat_type
CREATE TABLE stat_type (
    id int    NOT NULL  AUTO_INCREMENT,
    type varchar(16)    NOT NULL ,
    CONSTRAINT stat_type_pk PRIMARY KEY (id)
);

-- Table: user
CREATE TABLE user (
    id int    NOT NULL  AUTO_INCREMENT,
    username varchar(64)    NOT NULL ,
    email varchar(64)    NULL ,
    password varchar(64)    NOT NULL ,
    logged_in int    NOT NULL ,
    CONSTRAINT user_pk PRIMARY KEY (id)
);





-- foreign keys
-- Reference:  chat_users (table: chat)


ALTER TABLE chat ADD CONSTRAINT chat_users FOREIGN KEY chat_users (user_id)
    REFERENCES user (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
-- Reference:  game_user_challenge_type (table: game_user)


ALTER TABLE game_user ADD CONSTRAINT game_user_challenge_type FOREIGN KEY game_user_challenge_type (challenge_type_id)
    REFERENCES challenge_type (id);
-- Reference:  games_end_type (table: game)


ALTER TABLE game ADD CONSTRAINT games_end_type FOREIGN KEY games_end_type (end_type_id)
    REFERENCES end_type (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
-- Reference:  games_users_games (table: game_user)


ALTER TABLE game_user ADD CONSTRAINT games_users_games FOREIGN KEY games_users_games (game_id)
    REFERENCES game (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
-- Reference:  games_users_stat_type (table: game_user)


ALTER TABLE game_user ADD CONSTRAINT games_users_stat_type FOREIGN KEY games_users_stat_type (stat_type_id)
    REFERENCES stat_type (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
-- Reference:  games_users_users (table: game_user)


ALTER TABLE game_user ADD CONSTRAINT games_users_users FOREIGN KEY games_users_users (user_id)
    REFERENCES user (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
-- Reference:  stats_users (table: stat)


ALTER TABLE stat ADD CONSTRAINT stats_users FOREIGN KEY stats_users (user_id)
    REFERENCES user (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;



-- End of file.


INSERT INTO `user` (`id`, `username`, `email`, `password`, `logged_in`) VALUES
(1, 'seth', NULL, '25a3b6cc85a8cde49f8b0f9aa21c050bc04072d4', 0);

INSERT INTO `challenge_type` (`id`, `type`) VALUES
(1, 'challenger'),
(2, 'challenged');

INSERT INTO `stat_type` (`id`, `type`) VALUES
(1, 'win'),
(2, 'loss'),
(3, 'tie');

INSERT INTO `stat` (`id`, `user_id`, `wins`, `losses`, `ties`) VALUES
(1, 1, 0, 0, 0);

INSERT INTO `end_type` (`id`, `type`) VALUES
(1, 'tie'),
(2, 'across'),
(3, 'stacked'),
(4, 'diagonal_right'),
(5, 'diagonal_left');