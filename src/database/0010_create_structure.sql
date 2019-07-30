CREATE TABLE bot (
  bot_id int(10) UNSIGNED NOT NULL,
  github_user_id int(10) UNSIGNED NOT NULL,
  bot_client_id char(32) CHARACTER SET latin1_general_ci NOT NULL,
  bot_secret char(32) CHARACTER SET latin1_general_ci NOT NULL,
  game_id int(10) UNSIGNED NOT NULL,
  match_id int(10) UNSIGNED DEFAULT NULL,
  is_deleted int(1) NOT NULL DEFAULT '0',
  create_time int(10) UNSIGNED DEFAULT NULL,
  last_activity_time int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

ALTER TABLE bot
  ADD PRIMARY KEY (bot_id),
  ADD KEY github_user_id (github_user_id),
  ADD KEY bot_client_id (bot_client_id(4));

ALTER TABLE bot
  MODIFY bot_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


CREATE TABLE game (
  game_id int(10) UNSIGNED NOT NULL,
  game_name varchar(4000) CHARACTER SET latin1_general_ci NOT NULL,
  game_title varchar(4000) CHARACTER SET utf8 NOT NULL,
  game_description varchar(4000) CHARACTER SET utf8 NOT NULL,
  game_players int(10) UNSIGNED NOT NULL,
  is_deleted int(1) NOT NULL DEFAULT '0',
  create_time int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

ALTER TABLE game
  ADD PRIMARY KEY (game_id);

ALTER TABLE game
  MODIFY game_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
