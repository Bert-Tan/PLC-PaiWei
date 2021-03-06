CREATE TABLE IF NOT EXISTS admUsr (
  ID INT UNSIGNED PRIMARY KEY,
  SessTyp INT UNSIGNED NOT NULL,
  FOREIGN KEY ( ID ) REFERENCES Usr ( ID ) ON DELETE CASCADE
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS inCareOf (
  ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  UsrName VARCHAR(60) NOT NULL
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS pwParam (
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	rtrtDate DATE NOT NULL,
	pwExpires DATE NOT NULL,
	rtEvent ENUM('Qingming','Zhongyuan','ThriceYearning','Anniversary') NOT NULL,
	rtReason tinytext,
	annivYear VARCHAR(10),
	lastRtrtDate DATE
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

