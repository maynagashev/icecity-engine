<?php

/******************************************************************************
 *                                                                            *
 *   xbb.js.php, v 0.01 2007/07/25 - This is part of xBB library              *
 *   Copyright (C) 2006-2007  Dmitriy Skorobogatov  dima@pc.uz                *
 *                                                                            *
 *   This program is free software; you can redistribute it and/or modify     *
 *   it under the terms of the GNU General Public License as published by     *
 *   the Free Software Foundation; either version 2 of the License, or        *
 *   (at your option) any later version.                                      *
 *                                                                            *
 *   This program is distributed in the hope that it will be useful,          *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 *   GNU General Public License for more details.                             *
 *                                                                            *
 *   You should have received a copy of the GNU General Public License        *
 *   along with this program; if not, write to the Free Software              *
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA *
 *                                                                            *
 ******************************************************************************/

header('Content-type: application/x-javascript; charset=utf-8');
require_once './config/editor.config.php';
$xbb = new Xbb;

?>
if (typeof XBB == "undefined") { var XBB = {}; }
<?php
// РЈРјРѕР»С‡Р°Р»СЊРЅС‹Рµ Р·РЅР°С‡РµРЅРёСЏ РЅР°СЃС‚СЂРѕРµРє:
echo  "XBB.path = '{$xbb->path}';"
    . "XBB.textarea_id = '{$xbb->textarea_id}';"
    . "XBB.area_width = '{$xbb->area_width}';"
    . "XBB.area_height = '{$xbb->area_height}';"
    . "XBB.state = '{$xbb->state}';"
    . "XBB.lang = '{$xbb->lang}';"
    . "XBB.iframe_id = 'xbb_editor';";

?>
XBB.init = function() {
    // РџСЂРѕРІРµСЂСЏРµРј, РІРѕР·РјРѕР¶РЅР° Р»Рё РїРѕРґСЃРІРµС‚РєР° СЃРёРЅС‚Р°РєСЃРёСЃР°
    if (! document.execCommand) {
        XBB.state = 'plain';
    }
    // Textarea, СЃ РєРѕС‚РѕСЂРѕР№ Р±СѓРґРµРј СЂР°Р±РѕС‚Р°С‚СЊ.
    var textarea = document.getElementById(XBB.textarea_id);
    if (! textarea) { return null; }
    // Р•СЃР»Рё iframe СѓР¶Рµ СЃРѕР·РґР°РЅ, РЅРёС‡РµРіРѕ РЅРµ РґРµР»Р°РµРј, Р·Р°РІРµСЂС€Р°РµРј РёРЅРёС†РёР°Р»РёР·Р°С†РёСЋ.
    if (document.getElementById(XBB.iframe_id)) { return null; }
    var parent_textarea = textarea.parentNode;
    // Create a invisible div is accessible from iframe for a transport text
    var div = document.createElement('div');
    div.setAttribute('id', 'xbb_transport_div');
    parent_textarea.appendChild(div);
    if (document.body.textContent) {
        div.textContent = textarea.value;
    } else {
        div.innerText = textarea.value;
    }
    div.style.display = 'none';
    // РЎРѕР·РґР°РµРј iframe.
    var iframe = document.createElement('iframe');
    iframe.id = XBB.iframe_id;
    iframe.frameBorder = 0;
    var src = XBB.path + '/xbb.php?state=' + XBB.state;
    if (XBB.lang) { src += '&lang=' + XBB.lang; }
    iframe.src = src;
    parent_textarea.insertBefore(iframe, textarea);
    iframe.style.width = XBB.area_width;
    iframe.style.height = XBB.area_height;
    iframe.style.border = '1px solid #a9b8c2';
    // РЎРєСЂС‹РІР°РµРј textarea
    textarea.style.display = 'none';
    // РќР°Р·РЅР°С‡Р°РµРј С„СѓРЅРєС†РёСЋ, СЃСЂР°Р±Р°С‚С‹РІР°СЋС‰СѓСЋ РїСЂРё СЃР°Р±РјРёС‚Рµ
    textarea.form.onsubmit = function(ev) { XBB.form_submit(); }
    return true;
}

XBB.form_submit = function() {
    var iframe = document.getElementById(XBB.iframe_id).contentWindow;
    var state = iframe.document.forms.xbb.xbb_state.value;
    var textarea = document.getElementById(XBB.textarea_id);
    if ('highlight' == state) {
        var xbb_iframe = iframe.document.getElementById('xbb_iframe').contentWindow;
        try {
            textarea.value = XBB.innerText(xbb_iframe.document.body);
        } catch(e) {
            setTimeout("XBB.form_submit()", 0)
        }
    } else {
        textarea.value = iframe.document.forms.xbb.xbb_textarea.value;
    }
}

/*
РўРµРєСЃС‚РѕРІРѕРµ СЃРѕРґРµСЂР¶РёРјРѕРµ СѓР·Р»Р° СЃ Р·Р°РјРµРЅРѕР№ <br /> РЅР° СЂР°Р·СЂС‹РІ СЃС‚СЂРѕРєРё Рё РѕРєСЂС‹Р¶РµРЅРёРµРј
<p> СЂР°Р·СЂС‹РІР°РјРё СЃС‚СЂРѕРє.
*/
XBB.innerText = function(node) {
    if (node.innerText) {
        return node.innerText;
    }
    if (node.textContent) {
        for (var t = [], l = (c = node.childNodes).length, p, i = 0; i < l; i++) {
            t[t.length] =
                'p' == (p = c[i].nodeName.toLowerCase())
                    ? '\n' + c[i].textContent + '\n'
                    : 'br' == p ? '\n' : c[i].textContent;
        }
        return t.join('');
    }
    return '';
}
