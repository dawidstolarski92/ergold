<?php

function opt($b){

  $uri = $_SERVER['REQUEST_URI'];
  $brand = 'ERgold';

  switch($uri){
    
    case '/':
      $title = 'Złote pierścionki, biżuteria damska oraz ślubna - sklep internetowy, jubiler online';
      $desc = 'ERgold - sklep internetowy oferujący biżuterię damską. W naszej ofercie znajdziesz również złote pierścionki, kolczyki oraz modne celebrytki. Zapoznaj się z naszą ofertą już teraz!';
    break;
     case '/17-pierscionki':
      $title = 'Złote pierścionki z cykoriami, obrączki';
      $desc = 'Nieodłącznym elementem kobiecości są pierścionki. Oferujemy klasyczne obrączki z białego złota lub z dodatkami np. kamieniami, które idealnie pasują na wyjścia wieczorowe. Kliknij, aby zobaczyć szczegóły!';
    break;
     case '/22-pierscionki-z-diamentem':
      $title = 'Pierścionek zaręczynowy z brylantem, z rubinem lub szafirem';
      $desc = 'Chcesz aby pierścionek zaręczynowy dla swojej ukochanej był wyjątkowy? W naszej ofercie znajdziesz biżuterię z brylantami, rubinami, a nawet z szafirami! Sprawdź naszą ofertę już teraz!';
    break;
	 case '/13-kolczyki':
      $title = 'Złote kolczyki wiszące oraz z cyrkoniami, próba 585';
      $desc = 'Kolczyki od lat cieszą się popularnością wśród kobiet. Proponujemy klasyczne złote z próbą 585 oraz bardziej ozdobne z cyrkonami. Zapraszamy do zapoznania się z naszą ofertą!';
    break;
	 case '/15-wisiorki':
      $title = 'Złota zawieszka na łańcuszek, wisiorek serce';
      $desc = 'Idealnym miejscem dla złotych zawieszek oraz wisiorków jest kobieca szyja. To dlatego w naszym sklepie znajdziesz m.in unikatowe naszyjniki w kształcie serca. Zamów już dziś na Ergold.pl!';
    break;
	 case '/21-celebrytki':
      $title = 'Złote celebrytki, naszyjniki, łańcuszki, bransoletki';
      $desc = 'Subtelna z delikatnymi wisiorkami - to właśnie jest celebrytka. Ten rodzaj biżuterii oferujemy - złote naszyjniki, łańcuszki oraz bransoletki. Sprawdź naszą ofertę już teraz!';
    break;
  }


  if($title!=''){
    $wz = '#<title>(.*)</title>#Umsi';
    if(preg_match($wz,$b)){
      $b = preg_replace($wz,"<title>$title - $brand</title>",$b);
    }
  }
  
  if($desc!=''){
    if(strstr($b,'name="description"')){
      $b = preg_replace('#<meta[^"]*"description"[^>]*content="[^"]*"[^>]*>#Umsi',"<meta name=\"description\" content=\"$desc\" />",$b);                                                
    }else{
      $b = str_replace('</title>',"</title>\r\n\t\t<meta name=\"description\" content=\"$desc\" />",$b);
    }
  }
  

  
  if($robots!=''){
    if(strstr($b,'name="robots"')){
      $b = preg_replace('#<meta[^"]*"robots"[^>]*content="[^"]*"[^>]*>#Umsi',"<meta name=\"robots\" content=\"$robots\" />",$b);
    }else{
      $b = str_replace('</title>',"</title>\r\n\t\t<meta name=\"robots\" content=\"$robots\" />",$b); 
    }
  }
  
  if(strstr($b,'noindex') && strstr($b,'canonical')){
    if(preg_match('#<link[^"]*"canonical"[^>]*href="[^"]*"[^>]*>#Umsi',$b)){
      $b = preg_replace('#<link[^"]*"canonical"[^>]*href="[^"]*"[^>]*>#Umsi','',$b);
    }
  }
  



return $b;
}

if(!strstr($_SERVER['REQUEST_URI'],'wp-admin')){
  ob_start("opt");
}

?>