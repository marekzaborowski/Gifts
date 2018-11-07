<?php

	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}

	require_once "connect.php";
	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if ($rezultat = @$polaczenie->query("SELECT id FROM polaczenia WHERE idmikolaja='".$_SESSION['id']."'"))
	{
		$wiersz = $rezultat->fetch_assoc();
		if($wiersz['id']!=null)
		{
			$_SESSION['idpolaczenia_dlamikolaja']=$wiersz['id'];
		}
	}
	else
	echo "Error: ".$polaczenie->error;

	if ($rezultat = @$polaczenie->query("SELECT iddziecka, id FROM polaczenia WHERE idmikolaja='".$_SESSION['id']."'"))
	{
		$wiersz = $rezultat->fetch_assoc();
		if($wiersz['iddziecka']!=0)
		{
			$_SESSION['idpolaczenia_dladziecka']=$wiersz['id'];
			header('Location: chat.php');
			exit();
		}
	}
	else
	echo "Error: ".$polaczenie->error;

	if ($rezultat = @$polaczenie->query("SELECT id FROM uzytkownicy WHERE status = false"))
	{
		$ile = $rezultat->num_rows;
		if($ile==2)
		{

			if ($rezultat = @$polaczenie->query("SELECT uzytkownicy.id FROM uzytkownicy, polaczenia WHERE 
			status = false AND uzytkownicy.id NOT LIKE '".$_SESSION['id']."' AND iddziecka = 0 AND uzytkownicy.id=polaczenia.idmikolaja"))
			{
				$ile = $rezultat->num_rows;
				if($ile>0)
				{
					$wiersz = $rezultat->fetch_assoc();
					$wyjatek_losowania=$wiersz['id'];
				}
			}
			else
			echo "Error: ".$polaczenie->error;
		}
	}
	else
	echo "Error: ".$polaczenie->error;
	
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>prezenty - losowanie</title>
</head>
<body>
<?php
	echo "<p>Witaj ".$_SESSION['nick'].'! [ <a href="logout.php">Wyloguj się!</a> ]</p> <br>';
	if(!isset($_POST['obojetnie']))
	{
		echo "Losuj komu będziesz robił prezent w tym roku:"; 
		
		echo '<form method="post">';
			echo '<input type="submit" value="LOSUJ" name="losowanie">' ;
			echo '<input type="hidden" value="wylosuj" name="obojetnie" >' ;
		echo '</form>';
	}

	if ($polaczenie->connect_errno!=0)
		{
			echo "Error: ".$polaczenie->connect_errno;
		}
	else
	{
		if ($rezultat = @$polaczenie->query("SELECT id FROM uzytkownicy WHERE nick='".$_SESSION['nick']."'"))
		{
			$ile = $rezultat->num_rows;
			if($ile>0)
			{
				$wiersz = $rezultat->fetch_assoc();
				$iduzytkownika = $wiersz['id'];
			}
			else 
			{
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy email lub hasło!</span>';
				header('Location: index.php');
			}
		}
		else
		echo "Error: ".$polaczenie->error;
	}

	if (isset($_POST['obojetnie']) && $_POST['obojetnie']=="wylosuj")
	{	
		$liczba=0;
		if(isset($wyjatek_losowania))
		{
			$liczba = $wyjatek_losowania;
		}
		else
		{
			do
			{
				$liczba=rand(1,5);
				$poprawna=true;
	
				if ($rezultat = @$polaczenie->query("SELECT status FROM uzytkownicy where id='$liczba' "))
				{
					$wiersz = $rezultat->fetch_assoc();
					$status = $wiersz['status'];
				}
				else
				echo "Error: ".$polaczenie->error;
	
				if($liczba==$iduzytkownika || $status==true)
				{
					$poprawna=false;
				}
			}
			while($poprawna != true);
		}
		
		if ($rezultat = @$polaczenie->query("SELECT nick FROM uzytkownicy WHERE id='$liczba'"))
		{
			$ile = $rezultat->num_rows;
			if($ile>0)
			{
				$wiersz = $rezultat->fetch_assoc();
				$nazwadziecka = $wiersz['nick'];
				echo $nazwadziecka;

				if (!@$polaczenie->query("UPDATE polaczenia SET iddziecka='$liczba' WHERE idmikolaja='$iduzytkownika'"))
				{
					echo "Error: ".$polaczenie->error;
				}
				if (!@$polaczenie->query("UPDATE uzytkownicy SET status=true WHERE id='$liczba'"))
				{
					echo "Error: ".$polaczenie->error;
				}
			}
			else 
			{
				echo "blad";
			}
		}
		else
		echo "Error: ".$polaczenie->error;

		echo '<form method="post">';
			echo '<input type="submit" value="Przejdz do czatu" name="switch_to_chat"/>';
			echo '<input type="hidden" value="wylosowana" name="obojetnie"/>';
		echo '</form>';
	}
	if(isset($_POST['obojetnie']) && $_POST['obojetnie']=="wylosowana")
	{
		header('Location: chat.php');
		$polaczenie->close();
	}	
?>
</body>
</html>