-- Reset all email addresses to development addresses and the password to the word password
-- UPDATE useraccount set email =  INSERT(email,LOCATE('@', email)+1, 15,'devmail.infomacorp.com'), password = sha1('password');
UPDATE useraccount set email =  concat(id,'@veritracker.com'), password = sha1('password');

UPDATE useraccount set email = 'admin@veritracker.com' where email = '1@veritracker.com';
UPDATE useraccount set email = 'super@veritracker.com' where email = '2@veritracker.com';
UPDATE useraccount set email = 'manager@veritracker.com' where email = '4@veritracker.com';
UPDATE useraccount set email = 'owino@veritracker.com' where email = '7@veritracker.com';
UPDATE useraccount set email = 'gulu@veritracker.com' where email = '22@veritracker.com';
UPDATE useraccount set email = 'kasese@veritracker.com' where email = '17@veritracker.com';

UPDATE appconfig set optionvalue = 'notifications@veritracker.com' where optionname = 'databasebackupemail';
