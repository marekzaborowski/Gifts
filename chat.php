<?php
    include 'wypisywanie_wiadomosci.php';

	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
        header('Location: index.php');
        echo "blad";
		exit();
    }
    if($_SESSION['czy_wszyscy_losowali']==false)
    {
        header('Location: waiting_room.php');
        exit();
    }

    require_once "connect.php";
    $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
    //Tworzę tablicę z wiadomościami i informacjami o nadawcy w czasie gdzie zalogowany użytkownik jest mikołajem
    if ($rezultat = @$polaczenie->query("SELECT id, prezent, idnadawcy FROM wiadomosci 
        WHERE idpolaczenia ='".$_SESSION['idpolaczenia_dlamikolaja']."'"))
	{
        $i=0;
        $w;
        $wartosc=true;
        do
        {
            $w=$rezultat->fetch_assoc();
            if($w!=null)
            {
                $wiadomosci_zDzieckiem[$i]=$w;
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
    //Tworzę tablicę z wiadomościami i informacjami o nadawcy w czasie gdzie zalogowany użytkownik jest dzieckiem
    if ($rezultat = @$polaczenie->query("SELECT id, prezent, idnadawcy FROM wiadomosci 
        WHERE idpolaczenia ='".$_SESSION['idpolaczenia_dladziecka']."'"))
	{
        $i=0;
        $w;
        $wartosc=true;
        do
        {
            $w=$rezultat->fetch_assoc();
            if($w != null)
            {
                $wiadomosci_zMikolajem[$i]=$w;
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
    //Tworzę tablicę z komentarzami i informacjami o nadawcy w czasie gdzie zalogowany użytkownik jest dzieckiem
    if ($rezultat = @$polaczenie->query("SELECT * FROM komentarze"))
	{   
        $komentarze=NULL;
        $i=0;
        $w;
        $wartosc=true;
        do
        {
            $w=$rezultat->fetch_assoc();
            if($w != null)
            {
                $komentarze[$i]=$w;
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

    //Wyciągam nick dziecka
    if ($rezultat = @$polaczenie->query("SELECT nick FROM uzytkownicy, polaczenia 
        WHERE polaczenia.iddziecka = uzytkownicy.id AND idmikolaja='".$_SESSION['idpolaczenia_dlamikolaja']."'"))
	{   
        $wiersz = $rezultat->fetch_assoc();
        $nick_dziecka=$wiersz['nick'];
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
    <meta http-equiv="refresh" content="60" >
</head>
<body>
    <div style="text-align: justify; font-size: 19px">
        Strona, na której się znajdujesz służy do komunikacji. Po lewej stornie jest pole do 
        pisania z osobą, której robisz prezent, a po prawej jest pole do pisania z osobą, która prezent 
        robi Tobie (czyli z mikołajem). Pamiętaj, żeby nie pomylić który czat jest który ponieważ wiadomości
        nie da się cofnąć ani usunąć. Jeżeli zapomniałeś/aś komu robisz prezent, najedź myszką na czerwony napis poniżej "osobą" 
        W celu wylogowania się kliknij napis po prawej stronie:
        <b><a href="logout.php">Tak, to ten napis ;)</a></b><br>
    </div>
    <div id="form">
        <div id="czat_z_dzieckiem">
            <form method="post" action="dodawaniewiadomosci.php">
                <b>Czat z Twoją wylosowaną <span title='<?php echo $nick_dziecka; ?>' style='color: red;'>osobą</span>:</b> <br><br>
                <input type="text" name="wiadomosc_do_dziecka"/>
                <input type="submit" value="Wyślij wiadomość"/> 
            </form>
            <div class="czat">
                <?php
                //print_r($wiadomosci_zDzieckiem);
                    if(isset($wiadomosci_zDzieckiem))
                    {
                        wypisz_wiadomosci($wiadomosci_zDzieckiem, $komentarze);
                    }
                ?>
            </div>
        </div>

        <div id="czat_z_mikolajem">
            <form  method="post" action="dodawaniewiadomosci.php">
                <b>Czat z Twoim <span style='color: red;'>mikołajem</span>:</b> <br><br>
                <input type="text" name="wiadomosc_do_mikolaja"/> 
                <input type="submit" value="Wyślij wiadomość"/>
            </form>
            <div class="czat" >
                <?php
                    if(isset($wiadomosci_zMikolajem))
                    {
                        wypisz_wiadomosci($wiadomosci_zMikolajem, $komentarze);
                    }
                ?>
            </div>
        </div>
        <div style="clear: right"></div>  
    </div>
</body>
</html>