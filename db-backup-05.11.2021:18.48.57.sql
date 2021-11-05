DROP TABLE IF EXISTS domain;

CREATE TABLE `domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fqdn` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS record;

CREATE TABLE `record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('NS','A','AAAA','CNAME','MX','PTR','SOA','TXT','AXFR','SRV','CAA','none') DEFAULT 'A',
  `domain` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `val` text,
  `ttl` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `record_domain_id_fk` (`domain`),
  CONSTRAINT `record_domain_id_fk` FOREIGN KEY (`domain`) REFERENCES `domain` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




