<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d');

$entry_msg = "Welcome to the Notes submission page.";

$id = $_GET['id'] ?? 0;
$page_type = ($id === 0) ? 'create' : 'update';

$five_days_ago = return_date_relative_to_today('-5 days', 'string', 'Y-m-d');
$date_start = $date_start ?? $_POST['date-start'] ?? $five_days_ago;
$date_end = $date_end ?? $_POST['date-end'] ?? $today;

$predefined_dates = ['this week', 'last week', 'week before last', 'this month', 'last month', 'month before last', 'this quarter', 'last quarter', 'this year', 'last year'];

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

$conn->close();
?>
	<head>
<?php
// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');
?>
		<link rel="stylesheet" type="text/css" href="/homebase/resources/css/notes.css">
		<title>Note Form</title>
	</head>
	<body>
		<main>

			<h1>Notes</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post' id='main-form'>
				<label for='id'>ID</label>
				<input type='number' id='id' name='id' value='<?= $id; ?>' disabled />
				<label for='datetime'>Datetime (Optional)</label>
				<input type='datetime-local' name='datetime' <?php echo is_null($old_info['datetime']) ? "" : " value='" . date('Y-m-d\TH:i', strtotime($old_info['datetime'])) . "'" ?> />
				<label for='type'>Type</label>
				<select name='type' class='type'>
					<option value='thought' <?php if ($old_info['type'] == 'thought') echo "selected"; ?> >Thought</option>
					<option value='idea' <?php if ($old_info['type'] == 'idea') echo "selected"; ?> >Idea</option>
					<option value='reminder' <?php if ($old_info['type'] == 'reminder') echo "selected"; ?> >Reminder</option>
					<option value='positive experience' <?php if ($old_info['positive experience'] == 'reminder') echo "selected"; ?> >Positive Experience</option>
					<option value='negative experience' <?php if ($old_info['negative experience'] == 'reminder') echo "selected"; ?> >Negative Experience</option>
					<option value='quote' <?php if ($old_info['type'] == 'quote') echo "selected"; ?> >Quote</option>
					<option value='lesson' <?php if ($old_info['type'] == 'lesson') echo "selected"; ?> >Lesson</option>
					<option value='book to read' <?php if ($old_info['type'] == 'book to read') echo "selected"; ?> >Book To Read</option>
					<option value='learning resource' <?php if ($old_info['type'] == 'learning resource') echo "selected"; ?> >Learning Resource</option>
					<option value='homebase enhancement' <?php if ($old_info['type'] == 'homebase enhancement') echo "selected"; ?> >HomeBase Enhancement</option>
					<option value='habit' <?php if ($old_info['type'] == 'habit') echo "selected"; ?> >Habit</option>
				</select>
				<label for='summary-input'>Summary</label>
				<span class='char-count'>
					<input class='summary' name='summary' id='summary-input' type='text' placeholder='Heart to heart with Molly' maxlength='50' value='<?php echo $old_info['summary'] ?>'/>
					<h3 id='summary-char-count' class='char-count'>0/50</h3>
				</span>
				<label for='description-input'>Description</label>
				<span class='char-count'>
					<textarea name='description' class='description' id='description-input' maxlength='255' placeholder='Had a heart to heart with Molly Bolzano during shift today.'><?php echo htmlspecialchars_decode($old_info['description']); ?></textarea>
					<h3 id='desc-char-count' class='char-count'>0/255</h3>
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

			<section id='card-deck-parameters'>
				<h3>Note Search</h3>
				<div class='card-deck-parameter-row'>
					<?= generate_named_date_range($date_start, $date_end, $predefined_dates); ?>					
				</div>
				<div class='card-deck-parameter-row'>
					<span class='flex-input'>
						<label for='card-deck-type-input'>Type:</label>
						<select id='card-deck-type-input'>
							<option value='all'>All</option>
							<option value='thought'>Thought</option>
							<option value='idea'>Idea</option>
							<option value='reminder'>Reminder</option>
							<option value='positive experience'>Positive Experience</option>
							<option value='negative experience'>Negative Experience</option>
							<option value='quote'>Quote</option>
							<option value='lesson'>Lesson</option>
							<option value='book to read'>Book To Read</option>
							<option value='learning resource'>Learning Resource</option>
							<option value='homebase enhancement'>HomeBase Enhancement</option>
							<option value='habit'>Habit</option>
						</select>
					</span>
					<span class='flex-input'>
						<label for='card-deck-status-input'>Status:</label>
						<select id='card-deck-status-input'>
							<option value='all'>All</option>
							<option value='actionable'>Actionable</option>
							<option value='warning'>Warning</option>
							<option value='caution'>Caution</option>
							<option value='not-due'>Not Due</option>
							<option value='complete'>Completed</option>
							<option value='incomplete'>Incomplete</option>
						</select>
					</span>
					<?= return_label_and_input('card-deck-search-input', 'card-deck-search', 'text', 'Substring:'); ?>
				</div>
			</section>
			<section id='card-deck'></section>

		</main>
<?php
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php');
?>
	        <script type="text/javascript" src="/homebase/resources/resources.js"></script>
			<script>
				$(document).ready( function () {
					// Function to query DB for relevant notecards and 
					function updateNoteCardHTML(dateStart, dateEnd, searchStr, cardType, cardStatus) {
						$.ajax({
							type: "POST",
							url: '/homebase/resources/ajax/note_cards.php',
							data: {
								'date-start': dateStart,
								'date-end': dateEnd,
								'search-str': searchStr,
								'card-type': cardType,
								'card-status': cardStatus
							}
						})
						.done(function(response) {
							$('section#card-deck').empty().html(response);
							// DEPRECATED allowCompleteDatetimeToBeUpdated();
						});
					}
					// Initial note retrieval
					let dateStart = $('input#date-start').val();
					let dateEnd = $('input#date-end').val();
					let searchStr = $('input#card-deck-search-input').val();
					let cardType = $('select#card-deck-type-input').val();
					let cardStatus = $('select#card-deck-status-input').val();
					updateNoteCardHTML(dateStart, dateEnd, searchStr, cardType, cardStatus);
					
					// Change description textarea to template when type changes if textarea is currently empty
					$('select.type').on('change', function() {
						let newVal = $(this).val();
						let textAreaEl = $('textarea.description');
						if (textAreaEl.val().length === 0) {
							switch (newVal) {
								case 'habit':
									textAreaEl.html(`Purpose: \nTrigger:\nTime Cost:\nDescription: `);
									break;
								case 'learning resource':
									textAreaEl.html(`Summary: \nStrain: \nUsefulness: \nNeed to Review: \nUnderstanding: `);
									break;
								default:
									break;
							}
						}
					});

					// Show char limit for summary
					$('input.summary').on('keyup', function() {
						console.log('trigger');
						let charCount = $(this).val();
						charCount = charCount.length;
						console.log(`${charCount}`);
						$('h3#summary-char-count').html(`${charCount}/50`);
					});
					// Show char limit for description
					$('textarea.description').on('keyup', function() {
						console.log('trigger');
						let enteredText = $(this).val();
						charCount = enteredText.length;
						lineBreakCount = (enteredText.match(/\n/g)||[]).length; // Because we're converting newlines to <br /> we need to account for that in the character count
						lineBreakSizeDifferential = 5;
						charCount += lineBreakCount * lineBreakSizeDifferential;
						$('h3#desc-char-count').html(`${charCount}/255`);
						if (charCount > 255) {
							$('h3#desc-char-count').addClass('too-long');
						}
						else {
							$('h3#desc-char-count').removeClass('too-long');
						}
					});
					// Update dates and retrieve notes if predefined dates get changed
					$(document).on("change", "#predefined-dates", function(){
						var selVal = $(this).val();
						populatePredefinedDates(selVal, "date-start", "date-end");
						let dateStart = $('input#date-start').val();
						let dateEnd = $('input#date-end').val();
						let searchStr = $('input#card-deck-search-input').val();
						let cardType = $('select#card-deck-type-input').val();
						let cardStatus = $('select#card-deck-status-input').val();
						updateNoteCardHTML(dateStart, dateEnd, searchStr, cardType, cardStatus);
					});
					// Retrieve notes if inputs change
					$('section#card-deck-parameters input, select#card-deck-type-input, select#card-deck-status-input').bind('keyup change', function() {
						let dateStart = $('input#date-start').val();
						let dateEnd = $('input#date-end').val();
						let searchStr = $('input#card-deck-search-input').val();
						let cardType = $('select#card-deck-type-input').val();
						let cardStatus = $('select#card-deck-status-input').val();
						updateNoteCardHTML(dateStart, dateEnd, searchStr, cardType, cardStatus);
					});

				} );
			</script>
	</body>

</html>