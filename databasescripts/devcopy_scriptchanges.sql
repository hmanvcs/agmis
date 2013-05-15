-- Reset all email addresses to development addresses and the password to the word password
-- UPDATE useraccount set email =  INSERT(email,LOCATE('@', email)+1, 15,'devmail.infomacorp.com'), password = sha1('password');
UPDATE useraccount set email =  concat(id,'@devmail.infomacorp.com'), password = sha1('password');

UPDATE useraccount set email = 'admin@devmail.infomacorp.com' where id = '1';
UPDATE useraccount set email = 'super@devmail.infomacorp.com' where id = '2';
UPDATE useraccount set email = 'manager@devmail.infomacorp.com' where id = '4';
UPDATE useraccount set email = 'owino@veritracker.com' where id = '7';
UPDATE useraccount set email = 'gulu@devmail.infomacorp.com' where id = '22';
UPDATE useraccount set email = 'kasese@devmail.infomacorp.com' where id = '17';

UPDATE appconfig set optionvalue = 'notifications@devmail.infomacorp.com' where optionname = 'emailmessagesender';
UPDATE appconfig set optionvalue = 'admin@devmail.infomacorp.com' where optionname = 'coachadminemail';
