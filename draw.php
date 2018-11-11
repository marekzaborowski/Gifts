<?php
	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}

	require_once "connect.php";
	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if ($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}

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

	//Pobieram id połączenia (jeżeli istnieje) dla zalogowanego użytkownika.dziecka
	if ($rezultat = @$polaczenie->query("SELECT id, iddziecka FROM polaczenia WHERE iddziecka='".$_SESSION['id']."'"))
	{
		$ile = $rezultat->num_rows;
		if($ile>0)
		{
			$wiersz = $rezultat->fetch_assoc();
			$_SESSION['idpolaczenia_dladziecka']=$wiersz['id'];
		}
	}
	else
	echo "Error: ".$polaczenie->error;

	//Pobieram id połączenia dla zalogowanego użytkownika.mikołaja
	if ($rezultat = @$polaczenie->query("SELECT id, iddziecka FROM polaczenia WHERE idmikolaja='".$_SESSION['id']."'"))
	{
		$wiersz = $rezultat->fetch_assoc();
		$_SESSION['idpolaczenia_dlamikolaja']=$wiersz['id'];
		//Sprawdzam czy zalogowany użytkownik już losował
		if($wiersz['iddziecka']!=0)
		{
			if($_SESSION['czy_wszyscy_losowali']==true)
			{
				header('Location: chat.php');
			}
			else
			{
				header('Location: waiting_room.php');
			}
			$polaczenie->close();
			exit();
		}
	}
	else
	echo "Error: ".$polaczenie->error;

	//Wyjątek dla sytuacji kiedy zachodzi niebezpieczeństwo, 
	//że ostatni losujący będzie miał do wylosowania wyłącznie siebie 
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
	<?php
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    ?>
	<link rel="stylesheet" type="text/css" href="wyglad.css">
</head>
<body>
<?php
	echo "<h2><p>Witaj ".$_SESSION['nick'].'! [ <a href="logout.php">Wyloguj się!</a> ]</p></h2> <br>';
	//Sprawdzam czy użytkownik wcisnął przycisk "LOSUJ"
	if(!isset($_POST['losowanie']))
	{
		echo "<div id=\"losowanie\" >Losuj, komu będziesz robił prezent w tym roku:<br><br>"; 
		
		echo '<form method="post">';
			echo '<input style="width: 250px; height: 75px; font-size: 40px;" type="submit" value="LOSUJ">' ;
			echo '<input type="hidden" value="wylosuj" name="losowanie" >' ;
		echo '</form></div>';
	}//Sprawdzam który przycisk jest aktywny 
	elseif($_POST['losowanie']=="wylosuj")
	{	
		$liczba=0;
		if(isset($wyjatek_losowania))//jeżeli zachodzi wyjatek to z automatu przypisujemy użytkownika bez polosowania 
		{
			$liczba = $wyjatek_losowania;
		}
		else//jezeli wyjatek nie zachodzi przechodze do procedury losowania użytkownika
		{
			do
			{
				$liczba=rand(1,5);
				$poprawna=true;
	
				if ($rezultat = @$polaczenie->query("SELECT status FROM uzytkownicy where id='$liczba'"))
				{
					$wiersz = $rezultat->fetch_assoc();
					$status = $wiersz['status'];
				}
				else
				echo "Error: ".$polaczenie->error;
	
				if($liczba==$_SESSION['id'] || $status==true)
				{
					$poprawna=false;
				}
			}
			while($poprawna != true);
		}
		//procedura tworzenia połączenia mikołaja z dzieckiem i vice versa
		if ($rezultat = @$polaczenie->query("SELECT nick FROM uzytkownicy WHERE id='$liczba'"))
		{
			$ile = $rezultat->num_rows;
			if($ile>0)
			{
				$wiersz = $rezultat->fetch_assoc();
				$nazwadziecka = $wiersz['nick'];
				echo '<div id="wylosowane">';
				echo 'W tym roku robisz prezent:<br>';
				echo "<b>".$nazwadziecka."</b><br>";

				if (!@$polaczenie->query("UPDATE polaczenia SET iddziecka='$liczba' WHERE idmikolaja='".$_SESSION['id']."'"))
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
?>
		W celu rozpoczęcia czatu ze swoim tegorocznym mikołajem  <br>
		oraz z osobą bądź osobami, którym robisz prezenty kliknij poniższy przycisk. <br>
		<form method="post">
			<input type="submit" style="width: 400px; height: 75px; font-size: 30px;" value="Przejdź do czatu"/>
			<input type="hidden" value="wylosowana" name="losowanie"/>
		</form>
		</div>
<?php
	}//jeżeli poprzednie pytania nie przeszły testu to znaczy że użytkownik wcisnął przycisk "PRZEJDZ DO CZATU"
	else
	{
		if($_SESSION['czy_wszyscy_losowali']==true)
		{
			header('Location: chat.php');
		}
		else
		{
			header('Location: waiting_room.php');
		}
		$polaczenie->close();
	}	
?>
</body>
</html>