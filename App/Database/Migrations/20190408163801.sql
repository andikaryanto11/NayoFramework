CREATE TABLE `tests` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Example_Id` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Example_Id` (`Example_Id`),
  CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`Example_Id`) REFERENCES `examples` (`Id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
