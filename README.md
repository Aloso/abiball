# Abiball-Kartenbestellungen
Eine Website zur Kartenbestellung &amp; Organisation von Abibällen

## Idee und Entwicklung
Diese Website wurde erstellt, um den Organisationsaufwand für den diesjährigen Abiball weitestgehend in Grenzen zu halten
und um auf das ewige Listenführen verzichten zu können. Matthias Kammüller hat für diesen Zweck eine Website in PHP programmiert.

Ich habe im Jahr darauf die Website verbessert und nun **fast komplett neu geschrieben**, da sich einige Fehler eingeschlichen haben und das Projekt unübersichtlich geworden ist. Ich habe nur das Design größtenteils übernommen und Teile des Scripts für die PDF-Rechnung wiederverwendet. Ich habe mich besonders bemüht, die Website flexibel zu gestalten (viele Einstellungsmöglichkeiten in der Admin-Konsole) und sie übersichtlicher zu machen.

## Voraussetzungen & Installation
Dieses Paket benötigt einen Server mit:
-	PHP5- oder PHP7-Unterstützung 
-	Eine leere MySQL- oder MariaDB-Datenbank
-	Eine Domain oder Subdomain, auf der der Inhalt läuft (dies muss KEIN Root-Verzeichnis sein)
-	Eine Domain, auf die zugegriffen werden soll
-   **Einen Gmail-Account zum senden von E-Mails**

## Installation
1.  Hochladen der Dateien auf einen Webserver
2.  Erstellen einer MySql- oder MariaDB-Datenbank und eines zugehörigen DB-Nutzers
3.  Öffnen der Datei „settings.inc.php“ im Ordner „resources“. Einstellen der Passwörter an folgender Stelle:
    
        define("DefaultPassword", "Abitur17");   // Voreingestelltes Passwort der Accounts
        define("WebmasterPassword", "secret");   // Passwort für den Gmail Account
    
    Einstellen des Datenbankzugangs an folgender Stelle:

        define("DbHost",     "localhost");
        define("DbDatabase", "abiball");
        define("DbUsername", "php_abiball");
        define("DbPassword", "uxvHi9FGbNXkLXd1");
    
4.  Ausführen der SQL-Anfrage in der .sql Datei, die sich im selben Ordner befindet
5.  Wechseln zur Tabelle „user“ und Ändern des Vornamen, Nachnamen und der E-Mailadresse
6.  Anmeldung auf der Website mit dem neu eingestellten Namen und dem Passwort „adminadmin“
7.  Wechseln zum Profil und ändern des Passwortes
8.  Wechseln in den Admin-Bereich, zu „Einstellungen“ und Anpassen der Werte

    Achte darauf, dass bei „Webmaster E-Mail“ eine Gmail-Adresse steht.
    Bei dieser müssen unter https://www.google.com/settings/security/lesssecureapps
    **weniger sichere Apps aktiviert werden**!
9.  Füge unter „User“ ein paar Personen zum Testen ein. Diese wirst du später wieder löschen.
    Versuche, dich als eine der Personen anzumelden und einen Account zu erstellen.
    Möglicherweise blockiert Google den Anmeldeversuch. Gib an, dass das du selbst warst!
    Dann sollte beim nächsten Versuch die E-Mail zuverlässig ankommen.
    
    Lösche die Test-Accounts wieder.
10. Wechsle zu „Seitentexte“ und passe die Texte nach Bedarf an.
11. Wechsle zu „User“. Alle Abiturienten müssen manuell hinzugefügt werden.

## Funktionen

### User-Bereich

Ein User hat auf der Website folgende Möglichkeiten:

* Account registrieren und mit E-Mail verifizieren

  Voraussetzung: Ein Admin hat den Account bereits erstellt. Dadurch sind nur bestimmte Personen zugelassen.
* Anmelden, Abmelden
* Startseite: Neuigkeiten lesen
* Kartenbestellung: Informationen lesen, Karte für sich bestellen, danach Karten für andere bestellen
* Forum: Forenbeiträge lesen und schreiben. Es wird &#42;&#42;fetter Text&#42;&#42; und &#95;&#95;kursiver Text&#95;&#95; unterstützt.
* Location: Google Maps Karte ansehen, zoomen, etc.
* Impressum: Webmaster per E-Mail kontaktieren
* Profil: Passwort / E-Mailadresse ändern, Empfänger von bestellten Karten ändern, Rechnung als PDF speichern

### Admin-Bereich

Der Admin hat alle Möglichkeiten, die ein User hat. Zusätzlich hat er folgende Möglichkeiten:

* Accounts erstellen (damit ein User seinen Account registrieren kann, muss er zuvor von einem Admin erstellt worden sein)
* Alle User tabellarisch sehen. Auf Spaltenname klicken, um sie zu sortieren (so kann man besonders schnell einen Namen oder eine E-Mailadresse finden. Man kann User nach Rechten filtern und sehen, wer zuletzt online war und wann)
* Auf Nachname klicken, um dessen Profil zu ändern. Insbesondere kann man
  
  * E-Mailadresse ändern
  * Status ändern (Vorsicht!)
  * Passwort ändern
  * Account zurücksetzen / löschen

  Durch den Status können User u.a. zum Admin gemacht und blockiert werden.
* Bestellungen tabellarisch sehen. Auf Spaltenname klicken, um sie zu sortieren (so kann man z.B. neueste Bestellungen oder unbezahlte Bestellungen zuerst anzeigen oder nach der Bestellung eines bestimmten User suchen)
* Zahlung registrieren. Sollte gedrückt werden, wenn die Überweisung angekommen ist. Dadurch verschwindet die Zahlungsaufforderung aus dem Profil des Users.
* Einstellungen ändern. Dadurch kann das Aussehen der Seite und das Verhalten der Kartenbestellung verändert werden.
* Seitentexte ändern. Zur Formatierung stehen Überschriften, fetter / kursiver Text, Listen, Tabellen, Links etc. zur Verfügung. Diese stehen am Seitenende.

## Mobile Seite
Die Website ist für mobile Geräte verschiedener Größen optimiert. Keine Umleitung etc. nötig.

## Bestellprozess
Die Bestellung kann der Administrator individuell gestalten. Er kann sowohl festlegen, wie viele Karten er zum Verkauf freigibt, als auch, ob jeder so viele Karten bestellen darf oder ob insgesamt so viel Karten bestellt werden dürfen.

Die Bestellung läuft in mehreren Bestellrunden ab. Das hat den Vorteil, dass der Administrator in Bestellrunde 2 andere Einstellungen verwenden kann als in Bestellrunde 1.

### Beispiel:

Es gibt 150 Abiturienten und 700 Karten, d.h. jedem Abiturienten stehen mindestens 4 Karten zu. Es wird also eingestellt, dass in der 1. Bestellrunde jeder Nutzer bis zu 4 Karten bestellen kann. Da im Durchschnitt 3,5 Karten bestellt werden, bleiben 175 Karten übrig. Diese werden in den folgenden Runden freigegeben, wobei jeder Nutzer so viele Karten bestellen darf wie er will, bis der Vorrat aufgebraucht ist.

## Rechte & Werbung
Die Website benutzt ausschließlich selber produziertes Material oder solches, dass Lizenzfrei (oder unter freien Lizenzen) zu haben ist.

Hinweise dazu finden Sie im Impressum.

## TODO

* ReCaptcha verwenden
* FAQ Seite (Wird erst benötigt, wenn man _Frequently Asked Questions_ hat)
