DROP TABLE IF EXISTS users; 

CREATE TABLE users 
  ( 
	 id       SMALLINT UNSIGNED NOT NULL auto_increment, 
	 username VARCHAR(255) NOT NULL,
	 password TEXT NOT NULL, 
	 role     VARCHAR(255) NOT NULL,
	 PRIMARY KEY (id) 
  );

DROP TABLE IF EXISTS loginlogs; 

CREATE TABLE loginlogs (
	id SMALLINT UNSIGNED NOT NULL auto_increment,
	IpAddress varbinary(16) NOT NULL,
	TryTime bigint(20) NOT NULL,
	PRIMARY KEY (id) 
);

DROP TABLE IF EXISTS categories; 

CREATE TABLE categories 
  ( 
	 id          SMALLINT UNSIGNED NOT NULL auto_increment, 
	 name        VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	 slug     VARCHAR(30) NOT NULL,
	 PRIMARY KEY (id) 
  );

DROP TABLE IF EXISTS cat_links; 

CREATE TABLE cat_links 
  ( 
	 id         SMALLINT UNSIGNED NOT NULL auto_increment, 
	 gameid     SMALLINT UNSIGNED NOT NULL,
	 categoryid SMALLINT UNSIGNED NOT NULL,
	 PRIMARY KEY (id) 
  ); 

DROP TABLE IF EXISTS pages; 

CREATE TABLE pages 
  ( 
	 id          SMALLINT UNSIGNED NOT NULL auto_increment, 
	 createddate DATE NOT NULL,
	 title       VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	 slug        VARCHAR(255) NOT NULL,
	 content     MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	 PRIMARY KEY (id) 
  );

DROP TABLE IF EXISTS games; 

CREATE TABLE games 
  ( 
	 id           SMALLINT UNSIGNED NOT NULL auto_increment, 
	 createddate  DATE NOT NULL,
	 title        VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	 description  MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
	 instructions MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
	 category     TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
	 source       TEXT NOT NULL, 
	 thumb_1      TEXT NOT NULL, 
	 thumb_2      TEXT NOT NULL, 
	 url          TEXT NOT NULL, 
	 width        TEXT NOT NULL, 
	 height       TEXT NOT NULL, 
	 tags         TEXT NOT NULL, 
	 views        INT NOT NULL, 
	 upvote       INT NOT NULL, 
	 downvote     INT NOT NULL,
	 slug     VARCHAR(30) NOT NULL,
	 data MEDIUMTEXT NOT NULL, 
	 PRIMARY KEY (id) 
  );