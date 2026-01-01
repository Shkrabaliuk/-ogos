INSERT INTO `e2BlogNotes` (`SubsetID`, `Title`, `Text`, `FormatterID`, `OriginalAlias`, `Uploads`, `IsPublished`, `Stamp`, `LastModified`, `Offset`, `IsDST`) VALUES (1, 'Гнучкість контентної платформи', 'Сучасний блог повинен легко адаптуватися до змін. Можливість швидко редагувати записи, змінювати структуру сторінок або додавати нові розділи є важливою перевагою будь-якої контентної платформи.\r\n\r\nТестові публікації допомагають оцінити, наскільки зручно працювати з системою керування та чи відповідає вона поставленим вимогам до простоти й надійності.', 'neasden', 'gnuchkist-kontentnoyi-platformi', '', '0', '1766935552', '1766935552', '7200', '0');

DELETE FROM `e2BlogNotesKeywords` WHERE `SubsetID`=1 AND `NoteID`='1';

INSERT INTO `e2BlogKeywords` (`SubsetID`, `Keyword`, `OriginalAlias`, `Description`, `IsVisible`) VALUES (1, 'тег1', 'teg1', '', '1');

INSERT INTO `e2BlogAliases` (`SubsetID`, `EntityType`, `EntityID`, `Alias`, `Stamp`) VALUES (1, 't', '1', 'teg1', '1766935552');

INSERT INTO `e2BlogNotesKeywords` (`SubsetID`, `NoteID`, `KeywordID`) VALUES (1, 1, 1);

INSERT INTO `e2BlogKeywords` (`SubsetID`, `Keyword`, `OriginalAlias`, `Description`, `IsVisible`) VALUES (1, 'тег2', 'teg2', '', '1');

INSERT INTO `e2BlogAliases` (`SubsetID`, `EntityType`, `EntityID`, `Alias`, `Stamp`) VALUES (1, 't', '2', 'teg2', '1766935553');

INSERT INTO `e2BlogNotesKeywords` (`SubsetID`, `NoteID`, `KeywordID`) VALUES (1, 1, 2);

UPDATE `e2BlogNotes` SET `SubsetID`='1', `Title`='Гнучкість контентної платформи', `Text`='Сучасний блог повинен легко адаптуватися до змін. Можливість швидко редагувати записи, змінювати структуру сторінок або додавати нові розділи є важливою перевагою будь-якої контентної платформи.\r\n\r\nТестові публікації допомагають оцінити, наскільки зручно працювати з системою керування та чи відповідає вона поставленим вимогам до простоти й надійності.', `Summary`='', `FormatterID`='neasden', `OriginalAlias`='gnuchkist-kontentnoyi-platformi', `Uploads`='', `IsPublished`='1', `IsCommentable`='0', `IsVisible`='1', `IsFavourite`='0', `Stamp`='1766935554', `LastModified`='1766935552', `Offset`='7200', `IsDST`='0', `IsIndexed`='1', `IsExternal`='0', `ReadCount`='0', `SourceID`='0', `SourceNoteID`='0', `SourceNoteURL`='', `SourceNoteJSONURL`='', `SourceNoteData`='' WHERE `SubsetID`=1 AND (`ID`=1);

INSERT INTO `e2BlogAliases` (`SubsetID`, `EntityType`, `EntityID`, `Alias`, `Stamp`) VALUES (1, 'n', '1', 'gnuchkist-kontentnoyi-platformi', '1766935554');

UPDATE `e2BlogNotes` SET `IsCommentable`='1' WHERE `SubsetID`=1 AND (`ID`=1);

UPDATE LOW_PRIORITY `e2BlogNotes` SET `ReadCount` = `ReadCount` + 1 WHERE `ID` = 1;

INSERT LOW_PRIORITY INTO `e2BlogActions` (`SubsetID`, `EntityID`, `Stamp`, `ReadCount`) VALUES (1, '1', '1767258000', '1') ON DUPLICATE KEY UPDATE `ReadCount` = `ReadCount` + 1;

DELETE LOW_PRIORITY FROM `e2BlogActions` WHERE (`Stamp` < 1764668385);

INSERT INTO `e2BlogComments` (`SubsetID`, `NoteID`, `AuthorName`, `AuthorEmail`, `Text`, `Reply`, `IsVisible`, `IsAnswerAware`, `IsSubscriber`, `IsSpamSuspect`, `IsNew`, `Stamp`, `LastModified`, `IP`, `IsGIPUsed`, `GIP`, `GIPAuthorID`) VALUES (1, '1', 'Анатолій', '21@mail.ua', 'Тестовий коментар', '', '1', '1', '0', '0', '1', '1767260455', '1767260455', '91.236.251.222', '0', '', '');

UPDATE `e2BlogComments` SET `IsNew`='0' WHERE `SubsetID`=1 AND (`NoteID`='1');

UPDATE `e2BlogComments` SET `Reply`='Відповідь адміна на коментар', `ReplyStamp`='1767260514', `ReplyLastModified`='1767260514', `IsReplyVisible`='1' WHERE `SubsetID`=1 AND `ID`=1;

COMMIT;

UPDATE `e2BlogNotes` SET `SubsetID`='1', `Title`='Гнучкість контентної платформи', `Text`='Сучасний блог повинен легко адаптуватися до змін. Можливість швидко редагувати записи, змінювати структуру сторінок або додавати нові розділи є важливою перевагою будь-якої контентної платформи.\r\n\r\nТестові публікації допомагають оцінити, наскільки зручно працювати з системою керування та чи відповідає вона поставленим вимогам до простоти й надійності.', `Summary`='', `FormatterID`='neasden', `OriginalAlias`='gnuchkist-kontentnoyi-platformi', `Uploads`='a:0:{}', `IsPublished`='1', `IsCommentable`='1', `IsVisible`='1', `IsFavourite`='0', `Stamp`='1766935554', `LastModified`='1766935552', `Offset`='7200', `IsDST`='0', `IsIndexed`='1', `IsExternal`='0', `ReadCount`='1', `SourceID`='0', `SourceNoteID`='0', `SourceNoteURL`='', `SourceNoteJSONURL`='', `SourceNoteData`='' WHERE `SubsetID`=1 AND (`ID`=1);

DELETE FROM `e2BlogNotes` WHERE `SubsetID`=1 AND `ID` = '1';

DELETE FROM `e2BlogAliases` WHERE `SubsetID`=1 AND `EntityType` = 'n' AND `EntityID`=1;

DELETE FROM `e2BlogNotesKeywords` WHERE `SubsetID`=1 AND `NoteID`=1;

INSERT INTO `e2BlogNotes` (`SubsetID`, `Title`, `Text`, `FormatterID`, `OriginalAlias`, `Uploads`, `IsPublished`, `Stamp`, `LastModified`, `Offset`, `IsDST`) VALUES (1, 'Пеший тестовий пост', 'Текст першого поста', 'neasden', 'peshiy-testoviy-post', '', '0', '1767271096', '1767271096', '7200', '0');

DELETE FROM `e2BlogNotesKeywords` WHERE `SubsetID`=1 AND `NoteID`='2';

INSERT INTO `e2BlogKeywords` (`SubsetID`, `Keyword`, `OriginalAlias`, `Description`, `IsVisible`) VALUES (1, 'тег', 'teg', '', '1');

INSERT INTO `e2BlogAliases` (`SubsetID`, `EntityType`, `EntityID`, `Alias`, `Stamp`) VALUES (1, 't', '3', 'teg', '1767271096');

INSERT INTO `e2BlogNotesKeywords` (`SubsetID`, `NoteID`, `KeywordID`) VALUES (1, 2, 3);

UPDATE `e2BlogNotes` SET `SubsetID`='1', `Title`='Пеший тестовий пост', `Text`='Текст першого поста', `Summary`='', `FormatterID`='neasden', `OriginalAlias`='peshiy-testoviy-post', `Uploads`='', `IsPublished`='1', `IsCommentable`='0', `IsVisible`='1', `IsFavourite`='0', `Stamp`='1767271097', `LastModified`='1767271096', `Offset`='7200', `IsDST`='0', `IsIndexed`='1', `IsExternal`='0', `ReadCount`='0', `SourceID`='0', `SourceNoteID`='0', `SourceNoteURL`='', `SourceNoteJSONURL`='', `SourceNoteData`='' WHERE `SubsetID`=1 AND (`ID`=2);

INSERT INTO `e2BlogAliases` (`SubsetID`, `EntityType`, `EntityID`, `Alias`, `Stamp`) VALUES (1, 'n', '2', 'peshiy-testoviy-post', '1767271097');

UPDATE `e2BlogNotes` SET `IsCommentable`='1' WHERE `SubsetID`=1 AND (`ID`=2);

UPDATE LOW_PRIORITY `e2BlogNotes` SET `ReadCount` = `ReadCount` + 1 WHERE `ID` = 2;

INSERT LOW_PRIORITY INTO `e2BlogActions` (`SubsetID`, `EntityID`, `Stamp`, `ReadCount`) VALUES (1, '2', '1767268800', '1') ON DUPLICATE KEY UPDATE `ReadCount` = `ReadCount` + 1;

DELETE LOW_PRIORITY FROM `e2BlogActions` WHERE (`Stamp` < 1764679098);

