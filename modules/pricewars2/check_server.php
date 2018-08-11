<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Narzędzie diagnostyczne do modułu PriceWars 2 - seigi.eu</title>
</head>

<body>
<h1>Narzędzie diagnostyczne do modułu PriceWars 2 - więcej na <a href="http://seigi.eu">seigi.eu</a></h1>
<?php
/* 
 *	Bardzo prosty skrypt, który mam nadzieję pozwoli szybciej zidentyfikować problemy wynikające z konfiguracji serwera.
 *	
 *	@author SEIGI Grzegorz Zawadzki <kontakt@seigi.eu>
 *	@version 1.0
 *	@copyright SEIGI Grzegorz Zawadzki
 *
 */

$warning = $error = false;


$could_connect_seigi = $could_connect_external = true;
echo "<h2>Testy łączności</h2>";
echo "<h3>Serwery ogólne</h3>";
$could_connect_external &= testConnection('http://google.pl');
echo "<br>";
$could_connect_external &= testConnection('http://arena.pl');
echo "<br>";
$could_connect_external &= testConnection('https://arena.pl');
echo "<h3>Serwery licencji</h3>";
$could_connect_seigi &= testConnection('http://seigi.eu');
echo "<br>";
$could_connect_seigi &= testConnection('http://pl.seigi.eu');
echo "<br>";


if(!function_exists('curl_init')) {
    d_info('Rozszerzenie CURL nie jest zainstalowane');
}
function testConnection($url, &$result = false){
    $curl_timeout = 3;
    if(@file_get_contents($url) === false){
        d_info('Próba otworzenia zasobu '.$url.' przez file_get_contents nie powiodła się');
        $error = error_get_last() ;
        d_warning($error['message']);
        if(function_exists('error_clear_last'))
            error_clear_last();
        if(function_exists('curl_init')) {
            d_info('(CURL) Próba połączenia za pomocą rozszerzenia CURL');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            $curl_info = curl_getinfo($curl);
            $curl_result = curl_exec($curl);
            curl_close($curl);
            d_info('(CURL) Serwer zwrócił KOD HTTP: '. $curl_info['http_code']);
            if($curl_result === false){
                d_error( '(CURL) Połączenie nie udane. Nie można nawiązać połączenia z serwerem');    
                return false;
            } else {
                d_error( 'Twoja strona musi zezwalać na połączenie z zasobami zdalnymi. Musisz mieć zainstalowane i aktywne rozszerzenie curl i/lub aktywną opcję allow_url_fopen');    
                return true;
            }
        } else {
            d_error( 'Twoja strona musi zezwalać na połączenie z zasobami zdalnymi. Musisz mieć zainstalowane i aktywne rozszerzenie curl i/lub aktywną opcję allow_url_fopen');
            return false;
            
        }
    }
    d_ok('Próba otworzenia zasobu przez <b>file_get_contents</b> udana: '. $url);
    return true;
}


echo "<h3>Podsumowanie</h3>";

if($could_connect_seigi) {
	d_ok('Połączenie z serwerem licencji nawiązane');
} else {
	d_error( 'Nie można połączyć z serwerem licencji.');
}
if(!$could_connect_external){
	d_error( 'Konfiguracja serwera nie pozwala na żadne połączenie zdalne z poziomu skryptu. Proszę o kontakt z administratorem serwera', true);
}

if(get_magic_quotes_gpc())
	d_error( 'Opcja magic_quotes_gpc jest aktywna. Należy ją wyłączyć aby moduł (zresztą i sklep też) działał poprawnie.');
echo "<h2>Testy systemowe</h2>";
		

foreach(get_loaded_extensions() as $ext){
	if(strpos($ext, 'XCache') !== false){
		d_warning('Wykryto zainstalowane rozszerzenie "'.$ext.'" które <b>może - ALE NIE MUSI</b> sprawiać problemy. XCache jest przestażały (ost. akt ~2014 roku), a w sieci są lepsze moduły cacheujące<br> Czasami problemy z tym modułem mogą zostać rozwiązane po aktualizacji IonCube do najnowszej wersji');
	}
}

/* if(ini_get('allow_url_fopen') != 1)
echo '<h1 style="color: red">UWAGA! allow_url_fopen musi być ustawione na 1. Prosimy o kontakt z administratorem serwera</h1>';
 */
if(!extension_loaded('mcrypt'))
	d_error ('UWAGA! Brak biblioteki mcrypt</h1>');

if(extension_loaded('suhosin') || (defined('SUHOSIN_PATCH') && constant("SUHOSIN_PATCH")))
	d_warning('Na serwerze jest obecne rozszerzenie Suhosin. Potrafi on sprawiać problemy przy generowaniu XML. <b>Tym ostrzeżeniem nie należy się przejmować, jeśli XML generuje się prawidłowo</b>');

if(!extension_loaded('ionCube Loader'))
	d_error ('UWAGA! Brak zainstalowanego rozszerzenia "<b>ionCube Loader</b>" na serwerze, który jest niezbędny aby korzystać z tego modułu. Jeśli chcesz korzystać z modułu i widzisz tę wiadomośc, to skontaktuj się z administratorem Twojego serwera i poproś o instalację/aktywację rozszerzenia.');
else {
	if(!function_exists('ioncube_loader_iversion')) {
		d_warning('Wykryto IONCUBE loader - nie wiadomo jednak jaka wersja. Moduł może wymagać aktualizacji');
	} elseif (($vers = ioncube_loader_iversion()) < 40400) {
		d_warning('Moduł IONCUBE Loader jest bardzo nieaktualny. Nasz moduł nie będzie pracował na Twoim serwerze. Prosimy o aktualizację modułu IoncubeLoader Twoja wersja to: ' . ioncube_loader_version());
	} elseif ($vers < 50000) {
		d_warning('Twój moduł IONCUBE Loader jest w miarę aktualny. Jednak znane sa problemy z wersjami IONCUBE Loader 4.x. Zalecamy aktualizację IONCUBE Loader do najnowszej wersji (5.0 lub nowszej). Twoja wersja to: ' . ioncube_loader_version());
	} else {
		d_ok('Moduł Ioncube wydaje się być (w miarę) aktualny. Twoja wersja to: ' . ioncube_loader_version());
	}
}


function d_info ($s) {
	echo '<div style="color: #225ebf; background-color: #fff; border: 1px solid #225ebf; padding: 4px; margin: 5px">INFO: '.$s.'</div>';
}
function d_error ($s, $rev = false) {
    if($rev)
        echo '<div style="color: #fff; background-color: red; border: 1px solid #000; padding: 4px; margin: 5px">BŁĄD: '.$s.'</div>';
    else
        echo '<div style="color: red; background-color: #fff; border: 1px solid red; padding: 4px; margin: 5px">BŁĄD: '.$s.'</div>';
}
function d_warning ($s) {
	echo '<div style="color: darkorange; background-color: #fff; border: 1px solid darkorange; padding: 4px; margin: 5px">OSTRZEŻENIE: '.$s.'</div>';
}
function d_ok ($s) {
	echo '<div style="color: green; background-color: #fff; border: 1px solid green; padding: 4px; margin: 5px">SUKCES: '.$s.'</div>';
}

?>
</body>
</html>