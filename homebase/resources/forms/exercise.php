<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---RETRIEVE POST VARIABLES--------------------------------------------------------


//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------
	$today_time = time();
	$today_date = date('Y-m-d');
	$today_datetime = new DateTime();

	$exercises = array();
	$q = " SELECT * FROM fitness_exercises ORDER BY name ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while ($row = $res->fetch_assoc()) {
			$e = new stdClass();
			$e->id = $row['id'];
			$e->name = $row['name'];
			$exercises[] = $e;
		}
	}

	$equipments = array();
	// Determine what all of the equipment options are
	$q = " SELECT * FROM fitness_equipment ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$e = new stdClass();
			$e->id = $row['id'];
			$e->name = $row['name'];
			
			$equipments[] = $e;
		}
	}

?>


<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <title>Exercise</title>
<?php 	//include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/forms/form-resources/css-files.php');
		include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');
		include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php'); ?>
    

</head>

<body>

	<main style='background: none;'>
		
		<h1 class='exercise-name'><?php //echo "$exercise->name (#$exercise->id)"; ?></h1>
		<h2 class='msg'></h2>
		<h3></h3>
		<form>
			<section class='exercise-selection'>
				<select style='width: 100%;'>
					<option value='new'>New Exercise</option>
<?php
					//var_dump($exercises);
					foreach ($exercises as $e) {
						$str = "<option value='$e->id'>$e->name</option>";
						echo $str;
					}
?>
				</select>
				<input style='width: 100%;' type='text' name='exercise-name-input' value='' class='exercise-name-input' placeholder='Pushups' autocomplete="off"></input>
				<input style='width: 100%;' type='text' name='exercise-href-input' value='' class='exercise-href-input' placeholder='https://www.bodybuilding.com/exercises/pushups' autocomplete="off"></input>
				<textarea style='width: 100%;' type='text' name='exercise-desc-input' value='' class='exercise-desc-input' placeholder='Hands on ground. Back as straight as possible. Focus on feeling pecs contract on each rep.'></textarea>
			</form>
		</section>
		
		<section class='muscle-selection'>
			<h2>Muscles</h2>
		
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 400 420" style='border: none;'>
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
					C 84,51 85,52 85,52
					C 95,63 105,63 115,53" fill='hsla(0, 0%, 100%, 0.1)'/>
					<g class="muscles" stroke-width="0.5">
						<path class="muscle right traps" data-muscle-id="1"
						d="	M 85,52 
							C 75,68 57,57 50,76
							C 66,76 85,70 85,52"
						/>
						<path class="muscle left traps" data-muscle-id="1"
						d="	M 115,52 
							C 125,68 143,57 150,76
							C 134,76 115,70 115,52"
						/>
						<path class="muscle right shoulders" data-muscle-id="2"
						d="	M 50,76 
							C 33,75 21,94 28,107
							C 43,104 59,90 50,76"
						/>
						<path class="muscle left shoulders" data-muscle-id="2"
						d="	M 150,76 
							C 164,75 179,94 172,107
							C 157,104 141,90 150,76"
						/>
						<path class="muscle right triceps" data-muscle-id="5"
						d="	M 27,111 
							C 32,110 38,109 42,105
							C 32,114 40,129 26,136
							C 23,124 25,115 27,111"
						/>
						<path class="muscle left triceps" data-muscle-id="5"
						d="	M 173,111 
							C 168,110 162,109 158,105
							C 168,114 160,129 174,136
							C 177,124 175,115 173,111"
						/>
						<path class="muscle right biceps" data-muscle-id="4"
						d="	M 65,116 
							C 60,128 54,143 42,142
							C 43,124 44,113 46,104
							C 52,113 59,114 64,116"
						/>
						<path class="muscle left biceps" data-muscle-id="4"
						d="	M 135,116 
							C 140,128 146,143 158,142
							C 157,124 156,113 154,104
							C 148,113 141,114 136,116"
						/>
						<path class="muscle right forearms" data-muscle-id="6"
						d="	M 17,154 
							C 23,145 33,137 38,135
							C 33,158 49,162 24,198
							C 17,182 15,172 17,154"
						/>
						<path class="muscle left forearms" data-muscle-id="6"
						d="	M 183,154 
							C 177,145 167,137 162,135
							C 167,158 151,162 176,198
							C 183,182 185,172 183,154"
						/>
						<path class="muscle right chest" data-muscle-id="3"
						d="	M 52,100 
							C 63,80 78,72 96,68
							C 100,70 100,100 96,118
							C 80,118 60,107 52,100"
						/>
						<path class="muscle left chest" data-muscle-id="3"
						d="	M 148,100 
							C 137,80 122,72 104,68
							C 100,70 100,100 104,118
							C 120,118 140,107 148,100"
						/>
						<path class="muscle right obliques" data-muscle-id="10"
						d="	M 70,118 
							C 59,143 84,156 87,170
							C 85,150 83,132 82,123
							C 77,122 74,120 70,118"
						/>
						<path class="muscle left obliques" data-muscle-id="10"
						d="	M 130,118 
							C 141,143 116,156 113,170
							C 115,150 117,132 118,123
							C 123,122 126,120 130,118"
						/>
						<path class="muscle right abs" data-muscle-id="11"
						d="	M 87,123
							C 89,148 90,190 100,192
							C 100,170 100,150 100,125
							C 95,125 91,124 87,123"
						/>
						<path class="muscle left abs" data-muscle-id="11"
						d="	M 113,123
							C 111,148 110,190 100,192
							C 100,170 100,150 100,125
							C 105,125 109,124 113,123"
						/>
						<path class="muscle right quads" data-muscle-id="13"
						d="	M 61,188
							C 53,223 50,260 69,280
							C 72,284 76,280 79,282
							C 82,248 85,232 86,220
							C 94,205 71,203 61,188"
						/>
						<path class="muscle left quads" data-muscle-id="13"
						d="	M 139,188
							C 147,223 150,260 131,280
							C 128,284 124,280 121,282
							C 118,248 115,232 114,220
							C 106,205 129,203 139,188"
						/>
						<path class="muscle right calves" data-muscle-id="15"
						d="	M 69,280
							C 61,298 61,315 65,334
							C 69,316 71,295 69,280"
						/>
						<path class="muscle right calves" data-muscle-id="15"
						d="	M 95,284
							C 80,288 79,327 90,353
							C 94,334 96,312 95,284"
						/>
						<path class="muscle left calves" data-muscle-id="15"
						d="	M 131,280
							C 139,298 139,315 135,334
							C 131,316 129,295 131,280"
						/>
						<path class="muscle left calves" data-muscle-id="15"
						d="	M 105,284
							C 120,288 121,327 110,353
							C 106,334 104,312 105,284"
						/>
					</g>
				<text x="100" y="415" text-anchor="middle" fill="white" stroke="none">Front</text>
				</g>
				<g class="body back" stroke="white" stroke-width="0.5" fill="none" transform="translate(200,0)">
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
					<g class="muscles" stroke-width="0.5">
						<path class="muscle left traps" data-muscle-id="1"
						d="	M 97,38 
							C 75,68 57,57 50,76
							C 70,70 75,83 78,90 
							L 95,110
							C 97,97 93,72 89,71
							C 95,58 97,44 97,38"
						/>
						<path class="muscle right traps" data-muscle-id="1"
						d="	M 103,38 
							C 125,68 143,57 150,76
							C 130,70 125,83 122,90 
							L 105,110
							C 103,97 103,72 111,71
							C 105,58 103,44 103,38"
						/>
						<path class="muscle left middle-back" data-muscle-id="8"
						d="	M 85,105
							L 100,115
							L 100,138
							L 85,105"
						/>
						<path class="muscle right middle-back" data-muscle-id="8"
						d="	M 115,105
							L 100,115
							L 100,138
							L 115,105"
						/>
						<path class="muscle left shoulders" data-muscle-id="2"
						d="	M 50,76
							C 33,75 21,94 28,107
							C 40,103 51,93 58,88
							C 56,82 53,78 50,76"
						/>
						<path class="muscle right shoulders" data-muscle-id="2"
						d="	M 150,76
							C 167,75 179,94 172,107
							C 160,103 149,93 142,88
							C 144,82 147,78 150,76"
						/>
						<path class="muscle left triceps" data-muscle-id="5"
						d="	M 28,107
							Q 22,116 25,138
							C 29,127 32,122 37,120
							C 40,126 42,134 38,145
							C 50,143 60,140 60,124
							C 59,116 56,110 54,105
							C 44,109 32,109 28,107"
						/>
						<path class="muscle right triceps" data-muscle-id="5"
						d="	M 172,107
							Q 178,116 175,138
							C 171,127 168,122 163,120
							C 160,126 158,134 162,145
							C 150,143 140,140 140,124
							C 141,116 144,110 146,105
							C 156,109 168,109 172,107"
						/>
						<path class="muscle left forearms" data-muscle-id="6"
						d="	M 25,138
							Q 13,152 17,184
							Q 27,159 25,138"
						/>
						<path class="muscle right forearms" data-muscle-id="6"
						d="	M 175,138
							Q 187,152 183,184
							Q 173,159 175,138"
						/>
						<path class="muscle left lats" data-muscle-id="7"
						d="	M 59,98
							C 53,136 62,154 77,164
							C 74,146 80,142 95,140
							L 79,104
							C 74,106 65,104 59,98"
						/>
						<path class="muscle right lats" data-muscle-id="7"
						d="	M 141,98
							C 147,136 138,154 123,164
							C 126,146 120,142 105,140
							L 121,104
							C 126,106 135,104 141,98"
						/>
						<path class="muscle left lower-back" data-muscle-id="9"
						d="	M 100,142
							C 93,146 86,157 85,164
							C 86,172 97,192 100,196
							L 100,142"
						/>
						<path class="muscle right lower-back" data-muscle-id="9"
						d="	M 100,142
							C 107,146 114,157 115,164
							C 114,172 103,192 100,196
							L 100,142"
						/>
						<path class="muscle left glutes" data-muscle-id="12"
						d="	M 63,208
							Q 59,220 61,236
							Q 81,238 91,231
							Q 97,217 93,208
							Q 75,199 63,208"
						/>
						<path class="muscle right glutes" data-muscle-id="12"
						d="	M 137,208
							Q 141,220 139,236
							Q 119,238 109,231
							Q 103,217 107,208
							Q 125,199 137,208"
						/>
						<path class="muscle left hamstrings" data-muscle-id="14"
						d="	M 60,242
							C 65,258 69,280 85,281
							Q 87,256 90,239
							Q 69,244 60,242"
						/>
						<path class="muscle right hamstrings" data-muscle-id="14"
						d="	M 140,242
							C 135,258 131,280 115,281
							Q 113,256 110,239
							Q 131,244 140,242"
						/>
						<path class="muscle left calves" data-muscle-id="15"
						d="	M 73,280
							Q 60,313 80,354
							Q 91,322 90,296
							Q 78,290 73,280"
						/>
						<path class="muscle right calves" data-muscle-id="15"
						d="	M 127,280
							Q 140,313 120,354
							Q 109,322 110,296
							Q 122,290 127,280"
						/>
					</g>
				<text x="100" y="415" text-anchor="middle" fill="white" stroke="none">Back</text>
				</g>
			</svg>
			
		</section>
		
		<section class='column equipment-selection'>
			<h2>Equipment</h2>
			<div>
<?php
			foreach ($equipments as $e) {
				$str = "<span class='equipment-input'><label for='equipment-$e->id'>$e->name</label><input type='checkbox' name='equipments[]' id='equipment-$e->id' value='$e->id' class='equipment' ";
				if (in_array($e->id, $exercise->equipments)) {
					$str .= " checked ";
				}
				$str .= " ></span>";
				echo $str;
			}			
?>
			</div>
		</section>
		
		<section class='best-lifts'>
			<h2>Best Lifts</h2>
<?php
			/*
			foreach ($workout_structures as $wos) {
				$str = "<div class='workout-structure'>";
				$str .= "<h3>$wos->name</h3>";
				$str .= "<h4>$wos->best_total_reps reps @ $wos->best_weight lbs</h4>";
				$str .= "</div>";
				echo $str;
			}
			*/
?>			
		</section>
		
		<section>
			<button id='submit'>Update/Create</button>
		</section>
		
		<?php // TEST var_dump($muscle_objects); ?>
		
	</main>

	<script>
		/*
		$("svg path.muscle").each(function() {
			let muscleID = $(this).attr("data-muscle-id");
			//let muscleHue = 0;
			//let muscleLit = 100;
			if ( primMuscles.includes(muscleID) ) {
				//muscleHue = 0;
				//muscleLit = 50;
				$(this).addClass('primary');
			}
			else if ( secMuscles.includes(muscleID) ) {
				//muscleHue = 50;
				//muscleLit = 50;
				$(this).addClass('secondary');
			}
			$(this).on('click', function() {
				if ( $(this).hasClass('primary') ) {
					$("svg path.muscle").each(function() {
						if ( $(this).attr('data-muscle-id') == muscleID ) {
							$(this).removeClass('primary');
						}
					});
				}
				else if ( $(this).hasClass('secondary') ) {
					$("svg path.muscle").each(function() {
						if ( $(this).attr('data-muscle-id') == muscleID ) {
							$(this).addClass('primary').removeClass('secondary');
						}
					});
				}
				else {
					$("svg path.muscle").each(function() {
						if ( $(this).attr('data-muscle-id') == muscleID ) {
							$(this).addClass('secondary');
						}
					});
				}
			});
			*/
			/*
			let relMuscle = muscleObjects.filter(obj => {
				return obj.id === muscleID;
			});
			let muscleObj = relMuscle[0];
			let muscleHUR = muscleObj['hur'];
			let muscleHue = muscleHUR * (120 / muscleObj['ideal_rest']);
			if (muscleHue < 0) {
				muscleHue = 0;
			}
			else if (muscleHue > 120) {
				muscleHue = 120;
			}
			*/

			//$(this).css('fill', `hsl(${muscleHue}, 100%, ${muscleLit}%)`);
			/*
			let percIdeal = muscleObj['perc_ideal'];
			// If the muscle is ready and far from ideal or if the muscle has been ready for more than a week then apply the flashing class
			if ((percIdeal < 90 && muscleHUR <= 0) || muscleHUR < (-1 * 7 * 24))  {
				$(this).addClass('flashing');
			}
			*/
		/*
		});
<?php
		/*foreach ($exercise->p_muscles as $pm) {
			echo "$('svg path.muscle.$pm').css('fill', `hsl(0, 100%, 50%)`);";
		}
		foreach ($exercise->s_muscles as $sm) {
			echo "$('svg path.muscle.$sm').css('fill', `hsl(50, 100%, 50%)`);";
		}
		*/
?>
		*/
		function changeColorOfEquipmentSpans() {
			$('input.equipment').each(function() {
				if ($(this).is(':checked')) {
					$(this).parent('span').addClass('checked');
				}
				else {
					$(this).parent('span').removeClass('checked');
				}
			})
		}
		changeColorOfEquipmentSpans();
		$('input.equipment').on('change', function() {
			changeColorOfEquipmentSpans();
		});
		
		// If the exercise selection was changed then update the page information
		$('section.exercise-selection select').on('change', function() {
			$('svg path.muscle').removeClass('primary').removeClass('secondary');
			$('input.equipment').each(function() {
				$(this).attr('checked', false);
			});
			changeColorOfEquipmentSpans();
			$('section.best-lifts').html('<h2>Best Lifts</h2>');
			
			//console.log('changed');
			thisExerciseID = $(this).val();
			thisExerciseName = $(this).html();
			
			if (thisExerciseID == 'new') {
				$('button#submit').html('Create');
				$('input.exercise-name-input').css('display', 'block');
				$('section.best-lifts').css('display', 'none');
				$('h1.exercise-name').css('display', 'none');
			}
			else {
				$('button#submit').html('Update');
				$('input.exercise-name-input').css('display', 'none');
				$('section.best-lifts').css('display', 'inline-flex');
				$('h1.exercise-name').css('display', 'block');
				//console.log('existent');
				$.ajax({
					url: '/homebase/resources/forms/form-resources/return_exercise_info.php',
					method: 'POST',
					data: {
						'exercise-id' : thisExerciseID,
					},
					success: function(data) {
						selectedExercise = jQuery.parseJSON( data );
						console.log(selectedExercise);
						$('h1.exercise-name').html(`${selectedExercise['name']} ( #${selectedExercise['id']} )`);
						$(document).attr("title", `Exercise - ${selectedExercise['name']}`);
						if (selectedExercise['ref_url'] != '' && selectedExercise['ref_url'] != 'null') {
							$('input.exercise-href-input').val(`${selectedExercise['ref_url']}`);
						}
						else {
							$('input.exercise-href-input').val('');
						}
						if (selectedExercise['desc'] != '' && selectedExercise['desc'] != 'null') {
							$('textarea.exercise-desc-input').val(`${selectedExercise['desc']}`);
						}
						else {
							$('textarea.exercise-desc-input').val('');
						}
						// Set muscle class appropriately
						$('svg path.muscle').each(function() {
							let muscleID = $(this).attr("data-muscle-id");
							//let muscleHue = 0;
							//let muscleLit = 100;
							if ( selectedExercise['p_muscles'].includes(muscleID) ) {
								//muscleHue = 0;
								//muscleLit = 50;
								$(this).addClass('primary');
							}
							else if ( selectedExercise['s_muscles'].includes(muscleID) ) {
								//muscleHue = 50;
								//muscleLit = 50;
								$(this).addClass('secondary');
							}
							$(this).on('click', function() {
								if ( $(this).hasClass('primary') ) {
									$("svg path.muscle").each(function() {
										if ( $(this).attr('data-muscle-id') == muscleID ) {
											$(this).removeClass('primary');
										}
									});
								}
								else if ( $(this).hasClass('secondary') ) {
									$("svg path.muscle").each(function() {
										if ( $(this).attr('data-muscle-id') == muscleID ) {
											$(this).addClass('primary').removeClass('secondary');
										}
									});
								}
								else {
									$("svg path.muscle").each(function() {
										if ( $(this).attr('data-muscle-id') == muscleID ) {
											$(this).addClass('secondary');
										}
									});
								}
							});
						});
						// Set required equipment to checked
						$('input.equipment').each(function() {
							let equipmentID = $(this).val();
							if (selectedExercise['equipments'].includes(equipmentID)) {
								$(this).attr('checked', true);
							}
						});
						changeColorOfEquipmentSpans();
						// Display best lift info
						//$('section.best-lifts').html('<h2>Best Lifts</h2>');
						$.each(selectedExercise['workout_structures'], function( i, l ) {
							let newDiv = $('<div>', {'class' : 'workout-structure'});
							newDiv.append(`<h3>${l['name']}</h3>`);
							newDiv.append(`<h4>${l['best_total_reps']} @ ${l['best_weight']}</h4>`);
							$('section.best-lifts').append(newDiv);
						});
					}
				});
			}
		});
		
		
		
		
		$('button#submit').on('click', function() {
			//console.log('submitted');
			// If new exercise is created then we need to call an ajax function to create the exercise
			if ( $('section.exercise-selection select').val() == 'new' ) {
				let newExercise = {
					name : $('input.exercise-name-input').val(),
					p_muscles: [],
					s_muscles: [],
					equipments : [],
				};
				$('svg path.muscle').each(function() {
					let muscleID = $(this).attr("data-muscle-id");
					if ($(this).hasClass('primary') && ! newExercise['p_muscles'].includes(muscleID)) {
						newExercise['p_muscles'].push(muscleID);
					}
					else if ($(this).hasClass('secondary') && ! newExercise['s_muscles'].includes(muscleID)) {
						newExercise['s_muscles'].push(muscleID);
					}
				});
				$('input.equipment').each(function() {
					if ($(this).is(':checked')) {
						let equipmentID = $(this).val();
						newExercise['equipments'].push(equipmentID);
					}
				});
				
				
				
				//console.log(newExercise);
				$.ajax({
					url: '/homebase/resources/forms/form-resources/create_exercise.php',
					method: 'POST',
					data: {
						'new-exercise' : JSON.stringify(newExercise),
					},
					success: function(data) {
						if (data == 'success') {
							location.reload();
						}
						else {
							console.log(data);
						}
					}
				});
			}
			else {
				
			}
		});
		
		
	</script>

</body>