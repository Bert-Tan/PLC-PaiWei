CREATE TABLE IF NOT EXISTS sundayQifu
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	R_Name VARCHAR (40), /* Requestor's name */
	qWhom VARCHAR (40),	 /* Recipient */
	GuanXi VARCHAR (40), /* Relationship */
	Rsn VARCHAR (80)
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS sundayMerit
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	R_Name VARCHAR (40),
	mWhom VARCHAR (40),
	GuanXi VARCHAR (40),
	Age TINYINT,
    Deceased_D DATE,	/* Deceased Date */
	Deceased_P VARCHAR(60) /* Deceased Place */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

/*
CREATE TABLE IF NOT EXISTS sundayRq2GongDeZhu
(
    TblName VARCHAR(16),
	rqID INT UNSIGNED,
	GongDeZhu BOOLEAN,
	rqTime DATETIME,
	PRIMARY KEY ( TblName, rqID )
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
*/

CREATE TABLE IF NOT EXISTS sundayRq2Usr
(
    TblName VARCHAR(16),    /* qifu, or merit */
	rqID INT UNSIGNED,		/* ID in the qifu table or merit table */
	UsrName VARCHAR (60),	/* matches Usr.UsrName or inCareOf.UsrName */
	PRIMARY KEY ( TblName, rqID ) /* This takes care of N (requests) : 1 (owner) Data Relationship */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS sundayRq2Days
(
    TblName VARCHAR(16),
    rqID INT UNSIGNED,
    rqDate DATE,
    PRIMARY KEY ( TblName, rqID, rqDate ) /* This takes care of M (requests) : N (Sundays) Data Relationship */
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS sundayParam
(
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	expHH VARCHAR(2),
	expMM VARChar(2)
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;