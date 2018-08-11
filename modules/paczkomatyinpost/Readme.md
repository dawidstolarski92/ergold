#Integracja sklepu z serwisem Paczkomaty.pl#

Autor: Tomasz Dacka (http://tomaszdacka.pl)
Aktualna wersja: 1.7.0

Link do sklepu: http://prestahelp.com/pl/platne-moduly/151-paczkomaty-24-7.html
Link do zgłoszenia problemu: http://helpdesk.prestahelp.com/form.php

##CHANGELOG##
1.7.0
- usunięto z systemu liczenie cen przesyłek ze wzlędu na zmiany w api

1.4.1
- poprawiono listę paczkomatów za pobraniem

1.4.0 
- wersja na ps1.7
1.3.0 @ 22-06-2016
przebudowany proces wyświetlania się wyboru paczkomatu tak by zakupy na jednej stronie działały bez zarzutu

1.2.3 @ 15-03-2016
zmiana komunikatu w przypadku błędnego łączenia się z serwerem paczkomatów
ustawienie limitu czasu połączenia na 5 sekund również dla połączeń metodą GET.
poprawki ostrzeżeń php przy pętlach na pustych danych z api w klasie PaczkomatyInpostModel

1.2.2 @ 29-02-2016
poprawka zapisywania danych o stworzonych przesyłkach, powiązana z płatnością za pobraniem
poprawka wyświetlania informacji o wybranym paczkomacie dla płatności za pobraniem przy decydowaniu o pobraniu przez moduły płatności

1.2.1 @ 08-02-2015
poprawka błędu z brakiem szablonu dla PS 1.5
poprawka wyświetlania wybranego paczkomatu przez klienta w panelu, gdy wybrano decydowanie o pobraniu przez moduł płatności

1.2.0 @ 20-01-2015
aktualizacja bazy paczkomatów
alternatywna metoda wyboru paczkomatu dla klienta:
	lista select z propozycją 5 najbliższych paczkomatów na podstawie kodu pocztowego klienta, działa niezależnie od mapy i przeglądarki
	klient może wybrać paczkomat z listy lub za pomocą mapy
nowe funkcje w zapleczu:
	decyzja o pobraniu na podstawie sposóbu płatności w sklepie
	wysyłanie e-mail do klienta z listem przewozowym: gdy przesyłka została stworzona lub opłacona
	automatyczna zmiana statusu zamówienia na wybrany po wygenerowaniu przesyłki
	automatyczna zmiana statusu zamówienia na wybrany po opłaceniu przesyłki

1.1.6 @ 24-08-2015
aktualizacja bazy paczkomatów dla skryptu mapy
event handler w mapach dla Internet Explorera

1.1.5 @ 02-08-2015
naprawiony błąd z wyborem paczkomatów o nazwie dłuższej niż 6 znaków
naprawa wyświetlania przycisku z wyborem paczkomatu, który czasami wyświetla się dwa razy
naprawa błędu z cache dla listy paczkomatów
naprawa błedów występujących w Prestashop v.1.6.1
Prestashop Addons Ready

1.1.4 @ 20-05-2015
naprawiony błąd ze złą informacją o nie wybraniu paczkomatu za pobraniem przez klienta
wprowadzono usprawnienie skryptu map, który spowalniał wczytywanie się stron z zamówieniem
ograniczenie wczytywania się skryptów tylko do stron, gdzie są niezbędne
wprowadzenie ograniczenia wyboru takiego samego przewoźnika dla paczek zwykłych i za pobraniem
drobna poprawka błędu, który pojawiał się gdy odbiorcą przesyłki nie jest klient sklepu.

1.1.3 @ 08-04-2015
poprawki błędów i optymalizacja skryptów
poprawka bindów dla cgv (kontrola czy ktoś wybrał paczkomat)
zmiana komunikatów w konfiguracji modułu
dodano opcję generowania etykiet dla przewoźników różnych niż wybranych w panelu konfiguracyjnym modułu
poprawiono hook podczas instalacji dla displayHeader
prestashop addons ready

1.1.2 @ 24-03-2015
poprawki błędów + optymalizacja dla szybkich zakupów

1.1.1 @ 23-03-2015
FIX, nowy algorytm wyliczania ID dla modułu onepagecheckout

1.1.0 @ 22-03-2015
Kompatybilność modułu z dodatkiem onepagecheckout w wersji 2.3.6 dla ps 1.5-1.6

1.0.0 @ 19-03-2015
Oficjalna, początkowa wersja modułu