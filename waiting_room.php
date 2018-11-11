<?php
    session_start();
        
    if(!isset($_SESSION['zalogowany']))
    {
        header('Location: index.php');
        exit();
    }

    require_once "connect.php";
    $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
    
    //Sprawdzam czy wszyscy użytkownicy już losowali
	if ($rezultat = @$polaczenie->query("SELECT id FROM polaczenia WHERE iddziecka = 0"))
	{
		$_SESSION['czy_wszyscy_losowali']=true;
		$ile = $rezultat->num_rows;
		if($ile>0)
		{
			$_SESSION['czy_wszyscy_losowali']=false;
		}
	}
	else
    echo "Error: ".$polaczenie->error;
    
    if($_SESSION['czy_wszyscy_losowali']==true)
    {
        header('Location: chat.php');
        $polaczenie->close();
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <?php
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    ?>
	<link rel="stylesheet" type="text/css" href="wyglad.css">
</head>
<body>
    <a style="font-size: 30px" href="logout.php">Wyloguj</a><br>
    <div id="room">
        Wygląda na to, że jeszcze nie wszyscy użytkownicy wzięli udział w losowaniu. 
        Spróbuj odświeżyć witrynę bądź zalogować się ponownie później.
    </div>
</body>
</html>