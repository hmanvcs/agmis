-- clean up; update fuel prices with invalid location in kayunga
update commoditypricedetails set sourceid = 125 where id in('1464441','1464442','1464443');
DELETE FROM `commoditypricesubmission` where sourceid IN('165','166','420','421');
DELETE FROM `commoditypricedetails` where sourceid IN('165','166','420','421');

SET foreign_key_checks = 0;

-- delete and insert price source
DELETE from `pricesource` where id in('165','166');
INSERT INTO `pricesource` (`id`, `name`, `contactperson`, `phone`, `email`, `address`, `notes`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`, `locationid`, `applicationtype`) VALUES
(165,	'Isingiro Market',	'Deus',	'0779799308',	'',	'Isingiro',	'',	'2013-04-09 00:00:00',	1,	'2011-05-19 17:37:07',	1,	29,	0),
(166,	'Kayunga Market',	'Christine', '0782803754',	'',	'Kayunga',	'',	'2013-04-09 00:00:00',	1,	'2011-05-19 17:37:07',	1,	42,	0);

DELETE from `pricesourcecategory` where id in('190','191');
INSERT INTO `pricesourcecategory` (`id`, `pricesourceid`, `pricecategoryid`) VALUES
(190,165,2), (191,166,2);

-- delete and insert the users
DELETE from `useraccount` where id in('420','421');
INSERT INTO `useraccount` (`id`, `firstname`, `lastname`, `othername`, `email`, `phonenumber`, `phonenumber2`, `password`, `activationkey`, `notes`, `securityquestion`, `securityanswer`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`, `isactive`, `loginretries`, `nextpasswordchangedate`, `lastlogindate`, `changepassword`, `locationid`, `marketid`, `applicationtype`, `organisationid`, `activationdate`, `agreedtoterms`) VALUES
(420,	'Abaasa',	'Deus',	'',	'test1@devmail.infomacorp.com',	'0779799308',	NULL,	'5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',	NULL,	NULL,	'',	'',	'2011-04-06 10:38:10',	1,	'2011-05-27 12:01:19',	1,	'Y',	NULL,	NULL,	NULL,	'Y',	29,	165,	0,	NULL,	NULL,	1),
(421,	'Namutebi',	'Christine',	'',	'test2@devmail.infomacorp.com',	'0782803754',	NULL,	'5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',	NULL,	NULL,	'',	'',	'2011-04-06 10:38:10',	1,	'2011-05-27 12:01:19',	1,	'Y',	NULL,	NULL,	NULL,	'Y',	42,	166,	0,	NULL,	NULL,	1);

DELETE from `aclusergroup` where userid in('420','421');
INSERT INTO `aclusergroup` (`id`, `groupid`, `userid`) VALUES
(400,	3,	420),
(401,	3,	421);

DROP TABLE IF EXISTS `commoditypricesubmissionx`;
CREATE TABLE `commoditypricesubmissionx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datecollected` date NOT NULL,
  `collectedbyid` int(11) unsigned NOT NULL,
  `sourceid` int(11) unsigned NOT NULL,
  `comments` blob NOT NULL,
  `status` enum('Saved','Approved','Rejected') NOT NULL DEFAULT 'Saved',
  `dateapproved` date DEFAULT NULL,
  `approvedbyid` int(11) unsigned DEFAULT NULL,
  `datecreated` datetime NOT NULL,
  `createdby` int(11) unsigned NOT NULL,
  `lastupdatedate` datetime DEFAULT NULL,
  `lastupdatedby` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_submissionx` (`datecollected`,`collectedbyid`,`sourceid`),
  KEY `fk_pricesubmission_createdbyx` (`createdby`),
  KEY `fk_pricesubmission_lastupdatedbyx` (`lastupdatedby`),
  KEY `fk_pricesubmission_approvedbyidx` (`approvedbyid`),
  KEY `fk_pricesubmission_sourceidx` (`sourceid`),
  KEY `fk_pricesubmission_datecollectedx` (`datecollected`),
  KEY `fk_pricesubmission_collectedbyidx` (`collectedbyid`),
  KEY `index_pricesubmission_statusx` (`status`),
  CONSTRAINT `fk_pricesubmission_approvedbyidx` FOREIGN KEY (`approvedbyid`) REFERENCES `useraccount` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pricesubmission_collectedbyidx` FOREIGN KEY (`collectedbyid`) REFERENCES `useraccount` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pricesubmission_createdbyx` FOREIGN KEY (`createdby`) REFERENCES `useraccount` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pricesubmission_lastupdatedbyx` FOREIGN KEY (`lastupdatedby`) REFERENCES `useraccount` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pricesubmission_sourceidx` FOREIGN KEY (`sourceid`) REFERENCES `pricesource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33000 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `commoditypricedetailsx`;
CREATE TABLE `commoditypricedetailsx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `commodityid` int(11) unsigned NOT NULL,
  `pricecategoryid` int(11) unsigned NOT NULL,
  `retailprice` int(11) unsigned DEFAULT NULL,
  `wholesaleprice` int(11) unsigned DEFAULT NULL,
  `submissionid` int(11) unsigned NOT NULL,
  `notes` varchar(1000) NOT NULL DEFAULT '',
  `datecollected` date DEFAULT NULL,
  `sourceid` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_commoditypricedetails_datecollectedx` (`datecollected`),
  KEY `fk_commoditypricedetails_commodityx` (`commodityid`),
  KEY `fk_commoditypricedetails_pricecategoryx` (`pricecategoryid`),
  KEY `fk_commoditypricedetails_submissionidx` (`submissionid`),
  CONSTRAINT `fk_commoditypricedetails_commodityx` FOREIGN KEY (`commodityid`) REFERENCES `commodity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_commoditypricedetails_pricecategoryx` FOREIGN KEY (`pricecategoryid`) REFERENCES `pricecategory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_commoditypricedetails_submissionidx` FOREIGN KEY (`submissionid`) REFERENCES `commoditypricesubmissionx` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1563005 DEFAULT CHARSET=utf8;

-- mysqldump -t -udev -pdev agmis commoditypricesubmission --where="sourceid = 131 and datecollected >=  '2013-03-01'" 

INSERT INTO `commoditypricesubmissionx` (id, `datecollected`, `collectedbyid`, `sourceid`, `comments`, `status`, `dateapproved`, `approvedbyid`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`) 
SELECT id, `datecollected`, 420, 165, `comments`, `status`, `dateapproved`, `approvedbyid`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`from commoditypricesubmission WHERE (sourceid = 131);	

INSERT INTO `commoditypricesubmissionx` (id, `datecollected`, `collectedbyid`, `sourceid`, `comments`, `status`, `dateapproved`, `approvedbyid`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`) 
SELECT id, `datecollected`, 421, 166, `comments`, `status`, `dateapproved`, `approvedbyid`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`from commoditypricesubmission WHERE (sourceid = 130);	

INSERT INTO `commoditypricedetailsx` (`id`, `commodityid`, `pricecategoryid`, `retailprice`, `wholesaleprice`, `submissionid`, `notes`, `datecollected`, `sourceid`) 
SELECT `id`, `commodityid`, `pricecategoryid`, `retailprice`, `wholesaleprice`, `submissionid`, `notes`, `datecollected`, 165 from commoditypricedetails WHERE (sourceid = 131);
INSERT INTO `commoditypricedetailsx` (`id`, `commodityid`, `pricecategoryid`, `retailprice`, `wholesaleprice`, `submissionid`, `notes`, `datecollected`, `sourceid`) 
SELECT `id`, `commodityid`, `pricecategoryid`, `retailprice`, `wholesaleprice`, `submissionid`, `notes`, `datecollected`, 166 from commoditypricedetails WHERE (sourceid = 130);	

-- increase ids of new submissions to be added
update commoditypricesubmissionx set id = id + 30000;
update commoditypricedetailsx set id = id + 2000000;

INSERT INTO `commoditypricesubmission` (id, `datecollected`, `collectedbyid`, `sourceid`, `comments`, `status`, `dateapproved`, `approvedbyid`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`) 
SELECT id, `datecollected`, `collectedbyid`, `sourceid`, `comments`, `status`, `dateapproved`, `approvedbyid`, `datecreated`, `createdby`, `lastupdatedate`, `lastupdatedby`from commoditypricesubmissionx;	

INSERT INTO `commoditypricedetails` (`id`, `commodityid`, `pricecategoryid`, `retailprice`, `wholesaleprice`, `submissionid`, `notes`, `datecollected`, `sourceid`) 
SELECT `id`, `commodityid`, `pricecategoryid`, `retailprice`, `wholesaleprice`, `submissionid`, `notes`, `datecollected`, sourceid from commoditypricedetailsx;

SET foreign_key_checks = 1;

DROP TABLE IF EXISTS `commoditypricedetailsx`;
DROP TABLE IF EXISTS `commoditypricesubmissionx`;

UPDATE commoditypricesubmission set collectedbyid = 420, createdby = 420 where sourceid = 165;
UPDATE commoditypricesubmission set collectedbyid = 421, createdby = 421 where sourceid = 166;

UPDATE commoditypricesubmission set collectedbyid = 420, createdby = 420 where (collectedbyid = 68 AND sourceid NOT IN('130'));
UPDATE commoditypricesubmission set collectedbyid = 421, createdby = 421 where (collectedbyid = 69 AND sourceid NOT IN('131'));

DELETE FROM commoditypricedetails where sourceid IN (130,131);
DELETE FROM commoditypricesubmission where sourceid IN (130,131);

UPDATE offer set createdby = 420 where createdby = 68;
UPDATE offer set createdby = 421 where createdby = 69;

DELETE FROM pricesource where id IN(130,131);
DELETE FROM useraccount where id IN(68,69);

UPDATE useraccount set email = 'namutebi_christine@yahoo.com' where id = 421;
UPDATE useraccount set email = 'deusabaasa@gmail.com' where id = 420;
