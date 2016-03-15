CREATE TABLE IF NOT EXISTS parsed_links (
	id int(11) unsigned NOT NULL auto_increment,
	link varchar(300) unique,
	feeds text,
	date_added datetime default now(),
	date_updated timestamp,
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS exported_links (
	id int(11) unsigned NOT NULL auto_increment,
	runtime long,
	link varchar(300),
	date_added timestamp default current_timestamp,
	PRIMARY KEY (id)
);

