<?php

require_once 'PHPMailer-master/class.phpmailer.php';
require_once 'PHPMailer-master/class.smtp.php';

function phpmailerSend($adress, $subject, $htmlMailBody, $mailBody = '') {
    global $meta;

    $mail = new PHPMailer;

    $mail->SMTPDebug = 0;                                 // debugging: 1 = errors and messages, 2 = messages only
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $meta['webmasterMail'];             // SMTP username
    $mail->Password = WebmasterPassword;                  // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;

    $mail->setFrom($meta['webmasterMail']);
    $mail->addAddress($adress);
    $mail->addReplyTo($meta['webmasterMail']);

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = utf8_decode($subject);
    $mail->Body = '<html>
<head>
    <title>Aktivieren Sie Ihren Account</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700" rel="stylesheet">
</head>
<body>
    <div style="font-family: \'Roboto\', \'Open Sans\', \'Helvetica Neue\', \'Arial\', sans-serif; font-size: 17px; max-width:700px; margin: 15px auto;
    background-color: white; border: 1px solid #d7d7d7; padding: 18px">
    ' . utf8_decode($htmlMailBody) . '
    </div>
</body>
</html>';

    $mail ->AltBody = $mailBody;

    $result = $mail->send();
    if (!$result) echo $mail->ErrorInfo;

    return $result;
}
