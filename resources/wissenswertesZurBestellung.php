<h2>Alles, was Sie wissen müssen</h2>
<ul>
    <li>Eine Karte für den Abiball kostet <b><?php echo $meta['preis'] ?> Euro</b>. Dieser Betrag muss
        innerhalb von 14 Tagen ab Rechnungsdatum und vor dem <?php echo $meta['zahlungsFrist'] ?>
        an folgendes Konto überwiesen werden:
        <table>
            <tr><th>Kontoinhaber</th><td><?php echo $meta['kontoinhaber'] ?></td></tr>
            <tr><th>IBAN</th><td><?php echo $meta['iban'] ?></td></tr>
            <tr><th>Verwendungszweck</th><td>Abiball Eintrittskarten für <?php echo $vorname . ' ' . $nachname ?></td></tr>
        </table>
    <li>Bitte bezahlen Sie Ihre Karten <b>gesammelt</b>, falls Sie mehrere Karten bestellen.
    <li>Eine Karte kann im Allgemeinen nicht storniert, dafür aber auf eine andere Person
        übertragen werden.
    <li>Hierbei handelt es sich um einen <i>Privatverkauf</i>.
</ul>