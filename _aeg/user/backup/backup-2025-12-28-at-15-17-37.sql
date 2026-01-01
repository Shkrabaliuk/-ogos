SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=NO_AUTO_VALUE_ON_ZERO */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
CREATE TABLE `e2BlogActions` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `EntityID` int(10) unsigned NOT NULL DEFAULT 0,
  `Stamp` int(10) unsigned NOT NULL DEFAULT 0,
  `ReadCount` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EntityIDStamp` (`EntityID`,`Stamp`),
  KEY `SubsetID` (`SubsetID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `e2BlogAliases` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `EntityType` varchar(1) NOT NULL DEFAULT '',
  `EntityID` int(10) unsigned NOT NULL DEFAULT 0,
  `Alias` varchar(64) NOT NULL DEFAULT '',
  `Stamp` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `SubsetID` (`SubsetID`),
  KEY `Alias` (`Alias`),
  KEY `EntityID` (`EntityID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `e2BlogComments` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `NoteID` int(10) unsigned NOT NULL DEFAULT 0,
  `AuthorName` varchar(255) NOT NULL DEFAULT '',
  `AuthorEmail` varchar(255) NOT NULL DEFAULT '',
  `Text` mediumtext DEFAULT NULL,
  `Reply` mediumtext DEFAULT NULL,
  `IsVisible` tinyint(1) NOT NULL DEFAULT 1,
  `IsFavourite` tinyint(1) NOT NULL DEFAULT 0,
  `IsReplyVisible` tinyint(1) NOT NULL DEFAULT 0,
  `IsReplyFavourite` tinyint(1) NOT NULL DEFAULT 0,
  `IsAnswerAware` tinyint(1) NOT NULL DEFAULT 1,
  `IsSubscriber` tinyint(1) NOT NULL DEFAULT 0,
  `IsSpamSuspect` tinyint(1) NOT NULL DEFAULT 0,
  `IsNew` tinyint(1) NOT NULL DEFAULT 0,
  `Stamp` int(10) unsigned NOT NULL DEFAULT 0,
  `LastModified` int(10) unsigned NOT NULL DEFAULT 0,
  `ReplyStamp` int(10) unsigned NOT NULL DEFAULT 0,
  `ReplyLastModified` int(10) unsigned NOT NULL DEFAULT 0,
  `IP` varchar(39) NOT NULL DEFAULT '',
  `IsGIPUsed` tinyint(1) NOT NULL DEFAULT 0,
  `GIP` varchar(15) NOT NULL DEFAULT '',
  `GIPAuthorID` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `SubsetID` (`SubsetID`),
  KEY `NoteID` (`NoteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `e2BlogGIPsSessions` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `GIP` varchar(15) NOT NULL DEFAULT '',
  `GIPAuthorID` varchar(64) NOT NULL DEFAULT '',
  `AuthorName` varchar(255) NOT NULL DEFAULT '',
  `AuthorEmail` varchar(255) NOT NULL DEFAULT '',
  `AuthorProfileLink` varchar(255) NOT NULL DEFAULT '',
  `SessionToken` varchar(255) NOT NULL DEFAULT '',
  `Stamp` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `SubsetIDGIPGIPAuthorID` (`SubsetID`,`GIP`,`GIPAuthorID`),
  KEY `SubsetID` (`SubsetID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `e2BlogKeywords` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `Keyword` varchar(255) NOT NULL DEFAULT '',
  `OriginalAlias` varchar(64) NOT NULL DEFAULT '',
  `PageTitle` varchar(255) NOT NULL DEFAULT '',
  `Description` mediumtext DEFAULT NULL,
  `Summary` mediumtext DEFAULT NULL,
  `Uploads` mediumtext DEFAULT NULL,
  `IsVisible` tinyint(1) NOT NULL DEFAULT 1,
  `IsFavourite` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `SubsetID` (`SubsetID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `e2BlogNotes` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `Title` varchar(255) NOT NULL DEFAULT '',
  `Text` mediumtext DEFAULT NULL,
  `Summary` mediumtext DEFAULT NULL,
  `FormatterID` varchar(32) NOT NULL DEFAULT 'neasden',
  `OriginalAlias` varchar(64) NOT NULL DEFAULT '',
  `Uploads` mediumtext DEFAULT NULL,
  `IsPublished` tinyint(1) NOT NULL DEFAULT 0,
  `IsCommentable` tinyint(1) NOT NULL DEFAULT 0,
  `IsVisible` tinyint(1) NOT NULL DEFAULT 1,
  `IsFavourite` tinyint(1) NOT NULL DEFAULT 0,
  `Stamp` int(10) unsigned NOT NULL DEFAULT 0,
  `LastModified` int(10) unsigned NOT NULL DEFAULT 0,
  `Offset` int(11) NOT NULL DEFAULT 0,
  `IsDST` tinyint(1) NOT NULL DEFAULT 0,
  `IsIndexed` tinyint(1) NOT NULL DEFAULT 0,
  `IsExternal` tinyint(1) NOT NULL DEFAULT 0,
  `ReadCount` int(10) unsigned NOT NULL DEFAULT 0,
  `SourceID` int(10) unsigned NOT NULL DEFAULT 0,
  `SourceNoteID` int(10) unsigned NOT NULL DEFAULT 0,
  `SourceNoteURL` varchar(255) NOT NULL DEFAULT '',
  `SourceNoteJSONURL` varchar(255) NOT NULL DEFAULT '',
  `SourceNoteData` mediumtext DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `SubsetID` (`SubsetID`),
  KEY `Stamp` (`Stamp`),
  KEY `SourceID` (`SourceID`),
  KEY `SourceNoteID` (`SourceNoteID`),
  FULLTEXT KEY `TitleText` (`Title`,`Text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `e2BlogNotesKeywords` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `NoteID` int(10) unsigned NOT NULL DEFAULT 0,
  `KeywordID` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `SubsetID` (`SubsetID`),
  KEY `NoteID` (`NoteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `e2BlogSources` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubsetID` int(10) unsigned NOT NULL DEFAULT 1,
  `TrueID` int(10) unsigned NOT NULL DEFAULT 0,
  `Title` varchar(255) NOT NULL DEFAULT '',
  `AuthorName` varchar(255) NOT NULL DEFAULT '',
  `URL` varchar(255) NOT NULL DEFAULT '',
  `PictureURL` varchar(255) NOT NULL DEFAULT '',
  `IsWhiteListed` tinyint(1) NOT NULL DEFAULT 0,
  `IsTrusted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `SubsetID` (`SubsetID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
