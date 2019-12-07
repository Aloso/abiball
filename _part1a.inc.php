<!DOCTYPE html>
<html lang="de">
<head>
    <title><?php echo $meta['pageName'] ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="basic2_style.css">
    <link rel="icon" href="favicon.ico">
</head>
<body>
<script type="text/javascript">
    setTimeout(function () {
        window.location.href = window.location.href;
    }, <?php echo ($meta['loginTimeout'] * 1000) + 10000; ?>);
</script>
<div id="header1">
    <h1><?php echo $meta['pageName'] ?></h1><br>
    <h2><?php echo $meta['pageSubtitle'] ?></h2>
</div>
<?php
if (isset($status)) {
    echo '<div id="rightTop">
        Eingeloggt als <b>' . $vorname . ' ' . $nachname . '</b>
        <div style="height: 10px"></div>';
    
    if ($status == 'admin') echo '<a href="admin">Admin-Bereich</a>';
    
    echo '<a href="profil.php">Profil</a>
        <a href="logout.php">Logout</a>
    </div>';
}

if (isset($status) && $status != 'incomplete') {
    echo '<div id="footer">
        <div id="footerInner">
            <a href="index.php">Home</a>
            <a href="bestellung.php">Kartenbestellung</a>
            <a href="forum.php">Forum</a>
            <a href="location.php">Location</a>
            <a href="menue.php">Menü</a>
            <a href="impressum.php">Impressum</a>
        </div>
    </div>';
}
?>
