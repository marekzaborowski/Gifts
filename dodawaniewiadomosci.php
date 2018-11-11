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
    
    if(isset($_POST['wiadomosc_do_dziecka']))
    {
        if (!@$polaczenie->query("INSERT INTO wiadomosci VALUES (NULL,'".$_SESSION['idpolaczenia_dlamikolaja']."','".$_POST['wiadomosc_do_dziecka']."','".$_SESSION['id']."')"))
        {
            echo "Error: ".$polaczenie->error;
        }
        header('Location: chat.php');
    }
    else//if(isset($_POST['wiadomosc_do_mikolaja']))
    {
        if (!@$polaczenie->query("INSERT INTO wiadomosci VALUES (NULL,'".$_SESSION['idpolaczenia_dladziecka']."','".$_POST['wiadomosc_do_mikolaja']."','".$_SESSION['id']."')"))
        {
            echo "Error: ".$polaczenie->error;
        }
        header('Location: chat.php');
    }
?>