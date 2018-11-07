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
    
    if(isset($_POST['wiadomosc']))
    {
        $wiadomosc=$_POST['wiadomosc'];
        unset($_POST['wiadomosc']);
        if (!@$polaczenie->query("INSERT INTO wiadomosci VALUES (NULL,'".$_SESSION['idpolaczenia_dlamikolaja']."','".$wiadomosc."','".$_SESSION['id']."')"))
        {
            echo "Error: ".$polaczenie->error;
        }
        header('Location: chat.php');
    }
    else
    {
        if(isset($_POST['wiadomosc2']))
        {
            $wiadomosc2=$_POST['wiadomosc2'];
            unset($_POST['wiadomosc2']);
            if (!@$polaczenie->query("INSERT INTO wiadomosci VALUES (NULL,'".$_SESSION['idpolaczenia_dladziecka']."','".$wiadomosc2."','".$_SESSION['id']."')"))
            {
                echo "Error: ".$polaczenie->error;
            }
            header('Location: chat.php');
        }
    }
?>