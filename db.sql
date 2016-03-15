CREATE TABLE IF NOT EXISTS parsed_links (
	id int(11) unsigned NOT NULL auto_increment,
	link varchar(300),
	date_added datetime default now(),
	date_updated timestamp,
	PRIMARY KEY (id)
);

