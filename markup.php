<?php

// This page provides functions to deal with MARKUP

function markupToHtml($text) {
    global $meta;

    // escape HTML characters

    $text = str_replace('&', '&amp;', $text);
    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);
    $text = str_replace("\n", '<br>', $text);
    $text = str_replace("\r", '', $text);

    // parse **fetter Text** und __kursiver Text__

    $text = preg_replace('/(^|\.| |<br>)\*\*(.+?)\*\*( |\.|,|:|;|!|\?|\.|<br>|$)/m', '${1}<b>${2}</b>${3}', $text);
    $text = preg_replace('/(^|\.| |<br>)__(.+?)__( |\.|,|:|;|!|\?|\.|<br>|$)/m', '${1}<i>${2}</i>${3}', $text);

    // parse headings

    $text = preg_replace('/(^|<br>)####(.+?)<br>/m', '${1}<h1>${2}</h1>', $text);
    $text = preg_replace('/(^|<br>)###(.+?)<br>/m', '${1}<h2>${2}</h2>', $text);
    $text = preg_replace('/(^|<br>)##(.+?)<br>/m', '${1}<h3>${2}</h3>', $text);

    // parse links

    $text = preg_replace('/{{a:(.+?)&gt;&gt;(.+?)}}/m', '<a href="${1}">${2}</a>', $text);

    // parse meta elements

    $patterns = array(
            '{{meta:date}}',
            '{{meta:time}}',

            '{{meta:pageName}}',
            '{{meta:pageSubtitle}}',
            '{{meta:googleMaps}}',
            '{{meta:webmasterMail}}',
            '{{meta:webmasterAdress}}',

            '{{meta:currentRound}}',
            '{{meta:availableCards}}',
            '{{meta:perUser}}',
            '{{meta:preis}}',
            '{{meta:zahlungsFrist}}',
            '{{meta:kontoinhaber}}',
            '{{meta:iban}}');
    $replacements = array(
            '<span class="date">' . date('d.m.Y') . '</span>',
            '<span class="time">' . date('H:i') . '</span>',

            '<span class="pageName">' . $meta['pageName'] . '</span>',
            '<span class="pageSubtitle">' . $meta['pageSubtitle'] . '</span>',
            '<span class="googleMaps">' . $meta['googleMaps'] . '</span>',
            '<span class="webmasterMail">' . $meta['webmasterMail'] . '</span>',
            '<span class="webmasterAdress">' . $meta['webmasterAdress'] . '</span>',

            '<span class="currentRound">' . $meta['currentRound'] . '</span>',
            '<span class="availableCards">' . $meta['availableCards'] . '</span>',
            '<span class="perUser">' . ($meta['perUser'] ? 'Pro Nutzer' : 'Insgesamt') . '</span>',
            '<span class="preis">' . $meta['preis'] . '</span>',
            '<span class="zahlungsFrist">' . $meta['zahlungsFrist'] . '</span>',
            '<span class="kontoinhaber">' . $meta['kontoinhaber'] . '</span>',
            '<span class="iban">' . $meta['iban'] . '</span>');
    $text = str_replace($patterns, $replacements, $text);

    // parse lists

    $text = preg_replace('~(<br>|^)@@<br>(.+?)<br>@@(<br>|$)~', '${1}<ul><br>${2}</ul>${3}', $text);
    $text = preg_replace('~(?!<ul>.*?)(<br>\*.+?)(?=<br>\* |<\/ul>)~', '<li>${1}</li>', $text);
    $text = preg_replace('~<li><br>\* ~', '<li>', $text);

    // parse tables

    $text = preg_replace('~(<br>|^)\+\+\+\+<br>(.+?)<br>\+\+\+\+(<br>|$)~', '${1}<table><br>${2}</table>${3}', $text);

    $text = preg_replace('~(?!<table>.*?)(<br>\+\+.+?)<br>\+\+(?=<br>\+\+<br>|<\/table>)~', '<tr>${1}</tr>', $text);
    $text = str_replace('<tr><br>++', '<tr>', $text);

    $text = preg_replace('~(?!<tr>.*?)(<br>\$.*?)(?=<br>\$ |<\/tr>)~', '<td>$1</td>', $text);
    $text = str_replace('<td><br>$ ', '<td>', $text);

    // remove redundant empty lines

    $text = preg_replace('~(<br>)*<(h1|h2|h3|ul|ol|table)>~', '<${2}>', $text);
    $text = preg_replace('~</(h1|h2|h3|ul|ol|table)>(<br>)*~', '</${1}>', $text);

    return $text;
}

function simpleMarkupToHtml($text) {

    // escape HTML characters

    $text = str_replace('&', '&amp;', $text);
    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);
    $text = str_replace("\n", '<br>', $text);
    $text = str_replace("\r", '', $text);

    // parse **fetter Text** und __kursiver Text__

    $text = preg_replace('/(^|\.| |<br>)\*\*(.+?)\*\*( |\.|,|:|;|!|\?|\.|<br>|$)/m', '${1}<b>${2}</b>${3}', $text);
    $text = preg_replace('/(^|\.| |<br>)__(.+?)__( |\.|,|:|;|!|\?|\.|<br>|$)/m', '${1}<i>${2}</i>${3}', $text);

    return $text;
}


function htmlToMarkup($text) {

    $patterns = array(
            '~<br>~',
            '~<b>~',
            '~</b>~',
            '~<i>~',
            '~</i>~',
            '~<a href="(.+?)">(.+?)</a>~',
            '~<h1>(.+?)</h1>~',
            '~<h2>(.+?)</h2>~',
            '~<h3>(.+?)</h3>~');
    $replacements = array(
            "\n",
            '**',
            '**',
            '__',
            '__',
            '{{a:${1}>>${2}}}',
            "\n\n####\${1}\n\n",
            "\n\n###\${1}\n\n");

    $text = preg_replace($patterns, $replacements, $text);

    // spans in meta-Elemente übersetzen:
    $text = preg_replace('~<span class="(.+?)">(.+?)</span>~s', '{{meta:${1}}}', $text);

    // Listen:

    $search = array('<ul>', '</ul>', '<li>', '</li>');
    $replacements = array("\n\n@@", "\n@@\n\n", "\n* ", '');
    $text = str_replace($search, $replacements, $text);

    // Tabellen:

    $search = array('<table>', '</table>', '<tr>', '</tr>', '<td>', '</td>');
    $replacements = array("\n\n++++", "\n++++\n\n", "\n++", "\n++", "\n$ ", "");
    $text = str_replace($search, $replacements, $text);

    // Replace unnecessary space

    $text = preg_replace("~(\n(?=\n\n)|^\n+|\n+$)~", '', $text);

    return $text;
}
