<?php
require_once('../vendor/autoload.php');

$db = new SQLite3('../data/ffguru.db');

$app = new \Slim\Slim(array(
	'templates.path' => '../templates',
));

$app->get('/show(/:position)', function ($position=NULL) use ($app, $db) {
	$sql = '
		SELECT *
		FROM players
		LEFT JOIN pricing USING (id)
	';

	if ($position) {
		$sql .= 'WHERE position = "'. $db->escapeString($position). '"';
	}

	$results = $db->query($sql);

	if (!$results) {
		throw new RuntimeException('Database error fetching player list.');
	}

	$players = array();
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		$players[] = $row;
	}

	$app->render(
		'show.php',
		array(
			'players' => $players
		)
	);

})->conditions(array('position'=>'(QB|WR|RB|TE)'));

$app->run();
