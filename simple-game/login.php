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

        $loginQuery = $conn->prepare("SELECT * FROM game_users WHERE username = :username");
        $loginQuery->bindParam(':username', $username);

        $loginQuery->execute();

        $loginData = $loginQuery->fetchAll(PDO::FETCH_ASSOC);

        $passCheck = password_verify(trim($password), $loginData[0]['userpass']);

        if(trim($username) == '')
        {
            // no username
            $error_username = 'You must enter a username.';
        }

        if(trim($username) != $loginData[0]['username'])
        {
            // no username
            $error_username = 'Incorrect username.';
        }

        if(trim($password) == '')
        {
            // no password
            $error_password = 'You must enter a password.';
        }

        if(!$passCheck)
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
        $userid = $loginData[0]['userid'];
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
            Username:<br><input type="text" id="username" name="username" maxlength="20"> <?php echo $error_username; ?><br>
            Password:<br><input type="password" id="pw" name="pw"> <?php echo $error_password; ?><br>
            <input type="submit" name="submit" value="Login"/>
        </form>
    </body>
</html>

<?php
    }
?>