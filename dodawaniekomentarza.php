<?php

	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
        header('Location: index.php');
        echo "blad";
		exit();
    }

    require_once "connect.php";
    $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
    
    if (!@$polaczenie->query("INSERT INTO komentarze VALUES (NULL,'".$_POST['idwiadomosci']."','".$_POST['komentarz']."','".$_POST['nadawca']."')"))
    {
        echo "Error: ".$polaczenie->error;
    }
    header('Location: chat.php');
?>