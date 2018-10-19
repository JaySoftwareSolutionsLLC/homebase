<?php

?>


<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <title>Lift Inputs</title>
<?php include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php'); ?>

</head>

<body>

	<main>
		
		<form action="/homebase/resources/forms/generate_lift.php" method="post">
			<label for='time-to-lift'>How many minutes do you have to lift?</label>
			<input type='number' placeholder='30' name='time-to-lift' autofocus>
			<button type="submit">Generate</button>
		</form>
		
	</main>

</body>