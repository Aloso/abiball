<!DOCTYPE html>
<html>
<head>
    <title>Admin-Bereich</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<script type="text/javascript">
    setTimeout(function () {
        window.location.href = window.location.href;
    }, <?php echo ($meta['loginTimeout'] * 1000) + 10000; ?>);
</script>

<div id="header">
    <a href="index.php">Admin-Bereich</a> <hr>
    <a href="useruebersicht.php">User</a>
    <a href="bestelluebersicht.php">Bestellungen</a>
    <a href="settings.php">Einstellungen</a>
    <a href="pageTexts.php">Seitentexte</a>
    <a href="../index.php" style="float: right;">Zurück</a>
</div>
<div id="content">