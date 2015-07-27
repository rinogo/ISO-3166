<?php

$in_filename = $argv[1];
$out_filename = $in_filename . ".new";

$types = array();
if(($in = fopen($in_filename, "r")) !== false) {
	$out = fopen($out_filename, "w");

	for($line = 0; ($data = fgetcsv($in, 0, ";")) !== false; $line++) {
		if($line === 0) {
			fputcsv($out, array("COUNTRY NAME", "COUNTRY SHORT CODE", "COUNTRY LONG CODE", "COUNTRY NUMBER CODE", "REGION NAME", "REGION NAME (ASCII)", "REGION TYPE", "REGIONAL CODE", "REGIONAL NUMBER CODE"), ";");
			continue;
		}

		if(sizeof($data) !== 8) {
			var_dump($data);
			die("Invalid number of fields detected.");
		}

		$data_out = $data;
		
		//Change all brackets to parens
		$data_out[4] = str_replace("[", "(", $data_out[4]);
		$data_out[4] = str_replace("]", ")", $data_out[4]);
		
		$arg = escapeshellarg($data_out[4]);
		array_splice($data_out, 5, 0, `php /Users/rich/github/urlify/scripts/transliterate.php $arg`); //Add new REGION NAME (ASCII) column
		preg_match("/[A-Za-z0-9\- '\.,\(\)\/]*/", $data_out[5], $match);
		if(strlen($match[0]) !== strlen($data_out[5])) {
			echo $data_out[5] . "\n";
			var_dump($match);
		}

		$data_out[6] = strtolower($data[5]); //Ensure case consistency in REGION TYPE
		
		//Transform the region type (Misspellings, extraneous characters, unnecessary pluralization, etc.)
		switch($data_out[6]) {
			case "administrative regions": $data_out[6] = "administrative region"; break;
			case "council area (scotland)": $data_out[6] = "council area"; break;
			case "district council area (northern ireland)": $data_out[6] = "district council area"; break;
			case "development regions": $data_out[6] = "development region"; break;
			case "geographic regions": $data_out[6] = "geographic region"; break;
			case "geographic units": $data_out[6] = "geographic unit"; break;
			case "geographical entities": $data_out[6] = "geographical entity"; break;
			case "governorates": $data_out[6] = "governorate"; break;
			case "metropolitan regions": $data_out[6] = "metropolitan region"; break;
			case "overseas regions": $data_out[6] = "overseas region"; break;
			case "perfecture": $data_out[6] = "prefecture"; break;
			case "rerion": $data_out[6] = "region"; break;
			case "special zone.": $data_out[6] = "special zone"; break;
			case "states": $data_out[6] = "state"; break;
			case "unitary authority (wales)": $data_out[6] = "unitary authority"; break;
			case "urban perfecture": $data_out[6] = "urban prefecture"; break;
			case "voivodship": $data_out[6] = "voivodeship"; break;
		}
		
		// if(!isset($types[$data_out[6]])) {
		// 	echo $data_out[6] . "\n";
		// 	$types[$data_out[6]] = true;
		// }
		
		fputcsv($out, $data_out, ";");
	}
}

fclose($in);
fclose($out);

//Move the original input file to a backup location (name), and the output file to the original location (name)
$cmd = "mv {$in_filename} {$in_filename}.bak; mv {$out_filename} {$in_filename}";
echo $cmd . "\n";
exec($cmd);
