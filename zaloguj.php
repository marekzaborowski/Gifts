<?php

	session_start();
	
	if ((!isset($_POST['email'])) || (!isset($_POST['haslo'])))
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
	else
	{
		/*if ($rezultat = @$polaczenie->query("SELECT * FROM polaczenia WHERE idmikolaja='".$_SESSION['id']."'"))
		{
			$wiersz = $rezultat->fetch_assoc();
			$_SESSION['idmikolaja']=$wiersz['idmikolaja'];
			if($_SESSION['idmikolaja']==null)
			{
				header('Location: chat.php');
				$polaczenie->close();
			}
		}
		else
		echo "Error: ".$polaczenie->error;*/

		$email = $_POST['email'];
		$haslo = $_POST['haslo'];
	
		if ($rezultat = @$polaczenie->query("SELECT * FROM uzytkownicy WHERE email='$email'"))
		{
			$ile_email = $rezultat->num_rows;
			if($ile_email>0)
			{
				$wiersz = $rezultat->fetch_assoc();
				echo $haslo." ";
				echo $wiersz['haslo']." ";
				echo password_hash($haslo, PASSWORD_DEFAULT);
				if (password_verify($haslo, $wiersz['haslo']))
				{
					$_SESSION['zalogowany'] = true;
					$_SESSION['nick'] = $wiersz['nick'];
					$_SESSION['id'] = $wiersz['id'];
					$_SESSION['email'] = $wiersz['email'];
					//echo '<pre>'; print_r($_SESSION); echo '</pre>';
					unset($_SESSION['blad']);
					$rezultat->free_result();
					header('Location: draw.php');
				}
				else 
				{
					$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy email lub hasło!</span>';
					header('Location: index.php');
				}
				
			} else {
				
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy email lub hasło!</span>';
				header('Location: index.php');
			}
			
		}
		else
		echo "Error: ".$polaczenie->error;
		
		$polaczenie->close();
	}
	
?>