<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d H:i:s');

$entry_msg = "Welcome to the Notes submission page.";

$id = $_GET['id'] ?? 0;
$page_type = ($id === 0) ? 'create' : 'update';

// If variables have been posted insert into db
if(isset($_POST['summary']) && isset($_POST['description']) && isset($_POST['type'])) {
	$datetime = (empty($_POST['datetime'])) ? 'CURRENT_TIMESTAMP' : "'" . $_POST['datetime'] . "'";
	$type = "'" . $_POST['type'] . "'";
	$summary = "'" . htmlspecialchars( $_POST['summary'], ENT_QUOTES ) . "'";
	$description = "'" . htmlspecialchars( $_POST['description'], ENT_QUOTES ) . "'";
	$caution_datetime = (empty($_POST['caution-datetime'])) ? 'NULL' : "'" . $_POST['caution-datetime'] . "'"; // Not DRY...should be turned into function
	$warning_datetime = (empty($_POST['warning-datetime'])) ? 'NULL' : "'" . $_POST['warning-datetime'] . "'";
	$complete_datetime = (empty($_POST['complete-datetime'])) ? 'NULL' : "'" . $_POST['complete-datetime'] . "'";
	$est_min_to_comp = (empty($_POST['est-min-to-comp'])) ? 'NULL' : "'" . $_POST['est-min-to-comp'] . "'";
	// If id is not zero, then this is an update
	if ($page_type == 'update') {
		$id == $_POST['id'];
		//echo "$id<br/>";
		$qry = "UPDATE personal_notes
				SET datetime = $datetime
					,type = $type
					,summary = $summary
					,description = $description
					,caution_datetime = $caution_datetime
					,warning_datetime = $warning_datetime
					,complete_datetime = $complete_datetime
					,est_min_to_comp = $est_min_to_comp
				WHERE id = $id ";
		if ($conn->query($qry) === TRUE) {
			$entry_msg = "Row updated successfully";
		} else {
			$entry_msg = "Error with query: $qry <br> $conn->error";
		}
	}
	// If id is zero then this is a new record
	else if ($page_type == 'create') {
		$qry = "INSERT INTO `personal_notes` (`datetime`, `type`, `summary`, `description`, `caution_datetime`, `warning_datetime`, `complete_datetime`, `est_min_to_comp`)
				VALUES ($datetime, $type, $summary, $description, $caution_datetime, $warning_datetime, $complete_datetime, $est_min_to_comp);";
		//echo $qry;
		if ($conn->query($qry) === TRUE) {
			$entry_msg = "New record created successfully";
		} else {
			$entry_msg = "Error with query: $qry <br> $conn->error";
		}
	}
}	

// Populate page with current database info
if ($page_type == 'update') {
	$q = "	SELECT *
			FROM personal_notes 
			WHERE id = $id; ";
	$res = $conn->query($q);
	$old_info = $res->fetch_assoc();
	//var_dump($old_info);
}

/*
$qry = "SELECT 	*
		FROM personal_notes 
		ORDER BY datetime DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
	$tiles = "";
    while($row = $res->fetch_assoc()) {
		if ( ! empty( $row['complete_datetime'] ) ) {
			$complete_date_formatted = new DateTime( $row['complete_datetime'] );
			$complete_date_formatted = date_format($complete_date_formatted, 'Y-m-d\TH:i');
		}
		else {
			$complete_date_formatted = '';
		}
*/
		/*
        $data_log .= "
					<tr>
						<td>" . $row['datetime'] . "</td>
						<td>" . $row['summary'] . "</td>
						<td>" . $row['description'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>" . $row['est_min_to_comp'] . "</td>
						<td>" . $row['caution_datetime'] . "</td>
						<td>" . $row['warning_datetime'] . "</td>";
		if ($row['caution_datetime'] != '' || $row['warning_datetime'] != '') {
			$data_log .= "<td><input data-id='" . $row['id'] . "' type='datetime-local' name='complete-datetime' class='complete-datetime' value='$complete_date_formatted'/></td>";
		}
		else {
			$data_log .= "<td></td>";
		}
			$data_log .= "</tr>";
		*/
/*
		$tiles .= "	<div class='card' style='border: 1px solid white; padding: 0.5rem; margin: 1rem; width: 20%; min-width: 20rem;'>
						<h3 style='font-weight: 900;'>" . $row['summary'] . "</h3>
						<p style='font-size: 0.625rem; height: 4rem;'>" . $row['description'] . "</p>
						<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Created:</span><span>" . $row['datetime'] . "</span></h4>
						<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Caution:</span><span>" . $row['caution_datetime'] . "</span></h4>
						<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Warning:</span><span>" . $row['warning_datetime'] . "</span></h4>";
		if ( ($row['caution_datetime'] != '' || $row['warning_datetime'] != '') && $complete_date_formatted == '' ) {
			$tiles .= "<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Completed:</span><span><input data-id='" . $row['id'] . "' type='datetime-local' name='complete-datetime' class='complete-datetime' value='$complete_date_formatted'/></span></h4>";
		}
		else {
			$tiles .= "<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Completed:</span><span>$complete_date_formatted</span></h4>";
		}
		$tiles .= "	</div>";
    }
}
*/

$conn->close();
?>
	<head>
<?php
// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>
		<title>Note Form</title>
	</head>
	<body>
		<main style='width: 95%; max-width: none;'>

			<h1>Notes</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='id'>ID</label>
				<input type='number' id='id' name='id' value='<?= $id; ?>' disabled />
				<label for='datetime'>Datetime (Optional)</label>
				<input type='datetime-local' name='datetime' <?php echo is_null($old_info['datetime']) ? "" : " value='" . date('Y-m-d\TH:i', strtotime($old_info['datetime'])) . "'" ?> />
				<label for='type'>Type</label>
				<select name='type'>
					<option value='thought' <?php if ($old_info['type'] == 'thought') echo "selected"; ?> >Thought</option>
					<option value='idea' <?php if ($old_info['type'] == 'idea') echo "selected"; ?> >Idea</option>
					<option value='reminder' <?php if ($old_info['type'] == 'reminder') echo "selected"; ?> >Reminder</option>
					<option value='positive experience' <?php if ($old_info['positive experience'] == 'reminder') echo "selected"; ?> >Positive Experience</option>
					<option value='negative experience' <?php if ($old_info['negative experience'] == 'reminder') echo "selected"; ?> >Negative Experience</option>
					<option value='quote' <?php if ($old_info['type'] == 'quote') echo "selected"; ?> >Quote</option>
					<option value='lesson' <?php if ($old_info['type'] == 'lesson') echo "selected"; ?> >Lesson</option>
					<option value='book to read' <?php if ($old_info['book to read'] == 'reminder') echo "selected"; ?> >Book To Read</option>
					<option value='learning resource' <?php if ($old_info['learning resource'] == 'reminder') echo "selected"; ?> >Learning Resource</option>
					<option value='homebase enhancement' <?php if ($old_info['homebase enhancement'] == 'reminder') echo "selected"; ?> >HomeBase Enhancement</option>
				</select>
				<label for='summary-input'>Summary</label>
				<span style='position: relative;'>
					<input class='summary' name='summary' id='summary-input' type='text' placeholder='Heart to heart with Molly Bolzano' maxlength='50' style='width: 100%;' value='<?php echo $old_info['summary'] ?>'/>
					<h3 class='summary-char-used' style='position: absolute; bottom: 0.5rem; right: 0.5rem; color: hsla(0, 0%, 0%, 0.5);'>0/50</h3>
				</span>
				<label for='description-input'>Description</label>
				<span style='position: relative;'>
					<textarea name='description' class='description' id='description-input' maxlength='255' placeholder='Had a heart to heart with Molly during shift today.' style='width: 100%;'><?php echo htmlspecialchars_decode($old_info['description']); ?></textarea>
					<h3 class='desc-char-used' style='position: absolute; bottom: 0.5rem; right: 0.5rem; color: hsla(0, 0%, 0%, 0.5);'>0/255</h3>
				</span>
				<label for='caution-datetime-input'>Caution Datetime (Optional)</label>
				<input type='datetime-local' name='caution-datetime' id='caution-datetime-input' <?php echo is_null($old_info['caution_datetime']) ? "" : " value='" . date('Y-m-d\TH:i', strtotime($old_info['caution_datetime'])) . "'" ?> />
				<label for='warning-datetime-input'>Warning Datetime (Optional)</label>
				<input type='datetime-local' name='warning-datetime' id='warning-datetime-input' <?php echo is_null($old_info['warning_datetime']) ? "" : " value='" . date('Y-m-d\TH:i', strtotime($old_info['warning_datetime'])) . "'" ?> />
				<label for='complete-datetime-input'>Complete Datetime (Optional)</label>
				<input type='datetime-local' name='complete-datetime' id='complete-datetime-input' <?php echo is_null($old_info['complete_datetime']) ? "" : " value='" . date('Y-m-d\TH:i', strtotime($old_info['complete_datetime'])) . "'" ?> />
				<label for='est-min-to-comp-input'>Est Min. to Comp. (Optional)</label>
				<input type='number' name='est-min-to-comp' id='est-min-to-comp-input' min='0' max='65535' <?php echo is_null($old_info['est_min_to_comp']) ? "" : " value='" . $old_info['est_min_to_comp'] . "'" ?> />
				<button type="submit"><?php echo $page_type == 'update' ? 'Update' : 'Submit' ?></button>
			</form>

			<!--
			<table class='log' id='runs-table'>
				<thead>
					<tr>
						<th>DateTime</th>
						<th>Subject</th>
						<th>Description</th>
						<th>Type</th>
						<th>Est min. to comp.</th>
						<th>Caution DT</th>
						<th>Warning DT</th>
						<th>Complete</th>
					</tr>				
				</thead>
				<?php// echo $data_log; ?>
			</table>
			-->
			<section class='card-deck-parameters' style='flex-flow: row nowrap; width: 100%; justify-content: center;'>
				<?= return_label_and_input('card-deck-date-start-input', 'card-deck-date-start', 'date', 'Date Start'); ?>
				<?= return_label_and_input('card-deck-date-end-input', 'card-deck-date-end', 'date', 'Date End'); ?>
				<?= return_label_and_input('card-deck-search-input', 'card-deck-search', 'test', 'Search'); ?>
			</section>
			<section class='card-deck' style='width: 100%; padding: 1rem; flex-flow: row wrap; justify-content: space-evenly;'></section>

		</main>
<?php
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php');
?>
	        <script type="text/javascript" src="/homebase/resources/resources.js"></script>
			<script>
				$(document).ready( function () {

					$('#runs-table').DataTable( {
						"order": [ 0, 'desc' ],
						"pageLength": 250,
					} );
					function allowCompleteDatetimeToBeUpdated() {
						$('input.complete-datetime').on('change blur', function() {
							let val = "'" + $(this).val() + "'";
							if (val == "''") {
								val = "NULL";
							}
							let id = $(this).attr('data-id');
							console.log("CHANGE!");
							ajaxPostUpdate("/homebase/resources/forms/form-resources/update_note.php", { 'column-name' : 'complete_datetime', 'value' : val , 'id' : id }, false );
						});
					}

					$('input.summary').on('keyup', function() {
						let charCount = $(this).val();
						charCount = charCount.length;
						$('h3.summary-char-used').html(`${charCount}/50`);
					});

					$('textarea.description').on('keyup', function() {
						let enteredText = $(this).val();
						charCount = enteredText.length;
						lineBreakCount = (enteredText.match(/\n/g)||[]).length; // Because we're converting newlines to <br /> we need to account for that in the character count
						lineBreakSizeDifferential = 5;
						charCount += lineBreakCount * lineBreakSizeDifferential;
						$('h3.desc-char-used').html(`${charCount}/255`);
						if (charCount > 255) {
							$('h3.desc-char-used').css('color: red');
						}
					});

					$.ajax({
						type: "POST",
						url: '/homebase/resources/ajax/note_cards.php',
						data: {
							
						}
					})
					.done(function(response) {
						$('section.card-deck').empty().html(response);
						allowCompleteDatetimeToBeUpdated();
					});
					
					$('section.card-deck-parameters input').on('change', function() {
						let dateStart = $('input#card-deck-date-start-input').val();
						let dateEnd = $('input#card-deck-date-end-input').val();
						let searchStr = $('input#card-deck-search-input').val();
						console.log(`${dateStart}`);
						$.ajax({
							type: "POST",
							url: '/homebase/resources/ajax/note_cards.php',
							data: {
								'date-start': dateStart,
								'date-end': dateEnd,
								'search-str': searchStr
							}
						})
						.done(function(response) {
							$('section.card-deck').empty().html(response);
							allowCompleteDatetimeToBeUpdated();
						});
					});
					

				} );
			</script>
	</body>

</html>