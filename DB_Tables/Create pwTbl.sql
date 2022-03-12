CREATE TABLE IF NOT EXISTS C001A
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	C_Name VARCHAR (40),
	timestamp DATE,
	UNIQUE ( C_Name )	/* Shall not be unique for people may have the same name; consider Alter Table property !! */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS D001A
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	D_Name Varchar (100),
	D_Requestor VARCHAR (40),
	timestamp DATE,
	UNIQUE ( D_Name, D_Requestor )	/* only D_Name shall be Unique; consider Alter Table property !! */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS L001A
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	L_Name VARCHAR (40),
	L_Requestor VARCHAR (40),
	timestamp DATE,
	UNIQUE ( L_Name, L_Requestor ) /* may not be unique because people may have the same name!! - consider to Ulter Table property!! */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS W001A_4
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	W_Title VARCHAR (40),
	W_Name VARCHAR (40),
	R_Title VARCHAR (40),
	W_Requestor VARCHAR (40),
	timestamp DATE
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS Y001A
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	Y_Requestor VARCHAR (40),
	timestamp DATE,
	UNIQUE ( Y_Requestor )		/* may not be unique because people may have the same name!! - consider to Ulter Table property!! */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS DaPaiWei
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	W_Title VARCHAR (40),
	W_Name VARCHAR (40),
	deceasedDate Date,
	R_Title VARCHAR (40),
	W_Requestor VARCHAR (40),
	timestamp DATE
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS pw2Usr (
	TblName	VARCHAR (16),
	pwID INT UNSIGNED,
	pwUsrName VARCHAR(60),		/* matches Usr.UsrName or inCareOf.UsrName */
	PRIMARY KEY ( TblName, pwID )
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS DaPaiWeiRed
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	C_Name VARCHAR (40),
	timestamp DATE,
	UNIQUE ( C_Name )
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;