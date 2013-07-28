<?php
require_once('../vendor/autoload.php');

$db = new SQLite3('../data/ffguru.db');

$app = new \Slim\Slim();

$app->get('/show(/:position)', function ($position=NULL) use ($db) {
	$sql = '
		SELECT *
		FROM players
		LEFT JOIN pricing USING (id)
	';

	if ($position) {
		$sql .= 'WHERE position = "'. $db->escapeString($position). '"';
	}

})->conditions(array('position'=>'(QB|WR|RB|TE)'));

$app->run();
