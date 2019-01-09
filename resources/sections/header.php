<header>
	<div class="left">
		<a href='/homebase/index.php'><h1>Home Base 3.0</h1></a>
		<div class='year-select'>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
				<input type="hidden" name='year' value='2019'>
				<input type='submit' value='2019' class='<?php if ($year == '2019') {echo 'selected';} ?>'>
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
				<input type="hidden" name='year' value='2018'>
				<input type='submit' value='2018' class='<?php if ($year == '2018') {echo 'selected';} ?>'>
			</form>
			
		</div>
	</div>
	<div class="right">
		<nav>
			<a href="https://secure332.sgcpanel.com:2083/cpsess9548072040/frontend/Crystal/index.php" target="_blank">cPanel</a>
			<a href="https://secure332.sgcpanel.com:2083/cpsess6924724625/3rdparty/phpMyAdmin/index.php" target="_blank">phpMyAdmin</a>
			<a href="https://secure332.sgcpanel.com:2096/cpsess0106124978/webmail/Crystal/index.html?mailclient=horde" target="_blank">webmail</a>
			<a href="/homebase/resources/reports/weekly-report/index.php" target="_blank">Weekly Report</a>
			<a href="/homebase/resources/reports/monthly-report/index.php" target="_blank">Monthly Report</a>
			<!--<a href="https://quizlet.com/215065234/programming-terminology-flash-cards/" target="_blank">Quizlet</a>-->
		</nav>
	</div>
</header>
<div id="ghost-header"></div>