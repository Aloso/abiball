<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

$data = $mysqli->query("SELECT u.id, u.vorname, u.nachname, COUNT(*) anzahl FROM bestellungen b JOIN user u ON u.id = b.userID GROUP BY u.id");
$bestellData = array();
$i = 0;
while (($row = $data->fetch_assoc()) != null) {
    $bestellData[$i++] = $row;
}

$data = $mysqli->query("SELECT r.prioritaet prioritaet, u1.id u1id, u2.id u2id
FROM reservierung r
    JOIN user u1 ON r.userID = u1.id
    JOIN user u2 ON r.wunschUserID = u2.id");

$newData = array();
$i = 0;
while (($row = $data->fetch_assoc()) != null) {
    $newData[$i++] = $row;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Sitzplaner</title>
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700" rel="stylesheet">
    <link rel="icon" href="../favicon.ico">
    <style>
        * {
            font-family: Roboto, sans-serif;
        }
        html {
            margin: 0;
        }
        body {
            margin: 0;
            height: 100%;
        }
        button::-moz-focus-inner,
        [type="button"]::-moz-focus-inner,
        [type="reset"]::-moz-focus-inner,
        [type="submit"]::-moz-focus-inner {
            border-style: none;
            padding: 0;
        }

        #header {
            background-color: #eeeeee;
            border-bottom: 1px solid #cccccc;
            padding: 0 10px;
            font-size: 18px;
            height: 39px;
            line-height: 39px;
        }
        #header button {
            line-height: 25px;
            padding: 1px 6px;
            background-color: #dddddd;
            border: 1px solid #bbbbbb;
            font-size: 94%;
            border-radius: 3px;
        }
        #header button:hover {
            background-color: #cccccc;
        }
        #header input {
            line-height: 25px;
            padding: 1px 6px;
            background-color: white;
            border: 1px solid #bbbbbb;
            font-size: 94%;
            border-radius: 3px;
            width: 30px;
        }

        #canvas {
            position: fixed;
            left: 0;
            top: 40px;
            right: 0;
            bottom: 0;
            cursor: default;
        }

        #overlay {
            position: fixed;
            left: 0;
            top: 40px;
            right: 0;
            bottom: 0;
            background-color: rgba(255,46,0,0);
        }
    </style>
</head>
<body>
<div id="header">
    <b>Sitzplaner</b> &nbsp;
    <button id="createTable">Neuer Tisch</button> &nbsp;
    <button id="deleteTables">Tische löschen</button> &nbsp;
    <button id="showAllNames">Namen zeigen</button> &nbsp;
    Linien schmaler als <input type="number" id="hideThinLines" value="1" min="1"> verbergen
</div>
<canvas id="canvas">Dieser Browser ist veraltet und unterstützt das Canvas-Element nicht.</canvas>

<!--suppress SillyAssignmentJS, ES6ConvertVarToLetConst -->
<script type="text/javascript">
    "use strict";
    var i;

    function getWindowWidth() {
        return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    }
    function getWindowHeight() {
        return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
    }
    function isOnCircle(posX, posY, circleX, circleY, radius) {
        var distX = circleX - posX;
        var distY = circleY - posY;
        return Math.sqrt(distX * distX + distY * distY) <= radius;
    }
    function isOnRect(posX, posY, rectX1, rectY1, rectX2, rectY2) {
        return posX >= rectX1 && posX <= rectX2 &&
               posY >= rectY1 && posY <= rectY2;
    }

    function revalidateTables() {
        for (var i = 0; i < tables.length; i++) {
            var tisch = tables[i];
            tisch.personen = 0;

            for (var j = 0; j < userData.length; j++) {
                var user = userData[j];
                if (isOnRect(user.pos.x, user.pos.y, tisch.x1, tisch.y1, tisch.x2, tisch.y2)) {
                    tisch.personen += +user.anzahl;
                }
            }
        }
    }

    var userSort = [];

    /** @type {Array.<{
    *       id: string,
    *       vorname: string,
    *       nachname: string,
    *       anzahl: string,
    *
    *
    *       pos: {x: int, y: int},
    *       isSelected: boolean
    * }>} */
    var userData = <?php echo json_encode($bestellData); ?>;
    var idx = 0;
    var storedUserData = localStorage.userData ? JSON.parse(localStorage.userData) : {};

    for (i = 0; i < userData.length; i++) {
        var user = userData[i];
        var id = +user["id"];
        userSort[id] = user;
        user.isSelected = false;

        if (id in storedUserData) {
            user.pos = storedUserData[id]
        } else {
            user.pos = {x: 40 * (idx % 10) + 30, y: 30 + (idx - idx % 10) / 10 * 40};
            idx++;
        }
    }

    /** @type {Array.<{
    *       prioritaet: string,
    *       u1id: string,
    *       u2id: string,
    *       mail: string,
    *       stat: string,
    *       personenAnz: string
    * }>} */
    var data = <?php echo json_encode($newData); ?>;
    var lineData = [];
    for (i = 0; i < data.length; i++) {
        var identifier = Math.min(+data[i]['u1id'], +data[i]['u2id']) + "-" +
                Math.max(+data[i]['u1id'], +data[i]['u2id']);
        if (lineData[identifier] !== undefined) {
            lineData[identifier]['prioritaet'] += +data[i]['prioritaet'] + 1;
        } else {
            lineData[identifier] = data[i];
            lineData[identifier]['prioritaet'] = +lineData[identifier]['prioritaet'];
        }
    }
    i = 0;
    for (var x in lineData) {
        lineData[i++] = lineData[x];
    }

    var tables = [];
    if (localStorage.tables) tables = JSON.parse(localStorage.tables)



    var modes = {
        NONE: 0,
        DRAG: 1,
        CREATE_TABLE: 2,
        CREATE_TABLE_DRAG: 3,
        NORMAL_DRAG: 4,
        MULTI_DRAG: 5
    };
    var mode = modes.NONE;

    var radiusSmall = 18;
    var radiusBig = 28;
    var diameterSmall = radiusSmall * 2 - 7;
    var diameterBig = radiusBig * 2 - 4;

    var minPriority = 1;
    var showAllNames = false;
    var epsilon = 20;

    var mousePos = { x: 0, y: 0 };
    var oldMousePos = mousePos;
    var mouseDownPos = mousePos;
    var mouseDownOffset = mousePos;

    var focusedUser = null;
    var selectedUser = [];

    var positionsChanged = false;

    var canvas = document.getElementById("canvas");
    var context = canvas.getContext("2d");
    var buttons = {
        createTable: document.getElementById("createTable"),
        deleteTables: document.getElementById("deleteTables"),
        hideThinLines: document.getElementById("hideThinLines"),
        showAllNames: document.getElementById("showAllNames")
    };

    buttons.createTable.onclick = function () {
        if (mode !== modes.CREATE_TABLE) {
            mode = modes.CREATE_TABLE;
            buttons.createTable.innerHTML = "Abbrechen";
            canvas.style.backgroundImage = 'url("../resources/raster.png")';
        } else {
            mode = modes.NONE;
            buttons.createTable.innerHTML = "Neuer Tisch";
            canvas.style.backgroundImage = 'none';
        }
    };

    buttons.deleteTables.onclick = function () {
        tables = [];
        redraw();
    };

    buttons.hideThinLines.oninput = buttons.hideThinLines.onkeyup = function () {
        minPriority = buttons.hideThinLines.value;
        redraw();
    };

    buttons.showAllNames.onclick = function () {
        showAllNames = !showAllNames;
        buttons.showAllNames.innerHTML = showAllNames ? "Namen verbergen" : "Namen zeigen";
        redraw();
    };


    window.onresize = function () {
        var dpr = window.devicePixelRatio;
        var w = getWindowWidth();
        var h = getWindowHeight() - 40;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        redraw();
    };
    window.onresize(null);

    window.onmousemove = function (e) {
        e = e || window.event;
        oldMousePos = mousePos;
        mousePos = {
            x: e.clientX,
            y: e.clientY - 40
        };
        switch (mode) {
            case modes.NONE:
                for (var i = userData.length - 1; i >= 0; i--) {
                    var pos = userData[i].pos;
                    if (isOnCircle(mousePos.x, mousePos.y, pos.x, pos.y, radiusSmall)) {
                        focusedUser = userData[i];
                        break;
                    }
                    focusedUser = null;
                }
                break;
        }
        redraw();
    };

    canvas.onmousedown = function (e) {
        e = e || window.event;
        oldMousePos = mousePos;
        mousePos = {
            x: e.clientX,
            y: e.clientY - 40
        };

        switch (mode) {
            case modes.NONE:
                if (focusedUser !== null) {
                    if (selectedUser.length === 0 || !focusedUser.isSelected) {
                        if (selectedUser.length > 0) {
                            for (i = 0; i < selectedUser.length; i++) {
                                selectedUser[i].isSelected = false;
                            }
                            selectedUser = [];
                        }

                        mode = modes.DRAG;
                        mouseDownPos = focusedUser.pos;
                        mouseDownOffset = {
                            x: mousePos.x - mouseDownPos.x,
                            y: mousePos.y - mouseDownPos.y
                        };
                        userData.splice(userData.indexOf(focusedUser), 1);
                        userData.push(focusedUser);
                    } else {
                        mode = modes.MULTI_DRAG;
                        mouseDownPos = mousePos;
                    }
                } else {
                    if (!e.ctrlKey) {
                        selectedUser = [];
                        for (i = 0; i < userData.length; i++) {
                            userData[i].isSelected = false;
                        }
                    }

                    mode = modes.NORMAL_DRAG;
                    mouseDownPos = mousePos;
                }
                break;
            case modes.CREATE_TABLE:
                selectedUser = [];
                for (i = 0; i < userData.length; i++) {
                    userData[i].isSelected = false;
                }

                mode = modes.CREATE_TABLE_DRAG;
                mouseDownPos = mousePos;

                mouseDownPos.x = Math.round(mouseDownPos.x / epsilon) * epsilon;
                mouseDownPos.y = Math.round(mouseDownPos.y / epsilon) * epsilon;

                break;
            default:
                selectedUser = [];
                for (i = 0; i < userData.length; i++) {
                    userData[i].isSelected = false;
                }
        }
        redraw();
    };

    window.onmouseup = function (e) {
        e = e || window.event;
        oldMousePos = mousePos;
        mousePos = {
            x: e.clientX,
            y: e.clientY - 40
        };

        var xsm, ysm, xbg, ybg

        switch (mode) {
            case modes.NONE:
                break;
            case modes.DRAG:
                mode = modes.NONE;
                revalidateTables();
                break;
            case modes.CREATE_TABLE_DRAG:
                mode = modes.CREATE_TABLE;

                xsm = Math.min(mousePos.x, mouseDownPos.x);
                ysm = Math.min(mousePos.y, mouseDownPos.y);
                xbg = Math.max(mousePos.x, mouseDownPos.x);
                ybg = Math.max(mousePos.y, mouseDownPos.y);

                // Teste, ob der Tisch klein genug ist, dass der Tisch sofort gelöscht wird
                if (isOnCircle(xsm, ysm, xbg, ybg, 30)) {
                    mode = modes.NONE;
                    canvas.style.backgroundImage = 'none';
                    buttons.createTable.innerHTML = "Neuer Tisch";
                } else {
                    xsm = Math.round(xsm / epsilon) * epsilon;
                    ysm = Math.round(ysm / epsilon) * epsilon;
                    xbg = Math.round(xbg / epsilon) * epsilon;
                    ybg = Math.round(ybg / epsilon) * epsilon;

                    tables.push({x1: xsm, y1: ysm, x2: xbg, y2: ybg, personen: 0});
                    revalidateTables();
                }

                break;
            case modes.NORMAL_DRAG:
                xsm = Math.min(mousePos.x, mouseDownPos.x);
                ysm = Math.min(mousePos.y, mouseDownPos.y);
                xbg = Math.max(mousePos.x, mouseDownPos.x);
                ybg = Math.max(mousePos.y, mouseDownPos.y);

                for (i = 0; i < userData.length; i++) {
                    var user = userData[i];
                    if (!user.isSelected && isOnRect(user.pos.x, user.pos.y, xsm, ysm, xbg, ybg)) {
                        user.isSelected = true;
                        selectedUser.push(user);
                    }
                }
                mode = modes.NONE;
                canvas.style.backgroundImage = 'none';
                break;
            case modes.MULTI_DRAG:
                mode = modes.NONE;
                canvas.style.backgroundImage = 'none';
                revalidateTables();
                break;
        }
        redraw();
    };

    function redraw() {
        positionsChanged = true;
        canvas.width = canvas.width;

        switch (mode) {
            case modes.CREATE_TABLE:
            case modes.NONE:
                break;
            case modes.DRAG:
                focusedUser.pos = {
                    x: mousePos.x - mouseDownOffset.x,
                    y: mousePos.y - mouseDownOffset.y
                };
                break;
            case modes.MULTI_DRAG:
                var diffX = mousePos.x - mouseDownPos.x;
                var diffY = mousePos.y - mouseDownPos.y;
                mouseDownPos = mousePos;

                for (i = 0; i < selectedUser.length; i++) {
                    var user = selectedUser[i];
                    user.pos.x += diffX;
                    user.pos.y += diffY;
                }
        }
        basicRedraw();
    }

    redraw();

    setInterval(function () {
        if (positionsChanged) {
            localStorage.tables = JSON.stringify(tables);

            var storedUserData = {}
            userData.forEach(function (user) {
                storedUserData[user.id] = user.pos;
            })
            localStorage.userData = JSON.stringify(storedUserData)
        }
    }, 500)


    function basicRedraw() {
        var dpr = window.devicePixelRatio;
        context.lineWidth = 0.4 * dpr;
        context.textAlign = "center";

        var endAngle = 2 * Math.PI;

        for (var i = 0; i < tables.length; i++) {
            var tisch = tables[i];

            context.strokeStyle = "#999999";
            context.fillStyle = "#F7F7F7";
            context.textAlign = "right";

            context.fillRect(tisch.x1 * dpr, tisch.y1 * dpr, (tisch.x2 - tisch.x1) * dpr, (tisch.y2 - tisch.y1) * dpr);
            context.strokeRect(tisch.x1 * dpr, tisch.y1 * dpr, (tisch.x2 - tisch.x1) * dpr, (tisch.y2 - tisch.y1) * dpr);

            context.fillStyle = "#BBBBBB";
            context.font = (18 * dpr) + "px Roboto";
            context.fillText(tisch.personen, (tisch.x2 - 7) * dpr, (tisch.y1 + 22) * dpr);

            context.textAlign = "center";
            context.strokeStyle = "#000000";
        }

        context.strokeStyle = "rgba(255, 0, 0, 0.4)";
        for (i = 0; i < lineData.length; i++) {
            var line = lineData[i];
            if (line.prioritaet < minPriority) continue;

            var user1 = userSort[+line["u1id"]];
            var user2 = userSort[+line["u2id"]];

            context.beginPath();
            context.lineWidth = line.prioritaet * 0.7 * dpr;
            context.moveTo(user1.pos.x * dpr, user1.pos.y * dpr);
            context.lineTo(user2.pos.x * dpr, user2.pos.y * dpr);
            context.stroke();
        }
        if (!showAllNames) {
            context.lineWidth = 0.4 * dpr;
            for (i = 0; i < userData.length; i++) {
                var user = userData[i];
                context.beginPath();
                context.arc(user.pos.x * dpr, user.pos.y * dpr, radiusSmall * dpr, 0, endAngle, false);

                if (user === focusedUser) context.fillStyle = "#DDDDDD";
                else context.fillStyle = "#EEEEEE";

                if (user.isSelected) {
                    context.strokeStyle = "#0065dd";
                    context.lineWidth = 2 * dpr;
                    context.fillStyle = "#cbe5f9";
                } else {
                    context.strokeStyle = "#000000";
                    context.lineWidth = 0.4 * dpr;
                }

                context.fill();
                context.fillStyle = "#000000";
                context.font = (18 * dpr) + "px Roboto";
                context.fillText(user.anzahl, user.pos.x * dpr, (user.pos.y + 7) * dpr, diameterSmall * dpr);
                context.stroke();
            }
        } else {
            context.lineWidth = 0.4 * dpr;
            context.font = (12 * dpr) + "px Roboto";

            for (i = 0; i < userData.length; i++) {
                user = userData[i];
                context.beginPath();
                context.arc(user.pos.x * dpr, user.pos.y * dpr, radiusBig * dpr, 0, endAngle, false);

                if (user === focusedUser) context.fillStyle = "#DDDDDD";
                else context.fillStyle = "#EEEEEE";

                if (user.isSelected) {
                    context.strokeStyle = "#0065dd";
                    context.lineWidth = 2 * dpr;
                    context.fillStyle = "#cbe5f9";
                } else {
                    context.strokeStyle = "#000000";
                    context.lineWidth = 0.4 * dpr;
                }

                context.fill();
                context.fillStyle = "#000000";
                context.fillText(user.vorname, user.pos.x * dpr, (user.pos.y - 4) * dpr, diameterBig * dpr);
                context.fillText(user.nachname, user.pos.x * dpr, (user.pos.y + 10) * dpr, diameterBig * dpr);
                context.stroke();
            }
        }


        context.strokeStyle = "#000000";
        context.lineWidth = 0.4 * dpr;
        if (mode === modes.CREATE_TABLE) {
            context.fillStyle = "#2277ff";
            context.font = (16 * dpr) + "px Roboto";

            context.fillText("Klicken und ziehen, um", mousePos.x * dpr, (mousePos.y - 60) * dpr);
            context.fillText("einen Tisch zu erstellen", mousePos.x * dpr, (mousePos.y - 35) * dpr);
            context.fillText("Klicken zum abbrechen", mousePos.x * dpr, (mousePos.y + 50) * dpr);

            context.textAlign = "center";
        } else if (mode === modes.CREATE_TABLE_DRAG || mode === modes.NORMAL_DRAG) {
            context.strokeStyle = "#2277ff";
            context.fillStyle = "rgba(0, 107, 255, 0.2)";
            context.fillRect(mouseDownPos.x * dpr, mouseDownPos.y * dpr,
                    (mousePos.x - mouseDownPos.x) * dpr, (mousePos.y - mouseDownPos.y) * dpr);
            context.strokeRect(mouseDownPos.x * dpr, mouseDownPos.y * dpr,
                    (mousePos.x - mouseDownPos.x) * dpr, (mousePos.y - mouseDownPos.y) * dpr);

            context.strokeStyle = "#000000";
        }
        if (focusedUser !== null && mode !== modes.DRAG && mode !== modes.MULTI_DRAG && !showAllNames) {
            user = focusedUser;
            context.fillStyle = "#F7F7F7";

            context.fillRect((user.pos.x - 70.5) * dpr, (user.pos.y - 65.5) * dpr, 140 * dpr, 43 * dpr);
            context.strokeRect((user.pos.x - 70.5) * dpr, (user.pos.y - 65.5) * dpr, 140 * dpr, 43 * dpr);

            context.fillStyle = "#000000";
            context.font = (14 * dpr) + "px Roboto";

            context.fillText(user.vorname, user.pos.x * dpr, (user.pos.y - 49) * dpr, 130 * dpr);
            context.fillText(user.nachname, user.pos.x * dpr, (user.pos.y - 30) * dpr, 130 * dpr);

            context.beginPath();
            context.fillStyle = "#F7F7F7";

            context.moveTo((user.pos.x - 10) * dpr, (user.pos.y - 23) * dpr);
            context.lineTo(user.pos.x * dpr,        (user.pos.y - 13) * dpr);
            context.lineTo((user.pos.x + 10) * dpr, (user.pos.y - 23) * dpr);
            context.fill();
            context.stroke();
        }
    }


</script>
</body>
</html>
