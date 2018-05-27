<script>
	var thisWeekRicks, thisWeekSealAndDesign, annualIncomeProjection;
</script>
<?php
	$last_sunday = "'" . date('Y/m/d', strtotime('last Sunday')) . "'";

	$conn = new mysqli($serv, $user, $pass, $db);
	if (!$conn) {
		die("Connection to server failed: " . mysqli_connect_errno());
	}

	$query = "SELECT SUM(tips) FROM finance_ricks_shifts WHERE date > $last_sunday";
	$result = $conn->query($query);
	$row = mysqli_fetch_row($result);
	$this_week_ricks_tips = $row[0];

	$query = "SELECT SUM(hours) FROM finance_ricks_shifts WHERE date > $last_sunday";
	$result = $conn->query($query);
	$row = mysqli_fetch_row($result);
	$this_week_ricks_hours = $row[0];

	$query = "SELECT SUM(tips) FROM finance_ricks_shifts";
	$result = $conn->query($query);
	$row = mysqli_fetch_row($result);
	$total_ricks_tips = $row[0];

	$query = "SELECT SUM(hours) FROM finance_ricks_shifts";
	$result = $conn->query($query);
	$row = mysqli_fetch_row($result);
	$total_ricks_hours = $row[0];

?>
	<script>
		thisWeekRicks = "<?php echo round($this_week_ricks_tips + (7.5 * $this_week_ricks_hours))?>";
	</script>
<?php
	$conn->close();
?>
<section class="column finance">
	<h2>Finances</h2>
<!--
	<nav>
		<button>This Week</button>
		<button>Average</button>
		<button>Net</button>
	</nav>
-->
	<div class="content">
		<div class="stat" id="actual-net-worth">
			<h3>Current Net Worth</h3>
<!--			<h4>$______</h4>-->
<!--			<h5>As of 05/22/2018</h5>-->
		</div>
		<div class="stat" id="weekly-income">
			<h3>This Week Income</h3>
		</div>
		<div class="stat" id="weekly-expenditure">
			<h3>This Week Expenditure</h3>
		</div>
		<div class="stat" id="expenditure-breakdown">
			<h3>Expenditure Breakdown</h3>
		</div>
		<div class="stat" id="annual-projection">
			<h3>Annual Income Projection</h3>
<!--			<h4>$______</h4>-->
		</div>
		<div class="stat" id="annual-projection">
			<h3>Annual Expenditure Projection</h3>
<!--			<h4>$______</h4>-->
		</div>
		<div class="stat" id="projected-net-worth">
			<h3>Projected EOY Net Worth</h3>
<!--			<h4>$______</h4>-->
<!--			<h5>As of 05/22/2018</h5>-->
		</div>
	</div>
</section>