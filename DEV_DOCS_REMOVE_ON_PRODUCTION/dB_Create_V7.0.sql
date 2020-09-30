CREATE DATABASE `hootus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

use `hootus`;
-- DROP TABLE IF EXISTS `hootus`.`rootcause`;
CREATE TABLE IF NOT EXISTS `rootcause` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`rootCause` varchar(50) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DROP TABLE IF EXISTS `hootus`.`rootcause`;
INSERT INTO `hootus`.`rootcause` (rootCause)
VALUES 
('False'),
('Electrical'),
('Non-Electrical'),
('Personnel'),
('Others');

-- DROP TABLE IF EXISTS `hootus`.`apptimers`;
CREATE TABLE `hootus`.`apptimers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `popupTimer` INT(11) NOT NULL DEFAULT 60, -- SECONDs
  `healthTimer` INT(11) NOT NULL DEFAULT 6, -- SECONDs
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hootus`.`apptimers` (popupTimer, healthTimer)
VALUES (60,6);


-- DROP TABLE IF EXISTS `hootus`.`usertype`;
CREATE TABLE IF NOT EXISTS `usertype` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`userType` varchar(50) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hootus`.`usertype` (`userType`) VALUES
('Employee'),('Contractor'),('Guest'),('Supplier');

CREATE TABLE IF NOT EXISTS `title` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`title` varchar(50) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO title (`title`) VALUES
('Engineer'),('Sr.Engineer'),('Manager'),('Director'),('Admin'),('SuperAdmin'),('QA');

-- ROLES employee, contractor, guest
-- engineer, manager, director, admin, superadmin

-- http://iknowkungfoo.com/blog/index.cfm/2008/6/1/Please-stop-using-SELECT-MAX-id
-- employeeID creation can be automated using trigger and LAST_INSERT_ID() function of MySQL
-- similarly userID can be automated taking 1-2 chars from firstName and mixing it with lastName; if same firstName and lastName, can add some sequenct to it rsrinivasan1, rsrinivasan2 like

-- DROP TABLE IF EXISTS `hootus`.`role`;
-- Engineer, Manager, Director, Admin, SuperAdmin, Guest, Supplier
CREATE TABLE IF NOT EXISTS `role` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`role` varchar(50) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `uniq_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO role (`role`) VALUES
('ERT'),('Maintenance'),('Admin'),('SuperAdmin');


-- DROP TABLE IF EXISTS `hootus`.`entity`;
/*
CREATE TABLE IF NOT EXISTS `entity` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`entity` varchar(50) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `uniq_entity` (`entity`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO entity (`entity`) VALUES
('User'),('Zone'),('Location'),('Contact'),('Task'),('Update');
*/

-- http://sforsuresh.in/implemention-of-user-permission-with-php-mysql-bitwise-operators/
-- DROP TABLE IF EXISTS `hootus`.`permission`;
CREATE TABLE IF NOT EXISTS `permission` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`entity` VARCHAR(32) NOT NULL,
`permission` VARCHAR(48) NOT NULL,
`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
`updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
UNIQUE KEY `uniq_permission` (`entity`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Superadmin can do anything
-- only Superadmin can see Admin link on Alarms page; control set in alarms.js
INSERT INTO `permission` (`entity`, `permission`) VALUES
('Users','C'),
('Users','R'),
('Users','U'),
('Users','D'),
('Zones','C'),
('Zones','R'),
('Zones','U'),
('Zones','D'),
('Locations','C'),
('Locations','R'),
('Locations','U'),
('Locations','D'),
('Contacts','C'),
('Contacts','R'),
('Contacts','U'),
('Contacts','D'),
('ToDo','C'),
('ToDo','R'),
('ToDo','U'),
('ToDo','D'),
('Update Cause','C'),
('Update Cause','R'),
('Update Cause','U'),
('Update Cause','D');

-- DROP TABLE IF EXISTS `hootus`.`rolepermission`;
-- NO AUTO INCREMENT FOR THIS TABLE;  because the bit is the primary key which is multiple of 2 except 1
CREATE TABLE IF NOT EXISTS `rolepermission` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`role_id` INT(11) NOT NULL,
`permission_id` INT(11) NOT NULL,
`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
`updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
UNIQUE KEY `uniq_role_permission` (`role_id`,`permission_id`),
CONSTRAINT `rolePK` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
CONSTRAINT `permissionPK` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ERT role can Read alarms data, update data 
-- Maintenance role can Read alarms data, READ and UPDATE causes, zone, locations (but not users) -- can not Create any data
-- Manager, Director, Admin, SuperAdmin can READ, UPDATE, CREATE zone,locations, 
-- Admin and SuperAdmin can in addition, CREATE, UPDATE users data 
-- SuperAdmin only can set timers (it is not part of this rolepermission but app is handling that in alarms.js; admin icon is visible only to SuperAdmin login)
-- SuperAdmin (yet to implement;) can add permissions to Roles


-- DROP TABLE IF EXISTS zone;
-- zone is to group locations; we need to decide in same location two panels are there to use zone or locationname to differentiate them
CREATE TABLE `hootus`.`zone` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `zone` VARCHAR(48)  COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL,
  `status` TINYINT(4) DEFAULT 1,
  `comment` TEXT  COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_zone` (`zone`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DROP TABLE IF EXISTS location;
CREATE TABLE `hootus`.`location` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `zone_id` INT(11) NOT NULL,
  `locationID` INT(11) UNIQUE NOT NULL,
  `panelID` INT(11) UNIQUE DEFAULT 0,
  `locationName` VARCHAR(191)  COLLATE utf8mb4_unicode_ci DEFAULT '',
  -- `contactOut` DATETIME DEFAULT CURRENT_TIMESTAMP, -- if contactOut date is present he/she is no more current contact; this shd be in history table
  `comment` TEXT  COLLATE utf8mb4_unicode_ci ,
  `status` TINYINT(4) DEFAULT 1, 
  -- status is for active or inactive location; 1 - active device in location; 0 - location device down; -1 - location set inactive by admin; don't track inactive locations set by admin for alarms??; 
  -- if inactive set alarm 0 and don't bring in to alarm tables; in location page show different link to edit inactive locations
  -- under maintenance locations if set inactive can avoid any unnecesseary alarms
  `lastStatusUpdate` TIMESTAMP  NULL DEFAULT NULL, -- updated only when status changes and not other fields
  `swver` VARCHAR(15)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastAlarm` TINYINT(4) NOT NULL DEFAULT '0', -- 1 there is active fire alarm; 0 there is no fire alarm
  `lastAlarmTime` TIMESTAMP  NULL DEFAULT NULL,
  `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated` DATETIME ON UPDATE CURRENT_TIMESTAMP, -- any time any field updated, this column is time stamped
  `alertMe` BOOLEAN DEFAULT 0,
  `closeAlertTime` TIMESTAMP  NULL DEFAULT NULL,
 --  `alive` BOOLEAN DEFAULT 1, -- 1 device is alive, 0 device is down; run an event to compare the updated DATETIME field and set alive bit
  PRIMARY KEY (`id`),
  CONSTRAINT `uniq_location` UNIQUE (`zone_id`,`locationID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- https://dba.stackexchange.com/questions/56424/column-auto-updated-after-24-hours-in-mysql
-- https://www.sitepoint.com/how-to-create-mysql-events/

SET GLOBAL event_scheduler = ON;
SET @healthTimerValue = (SELECT healthTimer FROM hootus.apptimers WHERE id=1);

DROP EVENT IF EXISTS statusCheck;
DELIMITER $$
CREATE EVENT statusCheck
    ON SCHEDULE
      EVERY @healthTimerValue SECOND
DO 
BEGIN
SET @timenow = NOW();
UPDATE `hootus`.`location` SET `status` = 0
WHERE `panelID` IS NOT NULL AND `status` != -1 AND `lastStatusUpdate` < DATE_SUB(@timenow, INTERVAL @healthTimerValue SECOND);

UPDATE `hootus`.`location` SET `status` = 1
WHERE `panelID` IS NOT NULL AND `status` != -1 AND `lastStatusUpdate` > DATE_SUB(@timenow, INTERVAL @healthTimerValue SECOND);
END $$
DELIMITER ;


-- DROP TABLE IF EXISTS `hootus`.`contact`;
CREATE TABLE `hootus`.`contact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `firstName` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middleName` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastName` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userID` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` ENUM('Female','Male','Both','NA') DEFAULT 'NA',
  `dob` DATETIME DEFAULT NULL,
  `title` VARCHAR(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` BINARY(60) DEFAULT NULL,
  `primaryContact` VARCHAR(15)  COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL,
  `alternateContact` VARCHAR(15)  COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` TINYINT(4) NOT NULL DEFAULT 1, -- -1 for activer user not yet logged in, 1 for Active user, logged in and changed the password, 0 for Inactive
  -- do we need any login from contacts to this app and their permitted actions ??
  `comment` TEXT  COLLATE utf8mb4_unicode_ci ,
  `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `uniq_primaryContact` UNIQUE (primaryContact)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- have to change uniq constraint to email
-- ALTER TABLE contact MODIFY userID varchar(100) DEFAULT null;
-- ALTER TABLE contact MODIFY gender ENUM('Female','Male','Both', 'Unknown','NA') DEFAULT 'NA';
-- ALTER TABLE contact MODIFY status TINYINT(4) NOT NULL DEFAULT 1;

-- DROP TABLE IF EXISTS `hootus`.`locationcontact`;
CREATE TABLE `hootus`.`locationcontact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `location_id` INT(11) NOT NULL,
  `contact_id` INT(11) NOT NULL,
  `contactChkIn` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `plannedChkOut` DATETIME DEFAULT NULL,
  `actualChkOut` DATETIME DEFAULT NULL,
  `currentContact` BOOLEAN DEFAULT 1, -- whether this row is for the current person staying in the location; when location - contact map changes, currentContact will be set to 0 for old contact_id to maintain history; you shd see only one row with 1 for a given location
  `comment` TEXT  COLLATE utf8mb4_unicode_ci,
  `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `locationPK` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`), -- PK Primary Key
  CONSTRAINT `contactPK` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DROP TABLE IF EXISTS `hootus`.`user`;
CREATE TABLE `hootus`.`user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `employeeID` VARCHAR(30) COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL DEFAULT '',
  `firstName` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middleName` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastName` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userID` VARCHAR(100) COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL,
  `email` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` ENUM('Female','Male','Both', 'Unknown','NA') DEFAULT 'NA',
  `title_id` INT(11) DEFAULT NULL,
  `password` BINARY(60) DEFAULT NULL,
  `primaryContact` VARCHAR(15)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alternateContact` VARCHAR(15)  COLLATE utf8mb4_unicode_ci DEFAULT '',
  `manager_id` INT(11) NOT NULL,
  `usertype_id` INT(11) NOT NULL,
  `role_id` INT(11) NOT NULL,
  `status` TINYINT(4) NOT NULL DEFAULT -1, -- -1 for activer user not yet logged in, 1 for Active user, logged in and changed the password, 0 for Inactive
  `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `title_idPK` FOREIGN KEY (`title_id`) REFERENCES `title` (`id`),
  CONSTRAINT `usertype_idPK` FOREIGN KEY (`usertype_id`) REFERENCES `usertype` (`id`),
  CONSTRAINT `role_idPK` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (employeeID, firstName, middleName, lastName, userID, email, gender, title_id, password, primaryContact, alternateContact, manager_id, usertype_id, role_id, status)
VALUES  ('F1000','GOD','THE','GREAT','god','god@everywhere.com','NA',6,'$2y$10$IxAscckHJ5UfzEB19hfqBOH8lilMJ3fjFkdvq8bCOKSKshL3eAt/G','9999999999','8888888888',1,1,7,1);

INSERT INTO `user` (employeeID, firstName, middleName, lastName, userID, email, gender, title_id, password, primaryContact, alternateContact, manager_id, usertype_id, role_id, status)
VALUES  ('F1001','Admin','S','Super','sadmin','sadmin@test.com','NA',6,'$2y$10$ZAkNOlNpEG3b0GuvbrZuguUMo1gxAwQ44jcH5M/9/p/YU1UywMj7O','9981200121','8892311231',2,1,7,1);

-- DROP TABLE IF EXISTS `hootus`.`alarmhistory`;
CREATE TABLE `hootus`.`alarmhistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `location_id` INT(11) NOT NULL, -- location table primary key
  `alarmON` BOOLEAN DEFAULT 0, -- 1 for active alarm and 0 for no alarm (reset)
  `alarmONTime` TIMESTAMP NULL DEFAULT NULL,
  `alarmOFF` BOOLEAN DEFAULT 0, -- 1 for active alarm and 0 for no alarm (reset)
  `alarmOFFTime` TIMESTAMP NULL DEFAULT NULL,
  `rootcause_id` INT(11) DEFAULT NULL,
  `comment` TEXT  COLLATE utf8mb4_unicode_ci,
  `user_id` INT(11) DEFAULT NULL, -- the person who reset
  `updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `loc_ah_PK` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`), -- PK Primary Key
  CONSTRAINT `user_ah_PK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`), -- PK Primary Key
  CONSTRAINT `rootcause_ah_PK` FOREIGN KEY (`rootcause_id`) REFERENCES `rootcause` (`id`) -- PK Primary Key
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DROP TABLE IF EXISTS `hootus`.`downhistory`;
CREATE TABLE `hootus`.`downhistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `location_id` INT(11) NOT NULL, -- location table primary key
  `lastStatusUpdate` TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
  `rootcause_id` INT(11) DEFAULT NULL,
  `comment` TEXT  COLLATE utf8mb4_unicode_ci,
  `user_id` INT(11) DEFAULT NULL, -- the person who reset
  `updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `loc_dh_PK` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`), -- PK Primary Key
  CONSTRAINT `user_dh_PK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`), -- PK Primary Key
  CONSTRAINT `rootcause_dh_PK` FOREIGN KEY (`rootcause_id`) REFERENCES `rootcause` (`id`) -- PK Primary Key
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET @popupTimerValue = (SELECT popupTimer FROM hootus.apptimers WHERE id=1);

DROP TRIGGER IF EXISTS before_location_update;
DELIMITER $$
CREATE TRIGGER before_location_update 
    BEFORE UPDATE ON location FOR EACH ROW 
BEGIN
	IF (NEW.lastAlarm = 1) THEN
    -- NEW.closeAlertTime > one minute back means, it is within one minute range; 
    -- if less, age is more than a minute and set alertMe=1 again because fire alarm is still active (NEW.lastAlarm=1)
        SET NEW.lastAlarmTime = CURRENT_TIMESTAMP, NEW.alertMe=IF(NEW.closeAlertTime = NULL, 1, 
            IF(NEW.closeAlertTime > DATE_SUB(NOW(),INTERVAL @popupTimerValue SECOND),0,1));
    END IF;
    IF (NEW.lastAlarm = 0) THEN 
        SET NEW.alertMe = 0, NEW.closeAlertTime = NULL;
    END IF;
    IF (NEW.lastAlarm != OLD.lastAlarm) THEN
		IF (NEW.lastAlarm = 1) THEN 
			INSERT INTO alarmhistory
			SET  location_id = OLD.id,
				 alarmON = 1,
                 alarmOnTime = CURRENT_TIMESTAMP;
	    END IF;
        IF (NEW.lastAlarm = 0) THEN 
			UPDATE alarmhistory
			SET  alarmOFF = 1,
                 alarmOFFTime = CURRENT_TIMESTAMP
			WHERE location_id = OLD.id AND alarmON = 1 AND alarmONTime IS NOT NULL AND alarmOFFTime IS NULL;
	    END IF;
	END IF;
   IF (OLD.status = 1 AND NEW.status = 0) THEN
		INSERT INTO downhistory
		SET  location_id = OLD.id;
	END IF;
END$$
DELIMITER ;
         
-- DROP TABLE IF EXISTS `hootus`.`todo`;
CREATE TABLE `hootus`.`todo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `task` TEXT  COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_id` INT(11) DEFAULT 0,
  `owner_id` INT(11) DEFAULT 0,
  `dueDate` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  `status` ENUM('Open','Completed','OnHold') DEFAULT 'Open',
  `comment` TEXT  COLLATE utf8mb4_unicode_ci ,
  PRIMARY KEY (`id`),
  CONSTRAINT `creatorPK` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`)
  -- CONSTRAINT `ownerPK` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


