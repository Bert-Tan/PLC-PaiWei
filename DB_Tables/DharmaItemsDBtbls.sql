CREATE TABLE IF NOT EXISTS DIrqCart (
  DIrqID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  UsrName VARCHAR(60) NOT NULL,
  UsrAddrID INT UNSIGNED NOT NULL,
  DateReq DATE NOT NULL,
  DateProc DATE,
  ProcBy INT UNSIGNED,
  Remark VARCHAR (100),
  FOREIGN KEY ( UsrName ) REFERENCES Usr ( UsrName ) ON DELETE NO ACTION,
  FOREIGN KEY ( UsrAddrID ) REFERENCES UsrAddr ( AddrID ) ON DELETE NO ACTION,
  FOREIGN KEY ( ProcBy ) REFERENCES Usr ( ID ) ON DELETE NO ACTION
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS INVT_Type (
  invtTblName ENUM( 'INVT_BK_C', 'INVT_BK_E' ),
  invtDesc VARCHAR(100),
  PRIMARY KEY ( invtTblName )
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS DIrqItem (
  invtTblName ENUM( 'INVT_BK_C', 'INVT_BK_E' ),
  invtID INT UNSIGNED,
  DIrqID INT UNSIGNED,
  Remark VARCHAR(100),
  FOREIGN KEY ( invtTblName ) REFERENCES INVT_Type ( invtTblName ) ON DELETE NO ACTION,
  PRIMARY KEY ( invtTblName, invtID )
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS INVT_BK_C (
  invtID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  Strokes TINYINT UNSIGNED,
  Title VARCHAR(200),
  Author VARCHAR(100),
  Loc VARCHAR(30),
  Qty INT UNSIGNED,
  Remark VARCHAR(100)
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS INVT_BK_E (
  invtID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  Title VARCHAR (160),
  Author VARCHAR (120),
  Loc VARCHAR (16),
  Qty INT UNSIGNED,
  Remark VARCHAR(100)
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;