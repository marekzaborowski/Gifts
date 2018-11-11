<?php
    function wypisz_wiadomosci($wiadomosci, $komentarze)
    {
        foreach($wiadomosci as $wiadomosc) 
        {
            if(czy_wiadomosc_jest_moja($wiadomosc['idnadawcy']))
            {
                $id_stylu="ja";
            }
            else
            {
                $id_stylu="on";
            }
            $id_wiadomosci=$wiadomosc['id'];
            echo '<div class="'.$id_stylu.'">';
                if($wiadomosc['idnadawcy']==$_SESSION['id'])
                {   
                    echo "<b>Ja: </b>";
                    echo "<span class='wrap'> <b>".$wiadomosc["prezent"]."</b></span>";
                }
                else
                {
                    echo "<b>Rozmówca: </b>";
                    echo "<span class='wrap'> <b>".$wiadomosc["prezent"]."</b></span>";
                }
                echo '<br/>';
                echo '<form method="post" action="dodawaniekomentarza.php">';
                    echo '<input type="text" placeholder="Dodaj komentarz..." name="komentarz"/>';
                    echo '<input type="submit" value="Wyślij"/>';
                    echo '<input type="hidden" value="'.$wiadomosc['id'].'" name="idwiadomosci"/>';
                    echo "<input type=\"hidden\" value=\"".$_SESSION['id']."\" name=\"nadawca\"/>";
                echo '</form>';
                if(isset($komentarze))
                {
                    //print_r($komentarze);
                    wypisz_komentarze($komentarze, $id_wiadomosci);
                }
                
            echo '</div>';
            echo "<br/>";
        }
    }
 
    function wypisz_komentarze($komentarze, $id_wiadomosci)
    {
        foreach($komentarze as $komentarz) 
        {
            if($id_wiadomosci==$komentarz['idwiadomosci'])
            {
                if(czy_wiadomosc_jest_moja($komentarz['idnadawcy']))
                {
                    $id_stylu="ja_komentarz";
                }
                else
                {
                    $id_stylu="on_komentarz";
                }
                if($komentarz['idnadawcy']==$_SESSION['id'])
                {
                    echo '<div class="'.$id_stylu.'">';
                    echo "<div class='wrap' style=\"font-size: 15px\"> <b>Ja: </b>";
                        echo $komentarz['komentarz_do_prezentu']."</div>";
                        //echo $komentarz['idnadawcy'];
                        //echo $id_stylu;
                        //echo '<br/>';
                    echo '</div>';
                    // echo "<br/>";
                }
                else
                {
                    echo '<div class="'.$id_stylu.'">';
                        echo "<div class='wrap' style=\"font-size: 15px\"> <b>Rozmówca: </b>";
                        echo $komentarz['komentarz_do_prezentu']."</div>";
                        //echo $komentarz['idnadawcy'];
                        //echo $id_stylu;
                        //echo '<br/>';
                    echo '</div>';
                    // echo "<br/>"; 
                }
            }
        }
    }

    function czy_wiadomosc_jest_moja($nadawca)
    {
        if($nadawca == $_SESSION['id'])
        {
            return true;
        }
        else
        {
            return false;
        }
    }
?>

