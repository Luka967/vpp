# Bioskop projekat

Projekat je odrađen kao rešenje zadatka za predmet Veb programiranje.

- Landing i stranica repertoara se dinamično kreiraju uz ručni unos podataka
- Repertoar samo tekuće nedelje od srede do sledeće srede je vidljiv, kao i kod većine bioskopa na svetu
- Korisnik može rezervisati tikete od kojih svaki ima maksimum 6 sedišta, a kasnije i poništiti rezervaciju
- Postoje više tipova sedišta i više termina koje korisnik može rezervisati
- Korisnici sa menadžer dozvolama mogu upravljati repertoarom, podacima o filmovima, kreiranim rezervacijama i popust klubovima
- Administrator korisnici naknadno mogu upravljati dozvolama ostalih korisnika

Osnovne tehničke karakteristike:
- MVC arhitektura, funkcionalnost lično pravljena uz Twig renderer
- Veb adrese su virtuelne koristeći `.htaccess` rewrite engine, tj. sami fajlovi se ne izvršavaju, već se uvek učitava `public/index.php` a `src/Core/Router.php` izvršava dalju funkcionalnost
- Projekat kao celina je location-aware u smislu da ne zavisi od hardkodovanih lokacija fajlova za assets-e. Potrebno je jedino izmeniti `.htaccess` da upućuje ka index-u
- Rute aplikacije mogu zahtevati logovanje, permisije korisnika, određene ekstenzije uploadovanih fajlova, ograničenja kod veličine i tipova unesenih podataka itd.
- Logovanje je rađeno koristeći Monolog biblioteku i krajnja lokacija log fajla se može promeniti

# Instalacija i korišćenje

- Potreban je LAMP/WAMP sklop koji sadrži PHP 8.
- Poželjan je `utf8mb4_general_ci` collation za bazu podataka za podršku latiničnih/ćiriličnih karaktera. U `database2.sql` on nije naveden

Koraci:
1. Izvršiti `database2.sql` u celosti
2. Celinu source koda premestiti u bilo kom folderu, može i www-root
3. `.htaccess` premestiti u www-root
4. U `.htaccess` gde je `vpp/public/index.php`, to mora pokazati ka `public/index.php` u odnosu na www-root
5. Treba se pojaviti početna stranica. Biće prazna jer nema dummy podataka
6. Za pravljenje administrator korisnika potrebno je se registrovati i naknadno promeniti `permissions` kolonu za red tog korisnika u `2`.
7. Administrator i menadžer korisnici dalje mogu kreirati podatke vezane za bioskop
