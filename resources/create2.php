<!DOCTYPE html>
<html lang="de">
<head>
    <title>Seitenerstellung</title>
    <meta charset="utf-8">
    <style>
        * {
            font-family: "Open Sans", sans-serif;
        }
        
        body {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        form span {
            display: inline-block;
            width: 220px;
        }
        
        input[type=text] {
            border: 1px solid #aaaaaa;
            border-radius: 3px;
            padding: 4px 7px;
            font-size: 100%;
            width: 300px;
            margin: 8px 0 0 0;
        }
        
        input[type=text]:hover, input[type=text]:focus {
            border: 1px solid #006bc7;
        }
        
        input[type=submit] {
            border: 1px solid #bbbbbb;
            border-radius: 3px;
            padding: 6px 14px;
            font-size: 105%;
            color: #222222;
            background-color: #eeeeee;
            margin: 8px 0 0 0;
        }
        
        input[type=submit]:hover {
            color: black;
            border: 1px solid #aaaaaa;
            background-color: #e1e1e1;
        }
        
        input[type=submit]:active {
            color: white;
            border: 1px solid #005bcb;
            background-color: #1e73f0;
        }
        p {
            font-size: 120%;
        }
    </style>
</head>
<body>
<?php

if (!isset($_GET['host']) || !isset($_GET['name']) || !isset($_GET['user']) || !isset($_GET['pass'])) {
    header('Location: create.php');
    exit;
} else {
    $host = $_GET['host'];
    $name = $_GET['name'];
    $user = $_GET['user'];
    $pass = $_GET['pass'];
    $mysqli = @new mysqli($host, $user, $pass, $name);
    if ($mysqli->connect_errno) {
        echo '<p style="color: red">Fehler: ' . $mysqli->connect_errno . '</p>';
        exit;
    }
}

if (isset($_POST['email']) && isset($_POST['vorname']) && isset($_POST['nachname'])) {
    
    if ($_POST['email'] != '' && $_POST['vorname'] != '' && $_POST['nachname'] != '') {
    
        $email = $_POST['email'];
        $vorname = $_POST['vorname'];
        $nachname = $_POST['nachname'];
        
        $query = /** @lang MySQL */ '-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Mrz 2017 um 21:29
-- Server-Version: 10.1.21-MariaDB
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS bestellungen;
DROP TABLE IF EXISTS forum;
DROP TABLE IF EXISTS meta;
DROP TABLE IF EXISTS seitentexte;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS reservierung;


--
-- Datenbank: `abiball`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bestellungen`
--

CREATE TABLE `bestellungen` (
  `id` int(10) UNSIGNED NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `bezahlt` tinyint(1) NOT NULL DEFAULT \'0\',
  `bestelltAm` int(11) NOT NULL,
  `bezahltAm` int(11) DEFAULT NULL,
  `preis` double NOT NULL,
  `round` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum`
--

CREATE TABLE `forum` (
  `id` int(10) UNSIGNED NOT NULL,
  `autor` varchar(50) NOT NULL,
  `text` varchar(2000) NOT NULL,
  `datum` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `url` varchar(100) NOT NULL,
  `pageName` varchar(50) NOT NULL,
  `pageSubtitle` varchar(100) NOT NULL,
  `googleMaps` varchar(500) NOT NULL,
  `webmasterMail` varchar(50) NOT NULL,
  `webmasterAdress` varchar(200) NOT NULL,
  `loginTimeout` int(10) UNSIGNED NOT NULL,
  `currentRound` int(10) UNSIGNED NOT NULL,
  `availableCards` int(10) UNSIGNED NOT NULL,
  `perUser` tinyint(1) NOT NULL,
  `preis` double NOT NULL,
  `zahlungsFrist` varchar(20) NOT NULL,
  `kontoinhaber` varchar(50) NOT NULL,
  `iban` varchar(50) NOT NULL,
  `bic` varchar(20) NOT NULL,
  `kontonr` varchar(20) NOT NULL,
  `blz` varchar(20) NOT NULL,
  `reservierungAktiviert` tinyint(1) NOT NULL DEFAULT \'0\',
  `reservierungsPunkte` smallint(5) UNSIGNED NOT NULL DEFAULT \'6\',
  `maxReservierungen` smallint(5) UNSIGNED NOT NULL DEFAULT \'3\'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `meta`
--

INSERT INTO `meta` (`url`, `pageName`, `pageSubtitle`, `googleMaps`, `webmasterMail`, `webmasterAdress`, `loginTimeout`, `currentRound`, `availableCards`, `perUser`, `preis`, `zahlungsFrist`, `kontoinhaber`, `iban`, `bic`, `kontonr`, `blz`) VALUES
(\'\', \'Abiball 2017 des LMGU\', \'18. September 2017 &bull; Backstage München\', \'<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d941.2308162181648!2d11.52165585788617!3d48.14489589252878!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sde!2sde!4v1490131822677\" style=\"width:100%\" height=\"400\" frameborder=\"0\" style=\"border:0\" allowfullscreen></iframe>\', \'' . $email . '\', \'Max Mustermann<br>Musterstraße 14<br>12345 Mustern\', 7200, 1, 5, 1, 40, \'03.08.2017\', \'Max Mustermann\', \'DE01234567899876543210\', \'1234567890\', \'9876543210\', \'LaLaLa\');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `seitentexte`
--

CREATE TABLE `seitentexte` (
  `name` varchar(20) NOT NULL,
  `htmlText` varchar(20000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `seitentexte`
--

INSERT INTO `seitentexte` (`name`, `htmlText`) VALUES
(\'Admin-Text\', \'Dieser Bereich ist für Administratoren gedacht. Ein Administrator hat vollen Zugriff auf die Seite. Er kann Nutzer erstellen, löschen, deren Rechte ändern, deren Daten auslesen, Kartenbestellungen löschen, verändern und vieles mehr. <i>Daher ist es wichtig, dass mit dieser Verantwortung sorfältig umgegangen wird.</i>\'),
(\'Aktuelles\', \'<h1>Aktuelle Informationen</h1>Die Kartenbestellung wird voraussichtlich am 3. August freigeschaltet. Es stehen ca. 700 Karten zur Verfügung.<br><br>Wir suchen noch freiwillige Abiturienten fürs Orga-Team! Bei Interesse bitte hier melden:<br>orgateam-abiball@fake.de<br><br>Zur Vorbereitung auf den Abiball bieten wir einen Tanz-Crashkurs an. Dieser findet zwischen dem 5. und dem 9. August täglich um 16.00 in E118 statt. Anmeldung mündlich bei Eliza. Bitte kommt wenn möglich mit einem Tanzpartner / einer Tanzpartnerin.<br><br>Mitfahrgelegenheiten werden von Sebastian (sebastian.kuemmel@fake.de) vermittelt. Bitte meldet euch, wenn ihr einen freien Platz im Auto sucht oder anbieten könnt.\'),
(\'Bestellung\', \'<h2>Alles, was Sie wissen müssen</h2><ul><li>Eine Karte für den Abiball kostet <b><span class=\"preis\">40</span> Euro</b> und muss auf folgendes Konto überwiesen werden:<table><tr><td>Kontoinhaber</td><td><span class=\"kontoinhaber\">Max Mustermann</span></td></tr><tr><td>IBAN</td><td><span class=\"iban\">DE01234567899876543210</span></td></tr><tr><td>Verwendungszweck</td><td>Abiball Eintrittskarten für [Name]</td></tr></table>(wobei Sie [Name] durch Ihren Namen ersetzen)</li><li>Bitte bezahlen Sie Ihre Karten <b>gesammelt</b>, falls Sie mehrere Karten bestellen.</li><li>Eine Karte kann im Allgemeinen nicht storniert, dafür aber auf eine andere Person übertragen werden.</li><li>Hierbei handelt es sich um einen <i>Privatverkauf</i>.</li></ul>\'),
(\'Impressum\', \'<h1>Impressum</h1>Diese Website ist ausschließlich für Abiturienten und Angestellte des Lise-Meitner-Gymnasiums Unterhaching gedacht. Hier können Karten für den Abiturball 2017 bestellt werden. Die Bezahlung der Karten wird per Ãœberweisung abgewickelt.<br><br>Die Website ist privat, daher entfallen AGB und Datenschutzbestimmungen.<h2>Ansprechpartner</h2><span class=\"webmasterAdress\">Max Mustermann<br>Musterstraße 14<br>12345 Mustern</span><br>E-Mail: <span class=\"webmasterMail\">max-mustermann@example.com</span><h2>Entwicklung und Design</h2>Ludwig Stecher\'),
(\'Location\', \'<h1>Location</h1>Der Abiball findet dieses Jahr im großartigen XXXXXXXXXXXXXXXXX statt! Parkplätze sind ausreichend vorhanden, außerdem ist der S-Bahnhof XXXXXXXXXXX nur 5 Minuten entfernt.<br><br><span class=\"googleMaps\"><iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d941.2308162181648!2d11.52165585788617!3d48.14489589252878!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sde!2sde!4v1490131822677\" style=\"width:100%\" height=\"400\" frameborder=\"0\" style=\"border:0\" allowfullscreen></iframe></span>\'),
(\'Menü\', \'<h1>Menü</h1>Für das Essen ist der Catering-Service <b>\"Abiball-Catering Schröder\"</b> verantwortlich. Dieser wird uns mit einem abwechslungsreichen Drei-Gänge-Menü verwöhnen. Uns wurde garantiert, dass auch für Vegetarier und Veganer gesorgt ist.<br><br>Allergiker werden gebeten, sich vorher Samuel zu wenden: samuel-pfoertsch@fake.de\');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  `passwortHash` varchar(200) CHARACTER SET ascii NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT \'inactive\' COMMENT \'inactive/incomplete/member/admin/blocked\',
  `lastActive` int(10) UNSIGNED NOT NULL DEFAULT \'0\',
  `verificationString` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `email`, `vorname`, `nachname`, `passwortHash`, `status`, `lastActive`, `verificationString`) VALUES
(1, \'' . $email . '\', \'' . $vorname . '\', \'' . $nachname . '\', \'$2y$10$EEv5NQb5JKP80EKmog/IYu.0TiFDhAmYmiDehJZYYzVbvE5fVbU1e\', \'admin\', 0, \'\');


CREATE TABLE `reservierung` (
  `rID` int(11) UNSIGNED NOT NULL,
  `userID` int(11) UNSIGNED NOT NULL,
  `wunschUserID` int(11) UNSIGNED NOT NULL,
  `prioritaet` smallint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bestellungen`
--
ALTER TABLE `bestellungen`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `meta`
--
ALTER TABLE `meta`
  ADD PRIMARY KEY (`url`);

--
-- Indizes für die Tabelle `seitentexte`
--
ALTER TABLE `seitentexte`
  ADD PRIMARY KEY (`name`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `reservierung`
--
ALTER TABLE `reservierung`
  ADD PRIMARY KEY (`rID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bestellungen`
--
ALTER TABLE `bestellungen`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
--
-- AUTO_INCREMENT für Tabelle `forum`
--
ALTER TABLE `forum`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `reservierung`
  MODIFY `rID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
';
        
        
        $success = $mysqli->multi_query($query);
        if ($success) {
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                } else {
                    echo '<p style="color: red">Fehler!</p>';
                    echo $mysqli->error;
                    break;
                }
            } while ($mysqli->next_result());
    
            $email = urlencode($email);
            $host = urlencode($host);
            $name = urlencode($name);
            $user = urlencode($user);
            $pass = urlencode($pass);
            
            header("Location: create3.php?email=$email&host=$host&name=$name&user=$user&pass=$pass");
            exit;
        } else {
            echo '<p style="color: red">Fehler!</p>';
        }
        
    } else {
        echo '<p>Bitte alle Felder ausfüllen!</p>';
    }
    
}

if (!file_exists('settings.inc.php')) {
    echo '<p style="color: red">Fehler: Die Einstellungsdatei konnte nicht erstellt werden. Bitte stelle sicher, dass PHP Schreibrechte hat.</p>
    <p>Die einfachste Möglichkeit, um dies unter Linux sicherzustellen ist</p>
    <pre style="font-family:monospace; font-size: 1.2em">sudo chmod a+rwx -Rf .</pre>';
    exit;
}

echo '<div style="margin: 1em 0; font-size:120%; color:green">Erfolgreich mit der Datenbank verbunden. Einstellungsdatei erstellt.</div>';

echo '<h1>Admin-Account</h1>

<p>Der Webmaster muss auf der Seite als Administrator registriert sein, um die Seite zu verwalten.</p>
<p>Geben Sie bitte Ihre Daten ein:</p>

<form action="create2.php' . "?host=$host&name=$name&user=$user&pass=$pass" . '" method="post">
    <label>
        <span>GMail-Adresse:</span>
        <input type="text" name="email" value=""><br>
        <span></span> Wenn dies keine GMail Adresse ist, kann der Server keine SMTP-Verbindung herstellen!
    </label><br>
    <label>
        <span>Vorname:</span>
        <input type="text" name="vorname" value="">
    </label><br>
    <label>
        <span>Nachname:</span>
        <input type="text" name="nachname" value="">
    </label><br>
    
    <p>
        Bevor du auf "Account erstellen" klickst, <b>aktiviere weniger sicher Apps</b>:
        <a href="https://www.google.com/settings/security/lesssecureapps" target="_blank">https://www.google.com/settings/security/lesssecureapps</a>
    </p>
    
    <input type="submit" value="Account erstellen">
</form>';

?>
</body>
</html>
