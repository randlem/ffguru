<?php
require_once('../vendor/autoload.php');

$db = new SQLite3('../data/ffguru.db', SQLITE3_OPEN_READWRITE);

$app = new \Slim\Slim(array(
	'templates.path' => '../templates',
));

function computeEconomy() {
	global $db;
	$avail = (225 * 12) - 12 - 12; // availabe $ amount
	$vbd   = 8303.84; // total value by dollar

	$sql = '
		SELECT
			SUM(skew) AS totalSkew
		FROM pricing
	';
	$result = $db->query($sql);
	$row    = $result->fetchArray(SQLITE3_ASSOC);

	$row['avail']      = $avail;
	$row['inflated']   = $avail + $row['totalSkew'];
	$row['vbd']        = $vbd;
	$row['pf']         = $avail / $vbd;
	$row['inflatedPF'] = $row['inflated'] / $vbd;
	$row['inflation']  = $row['inflatedPF'] / $row['pf'];

	return $row;
}

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
	$total   = 0;
	$byes    = array();
	$team    = array(
		'RB' => array(
			'have' => 0,
			'need' => 2,
			'byes' => array(),
		),
		'WR' => array(
			'have' => 0,
			'need' => 3,
			'byes' => array(),
		),
		'QB' => array(
			'have' => 0,
			'need' => 1,
			'byes' => array(),
		),
		'TE' => array(
			'have' => 0,
			'need' => 1,
			'byes' => array(),
		)
	);
	$bench   = array(
		'RB' => array(
			'have' => 0,
			'byes' => array(),
		),
		'WR' => array(
			'have' => 0,
			'byes' => array(),
		),
		'QB' => array(
			'have' => 0,
			'byes' => array(),
		),
		'TE' => array(
			'have' => 0,
			'byes' => array(),
		),
	);
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		$players[] = $row;

		if ($row['mine']) {
			$total += $row['paid'];
			$byes[$row['position']][] = $row['bye'];

			if ($team[$row['position']]['have'] < $team[$row['position']]['need']) {
				$team[$row['position']]['have']++;
				$team[$row['position']]['byes'][] = $row['bye'];
			} else {
				$bench[$row['position']]['have']++;
				$bench[$row['position']]['byes'][] = $row['bye'];
			}
		}
	}

	$economy = computeEconomy();

	$app->render(
		'show.php',
		array(
			'players'   => $players,
			'economy'   => $economy,
			'totalPaid' => $total,
			'team'      => $team,
			'bench'     => $bench,
			'app'       => $app,
		)
	);

})->name('show')->conditions(array('position'=>'(QB|WR|RB|TE)'));

$app->post('/buy/:id', function ($id) use ($app, $db) {
	$price = $app->request->post('price');
	$mine  = $app->request->post('mine');

	$stmt = $db->prepare('
		UPDATE pricing
		SET paid = :price,
			skew = projected - :price
		WHERE id = :id
		;
	');
	$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
	$stmt->bindValue(':price', $price, SQLITE3_INTEGER);
	$stmt->execute();

	$stmt = $db->prepare('
		UPDATE players
		SET mine = :mine
		WHERE id = :id
		;
	');
	$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
	$stmt->bindValue(':mine', ($mine) ? 1 : 0, SQLITE3_INTEGER);
	$stmt->execute();

})->name('buy');

$app->run();
