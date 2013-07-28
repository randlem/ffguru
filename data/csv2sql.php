<?php

$inFile  = '2013_FantasyFootball_v7_4.csv';
$outFile = 'import.sql';

$in  = fopen($inFile, 'r');
$out = fopen($outFile, 'w');
$num = 0;

fgetcsv($in, 1000, ","); // skip first line
while (($row = fgetcsv($in, 1000, ",")) !== FALSE) {


	$players[] = sprintf('(%d,"%s","%s",%d,"%s")',
		$num, addslashes($row[0]), $row[1],	$row[2], $row[3]
	);

	$pricing[] = sprintf("(%d, %d, 0, 0, %d)",
		$num, intval(substr($row[4],1)), intval(substr($row[7],1))
	);

	$num++;
}

foreach (array_chunk($players, 100) as $chunk) {
	fwrite($out,
		'INSERT INTO players VALUES '. PHP_EOL.
		implode(','. PHP_EOL, $chunk). ';'. PHP_EOL
	);
}
fwrite($out, PHP_EOL);

foreach (array_chunk($pricing, 100) as $chunk) {
	fwrite($out,
		'INSERT INTO pricing VALUES '. PHP_EOL.
		implode(','. PHP_EOL, $chunk). ';'. PHP_EOL
	);
}
