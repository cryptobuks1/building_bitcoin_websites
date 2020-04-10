<?php
    require('./config.inc.php');
    require('./easybitcoin.php');
    session_start();
    $_SESSION['nuid'] = -1;

    $error_username = '';
    $error_password = '';

    if($_GET['run'] == "login")
    {
        $username = $_POST['username'];
        $password = $_POST['pw'];

        mysqli_set_charset($conn, 'utf8');
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        $loginQuery = mysqli_query($conn, "SELECT * FROM game_users WHERE username = '$username'");
        $loginData = mysqli_fetch_assoc($loginQuery) or die(mysqli_error($conn));

        if(trim($username) == '')
        {
            // no username
            $error_username = 'You must enter a username.';
        }

        if(trim($username) != $loginData['username'])
        {
            // no username
            $error_username = 'Incorrect username.';
        }

        if(trim($password) == '')
        {
            // no password
            $error_password = 'You must enter a password.';
        }
        
        if(!password_verify(trim($password), $loginData['userpass']))
        {
            // incorrect password
            $error_password = 'Incorrect password.';
        }
    
        if($error_username != '' || $error_password != '')
        {
            output($error_username, $error_password);
            die();
        }

        // Input checks passed, continue login
        $userid = $loginData['userid'];
        $_SESSION['nuid'] = $userid;

        header('Location: game.php');
    }
    else
    {
        output($error_username, $error_password);
        die();
    }

    function output($error_username, $error_password)
    {
?>

<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>
        <form method="post" action="?run=login">
            Username:<br><input type="text" id="username" maxlength="20"> <?php echo $error_username; ?><br>
            Password:<br><input type="password" id="pw" name="pw"> <?php echo $error_password; ?><br>
            <input type="submit" name="submit" value="Login"/>
        </form>
    </body>
</html>

<?php
    }
?>