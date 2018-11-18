SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE DATABASE url_shortener;
USE url_shortener;

CREATE TABLE IF NOT EXISTS shortened_urls (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  sid varchar(8) NOT NULL,
  url varchar(150) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS statistics (
  sid varchar(8) NOT NULL,
  created_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  last_accessed_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  number_of_visits int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY sid (sid)
) ENGINE=MyISAM;