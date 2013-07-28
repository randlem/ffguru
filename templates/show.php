<table>
	<tr>
		<th>Name</th>
		<th>Position</th>
		<th>Bye</th>
		<th>Projected $</th>
		<th>Inflated $</th>
		<th>Sold $</th>
		<th>Skew $</th>
	</tr>
	<?php foreach ($players as $player) : ?>
	<tr>
		<td><?= $player['name']; ?></td>
		<td><?= $player['position']; ?></td>
		<td><?= $player['bye']; ?></td>
		<td><?= $player['projected']; ?></td>
		<td><?= $player['inflated']; ?></td>
		<td><?= ($player['paid'] == 0) ? '<input type="text" ; ?></td>
		<td><?= $player['skew']; ?></td>
	</tr>
	<? endforeach; ?>

</table>
