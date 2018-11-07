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

    if ($rezultat = @$polaczenie->query("SELECT prezent, nadawca FROM wiadomosci WHERE idpolaczenia ='".$_SESSION['idpolaczenia_dlamikolaja']."'"))
	{
        $i=0;
        $w;
        $wartosc=true;
        do
        {
            $w=$rezultat->fetch_assoc();
            if($w != null)
            {
                $wiersz_dlamikolaja[$i]=$w;
                $i++;
            }
            else
            {
                $wartosc=false;
            }
        }
        while($wartosc==true); 
	}
	else
    echo "Error: ".$polaczenie->error;

    if ($rezultat2 = @$polaczenie->query("SELECT prezent, nadawca FROM wiadomosci WHERE idpolaczenia ='".$_SESSION['idpolaczenia_dladziecka']."'"))
	{
        $i=0;
        $w;
        $wartosc=true;
        do
        {
            $w=$rezultat2->fetch_assoc();
            if($w != null)
            {
                $wiersz_dladziecka[$i]=$w;
                $i++;
            }
            else
            {
                $wartosc=false;
            }
        }
        while($wartosc==true); 
	}
	else
    echo "Error: ".$polaczenie->error;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="wyglad.css">
    <?php
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
    ?>
</head>
<body>
    <div>
        <a href="logout.php">Wyloguj</a><br>
    </div>
    <div id="form">
        <div id="czat_z_dzieckiem">
            <form method="post" action="dodawaniewiadomosci.php">
                Napisz wiadomość do dziecka <input type="text" name="wiadomosc"/><br>
                <input type="submit" type="wyslanie_wiadomosci" value="Wyślij wiadomość"/> 
            </form>
            <div id="czat">
                <?php
                    if(isset($wiersz_dlamikolaja))
                    {
                        foreach($wiersz_dlamikolaja as $item) 
                        {
                            if($item['nadawca']==$_SESSION['id'])
                            {
                                echo '<div id="ja">';
                                    echo $item['prezent'];
                                    echo ' <input type="submit" value="Dodaj komentarz" />';
                                echo '</div>';
                            }
                            else
                            {
                                echo '<div id="on">';
                                    echo $item['prezent'];
                                    echo ' <input type="submit" value="Dodaj komentarz" />';
                                echo '</div>';
                            } 
                            echo "<br/>";
                        }
                    }
                ?>
            </div>
        </div>

        <div id="czat_z_mikolajem">
            <form  method="post" action="dodawaniewiadomosci.php">
                Napisz wiadomość do mikołaja <input type="text" name="wiadomosc2"/> <br>
                <input type="submit" name="wyslanie_wiadomosci2" value="Wyślij wiadomość"/>
            </form>
            <div id="czat2" >
                <?php
                    if(isset($wiersz_dladziecka))
                    {
                        foreach($wiersz_dladziecka as $_SESSION['item']) 
                        {
                            if($_SESSION['item']['nadawca']==$_SESSION['id'])
                            {
                                echo '<div id="ja">';
                                    echo '<form method="post" >';
                                        echo $_SESSION['item']['prezent'];
                                        echo ' <input type="submit" name="moj_komentarz" value="Dodaj komentarz" />';
                                        echo ' <input type="hidden" name="numer_indeksu"';
                                       // $zmienna = $_SESSION['item']['prezent'].key;
                                    echo '<form>';
                                    if(isset($_POST['moj_komentarz']))
                                    {
                                        echo "<div style='background-color: white'>";
                                            //echo "$zmienna";
                                        echo "</div>";
                                    }
                                echo '</div>';
                            }
                            else
                            {
                                echo '<div id="on">';
                                    echo $item['prezent'];
                                    echo ' <input type="submit" value="Dodaj komentarz" />';
                                echo '</div>';
                            } 
                            echo "<br/>";
                        }
                    }
                ?>
            </div>
        </div>
        <div style="clear: right"></div>  
    </div>
</body>
</html>