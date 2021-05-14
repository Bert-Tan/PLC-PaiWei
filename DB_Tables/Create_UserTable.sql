CREATE TABLE IF NOT EXISTS Usr (
  ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  UsrName VARCHAR(60) NOT NULL,
  UsrPass VARCHAR(60) NOT NULL,
  UsrEmail VARCHAR(255) NOT NULL,
  UNIQUE ( UsrName ),
  UNIQUE ( UsrEmail ) /* Does this require to be Unique? It shoud not! However, if NOT, then the password reset logic needs to be redesigned */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS UsrRst (
  ID INT UNSIGNED PRIMARY KEY,
  Token VARCHAR (60) UNIQUE NOT NULL,
  Expires DATETIME,
	FOREIGN KEY ( ID ) REFERENCES Usr ( ID )
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
