<?php
    if (empty($_POST['username']) || empty($_POST['password'])) {
        // Do nothing
    }
    else { // Check credentials
        $u = $_POST['username'];
        $p = $_POST['password'];
        if ($u == 'bbrewster' && $p == 'test') {
            session_start();
            $_SESSION['logged_in']  = TRUE;
            $_SESSION['admin']  = TRUE;
            header("HTTP/1.0 200 OK");
            header("Location: /homebase/");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>HomeBase Login</title>
</head>

<body>
    <form action="" method="post">
        <span class='flex-input' style=''>
            <label for='username'>Username</label>
            <input type='text' name='username' id='username' value='' placeholder='' />
        </span>
        <span class='flex-input' style=''>
            <label for='password'>Password</label>
            <input type='password' name='password' id='password' value='' placeholder='' />
        </span>
        <input type="submit" value="Login">
    </form>
</body>

</html>