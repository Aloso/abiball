<?php

session_start();
require_once "resources/settings.inc.php";

include "_part1.inc.php";

?>

<div class="message">
    <div style="text-align: left">
        <h3 style="margin-top: 0">Passwort vergessen</h3>
        <p>
            Wenn Sie sich zum ersten Mal anmelden, brauchen Sie das <b>voreingestellte Passwort</b>.
            Dieses sollten Sie von den Abiball-Organisatoren erhalten haben. Wenn Sie es verloren haben
            oder es nicht griffbereit ist, fragen Sie bitte jemand anderen danach.
        </p>
        <p>
            Wenn Sie bereits ein eigenes Passwort festgelegt haben und dieses vergessen haben, schreiben
            Sie bitte eine E-Mail an den Webmaster (<?php echo $meta['webmasterMail']; ?>).
            Achten Sie darauf, dass Sie die E-Mailadresse verwenden, die Sie auf dieser Website
            hinterlegt haben!
        </p>
    </div>
    <a href="login.php" class="button">Zurück</a>
</div>

<?php include "_part2.inc.php"; ?>