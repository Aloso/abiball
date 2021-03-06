# Abiball-Kartenbestellungen
Eine Website zur Kartenbestellung & Organisation von Abibällen

## Testumgebung

Die Testumgebung ist [wieder online](http://abiball-demo.bplaced.net).

 - Standardpasswort: Abitur19
 - User-Account: Rabe Socke, Passwort 12345678
 - Admin-Account: Meister Eder, Passwort 12345678

Die Testumgebung nutzt einen kostenlosen Service mit begrenzten Funktionen, daher ist reCAPTCHA und SMTP leider deaktiviert.

Falls du mehr über das Projekt wissen willst, kannst du mich direkt [auf Telegram](https://t.me/joinchat/AAAAAEF9Ddvu9ancJ730zA) anschreiben.

## Idee und Entwicklung

Diese Website wurde erstellt, um den Organisationsaufwand für den Abiball am LMGU weitestgehend in Grenzen zu halten
und um auf das ewige Listenführen verzichten zu können. Matthias Kammüller hat für diesen Zweck eine Website in PHP programmiert.

Ich habe im Jahr darauf die Website verbessert und nun fast komplett neu geschrieben, da sich einige Fehler eingeschlichen haben und das Projekt unübersichtlich geworden ist. Ich habe nur das Design größtenteils übernommen und Teile des Scripts für die PDF-Rechnung wiederverwendet.

Ich habe mich besonders bemüht, die Website flexibel zu gestalten (viele Einstellungsmöglichkeiten) und sie übersichtlicher zu machen. Außerdem habe ich Installationsscripte erstellt, sodass **keine PHP-Kenntnisse nötig** sind.

Die Website achtet darauf, alle Eingaben zu bereinigen und ist auf Sichheit bedacht.

## Voraussetzungen & Installation

Dieses Paket benötigt:

 - Einen Server mit PHP 7
    - falls du PHP manuell installierst, achte darauf, dass die Erweiterungen `mysqli` und `iconv` in der `php.ini` aktiviert sind und PHP Schreibrechte hat
 - Eine leere MySql- oder MariaDB-Datenbank
 - Eine Domain oder Subdomain, auf der die Seite läuft (dies muss _kein_ Root-Verzeichnis sein)
 - Einen Gmail-Account, falls du reCAPTCHA und/oder SMTP verwenden willst (empfohlen)

## Installation

 1. Lade die Dateien auf deinen Webserver hoch
 2. Erstelle eine Datenbank und einen zugehörigen DB-Nutzer
 3. Öffne die Website im Browser
 4. Folge den Anweisungen, bis die Seite einsatzbereit ist

## Funktionen

### User-Bereich

Ein User hat auf der Website folgende Möglichkeiten:

 - Account registrieren und mit E-Mail verifizieren
   - Voraussetzung: Ein Admin hat den Account bereits erstellt. Dadurch sind nur bestimmte Personen zugelassen.
 - Anmelden, Abmelden
 - Startseite: Neuigkeiten lesen
 - Kartenbestellung: Informationen lesen, Karte für sich bestellen, danach Karten für andere bestellen
 - Forum: Forenbeiträge lesen und schreiben. Es wird `**fetter Text**` und `__kursiver Text__` unterstützt.
 - Location: Google-Maps-Integration
 - Sitzplätze: Es können bis zu _n_ Wünsche (einstellbar) angegeben werden, wer mit am Tisch sitzen soll. Diese können unterschiedlich gewichtet werden.
 - Impressum: Webmaster per E-Mail kontaktieren
 - Profil: Passwort / E-Mailadresse ändern, Empfänger von bestellten Karten ändern, Rechnung als PDF speichern

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
* Sitzplaner: Hier kann der Admin die optimale Sitzordnung herausfinden.
* Website zurücksetzen

## Mobile Seite
Die Website ist für mobile Geräte verschiedener Größen optimiert. Keine Umleitung etc. nötig.

## Bestellprozess
Die Bestellung kann der Administrator individuell gestalten. Er kann sowohl festlegen, wie viele Karten er zum Verkauf freigibt, als auch, ob jeder so viele Karten bestellen darf oder ob insgesamt so viel Karten bestellt werden dürfen.

Die Bestellung läuft in mehreren Bestellrunden ab. Das hat den Vorteil, dass der Administrator in Bestellrunde 2 andere Einstellungen verwenden kann als in Bestellrunde 1.

### Beispiel:
Es gibt 150 Abiturienten und 700 Karten, d.h. jedem Abiturienten stehen mindestens 4 Karten zu. Es wird also eingestellt, dass in der 1. Bestellrunde jeder Nutzer bis zu 4 Karten bestellen kann. Da im Durchschnitt 3,5 Karten bestellt werden, bleiben 175 Karten übrig. Diese werden in den folgenden Runden freigegeben, wobei jeder Nutzer so viele Karten bestellen darf wie er will, bis der Vorrat aufgebraucht ist.

## Sitzplaner
![Sitzplaner mit Drag'n'Drop](http://fs5.directupload.net/images/170327/8ji8c9ld.png)

Eine zufriedenstellende Sitzordnung aufzustellen, kann nervenaufreibend sein und viel Papier kosten. Mit dem Sitzplaner ist es für alle Beteiligten einfacher: Jeder Abiturient kann Wünsche angeben, wer mit am Tisch sitzt und kann diese unterschiedlich gewichten. Wenn beispielsweise 3 Wünsche und 6 Punkte erlaubt sind, sind Aufteilungen wie 2/2/2, 1/2/3, 3/3 oder 6 usw. erlaubt.

Wenn alle Wünsche angekommen sind, öffnen die Administratoren den Sitzplaner unter Admin-Bereich -> Bestellungen -> Zum Sitzplaner. Abiturienten werden durch Kreise dargestellt. In jedem Kreis steht, wie viele Karten er gekauft hat. Rote Verbindungslinien zeigen Wünsche an, die Dicke der Linien die Gewichtung. Ein Administrator kann die Kreise (Abiturienten) bequem per Drag'n'Drop neu anordnen. Außerdem kann er Tische (Rechtecke) zeichnen. Es lassen sich mehrere Kreise auswählen, um sie einfacher zu verschieben. Besonders dünne Linien lassen sich ausblenden.

## Website zurücksetzen

Die Website kann im Adminbereich zurückgesetzt werden, indem der Installationsprozess erneut durchgeführt wird.

## Rechte & Werbung
Die Website benutzt ausschließlich selber produziertes Material oder solches, dass Lizenzfrei (oder unter freien Lizenzen) zu haben ist.

Hinweise dazu finden Sie im Impressum.

## TODO

*   Bestätigungs-Email beim Ändern der E-Mailadresse
*   Rechnung als E-Mail zusenden lassen
*   Nach n falschen Passworteingaben Account blockieren
*   Bei unsicheren Passwörtern beschweren (momentan: Mindestlänge 8 Zeichen)
