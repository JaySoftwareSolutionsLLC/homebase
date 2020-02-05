<?php

?>
<section class="column habits" style="">
	<h2>Habits</h2>
	<main>
		<ul id='habit-selection'>
			<li data-selection='all'>All</li>
			<li data-selection='started'>Started</li>
			<li data-selection='incomplete'>Incomplete</li>
			<li data-selection='clear-started'>Clear Started</li>
		</ul>
		<ul id='habits-list'>
			<?= $habits_list_html; ?>
		</ul>
		<table id='habits-summary'>
			<thead>
				<tr>
					<th>Total Time</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td></td>
				</tr>
			</tbody>
		</table>
	</main>
</section>