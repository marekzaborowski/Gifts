<?php

	session_start();
	
	if (!isset($_SESSION['udanarejestracja']))
	{
		header('Location: index.php');
		exit();
	}
	
	//Usuwanie zmiennych pamiętających wartości wpisane do formularza
	if (isset($_SESSION['fr_nick'])) unset($_SESSION['fr_nick']);
	if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if (isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
	if (isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
	
	//Usuwanie błędów rejestracji
	if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
	if (isset($_SESSION['e_uzytkownik'])) unset($_SESSION['e_uzytkownik']);
	if (isset($_SESSION['e_kod'])) unset($_SESSION['e_kod']);
	if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>prezenty</title>
	<?php
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    ?>
	<link rel="stylesheet" type="text/css" href="wyglad.css">
</head>

<body>
	<div style="text-align: center">
		<h1>Rejestracja przebiegła pomyślnie! Przejdź z powrotem do strony głównej, 
		żeby zalogować się na swoje konto klikając poniższy link.</h1>
		<a style="font-size: 40px" href="index.php">Zaloguj się na swoje konto!</a>
	</div>
	
	
	
	<br /><br />

</body>
</html>