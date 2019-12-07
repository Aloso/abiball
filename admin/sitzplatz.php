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
            width: 50px;
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
    <button id="createTable">Tisch erstellen</button> &nbsp;
    <button id="deleteTables">Tische löschen</button> &nbsp;
    <button id="showAllNames">Alle Namen zeigen</button> &nbsp;
    Linien verbergen die schmaler sind als: <input type="number" id="hideThinLines" value="1" min="1">
</div>
<canvas id="canvas">Dieser Browser ist veraltet und unterstützt das Canvas-Element nicht.</canvas>

<!--suppress SillyAssignmentJS -->
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
        for (var i = 0; i < tische.length; i++) {
            var tisch = tische[i];
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
    for (i = 0; i < userData.length; i++) {
        var user = userData[i];
        user.pos = {x: 40 * (i % 10) + 30, y: 30 + (i - i % 10) / 10 * 40};
        userSort[+user["id"]] = user;
        user.isSelected = false;
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
    
    var tische = [];
    
    
    
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
    var durchmesserSmall = radiusSmall * 2 - 7;
    var durchmesserBig = radiusBig * 2 - 4;
    
    var minPrioritaet = 1;
    var showAllNames = false;
    var epsilon = 20;
    
    var mousePos = { x: 0, y: 0 };
    var oldMousePos = mousePos;
    var mouseDownPos = mousePos;
    var mouseDownOffset = mousePos;

    var focusedUser = null;
    var selectedUser = [];
    
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
        } else {
            mode = modes.NONE;
            buttons.createTable.innerHTML = "Tisch erstellen";
        }
    };

    buttons.deleteTables.onclick = function () {
        tische = [];
        redraw();
    };
    
    buttons.hideThinLines.oninput = buttons.hideThinLines.onkeyup = function () {
        minPrioritaet = buttons.hideThinLines.value;
        redraw();
    };
    
    buttons.showAllNames.onclick = function () {
        showAllNames = !showAllNames;
        buttons.showAllNames.innerHTML = showAllNames ? "Namen verbergen" : "Alle Namen zeigen";
        redraw();
    };
    
    
    window.onresize = function () {
        canvas.width = getWindowWidth();
        canvas.height = getWindowHeight() - 40;
        redraw();
    };
    window.onresize();
    
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
                canvas.style.backgroundImage = 'url("../resources/raster.png")';
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
        
        canvas.style.backgroundImage = 'none';
    
        switch (mode) {
            case modes.NONE:
                break;
            case modes.DRAG:
                mode = modes.NONE;
                revalidateTables();
                break;
            case modes.CREATE_TABLE_DRAG:
                mode = modes.CREATE_TABLE;
    
                var xsm = Math.min(mousePos.x, mouseDownPos.x);
                var ysm = Math.min(mousePos.y, mouseDownPos.y);
                var xbg = Math.max(mousePos.x, mouseDownPos.x);
                var ybg = Math.max(mousePos.y, mouseDownPos.y);
                
                // Teste, ob der Tisch klein genug ist, dass der Tisch sofort gelöscht wird
                if (isOnCircle(xsm, ysm, xbg, ybg, 30)) {
                    mode = modes.NONE;
                    buttons.createTable.innerHTML = "Tisch erstellen";
                } else {
                    xsm = Math.round(xsm / epsilon) * epsilon;
                    ysm = Math.round(ysm / epsilon) * epsilon;
                    xbg = Math.round(xbg / epsilon) * epsilon;
                    ybg = Math.round(ybg / epsilon) * epsilon;
                    
                    tische.push({x1: xsm, y1: ysm, x2: xbg, y2: ybg, personen: 0});
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
                break;
            case modes.MULTI_DRAG:
                mode = modes.NONE;
                revalidateTables();
                break;
        }
        redraw();
    };
    
    function redraw() {
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
    
    
    function basicRedraw() {
        context.lineWidth = 0.4;
        context.textAlign = "center";
        
        var endAngle = 2 * Math.PI;
        
        for (var i = 0; i < tische.length; i++) {
            var tisch = tische[i];
            
            context.strokeStyle = "#999999";
            context.fillStyle = "#F7F7F7";
            context.textAlign = "right";
            
            context.fillRect(tisch.x1, tisch.y1, tisch.x2 - tisch.x1, tisch.y2 - tisch.y1);
            context.strokeRect(tisch.x1, tisch.y1, tisch.x2 - tisch.x1, tisch.y2 - tisch.y1);
    
            context.fillStyle = "#BBBBBB";
            context.font = "18px Roboto";
            context.fillText(tisch.personen, tisch.x2 - 7, tisch.y1 + 22);
    
            context.textAlign = "center";
            context.strokeStyle = "#000000";
        }
        
        context.strokeStyle = "rgba(255, 0, 0, 0.4)";
        for (i = 0; i < lineData.length; i++) {
            var line = lineData[i];
            if (line.prioritaet < minPrioritaet) continue;
            
            var user1 = userSort[+line["u1id"]];
            var user2 = userSort[+line["u2id"]];
            
            context.beginPath();
            context.lineWidth = line.prioritaet * 0.7;
            context.moveTo(user1.pos.x, user1.pos.y);
            context.lineTo(user2.pos.x, user2.pos.y);
            context.stroke();
        }
        if (!showAllNames) {
            context.lineWidth = 0.4;
            for (i = 0; i < userData.length; i++) {
                var user = userData[i];
                context.beginPath();
                context.arc(user.pos.x, user.pos.y, radiusSmall, 0, endAngle, false);
        
                if (user === focusedUser) context.fillStyle = "#DDDDDD";
                else context.fillStyle = "#EEEEEE";
        
                if (user.isSelected) {
                    context.strokeStyle = "#0065dd";
                    context.lineWidth = 2;
                    context.fillStyle = "#cbe5f9";
                } else {
                    context.strokeStyle = "#000000";
                    context.lineWidth = 0.4;
                }
        
                context.fill();
                context.fillStyle = "#000000";
                context.font = "18px Roboto";
                context.fillText(user.anzahl, user.pos.x, user.pos.y + 7, durchmesserSmall);
                context.stroke();
            }
        } else {
            context.lineWidth = 0.4;
            context.font = "12px Roboto";
            
            for (i = 0; i < userData.length; i++) {
                user = userData[i];
                context.beginPath();
                context.arc(user.pos.x, user.pos.y, radiusBig, 0, endAngle, false);
        
                if (user === focusedUser) context.fillStyle = "#DDDDDD";
                else context.fillStyle = "#EEEEEE";
    
                if (user.isSelected) {
                    context.strokeStyle = "#0065dd";
                    context.lineWidth = 2;
                    context.fillStyle = "#cbe5f9";
                } else {
                    context.strokeStyle = "#000000";
                    context.lineWidth = 0.4;
                }
                
                context.fill();
                context.fillStyle = "#000000";
                context.fillText(user.vorname, user.pos.x, user.pos.y - 4, durchmesserBig);
                context.fillText(user.nachname, user.pos.x, user.pos.y  + 10, durchmesserBig);
                context.stroke();
            }
        }
        
        
        context.strokeStyle = "#000000";
        context.lineWidth = 0.4;
        if (mode === modes.CREATE_TABLE) {
            context.fillStyle = "#2277ff";
            context.font = "16px Roboto";
    
            context.fillText("Klicken und ziehen, um", mousePos.x, mousePos.y - 60);
            context.fillText("einen Tisch zu erstellen", mousePos.x, mousePos.y - 35);
            context.fillText("Klicken zum abbrechen", mousePos.x, mousePos.y + 50);
        
            context.textAlign = "center";
        } else if (mode === modes.CREATE_TABLE_DRAG || mode === modes.NORMAL_DRAG) {
            context.strokeStyle = "#2277ff";
            context.fillStyle = "rgba(0, 107, 255, 0.2)";
            context.fillRect(mouseDownPos.x, mouseDownPos.y,
                    mousePos.x - mouseDownPos.x, mousePos.y - mouseDownPos.y);
            context.strokeRect(mouseDownPos.x, mouseDownPos.y,
                    mousePos.x - mouseDownPos.x, mousePos.y - mouseDownPos.y);
    
            context.strokeStyle = "#000000";
        }
        if (focusedUser !== null && mode !== modes.DRAG && mode !== modes.MULTI_DRAG && !showAllNames) {
            user = focusedUser;
            context.fillStyle = "#F7F7F7";
            
            context.fillRect(user.pos.x - 70.5, user.pos.y - 65.5, 140, 43);
            context.strokeRect(user.pos.x - 70.5, user.pos.y - 65.5, 140, 43);
            
            context.fillStyle = "#000000";
            context.font = "14px Roboto";
            
            context.fillText(user.vorname, user.pos.x, user.pos.y - 49, 130);
            context.fillText(user.nachname, user.pos.x, user.pos.y - 30, 130);
    
            context.beginPath();
            context.fillStyle = "#F7F7F7";
            
            context.moveTo(user.pos.x - 10, user.pos.y - 23);
            context.lineTo(user.pos.x,       user.pos.y - 13);
            context.lineTo(user.pos.x + 10, user.pos.y - 23);
            context.fill();
            context.stroke();
        }
    }
    
    
</script>
</body>
</html>
