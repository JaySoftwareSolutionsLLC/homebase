<header>
	<div class="left">
		<a href='/homebase/index.php'><h1>Home Base 3.0</h1></a>
		<div class='year-select'>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
				<input type="hidden" name='year' value='2021'>
				<input type='submit' value='2021' class='<?php if ($year == '2021') {echo 'selected';} ?>'>
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
				<input type="hidden" name='year' value='2020'>
				<input type='submit' value='2020' class='<?php if ($year == '2020') {echo 'selected';} ?>'>
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
				<input type="hidden" name='year' value='2019'>
				<input type='submit' value='2019' class='<?php if ($year == '2019') {echo 'selected';} ?>'>
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
				<input type="hidden" name='year' value='2018'>
				<input type='submit' value='2018' class='<?php if ($year == '2018') {echo 'selected';} ?>'>
			</form>
		</div>
		<button id='blur-data' style='background: none; color: #00d5ff; border: 1px solid #00d5ff; border-radius: 0.5rem; padding: 0.25rem; margin: 0 0.25rem; cursor: pointer;'>Blur</button>
	</div>
	<div class="right">
		<nav>
			<a href="/homebase/habits/calendar.php" target="_blank">Habit Calendar</a>
			<a href="https://tools.siteground.com/filemanager?siteId=TEFyMFlITUpJUT09" target="_blank">SiteGround</a>
			<a href="https://siteground332.com/phpmyadmin/index.php?lang=en" target="_blank">phpMyAdmin</a>
			<a href="/homebase/resources/forms/day_info.php" target="_blank">Daily</a>
			<a href="/homebase/resources/reports/weekly-report/index.php" target="_blank">Week</a>
			<a href="/homebase/resources/reports/monthly-report/index.php" target="_blank">Month</a>
			<a href="/homebase/resources/reports/annual-report/index.php" target="_blank">Annual</a>
		</nav>
	</div>
	<div class='right-mobile'>
		<img src='/homebase/resources/assets/images/icon-hamburger-nav-white.png'/>
		<nav>
		<a href="/homebase/habits/calendar.php" target="_blank">Habit Calendar</a>
			<a href="https://tools.siteground.com/filemanager?siteId=TEFyMFlITUpJUT09" target="_blank">SiteGround</a>
			<a href="https://siteground332.com/phpmyadmin/index.php?lang=en" target="_blank">phpMyAdmin</a>
			<a href="/homebase/resources/forms/day_info.php" target="_blank">Daily</a>
			<a href="/homebase/resources/reports/weekly-report/index.php" target="_blank">Week</a>
			<a href="/homebase/resources/reports/monthly-report/index.php" target="_blank">Month</a>
			<a href="/homebase/resources/reports/annual-report/index.php" target="_blank">Annual</a>
		</nav>
	</div>
</header>
<div id="ghost-header"></div>