<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

if (!isset($_POST['name'])) {
    header('Location: bestellung2.php?error=invalidRequest');
    exit;
}

$name = $_POST['name'];
if ($name == '') {
    header('Location: bestellung2.php?error=emptyString');
    exit;
}
if (strlen($name) < 5) {
    header('Location: bestellung2.php?error=shortString');
    exit;
}
if (strlen($name) > 50) {
    header('Location: bestellung2.php?error=longString');
    exit;
}

$encName = $mysqli->real_escape_string($name);
$now = time();
$preis = $mysqli->real_escape_string($meta['preis']);
$round = $mysqli->real_escape_string($meta['currentRound']);

if ($meta['currentRound'] == 0) {
    header('Location: bestellung2.php?error=orderingDisabled');
    exit;
} else {
    
    
    if ($meta['perUser'] == 1) {
        
        $orderedInThisRound = $mysqli->query("SELECT COUNT(*) FROM bestellungen
                WHERE round = $meta[currentRound] AND userID = $userID");
        $orderedInThisRound = $orderedInThisRound->fetch_assoc()['COUNT(*)'];
        $anz = $meta['availableCards'] - $orderedInThisRound;
        
        if ($anz > 0) {
            
            $success = $mysqli->query("INSERT INTO bestellungen (userID, name, bestelltAm, bezahltAm, preis, round)
                    VALUES ($userID, '$encName', $now, null, $preis, $round)");
            
            if ($success) {
                header('Location: bestellung2.php?ordered=' . urlencode($name));
                exit;
            } else {
                header('Location: bestellung2.php?error=databaseError');
                exit;
            }
            
        } else {
            header('Location: bestellung2.php?error=noCardsAvailablePerUser');
            exit;
        }
        
    } else {
        
        $orderedInThisRound = $mysqli->query("SELECT COUNT(*) FROM bestellungen
                WHERE round = $meta[currentRound]");
        $orderedInThisRound = $orderedInThisRound->fetch_assoc()['COUNT(*)'];
        $anz = $meta['availableCards'] - $orderedInThisRound;
        
        if ($anz > 0) {
        
            $success = $mysqli->query("INSERT INTO bestellungen (userID, name, bestelltAm, bezahltAm, preis, round)
                    VALUES ($userID, '$encName', $now, null, $preis, $round)");
            
            if ($success) {
                header('Location: bestellung2.php?ordered=' . urlencode($name));
                exit;
            } else {
                header('Location: bestellung2.php?error=databaseError');
                exit;
            }
        
        } else {
            header('Location: bestellung2.php?error=noCardsAvailablePerAll');
            exit;
        }
        
    }
    
}