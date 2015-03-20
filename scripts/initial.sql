CREATE TABLE mail (
	'id' INTEGER,
	'username' TEXT(255),
	'to' TEXT(255),
	'from' TEXT(255),
	'received' INTEGER,
	'date' TEXT(50),
	'subject' TEXT(512),
	'body_text' TEXT(2000000000),
	'body_html' TEXT(2000000000),
	'delivered_to' TEXT(255),
	'raw' TEXT(2000000000),
	CONSTRAINT MAIL_PK PRIMARY KEY (id)
);

CREATE TABLE account (
	id INTEGER,
	username TEXT(255),
	created INTEGER,
	blocked BOOLEAN,
	CONSTRAINT ACCOUNT_PK PRIMARY KEY (id)
);
