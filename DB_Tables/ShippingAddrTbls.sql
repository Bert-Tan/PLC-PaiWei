CREATE TABLE IF NOT EXISTS UsrAddr (
  AddrID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  Addressee VARCHAR(60) NOT NULL,
  TelNo VARCHAR(10) NOT NULL,
  Email VARCHAR(255) NOT NULL,
  StNum VARCHAR(255) NOT NULL,
  Unit VARCHAR(8),
  City VARCHAR(60) NOT NULL,
  US_State VARCHAR(2) NOT NULL,
  ZipCode VARCHAR(5) NOT NULL
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS Addr2Usr (
  AddrID INT UNSIGNED,
  UsrName VARCHAR(60)
  Primary Key ( AddrID, UsrName)
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `Usr` ADD `PrimAddrID`  INT(10) UNSIGNED NULL DEFAULT NULL AFTER `UsrEmail`;

ALTER TABLE `Usr` ADD CONSTRAINT `Usr_ibfk_1` FOREIGN KEY ( PrimAddrID ) REFERENCES `UsrAddr` ( AddrID ) ON DELETE SET NULL;

ALTER TABLE `Addr2Usr` ADD CONSTRAINT `Addr2Usr_ibfk_1` FOREIGN KEY ( AddrID ) REFERENCES `UsrAddr` ( AddrID ) ON DELETE CASCADE;

ALTER TABLE `Addr2Usr` ADD CONSTRAINT `Addr2Usr_ibfk_2` FOREIGN KEY ( UsrName ) REFERENCES `Usr` ( UsrName ) ON DELETE CASCADE;