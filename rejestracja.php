<?php

	session_start();
	
	if (isset($_POST['email']))
	{
		//Udana walidacja? Załóżmy, że tak!
		$wszystko_OK=true;
		
		// Sprawdź poprawność adresu email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$wszystko_OK=false;
			$_SESSION['e_email']="Podaj poprawny adres e-mail!";
		}
		
		//Sprawdź poprawność hasła
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
		
		if ((strlen($haslo1)<8) || (strlen($haslo1)>16))
		{
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Hasło musi posiadać od 8 do 16 znaków!";
		}
		
		if ($haslo1!=$haslo2)
		{
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Podane hasła nie są identyczne!";
		}	

		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);				
		
		//Sprawdź czy został zaznaczony użytkownik

		if(!isset($_POST['uzytkownik']))
		{
			$wszystko_OK=false;
			$_SESSION['e_uzytkownik']="Zaznacz kim jesteś!";
		}
		else
		$uzytkownik = $_POST['uzytkownik'];

		//Sprawdź czy został podany kod dostępu

		$kod = $_POST['kod'];

		if($kod=="")
		{
			$wszystko_OK=false;
			$_SESSION['e_kod']="Podaj kod dostępu!";
		}

		//Bot or not? Oto jest pytanie!
		$sekret = "6LeXKHQUAAAAAPGO0vjOKt9jh86ymW4JqYuJDOYQ";
		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if ($odpowiedz->success==false)
		{
			$wszystko_OK=false;
			$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}		
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_haslo1'] = $haslo1;
		$_SESSION['fr_haslo2'] = $haslo2;
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try 
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//Czy email już istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
				
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					$wszystko_OK=false;
					$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
				}		
				
				//Czy kod zgadza się z uzytkownikiem?
				if(isset($uzytkownik))
				{
					$rezultat = $polaczenie->query("SELECT * from uzytkownicy WHERE kod='$kod' AND nick='$uzytkownik'");

					if (!$rezultat) throw new Exception($polaczenie->error);
					
					$ile_takich_uzytkownikow = $rezultat->num_rows;
					if($ile_takich_uzytkownikow==0)
					{
						$wszystko_OK=false;
						$_SESSION['e_kod']="Podałeś zły kod dostępu";
					}	
				}
				
				//Czy istnieje już użytkownik przypisany do danego nicku?
				$rezultat = $polaczenie->query("SELECT email from uzytkownicy WHERE nick='$uzytkownik'");
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$wiersz = $rezultat->fetch_assoc();

				$czy_istnieje_juz_konto=$wiersz['email'];

				if($czy_istnieje_juz_konto!=null)
				{
					$wszystko_OK=false;
					$_SESSION['e_email']="Ten użytkownik posiada już konto!";
				}	

				if ($wszystko_OK==true)
				{
					//Hurra, wszystkie testy zaliczone, dodajemy uzytkownika do bazy
					
					if ($polaczenie->query("UPDATE uzytkownicy SET email='$email', haslo='$haslo_hash' WHERE nick='$uzytkownik'"))
					{
						$_SESSION['udanarejestracja']=true;
						header('Location: witamy.php');
					}
					else
					{
						throw new Exception($polaczenie->error);
					}
					
				}
				
				$polaczenie->close();
			}
			
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;
		}
		
	}
	
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>prezenty - rejestracja</title>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
	<style>
		.error
		{
			color:red;
			margin-top: 10px;
			margin-bottom: 10px;
		}
	</style>
</head>

<body>
	
	<form method="post">

		E-mail: <br /> <input type="text" value="<?php
			if (isset($_SESSION['fr_email']))
			{
				echo $_SESSION['fr_email'];
				unset($_SESSION['fr_email']);
			}
		?>" name="email" /><br />
		
		<?php
			if (isset($_SESSION['e_email']))
			{
				echo '<div class="error">'.$_SESSION['e_email'].'</div>';
				unset($_SESSION['e_email']);
			}
		?>
		
		Twoje hasło: <br /> <input type="password"  value="<?php
			if (isset($_SESSION['fr_haslo1']))
			{
				echo $_SESSION['fr_haslo1'];
				unset($_SESSION['fr_haslo1']);
			}
		?>" name="haslo1" /><br />
		
		<?php
			if (isset($_SESSION['e_haslo']))
			{
				echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
				unset($_SESSION['e_haslo']);
			}
		?>		
		
		Powtórz hasło: <br /> <input type="password" value="<?php
			if (isset($_SESSION['fr_haslo2']))
			{
				echo $_SESSION['fr_haslo2'];
				unset($_SESSION['fr_haslo2']);
			}
		?>" name="haslo2" /><br />

		Zaznacz kim jesteś: <br> 
		<input type="radio" name="uzytkownik" value="Mama&Tata"/>MAMA&TATA<br>
		<input type="radio" name="uzytkownik" value="Liza&Grzegorz"/>LIZA&GRZEGORZ<br>
		<input type="radio" name="uzytkownik" value="Dorota&Adam"/>DOROTA&ADAM<br>
		<input type="radio" name="uzytkownik" value="Ola&Michał"/>OLA&MICHAŁ<br>
		<input type="radio" name="uzytkownik" value="Marek"/>MAREK<br>

		<?php
			if (isset($_SESSION['e_uzytkownik']))
			{
				echo '<div class="error">'.$_SESSION['e_uzytkownik'].'</div>';
				unset($_SESSION['e_uzytkownik']);
			}
		?>

		Kod dostępu:<br>
		<input type="text" name="kod"/><br>

		<?php
			if (isset($_SESSION['e_kod']))
			{
				echo '<div class="error">'.$_SESSION['e_kod'].'</div>';
				unset($_SESSION['e_kod']);
			}
		?>
		
		<div class="g-recaptcha" data-sitekey="6LeXKHQUAAAAAOui24j0Ix2STymw77YyshMziJSb"></div>
		
		<?php
			if (isset($_SESSION['e_bot']))
			{
				echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
				unset($_SESSION['e_bot']);
			}
		?>	
		
		<br />
		
		<input type="submit" value="Zarejestruj się" />
		
	</form>

</body>
</html>