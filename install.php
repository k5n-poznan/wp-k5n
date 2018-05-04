<?php

global $table_prefix;

$create_k5n_subscribes = ( "CREATE TABLE IF NOT EXISTS {$table_prefix}k5n_subscribes(
	ID int(10) NOT NULL auto_increment,
	date DATETIME,
	name VARCHAR(20),
	surname VARCHAR(20),
	mobile VARCHAR(20) NOT NULL,
	status tinyint(1),
	bulletin tinyint(1),
	activate_key INT(11),
	group_ID int(5),
	PRIMARY KEY(ID)) CHARSET=utf8
" );

$create_k5n_subscribes_group = ( "CREATE TABLE IF NOT EXISTS {$table_prefix}k5n_subscribes_group(
	ID int(10) NOT NULL auto_increment,
	name VARCHAR(250),
	PRIMARY KEY(ID)) CHARSET=utf8
" );

$create_k5n_outbox = ( "CREATE TABLE IF NOT EXISTS {$table_prefix}k5n_sms_outbox(
	ID int(10) NOT NULL auto_increment,
	date DATETIME,
        send DATETIME,
	sender VARCHAR(20) NOT NULL,
	message TEXT NOT NULL,
	recipient TEXT NOT NULL,
        flash tinyint(1),
	status tinyint(1),
	PRIMARY KEY(ID)) CHARSET=utf8
" );
