<?php

$in_filename = "ISO-3166.csv";

echo <<<SQL
<?php
\$database = R::\$adapter;

\$database->exec("
CREATE TABLE `iso_3166_2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`country_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_short_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_long_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_number_code` smallint unsigned unsigned DEFAULT NULL,
  `region_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region_name_ascii` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regional_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regional_number_code` smallint unsigned unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
	UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
");


SQL;

$types = array();
$codes = array();
if(($in = fopen($in_filename, "r")) !== false) {

	for($line = 0; ($d = fgetcsv($in, 0, ";")) !== false; $line++) {
		if($line === 0) {
			continue;
		}

		$code = "{$d[1]}-{$d[7]}";
		if(isset($codes[$code])) {
			echo "//DUPLICATE FOUND - $code\n";
		}
		$codes[$code] = true;
		
		echo "echo \"$code\\n\";\n";
		echo <<<SQL
\$database->exec("INSERT INTO iso_3166_2 (`code`, `country_name`, `country_short_code`, `country_long_code`, `country_number_code`, `region_name`, `region_name_ascii`, `region_type`, `regional_code`, `regional_number_code`) VALUES (
\"{$code}\",
\"{$d[0]}\",
\"{$d[1]}\",
\"{$d[2]}\",
{$d[3]},
\"{$d[4]}\",
\"{$d[5]}\",
\"{$d[6]}\",
\"{$d[7]}\",
{$d[8]}
) ");


SQL;

	}
}

fclose($in);
