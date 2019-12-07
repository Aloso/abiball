<?php

ob_clean();

if (!isset($meta)) {
    require_once "../resources/settings.inc.php";
    $error = 'Zugriff verweigert';
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <title><?php echo $meta['pageName'] ?></title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="../basic_style.css">
</head>
<body>
<div id="header1">
    <h1><?php echo $meta['pageName'] ?></h1><br>
    <h2><?php echo $meta['pageSubtitle'] ?></h2>
</div>

<div class="message error">
    <h3 style="margin-top: 0">Ein Fehler ist aufgetreten:</h3>
    <?php echo (isset($error) ? $error : 'unbekannt'); ?><br><br><br><br>
    <a class="button" href="../index.php">Zur Startseite</a><br><br>
    Webmaster kontaktieren: <i><?php echo $meta['webmasterMail']; ?></i>
</div>
</body>
</html>
<?php exit; ?>
