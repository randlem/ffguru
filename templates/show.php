<!DOCTYPE html>
<html>
<head>
	<style text="text/css">
		th {
			background-color: #eee;
		}
		td {
			text-align: center;
		}
		th, td {
			padding: 0 0.4em;
		}
		.mine td {
			background-color: #1e90ff;
			color: #fff;
		}
		.paid td {
			background-color: #e2725b;
			color: #fff;
		}
	</style>
</head>
<body>
<p>
	<a href="/show">All</a>&nbsp;|&nbsp;
	<a href="/show/RB">RB</a>&nbsp;|&nbsp;
	<a href="/show/WR">WR</a>&nbsp;|&nbsp;
	<a href="/show/QB">QB</a>&nbsp;|&nbsp;
	<a href="/show/TE">TE</a>
</p>
<table border="1">
	<tr>
		<td></td>
		<th># Have (# Needed)</th>
		<th>Byes</th>
		<th># Bench (Bench Byes)</th>
	</tr>
	<? foreach ($team as $pos=>$bkdown) : ?>
	<tr>
		<th><?= $pos; ?></th>
		<td><?= $bkdown['have'] ?>&nbsp;(<?= $bkdown['need']; ?>)</td>
		<td><?= implode(',&nbsp;', $bkdown['byes']); ?></td>
		<td><?= $bench[$pos]['have']; ?>&nbsp;<?= implode(',&nbsp;', $bench[$pos]['byes']); ?></td>
	</tr>
	<? endforeach; ?>
</table>
<p><b>NOTE:</b> Don't forget a kicker and defense dumbass.</p>
<hr/>
<table border="1">
	<tr>
		<th>Budget Left $</th>
		<td><?= 225 - $totalPaid; ?></td>
	</tr>
	<tr>
		<th>Inflation %</th>
		<td><? printf('%.2f', $economy['inflation'] * 100); ?>%</td>
	</tr>
</table>
<hr/>
<table border="1">
	<tr>
		<th>Name</th>
		<th>Position</th>
		<th>Bye</th>
		<th>Projected $</th>
		<th>Inflated $</th>
		<th>Sold $</th>
		<th>Skew $</th>
	</tr>
	<? foreach ($players as $player) :
		$paid = '';
		$rowclass = '';
		if ($player['paid'] == 0) :
			$url  = $app->urlFor('buy', array('id' => $player['id']));
			$paid = '<form action="'. $url. '" method="post"><input type="text" maxlength="3" size="3" name="price" />&nbsp;<input type="checkbox" value="1" name="mine" />&nbsp;<input type="submit" value="BUY" /></form>';
		else :
			$rowclass = 'paid';
			$paid     = $player['paid'];
		endif;

		if ($player['mine']) :
			$rowclass = 'mine';
		endif;
	?>
	<tr class="<?= $rowclass ?>">
		<td style="text-align: left"><?= $player['name']; ?>&nbsp;(<?= $player['team']; ?>)</td>
		<td><?= $player['position']; ?></td>
		<td><?= $player['bye']; ?></td>
		<td><?= $player['projected']; ?></td>
		<td><?= ceil($player['projected'] * $economy['inflation']); ?></td>
		<td><?= $paid; ?></td>
		<td><?= $player['skew']; ?></td>
	</tr>
	<? endforeach; ?>
</table>
</body>
</html>
