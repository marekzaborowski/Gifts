<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		header('Location: draw.php');
		exit();
	}

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>prezenty</title>
	<?php
	//zapis konieczny do poprawnego wyświetlania się styli
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    ?>
	<link rel="stylesheet" type="text/css" href="wyglad.css">
</head>

<body>
	<div id="index">
		<div id="logo">
			<h1>Witajcie na stronie <br/> Prezenty 2.0</h1>
		</div>
		<div id="rejestracja">
			Jeżeli jesteś pierwszy raz na stronie, <br>
			zapraszam Cię do założenia konta. <br>
			Kliknij w przycisk poniżej, <br>
			żeby założyć swoje konto. <br><br>
			<div style="font-size: 40px"><b><a href="rejestracja.php">Rejestracja - załóż konto!</a></b></div>
		</div>
		<div id="logowanie">
			<form action="zaloguj.php" method="post">
				Email: <br /> <input style="width: 400px; height: 25px; font-size: 25px;" type="text" name="email" /> <br />
				Hasło: <br /> <input style="width: 400px; height: 25px; font-size: 40px;" type="password" name="haslo" /> <br /><br />
				<input type="submit" style="width: 300px; height: 100px; font-size: 40px;" value="Zaloguj się" />
			</form>
			<?php
				if(isset($_SESSION['blad']))
				{
					echo $_SESSION['blad'];
					unset($_SESSION['blad']);
				}	
			?>
		</div>
		<div style="clear:both"></div>
	</div>
</body>
</html>