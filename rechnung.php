<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

// Header & Footer festlegen
require 'fpdf/fpdf.php';

class PDF extends FPDF {
    
    // Page header
    function Header() {
        global $meta;
        
        // Arial bold 15
        $this->AddFont('MontS','','Montserrat-Regular.php');
        $this->SetFont('MontS','',15);
        // Title
        $this->Cell(0, 10, $meta['pageName'], 0, 0, 'L');
        // Line break
        $this->Ln(15);
    }
    
    // Page footer
    function Footer() {
        global $meta;
        
        // Position at 2.5 cm from bottom
        $this->SetY(-25);
        $this->SetFont('MontS','',8);
        // Kontoverbindungsdaten etc
        $kontakt = utf8_decode("Kontakt \n
                    ". $meta['pageName'] ." \n
                    Erreichbar unter ". $meta['url'] ." \n
                    E-Mail: ". $meta['webmasterMail']);
        $konto1 = utf8_decode("Konto \n
                    Kontoinhaber: ". $meta['kontoinhaber'] ." \n
                    IBAN: ". $meta['iban'] ." \n
                    BIC: ". $meta['bic'] ." \n ");
        $konto2 = utf8_decode("\n
                    Konto-Nr. ". $meta['kontonr'] ."  \n
                    BLZ ". $meta['blz'] ."  ");
        
        $this->MultiCell(0,2,$kontakt);
        $this->SetXY(80,-25);
        $this->MultiCell(0,2,$konto1);
        $this->SetXY(150,-25);
        $this->MultiCell(0,2,$konto2);
        // Page number
        $this->SetX(10);
        $this->Cell(0,10,'Rechnung erstellt am '.date("d.m.Y H:i:s"),0,0,'L');
        $this->Cell(0,10,'Seite '.$this->PageNo().' von {nb}',0,0,'R');
    }
}

$encUserID = $mysqli->real_escape_string($userID);

$pdf = new PDF('P', 'mm', 'A4'); // horizontales A4; Maße in mm

$Nummern = "";

$rechnungenVorhanden = false;

$data = $mysqli->query("SELECT * FROM bestellungen WHERE userID = $encUserID AND bezahlt = 0");
if ($data->num_rows != 0) {
    $rechnungenVorhanden = true;
    
    $auftrag = utf8_decode("\nKunde: $vorname $nachname\n\nKundennummer: $userID");
    
    // PDF-Seite erstellen
    $pdf->AddPage();
    $pdf->SetFont('MontS','',24);
    $pdf->Cell(0,10,'Rechnung Kartenbestellung',0,1,'C'); //Titel
    $pdf->ln(); // Leere Zeile
    $pdf->SetFont('Times','',12);
    $pdf->Cell(10,3); // Einrückung
    $pdf->MultiCell(0,3,$auftrag,0,'L'); //Auftragsbeschreibung
    $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
    // Tabelle
    //Tabellenüberschrift
    $pdf->SetFont('', 'B', '');
    $pdf->Cell(35,5,'Kartennummer','BR', 0, 'R');
    $pdf->Cell(95,5,'Kartenbeschreibung','BR', 0,'L');
    $pdf->Cell(30,5,'Bestellt am','BR', 0,'L');
    $pdf->Cell(30,5,'Preis','B', 0, 'L');
    $pdf->ln();
    $pdf->SetFont('', '', '10');
    
    $kosten = 0;
    
    // Für jede Bestellung eine Seite
    while (($row = $data->fetch_assoc()) != null) {
        // Daten sammeln
        
        $Nummer = $row['id'];
        $Nummern .= ", " . $Nummer;
        
        $Datum = $row['bestelltAm'];
        $Datum = date('d.m.Y', $Datum);
        $Name = $row['name'];
        
        $kosten += $row['preis'];
        $preis = number_format((float)$row['preis'], 2, ',', '.') . '€';
        $preis = iconv('UTF-8', 'windows-1252', $preis);
        
        $pdf->Cell(35,5, $Nummer, 'BR', 0, 'R');
        $pdf->Cell(95,5, utf8_decode('Persönliche Eintrittskarte für '. $Name), 'BR', 0,'L');
        $pdf->Cell(30,5, $Datum, 'BR', 0,'L');
        $pdf->Cell(30,5, $preis, 'B', 0, 'L');
        $pdf->ln();
    }
    
    //Summe
    $summe = number_format((float)$kosten, 2, ',', '.') . '€';
    $summe = iconv('UTF-8', 'windows-1252', $summe); // Berechnen
    $pdf->SetFont('', 'B', '12');
    $pdf->Cell(160,6,'Summe:', 'R' , 0, 'R');
    $pdf->Cell(30,6, $summe ,0 , 1, 'L');
    
    // Überweisungsauftrag
    $pdf->Cell(0,5, '', 0, 1); // Platzhalter
    $pdf->SetFont('', '', '10');
    $pdf->Cell(0,10, '', 0, 1); // Platzhalter
    
    $info = utf8_decode("Bitte überweisen Sie die oben genannte Summe innerhalb von 14 Tagen ab Rechnungsdatum auf untenstehendes Konto. Als Verwendungszweck geben Sie bitte ``Abiball Eintrittskarten für $vorname $nachname`` an.\n\nTeilzahlungen sind nicht zulässig, Rückerstattung des Kaufpreises ist nicht möglich.");
    $pdf->MultiCell(0,5,$info);
}

$data = $mysqli->query("SELECT * FROM bestellungen WHERE userID = $encUserID AND bezahlt = 1");
if ($data->num_rows != 0) {
    $rechnungenVorhanden = true;
    
    $auftrag = utf8_decode("\nKunde: $vorname $nachname\n\nKundennummer: $userID");
    
    // PDF-Seite erstellen
    $pdf->AddPage();
    $pdf->SetFont('MontS','',24);
    $pdf->Cell(0,10,'Rechnung Kartenbestellung',0,1,'C'); //Titel
    $pdf->ln(); // Leere Zeile
    $pdf->SetFont('Times','',12);
    $pdf->Cell(10,3); // Einrückung
    $pdf->MultiCell(0,3,$auftrag,0,'L'); //Auftragsbeschreibung
    $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
    // Tabelle
    //Tabellenüberschrift
    $pdf->SetFont('', 'B', '');
    $pdf->Cell(35,5,'Kartennummer','BR', 0, 'R');
    $pdf->Cell(95,5,'Kartenbeschreibung','BR', 0,'L');
    $pdf->Cell(30,5,'Bezahlt am','BR', 0, 'L');
    $pdf->Cell(30,5,'Preis','B', 0, 'L');
    $pdf->ln();
    $pdf->SetFont('', '', '10');
    
    $kosten = 0;
    
    // Für jede Bestellung eine Seite
    while (($row = $data->fetch_assoc()) != null) {
        // Daten sammeln
        
        $Nummer = $row['id'];
        $Nummern .= ", " . $Nummer;
        
        $Datum = $row['bestelltAm'];
        $Datum = date('d.m.Y', $Datum);
        $Zahltag = $row['bezahltAm'];
        $Zahltag = date('d.m.Y', $Zahltag);
        $Name = $row['name'];
        
        $kosten += $row['preis'];
        $preis = number_format((float)$row['preis'], 2, ',', '.') . '€';
        $preis = iconv('UTF-8', 'windows-1252', $preis);
        
        $pdf->Cell(35,5, $Nummer, 'BR', 0, 'R');
        $pdf->Cell(95,5, utf8_decode('Persönliche Eintrittskarte für '. $Name), 'BR', 0,'L');
        $pdf->Cell(30,5, $Zahltag, 'BR', 0, 'L');
        $pdf->Cell(30,5, $preis, 'B', 0, 'L');
        $pdf->ln();
    }
    
    //Summe
    $summe = number_format((float)$kosten, 2, ',', '.') . '€';
    $summe = iconv('UTF-8', 'windows-1252', $summe); // Berechnen
    $pdf->SetFont('', 'B', '12');
    $pdf->Cell(160,6,'Summe:', 'R' , 0, 'R');
    $pdf->Cell(30,6, $summe ,0 , 1, 'L');
    
    // Überweisungsauftrag
    $pdf->Cell(0,5, '', 0, 1); // Platzhalter
    $pdf->SetFont('', '', '10');
    $pdf->Cell(0,10, '', 0, 1); // Platzhalter
    
    $pdf->SetFont('', 'B', '');
    $write = utf8_decode("Diese Rechnung wurde bereits beglichen. Bitte überweisen Sie das Geld NICHT nochmals.\nDie Bestellung wird hier bloß aus Gründen der Vollständigkeit und zum Verfügungstellen der Daten aufgeführt.");
    $pdf->MultiCell(0,5,$write);
    $pdf->SetFont('', '', '');
    $write = utf8_decode("Vielen Dank für ihre Bestellung.");
    $pdf->MultiCell(0,5,$write);
    
}

if (!$rechnungenVorhanden) {
    // PDF-Seite erstellen
    $pdf->AddPage();
    $pdf->SetFont('MontS','',24);
    $pdf->Cell(0,10,'Sie haben noch keine Karten bestellt.',0,1,'C'); //Titel
}


// Letzte Seite, Rechtliches
/*
if ($bausteine[7] != "") {
    $pdf->AddPage();
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(0, 5, 'Rechtliche Hinweise', 0, 1, 'C');
    $pdf->Ln();
    $pdf->SetFont('', '', '10');
    $pdf->MultiCell(0, 5, $recht);
}
*/

$Nummern = substr($Nummern, 2);

//Meta Tags
$pdf->AliasNbPages();
$pdf->SetCreator($meta['pageName']);
$pdf->SetSubject('Rechnungen mit den Nummern '. $Nummern .' zur Kartenbestellung');
$pdf->SetTitle('Kartenbestellung Abiball 2017');

$pdf->Output('Kartenbestellungen '. $Nummern .'.pdf', 'D'); // Datei zum Speichern ausgeben
//$pdf->Output(); //Kontrollausgabe im Fenster