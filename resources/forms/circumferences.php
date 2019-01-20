<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	//include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	//include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

	function return_circ_input_html_str($label_str, $circ_str, $circ_id, $ideal_val) {
		$str = "<label for='$circ_str-circ-input'>$label_str</label>
				<input class='circ' id='$circ_str-circ-input' type='number' step='0.125' min='0' name='$circ_str-circ-input' data-circ-id='$circ_id' placeholder='$ideal_val'>";
		return $str;
	};

//---CONNECT TO DATABASE------------------------------------------------------------
	//$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES----------------------------------------------------
	//$today_time = time();
	//$today_date = date('Y-m-d');
	//$last_sunday = "'" . date('Y/m/d', strtotime('last Sunday')) . "'";
	//$days_left_in_2018 = floor((strtotime('January 1st, 2019') - $today_time) / SEC_IN_DAY);

//---SELECT FROM DATABASE-----------------------------------------------------------
	


//---CLOSE DATABASE CONNECTION------------------------------------------------------
	//$conn->close();

//---BEGIN HTML---------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <title>Circumference Form</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
<?php
	// Link to Style Sheets
	include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/forms/form-resources/css-files.php');
?>
    <link rel="stylesheet" type="text/css" href="../css	/circumferences.css">
</head>

<body>
	<main>
		<h1>Log Circumference(s)</h1>
		<h2 class='msg'>Welcome to Circumference Entry Page</h2>
		<span style='display: inline-flex; flex-flow: row nowrap; width: 100%; height: 700px;'>
			<form style='width: 70%'>
				<label for='datetime'>DateTime</label>
				<input id='datetime' type='datetime-local' name='datetime' value=''/>
<?php
				echo return_circ_input_html_str('Neck', 'neck', '1', '15');
				echo return_circ_input_html_str('Shoulder', 'shoulder', '2', '48');
				echo return_circ_input_html_str('Chest', 'chest', '3', '42.25');
				echo return_circ_input_html_str('Upper Arm (Avg)', 'upper-arm', '4', '15');
				echo return_circ_input_html_str('Fore Arm (Avg)', 'fore-arm', '5', '12.25');
				echo return_circ_input_html_str('Waist', 'waist', '6', '29.5');
				echo return_circ_input_html_str('Hip', 'hips', '7', '36');
				echo return_circ_input_html_str('Thigh', 'thigh', '8', '22.5');
				echo return_circ_input_html_str('Calf', 'calf', '9', '14.5');
?>
			</form>
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 200 880" style='width: 50%;'>
				<defs>
					<style>
					</style>
				</defs>
				<g class="body front" stroke="white" stroke-width="0.5" fill="none">
				<path class='outline' d="M 85,52 
					C 75,68 57,57 50,76 
					C 33,75 21,94 28,107 
					Q 22,116 25,138 
					Q 13,152 17,184
					L 16,203
					Q 9,214 19,227
					Q 31,228 42,224
					C 47,220 34,210 41,204
					C 42,199 27,193 31,191
					C 43,177 55,154 50,140
					C 50,142 57,136 61,126
					C 53,144 65,150 67,158
					C 68,168 65,181 61,188
					C 53,223 50,260 69,280
					C 58,300 60,330 75,359
					C 80,382 52,380 48,394
					C 61,395 86,392 96,390
					C 95,375 85,359 90,353
					C 96,337 96,302 95,280
					C 93,258 95,248 100,230

					C 105,248 107,258 105,280
					C 104,302 104,337 110,353
					C 115,359 105,375 104,390
					C 114,392 139,395 152,394
					C 148,380 120,382 125,359
					C 140,330 142,300 131,280
					C 150,260 147,223 139,188
					C 135,181 132,168 133,158
					C 135,150 147,144 139,126
					C 143,136 150,142 150,140
					C 145,154 157,177 169,191
					C 173,193 158,199 158,204
					C 166,210 153,220 158,224
					Q 169,228 181,227
					Q 191,214 184,203
					L 183,184
					Q 187,152 175,138
					Q 178,116 172,107
					C 179,94 167,75 150,76
					C 143,57 125,68 115,52

					C 115,53 116,51 115,45
					C 120,45 120,25 115,30
					C 120,20 110,6 100,8
					C 90,6 80,20 85,30
					C 80,35 80,40 85,45
					C 84,51 85,52 85,52" fill='hsla(0, 0%, 100%, 0.1)'/>
					<g class='circumferences' stroke='hsl(190, 100%, 50%)' stroke-width="5" opacity='0.1'>
						<path class="circumference neck" data-circ-id="1"
						d=" M 85,52
							L 115,52						
						"
						/>
						<path class="circumference shoulders" data-circ-id="2"
						d=" M 27,90
							L 173,90						
						"
						/>
						<path class="circumference chest" data-circ-id="3"
						d=" M 55,115
							L 145,115						
						"
						/>
						<path class="circumference left upper-arm" data-circ-id="4"
						d=" M 140,130
							L 175,115						
						"
						/>
						<path class="circumference right upper-arm" data-circ-id="4"
						d=" M 60,130
							L 25,115						
						"
						/>
						<path class="circumference left fore-arm" data-circ-id="5"
						d=" M 150,160
							L 183,153						
						"
						/>
						<path class="circumference right fore-arm" data-circ-id="5"
						d=" M 50,160
							L 17,153						
						"
						/>
						<path class="circumference waist" data-circ-id="6"
						d=" M 67,160
							L 133,160						
						"
						/>
						<path class="circumference hips" data-circ-id="7"
						d=" M 57,210
							L 143,210						
						"
						/>
						<path class="circumference left thigh" data-circ-id="8"
						d=" M 103,240
							L 145,235						
						"
						/>
						<path class="circumference right thigh" data-circ-id="8"
						d=" M 97,240
							L 55,235						
						"
						/>
						<path class="circumference left calf" data-circ-id="9"
						d=" M 104,305
							L 138,303						
						"
						/>
						<path class="circumference right calf" data-circ-id="9"
						d=" M 96,305
							L 62,303						
						"
						/>
					</g>
			</svg>
        </span>
        
		
		
	<?php

	?>
	</main>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src=""></script>
	<script>
		$('input.circ').on('focus', function() {
			let circID = $(this).attr('data-circ-id');
			$('svg path.circumference').each(function() {
				if ($(this).attr('data-circ-id') == circID) {
					$(this).attr('opacity', 1);
				}
			});
		});
	
	</script>
</body>

</html>