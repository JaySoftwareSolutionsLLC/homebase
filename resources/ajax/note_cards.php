<?php
// File to be called via ajax to load a subset of notes in the form of cards

// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Initialize variables
$today = return_date_from_str('now');
$date_start = $_POST['date-start'] ?? return_date_from_str('2018-06-01');
$date_end = return_end_of_day($_POST['date-end']) ?? date('Y-m-d H:i:s');
$date_end = empty( $date_end ) ? date('Y-m-d H:i:s') : $date_end;
$date_start = empty( $date_start ) ? return_date_from_str('2018-06-01') : $date_start;
$search_str = $_POST['search-str'] ?? '';
$card_type = $_POST['card-type'] ?? 'all';
$card_status = $_POST['card-status'] ?? 'all';
$return_count = $_POST['count'] ?? 0; // Related to page count WIP
$offset = $_POST['offset'] ?? 0; // Related to current page WIP
// TEST

$note_type_icons = array();
$note_type_icons['positive experience'] = "<i title='positive experience' class='fas fa-plus-circle' style='color: hsla(120, 100%, 50%, 1);'></i>";
$note_type_icons['negative experience'] = "<i title='negative experience' class='fas fa-minus-circle' style='color: hsla(0, 100%, 50%, 1);'></i>";
$note_type_icons['reminder'] = "<i title='reminder' class='fas fa-bell' style='color: hsla(300, 100%, 50%, 1);'></i>";
$note_type_icons['idea'] = "<i title='idea' class='fas fa-lightbulb' style='color: hsla(50, 100%, 50%, 1);'></i>";
$note_type_icons['thought'] = "<i title='thought' class='fas fa-brain' style='color: hsla(0, 100%, 80%, 1);'></i>";
$note_type_icons['quote'] = "<i title='quote' class='fas fa-quote-right'></i>";
$note_type_icons['lesson'] = "<i title='lesson' class='fas fa-graduation-cap'></i>";
$note_type_icons['book to read'] = "<i title='book to read' class='fas fa-book'></i>";
$note_type_icons['learning resource'] = "<i title='learning resource' class='fas fa-flask'></i>";
$note_type_icons['homebase enhancement'] = "<i title='homebase enhancement' class='fas fa-tools'></i>";

//echo "$date_start - $date_end";

// Connect to DB
$conn = connect_to_db();

// Query DB
$qry = "SELECT 	*
				FROM personal_notes 
        WHERE   datetime >= '$date_start'
        	AND datetime <= '$date_end'
			AND (summary LIKE '%$search_str%'
				  OR description LIKE '%$search_str%')";
if ($card_type != 'all') {
	$qry .= " AND type = '$card_type' ";
}
switch ($card_status) {
	case 'all':
		break;
	case 'actionable':
		$qry .= " AND (caution_datetime IS NOT NULL OR warning_datetime IS NOT NULL) ";
		break;
	case 'warning':
		$qry .= " AND warning_datetime <= '$today' AND complete_datetime IS NULL ";
		break;
	case 'caution':
		$qry .= " AND caution_datetime <= '$today' AND (warning_datetime >= '$today' OR warning_datetime IS NULL) AND complete_datetime IS NULL ";
		break;
	case 'not-due':
		$qry .= " AND (caution_datetime IS NOT NULL OR warning_datetime IS NOT NULL) AND (caution_datetime >= '$today' OR caution_datetime IS NULL) AND (warning_datetime >= '$today' OR warning_datetime IS NULL) AND complete_datetime IS NULL ";
		break;
	case 'complete':
		$qry .= " AND complete_datetime IS NOT NULL ";
		break;
	default:
		# code...
		break;
}
$qry .= "ORDER BY datetime DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
	$tiles = "";
    while($row = $res->fetch_assoc()) {
		$status = 'none';
		if ( ! empty( $row['caution_datetime'] ) || ! empty( $row['warning_datetime'] ) ) {
			$status = 'actionable';
		}
		if ( return_days_between_dates( $row['caution_datetime'], return_date_from_str() ) > 0 ) {
			$status = 'caution';
		}
		if ( return_days_between_dates( $row['warning_datetime'], return_date_from_str() ) > 0 ) {
			$status = 'warning';
		}
		if ( ! empty( $row['complete_datetime'] ) ) {
			$status = 'completed';
			$complete_date_formatted = new DateTime( $row['complete_datetime'] );
			$complete_date_formatted = date_format($complete_date_formatted, 'Y-m-d\TH:i');
		}
		else {
			$complete_date_formatted = '';
		}

		$tiles .= "	<div class='card $status' style='border: 1px solid black; padding: 1rem; margin: 1rem; width: 15%; min-width: 20rem; border-radius: 0.5rem;'>
						<h3 style='font-weight: 900; font-size: 0.875rem; z-index: 10;'>" . $row['summary'] . "</h3>
						<p style='font-size: 0.625rem; height: 9rem; overflow: hidden;'>" . nl2br($row['description']) . "</p> ";
						/*
						"<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Created:</span><span>" . $row['datetime'] . "</span></h4>
						<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Caution:</span><span>" . $row['caution_datetime'] . "</span></h4>
						<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Warning:</span><span>" . $row['warning_datetime'] . "</span></h4>"; */
						/*
		if ( ($row['caution_datetime'] != '' || $row['warning_datetime'] != '') && $complete_date_formatted == '' ) {
			$tiles .= "<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Completed:</span><span><input data-id='" . $row['id'] . "' type='datetime-local' name='complete-datetime' class='complete-datetime' value='$complete_date_formatted' style='font-size: 0.75rem;'/></span></h4>";
		}
		else {
			$tiles .= "<h4 style='font-family: monospace; width: 100%; display: inline-flex; flex-flow: row nowrap; justify-content: space-between;'><span>Completed:</span><span>$complete_date_formatted</span></h4>";
		}				*/
		foreach ($note_type_icons as $key => $val) {
			if ($key == $row['type']) {
				hidden_var_dump($val);
				$tiles .= $val;
			}
		}
		if ( ! empty( $row['est_min_to_comp'] ) ) {
			$tiles .= "<span class='est-min-to-comp'>" . $row['est_min_to_comp'] . "</span>";
		}
		$tiles .= "<a href='/homebase/resources/forms/notes.php?id=" . $row['id'] . "'><i class='fas fa-edit edit'></i></a>";
		$tiles .= "	</div>";
	}
}

$conn->close();

echo $tiles;

?>