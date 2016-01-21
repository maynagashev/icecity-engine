<?php

/******************************************************************************
 *                                                                            *
 *   xbb.php, v 0.01 2007/07/26 - This is part of xBB library                 *
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

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'default';
if (! is_file('./i18n/'.$lang.'/lang.php')) {
    $lang = 'default';
}
require_once './i18n/default/lang.php';
if ('default' != $lang) {
	include_once './i18n/'.$lang.'/lang.php';
}
$state = isset($_GET['state']) ? $_GET['state'] : '';
if ('highlight' != $state) { $state = 'plain'; }
header('Content-type: text/html; charset='.$xbb_lang['charset']);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html;
 charset=<?php echo $xbb_lang['charset']; ?>" />
<title>xBBEditor</title>
<script src="./bbcode.lib.js" type="text/javascript"></script>
<meta name="author" content="Dmitriy Skorobogatov" />
<style type="text/css">
body {
    padding: 0px;
    margin: 0px;
    background-color: #ffffff;
    font-size:14px;
}
form {
    margin: 0px;
    padding: 0px;
}
a.button {
    color: #000000;
    font-weight: bold;
    text-decoration: none;
}
a.button:hover {
    border: 1px solid #eeee00;
}
a.button:active {
    background-color: #eeee00;
}
a.opt {
    color: #000000;
    text-decoration: none;
}
a.opt:hover {
    color: #ff0000;
}
a.opt:active {
    color: #ff00ff;
}
img.opt_color {
    border: 1px solid gray;
}
#xbb_iframe {
    width: 100%;
    border: 1px solid #a9b8c2;
    margin: 0px;
    padding: 0px;
}
#xbb_textarea {
    font-family: 'Monaco', 'Courier New', monospace;
    width: 100%;
    border: 1px solid #a9b8c2;
    margin: 0px;
    padding: 0px;
    color: #000000;
}
#hidden_div {
    border: 1px solid #a9b8c2;
    background-color: #ffffff;
    padding: 3px;
}
a.buttonMenu img {
    width: 9px;
}
a.toolbarButton img {
    width: 20px;
}
a.toolbarButton img, a.buttonMenu img {
	height: 20px;
	cursor: default;
	margin-top: 0px;
	margin-left: 2px;
	border: 0 !important;
}
a.toolbarButton img:hover, a.buttonMenu img:hover {
	border: 1px solid #eeee00 !important;
	margin-left: 0px;
}
a.toolbarButton img:active, a.buttonMenu img:active {
	border: 1px solid #eeee00 !important;
	background-color: #eeee00;
	margin-left: 0px;
}
/* MSIE specific rules */
* html a.toolbarButton img, * html a.buttonMenu img {
	border: 0px;
	margin-top: 3px;
	margin-bottom: 1px;
	margin-left: 2px;
}
* html a.toolbarButton, * html a.buttonMenu {
	border: 0px;
}
* html a.toolbarButton:hover img, * html a.buttonMenu:hover img {
	margin-left: 0;
}
* html a.toolbarButton:hover, * html a.buttonMenu:hover {
	border: 1px solid #eeee00;
	margin-left: 0px;
	cursor: default;
}
* html a.toolbarButton:active, * html a.buttonMenu:active {
	border: 1px solid #eeee00;
	margin-left: 0px;
	cursor: default;
	background-color: #eeee00;
}
</style>
<script type="text/javascript"><?php
// Р¤СѓРЅРєС†РёСЏ РїСЂРµРѕР±СЂР°Р·РѕРІС‹РІР°РµС‚ РјР°СЃСЃРёРІ PHP РІ РјР°СЃСЃРёРІ JavaScript
function phpArray2Javascript(&$php_array, $key = 0) {
    if (! is_array($php_array)) {
        $php_array = "'" . $php_array . "'";
        return true;
    }
    array_walk($php_array, 'phpArray2Javascript');
    reset($php_array);
    $php_array = '[' . implode(',', $php_array) . ']';
    return true;
}
require './config/tags.php';
require './config/xbbeditor.config.php';
// Р?РЅРёС†РёР°Р»РёР·Р°С†РёСЏ СЂРµРґР°РєС‚РѕСЂР°
echo "var bb = new bbcode('');";
// РЎРїРёСЃРѕРє РїРѕРґРґРµСЂР¶РёРІР°РµРјС‹С… С‚РµРіРѕРІ
$tags = array_keys($tags);
phpArray2Javascript($tags);
echo 'bb.tags=' . $tags . ';';
// РџРѕРґСЃРІРµС‡РёРІР°РµРјС‹Рµ СЃРјР°Р№Р»РёРєРё Рё РїСЂРѕС‡РёРµ РјРЅРµРјРѕРЅРёРєРё
$pak = file('./images/smiles/Set_Smiles_YarNET.pak');
$mnemonics = '';
foreach ($pak as $val) {
    $val = trim($val);
    if (! $val || '#' == $val{0}) {
    	continue;
    }
    list($gif, $alt, $symbol) = explode('=+:', $val);
    if ($mnemonics) {
        $mnemonics .= ',';
    }
    $mnemonics .= "'" . $symbol . "'";
}
echo 'bb.mnemonics=[' . $mnemonics . '];';
/*
Р РµР¶РёРј, РІ РєРѕС‚РѕСЂРѕРј РІ РґР°РЅРЅС‹Р№ РјРѕРјРµРЅС‚ РЅР°С…РѕРґРёС‚СЃСЏ СЂРµРґР°РєС‚РѕСЂ. Р’РѕР·РјРѕР¶РЅС‹Рµ Р·РЅР°С‡РµРЅРёСЏ:
'plain' (textarea) РёР»Рё 'highlight' (РїРѕРґСЃРІРµС‚РєР° СЃРёРЅС‚Р°РєСЃРёСЃР°)
*/
echo "bb.state='" . $state . "';";
// РЎРїРёСЃРѕРє С€СЂРёС„С‚РѕРІ, РїСЂРµРґР»Р°РіР°РµРјС‹С… РЅР° РІС‹Р±РѕСЂ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ
phpArray2Javascript($fonts);
echo 'bb.fonts=' . $fonts . ';';
// РџР°Р»РёС‚СЂР° С†РІРµС‚РѕРІ, РїСЂРµРґР»Р°РіР°РµРјС‹С… РЅР° РІС‹Р±РѕСЂ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ
phpArray2Javascript($colors);
echo 'bb.colors=' . $colors . ';';
// РћСЃРЅРѕРІРЅС‹Рµ СЃРјР°Р№Р»РёРєРё, РїСЂРµРґР»Р°РіР°РµРјС‹Рµ РЅР° РІС‹Р±РѕСЂ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ
phpArray2Javascript($smiles);
echo 'bb.smiles=' . $smiles . ';';
?>
var xbb_code_names = {
    0:  {0: 'code',         1: 'Text'                  },
    1:  {0: 'actionscript', 1: 'ActionScript'          },
    2:  {0: 'c++',          1: 'C++'                   },
    3:  {0: 'css',          1: 'CSS'                   },
    4:  {0: 'delphi',       1: 'Delphi (Object Pascal)'},
    5:  {0: 'html4',        1: 'HTML 4.01 Strict'      },
    6:  {0: 'java',         1: 'Java'                  },
    7:  {0: 'js',           1: 'JavaScript'            },
    8:  {0: 'latex',        1: 'LaTeX'                 },
    9:  {0: 'perl',         1: 'Perl'                  },
    10: {0: 'php',          1: 'PHP'                   },
    11: {0: 'sql',          1: 'SQL'                   },
    12: {0: 'vb',           1: 'Visual Basic'          },
    13: {0: 'xml',          1: 'XML'                   }
};
var xbb_current_range;

// РљРѕРїРёСЂРѕРІР°РЅРёРµ С‚РµРєСЃС‚Р° РёР· bb.transportDiv РІ РїРµСЂРµРјРµРЅРЅСѓСЋ bb.text
function xbb_text2CurrentText() {
    try {
        bb.text = bb.innerText(bb.transportDiv);
    } catch(e) {
        setTimeout("xbb_text2CurrentText()", 0)
    }
}

// РљРѕРїРёСЂСѓРµС‚ С‚РµРєСЃС‚ РёР· Iframe РІ РїРµСЂРµРјРµРЅРЅСѓСЋ bb.text
function xbb_textFromIframe() {
    try {
        bb.text = bb.innerText(xbb_iframe.contentWindow.document.body);
    } catch(e) {
        setTimeout("xbb_textFromIframe()", 0);
    }
}

// РџРѕРґСЃРІРµС‚РєР° СЃРёРЅС‚Р°РєСЃРёСЃР°
function xbb_highlight() {
    xbb_startLoader();
    bb.parse(bb.text);
    xbb_iframe.contentWindow.document.body.innerHTML = bb.highlight();
    xbb_stopLoader();
    return false;
}

// РЎСЂР°Р±Р°С‚С‹РІР°РµС‚ РїСЂРё РѕРєРѕРЅС‡Р°РЅРёРё Р·Р°РіСЂСѓР·РєРё СЌС‚РѕРіРѕ РёС„СЂРµР№РјР°
function xbb_onload() {
    if ('highlight' == bb.state) {
        xbb_highlight();
    } else {
        xbb_iframe.style.display = 'none';
    }
    xbb_stopLoader();
}

// Р¤СѓРЅРєС†РёСЏ РїРµСЂРµРєР»СЋС‡Р°РµС‚ СЂРµР¶РёРј СЂР°Р±РѕС‚С‹ СЃ textarea РЅР° РїРѕРґСЃРІРµС‡РµРЅРЅС‹Р№ РєРѕРґ Рё РѕР±СЂР°С‚РЅРѕ
function xbb_changeState() {
    if ('highlight' == bb.state) {
        // РџРµСЂРµРєР»СЋС‡Р°РµРјСЃСЏ СЃ РїРѕРґСЃРІРµС‡РµРЅРЅРѕРіРѕ РєРѕРґР° РЅР° textarea
        bb.text = bb.innerText(xbb_iframe.contentWindow.document.body);
        xbb_iframe.style.display = 'none';
        xbb_textarea.style.display = '';
        xbb_textarea.style.height = height;
        xbb_textarea.value = bb.text;
        bb.state = 'plain';
        document.forms.xbb.xbb_state.value = 'plain';
        var change_text = '&nbsp;<?php echo $xbb_lang['changestate_plane']; ?>&nbsp;';
        xbb_textarea.focus();
    } else {
        // РџРµСЂРµРєР»СЋС‡Р°РµРјСЃСЏ СЃ textarea РЅР° РїРѕРґСЃРІРµС‡РµРЅРЅС‹Р№ РєРѕРґ
        if (! document.execCommand) {
            alert('<?php echo $xbb_lang['not_execCommand']; ?>');
            return false;
        }
        bb.text = xbb_textarea.value;
        xbb_textarea.style.display = 'none';
        xbb_iframe.style.display = '';
        xbb_iframe.style.height = height;
        xbb_highlight();
        bb.state = 'highlight';
        document.forms.xbb.xbb_state.value = 'highlight';
        if (xbb_iframe.contentWindow.scrollTo) { // РґР»СЏ Opera
            xbb_iframe.contentWindow.scrollTo(0, 0);
        }
        var change_text = '&nbsp;<?php echo $xbb_lang['changestate_highlight']; ?>&nbsp;';
        xbb_iframe.contentWindow.focus();
    }
    // РњРµРЅСЏРµРј РЅР°Р·РІР°РЅРёРµ РєРЅРѕРїРєРё РїРµСЂРµРєР»СЋС‡РµРЅРёСЏ
    document.getElementById('changeState').innerHTML = change_text;
    return false;
}

function xbb_startLoader() {
    document.getElementById('loader').style.display = '';
}

function xbb_stopLoader() {
    document.getElementById('loader').style.display = 'none';
}

// Р’СЃС‚Р°РІР»СЏРµС‚ begin Рё end РІРѕРєСЂСѓРі РІС‹РґРµР»РµРЅРёСЏ
function xbb_insertTags(begin, end) {
    if ('highlight' == bb.state) {
        xbb_insertTags2iframe(begin, end);
    } else {
        xbb_insertTags2textarea(begin, end);
    }
    return false;
}

// Р’СЃС‚Р°РІР»СЏРµС‚ begin Рё end РІРѕРєСЂСѓРі РІС‹РґРµР»РµРЅРёСЏ РІ iframe
function xbb_insertTags2iframe(begin, end) {
    var iframe = document.getElementById(bb.iframeId);
    var wysiwyg = iframe.contentWindow.document;
    var sel;
    var range;
    if (wysiwyg.body.scrollTop) {
        var x = wysiwyg.body.scrollLeft;
        var y = wysiwyg.body.scrollTop;
    } else {
        var x = 0;
        var y = 0;
    }
    // РґР»СЏ Р±СЂР°СѓР·РµСЂРѕРІ, РЅРµ РїРѕРґРґРµСЂР¶РёРІР°СЋС‰РёС… СЂР°Р±РѕС‚Сѓ СЃ РІС‹РґРµР»РµРЅРёРµРј
    if (! wysiwyg.execCommand) {
        wysiwyg.body.innerHTML += begin + end;
        return false;
    }
    if (wysiwyg.selection) { // IE, Opera
		//retrieve selected range
		sel = wysiwyg.selection;
		if (null != sel) {
			range = sel.createRange();
			range = xbb_current_range;
			range.select();
		}
	}
	iframe.contentWindow.focus();
    // РґР»СЏ Р±СЂР°СѓР·РµСЂРѕРІ,РїРѕРґРґРµСЂР¶РёРІР°СЋС‰РёС… СЂР°Р±РѕС‚Сѓ СЃ РІС‹РґРµР»РµРЅРёРµРј
    // Р‘РµСЂРµРј РІС‹РґРµР»РµРЅРёРµ РІ <font color="#000000">. Р?С… РјРѕР¶РµС‚ РїРѕР»СѓС‡РёС‚СЊСЃСЏ РЅРµСЃРєРѕР»СЊРєРѕ.
    wysiwyg.execCommand('ForeColor', false, '#000000');
    var nodes = wysiwyg.getElementsByTagName('font');
    if (! nodes.length && iframe.contentWindow.getSelection) { // Gecko, Opera
        sel = iframe.contentWindow.getSelection();
        range = sel.getRangeAt(0);
        var html = wysiwyg.createElement('span');
        html.innerHTML = begin + range + end;
        var range2 = range.cloneRange();
        // Insert text at cursor position
		sel.removeAllRanges();
		range.deleteContents();
		range.insertNode(html);
        // Move the cursor to the end of text
		range2.selectNode(html);
		range2.collapse(false);
		sel.removeAllRanges();
		sel.addRange(range2);
		xbb_removeNode(html);
		iframe.contentWindow.focus();
		if (y && iframe.contentWindow.scrollTo) {
            iframe.contentWindow.scrollTo(x, y);
        }
        return false;
    }
    while (nodes.length) {
    	// Р’СЃС‚Р°РІР»СЏРµРј begin Рё end
    	if ('span' != nodes.item(0).parentNode.tagName.toLowerCase()) {
    	    nodes.item(0).innerHTML = begin + nodes.item(0).innerHTML + end;
    	}
    	// РЈРґР°Р»СЏРµРј font
    	xbb_removeNode(nodes.item(0));
    }
    iframe.contentWindow.focus();
    if (y && iframe.contentWindow.scrollTo) { // РґР»СЏ Opera
        iframe.contentWindow.scrollTo(x, y);
    }
    return false;
}

function xbb_insertTags2textarea(begin, end) {
    surroundText(begin, end);
}

// Р’С‹РїРѕР»РЅСЏРµС‚ РЅРµРєРѕС‚РѕСЂС‹Рµ РґРµР№СЃС‚РІРёСЏ РїСЂРё РєР»РёРєРµ РЅР° РєРЅРѕРїРєРё
function xbb_buttonClick() {
    var sel;
	if (xbb_iframe.contentWindow.document.selection) { // IE, Opera
	    sel = xbb_iframe.contentWindow.document.selection;
	    xbb_current_range = sel.createRange();
	}
}

// Р¤СѓРЅРєС†РёСЏ РґР»СЏ СѓРґР°Р»РµРЅРёСЏ РІРЅРµС€РЅРµР№ РЅРѕРґС‹ СЃ СЃРѕС…СЂР°РЅРµРЅРёРµРј РµРµ РґРѕС‡РµСЂРЅРёС… РЅРѕРґ.
function xbb_removeNode(node) {
    if (node.removeNode) { // IE
        node.removeNode(false);
    } else { // FF
        var docFragment = document.createDocumentFragment();
        while (node.childNodes.length) {
        	docFragment.appendChild(node.childNodes.item(0));
        }
        node.parentNode.replaceChild(docFragment, node);
    }
}

// Р’СЃС‚Р°РІРєР° РїСЂРѕСЃС‚РµР№С€РёС… С‚РµРіРѕРІ (С‚Р°РєРёС… РєР°Рє [b], [i], [u] Рё С‚.Рї.)
function xbb_insertSimpleTags(tag_name) {
    xbb_buttonClick();
    document.getElementById('hidden_div').style.display = 'none';
    if ('highlight' == bb.state) {
        begin = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_tagname">' + tag_name
            + '</span><span class="bb_bracket">]</span></span>';
        end = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_slash">/</span><span class="bb_tagname">'
            + tag_name + '</span><span class="bb_bracket">]</span></span>';
        xbb_insertTags(begin, end);
    } else {
        xbb_insertTags('[' + tag_name + ']', '[/' + tag_name + ']');
    }
}

// Р’СЃС‚Р°РІРєР° РѕРґРёРЅР°СЂРЅС‹С… С‚РµРіРѕРІ (С‚Р°РєРёС… РєР°Рє [hr])
function xbb_insertSingleTag(tag_name) {
    xbb_buttonClick();
    if ('highlight' == bb.state) {
        tag = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_tagname">' + tag_name
            + '</span><span class="bb_bracket">]</span></span>';
        xbb_insertTags(tag, '');
    } else {
        xbb_insertTags('[' + tag_name + ']', '');
    }
}

// Р’СЃС‚Р°РІРєР° СЃРјР°Р№Р»РёРєРѕРІ
function xbb_insertSmile(smile) {
	xbb_buttonClick();
	document.getElementById('hidden_div').style.display = 'none';
	if ('highlight' == bb.state) {
        xbb_insertTags('<span class="bb_mnemonic">' + smile  + '</span>', '');
    } else {
        xbb_insertTags(smile, '');
    }
}

// Insert tags [url] and [img]
function xbb_insertLink(tag, text) {
	xbb_buttonClick();
	if ('none' != document.getElementById('hidden_div').style.display) {
        document.getElementById('hidden_div').style.display = 'none';
    }
	var url = prompt(text, "");
	if (! url) { return false; }
	if ('highlight' == bb.state) {
	    url = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_tagname">' + tag
            + '</span><span class="bb_bracket">]</span></span>'
            + '<span class="bb_autolink">' + url
            + '</span><span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_slash">/</span><span class="bb_tagname">' + tag
            + '</span><span class="bb_bracket">]</span></span>';
        xbb_insertTags(url, '');
    } else {
        xbb_insertTags('[' + tag + ']' + url + '[/' + tag + ']', '');
    }
    return false;
}

// РџРѕРєР°Р·С‹РІР°РµС‚ СЃРїРёСЃРѕРє РїРѕРґСЃРІРµС‚РѕРє РєРѕРґР°
function xbb_codeList() {
    xbb_buttonClick();
    var div = document.getElementById('hidden_div');
	if ('none' != div.style.display) {
        div.style.display = 'none';
        return false;
    }
    var coords = xbb_getCoords(document.getElementById('img_code'));
    div.style.left = coords['left'] + 'px';
    div.style.top = coords['top'] + coords['height'] + 'px';
    var html = '';
    for (var i = 0; xbb_code_names[i]; ++i) {
        html += '<a href="#" class="opt" onclick="xbb_insertSimpleTags(\''
            + xbb_code_names[i][0] + '\')">' + xbb_code_names[i][1] + '</a><br />';
    }
    div.innerHTML = html;
    div.style.display = '';
    return false;
}

// РџРѕРєР°Р·С‹РІР°РµС‚ СЃРїРёСЃРѕРє СЂР°Р·РјРµСЂРѕРІ С€СЂРёС„С‚Р°
function xbb_sizeList() {
    xbb_buttonClick();
    var div = document.getElementById('hidden_div');
	if ('none' != div.style.display) {
        div.style.display = 'none';
        return false;
    }
    var coords = xbb_getCoords(document.getElementById('img_size'));
    div.style.left = coords['left'] + 'px';
    div.style.top = coords['top'] + coords['height'] + 'px';
    var html = '';
    for (var i = 1; i <= 7; ++i) {
        html += '<a href="#" class="opt" onclick="xbb_insertSize(' + i
            + ')"><font size="' + i + '"><?php echo $xbb_lang['font_size']; ?> '
            + i + '</font></a><br />';
    }
    div.innerHTML = html;
    div.style.display = '';
    return false;
}

// Insert tag [size]
function xbb_insertSize(size) {
    if ('highlight' == bb.state) {
	    begin = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_tagname">size</span>'
            + '<span class="bb_equal">=</span>'
            + '<span class="bb_attrib_val">' + size + '</span>'
            + '<span class="bb_bracket">]</span></span>';
        end = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_slash">/</span>'
            + '<span class="bb_tagname">size</span>'
            + '<span class="bb_bracket">]</span></span>';
    } else {
        begin = '[size=' + size + ']';
        end = '[/size]'
    }
    document.getElementById('hidden_div').style.display = 'none';
	xbb_insertTags(begin, end);
	return false;
}

// РџРѕРєР°Р·С‹РІР°РµС‚ РїР°Р»РёС‚СЂСѓ РІС‹Р±РѕСЂР° С†РІРµС‚Р°
function xbb_colorList() {
    xbb_buttonClick();
    var div = document.getElementById('hidden_div');
	if ('none' != div.style.display) {
        div.style.display = 'none';
        return false;
    }
    var coords = xbb_getCoords(document.getElementById('img_color'));
    div.style.left = coords['left'] + 'px';
    div.style.top = coords['top'] + coords['height'] + 'px';
    var html = '<table border="0" cellpadding="0" cellspacing="1">';
    for (var i = 0; bb.colors[i]; ++i) {
        html += '<tr>';
        for (var j = 0; bb.colors[i][j]; ++j) {
            html += '<td height="20" width="20" bgcolor="' + bb.colors[i][j]
                + '" onclick="xbb_insertColor(\'' + bb.colors[i][j]
                + '\')"><a href="#" class="opt_color"><img alt="' + bb.colors[i][j]
                + '"  src="./images/pixel.gif" height="20" width="20" class="opt_color" /></a></td>';
        }
        html += '</tr>';
    }
    html += '</table>';
    div.innerHTML = html;
    div.style.display = '';
    return false;
}

// Insert tag [color]
function xbb_insertColor(color) {
    if ('highlight' == bb.state) {
	    begin = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_tagname">color</span>'
            + '<span class="bb_equal">=</span>'
            + '<span class="bb_attrib_val">' + color + '</span>'
            + '<span class="bb_bracket">]</span></span>';
        end = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_slash">/</span>'
            + '<span class="bb_tagname">color</span>'
            + '<span class="bb_bracket">]</span></span>';
    } else {
        begin = '[color=' + color + ']';
        end = '[/color]'
    }
    document.getElementById('hidden_div').style.display = 'none';
	xbb_insertTags(begin, end);
	return false;
}

// РџРѕРєР°Р·С‹РІР°С‚ СЃРїРёСЃРѕРє С€СЂРёС„С‚РѕРІ РґР»СЏ РІС‹Р±РѕСЂР°
function xbb_fontList() {
	xbb_buttonClick();
	var div = document.getElementById('hidden_div');
	if ('none' != div.style.display) {
        div.style.display = 'none';
        return false;
    }
    var coords = xbb_getCoords(document.getElementById('img_font'));
    div.style.left = coords['left'] + 'px';
    div.style.top = coords['top'] + coords['height'] + 'px';
    var html = '';
    for (var i = 0; bb.fonts[i]; ++i) {
        html += '<a href="#" class="opt" style="font-family:'
            + bb.fonts[i] + '" onclick="xbb_insertFont(\'' + bb.fonts[i]
            + '\')">' + bb.fonts[i] + '</a><br />';
    }
    div.innerHTML = html;
    div.style.display = '';
    return false;
}

// Insert tag [font]
function xbb_insertFont(font) {
	if ('highlight' == bb.state) {
	    begin = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_tagname">font</span>'
            + '<span class="bb_equal">=</span><span class="bb_quote">"</span>'
            + '<span class="bb_attrib_val">' + font + '</span>'
            + '<span class="bb_quote">"</span>'
            + '<span class="bb_bracket">]</span></span>';
        end = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_slash">/</span>'
            + '<span class="bb_tagname">font</span>'
            + '<span class="bb_bracket">]</span></span>';
    } else {
        begin = '[font="' + font + '"]';
        end = '[/font]'
    }
    document.getElementById('hidden_div').style.display = 'none';
	xbb_insertTags(begin, end);
	return false;
}

function xbb_insertTagWithAttribute(tag, text) {
    xbb_buttonClick();
    var begin;
    var end;
    var val = prompt(text, "");
	if ('highlight' == bb.state) {
	    begin = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_tagname">' + tag + '</span>';
        if (val) {
            begin += '<span class="bb_equal">=</span>'
                + '<span class="bb_quote">"</span>'
                + '<span class="bb_attrib_val">' + val + '</span>'
                + '<span class="bb_quote">"</span>';
        }
        begin += '<span class="bb_bracket">]</span></span>';
        end = '<span class="bb_tag"><span class="bb_bracket">[</span>'
            + '<span class="bb_slash">/</span><span class="bb_tagname">' + tag
            + '</span><span class="bb_bracket">]</span></span>';
    } else {
        begin = '[' + tag;
        if (val) { begin += '="' + val + '"'; }
        begin += ']';
        end = '[/' + tag + ']';
    }
    xbb_insertTags(begin, end);
    return false;
}

// РћРїСЂРµРґРµР»РµРЅРёРµ РєРѕРѕСЂРґРёРЅР°С‚ Рё СЂР°Р·РјРµСЂРѕРІ СЌР»РµРјРµРЅС‚Р°
function xbb_getCoords(element) {
    var left = element.offsetLeft;
    var top = element.offsetTop;
    for (var parent = element.offsetParent; parent; parent = parent.offsetParent) {
        left += parent.offsetLeft - parent.scrollLeft;
        top += parent.offsetTop - parent.scrollTop
    }
    return {
    	left: left,
    	top: top,
    	width: element.offsetWidth,
    	height: element.offsetHeight
    };
}

// Р’С‹РІРѕРґРёС‚ СЃРІРµРґРµРЅРёСЏ Рѕ РїСЂРѕРіСЂР°РјРјРµ
function xbb_aboutProgramm() {
    xbb_buttonClick();
	var div = document.getElementById('hidden_div');
	if ('none' != div.style.display) {
        div.style.display = 'none';
        return false;
    }
    if (window.innerWidth) {
        var width = window.innerWidth;
    } else {
        var width = parseInt(xbb_editor.currentStyle.width);
    }
    div.style.top = (parseInt(xbb_height/2) - 100) + 'px';
    div.style.left = (parseInt(width/2) - 125) + 'px';
    div.innerHTML = '<div align="center" style="width:250px;height:200px">'
        + '<h3><font color="red">x</font>BBEditor for '
        + '<font color="red">x</font>BB v. 0.29</h3>'
        + '&copy; 2006-2007 Dmitriy Skorobogatov<br />'
        + 'All rights reserved.<br />&nbsp;<br />'
        + 'License: GNU GPL v. 2.<br />'
        + 'This program is free software.<br />&nbsp;<br />'
        + 'www: <a href="http://xbb.uz" target="_blank">http://xbb.uz</a>'
        + '</div>';
    div.style.display = '';
    return false;
}

// Р’С‹РІРѕРґРёС‚ СЃРїРёСЃРѕРє СЃРјР°Р№Р»РѕРІ
function xbb_smilesList() {
    xbb_buttonClick();
    var div = document.getElementById('hidden_div');
	if ('none' != div.style.display) {
        div.style.display = 'none';
        return false;
    }
    var coords = xbb_getCoords(document.getElementById('img_smile'));
    div.style.left = coords['left'] + 'px';
    div.style.top = coords['top'] + coords['height'] + 'px';
    var html = '<table border="0" cellpadding="0" cellspacing="1">';
    for (var i = 0; bb.smiles[i]; ++i) {
        html += '<tr>';
        for (var j = 0; bb.smiles[i][j]; ++j) {
            html += '<td height="20" width="20" ><a href="#" '
                + 'onclick="xbb_insertSmile(\'' + bb.smiles[i][j][1]
                + '\')"><img alt="' + bb.smiles[i][j][1]
                + '" src="./images/smiles/' + bb.smiles[i][j][0]
                + '" border="0" /></a></td>';
        }
        html += '</tr>';
    }
    html += '</table>';
    div.innerHTML = html;
    div.style.display = '';
    return false;
}

// РЎР°Р±РјРёС‚РёС‚ С„РѕСЂРјСѓ xbb СЂР°РґРё РїСЂРµРґРІР°СЂРёС‚РµР»СЊРЅРѕРіРѕ РїСЂРѕСЃРјРѕС‚СЂР°
function xbb_submit() {
    if ('highlight' == bb.state) {
        bb.text = bb.innerText(xbb_iframe.contentWindow.document.body);
        xbb_textarea.value = bb.text;
    }
    document.xbb.submit();
    return true;
}
</script>

</head>
<body onload="xbb_onload()">

<img id="loader" src="./images/loader.gif" alt="РёРґРµС‚ Р·Р°РіСЂСѓР·РєР°"
style="position:absolute;top:0px;left:50%" />
<script type="text/javascript">
var xbb_editor = parent.document.getElementById('xbb_editor');
var xbb_height = 0;
if (window.innerHeight) {
    xbb_height = window.innerHeight;
} else {
    xbb_height = parseInt(xbb_editor.currentStyle.height);
}
document.getElementById('loader').style.top = (parseInt(xbb_height/2) - 8) + 'px';
</script>

<table border="0" cellpadding="0" cellspacing="2" width="100%" id="main">
<tr valign="middle" style="background-image:url(./images/background.gif)"><td>

<table id="toolbar" border="0" cellpadding="0" cellspacing="0"
style="background-image:url(./images/background.gif)">
<tr>
<td><img src="./images/left.gif" alt="" /></td>

<td><a href="#" onclick="xbb_insertSimpleTags('b');return false;"
 class="toolbarButton"><img alt="[b]" src="./images/buttons/bold.gif"
 id="img_b" /></a></td>

<td><a href="#" onclick="xbb_insertSimpleTags('i');return false;"
 class="toolbarButton"><img alt="[i]" src="./images/buttons/italic.gif"
 id="img_i" /></a></td>

<td><a href="#" onclick="xbb_insertSimpleTags('u');return false;"
 class="toolbarButton"><img alt="[u]" src="./images/buttons/underline.gif"
 id="img_u" /></a></td>

<td><a href="#" onclick="xbb_insertSimpleTags('s');return false;"
 class="toolbarButton"><img alt="[s]" src="./images/buttons/s.gif"
 id="img_s" /></a></td>

<td><img src="./images/separator.gif" alt="" /></td>

<td><a href="#" onclick="xbb_insertSimpleTags('sub');return false;"
 class="toolbarButton"><img alt="[sub]" src="./images/buttons/sub.gif"
 id="img_sub" /></a></td>

<td><a href="#" onclick="xbb_insertSimpleTags('sup');return false;"
 class="toolbarButton"><img alt="[sup]" src="./images/buttons/sup.gif"
 id="img_sup" /></a></td>

<td><img src="./images/separator.gif" alt="" /></td>

<td><a href="#" onclick="xbb_insertSimpleTags('left');return false;"
 class="toolbarButton"><img alt="[left]" src="./images/buttons/left.gif"
 id="img_left" /></a></td>

<td><a href="#" onclick="xbb_insertSimpleTags('center');return false;"
 class="toolbarButton"><img alt="[center]" src="./images/buttons/center.gif"
 id="img_center" /></a></td>

<td><a href="#" onclick="xbb_insertSimpleTags('right');return false;"
 class="toolbarButton"><img alt="[right]" src="./images/buttons/right.gif"
 id="img_right" /></a></td>

<td><a href="#" onclick="xbb_insertSimpleTags('justify');return false;"
 class="toolbarButton"><img alt="[justify]" src="./images/buttons/justify.gif"
 id="img_justify" /></a></td>

<td><img src="./images/separator.gif" alt="" /></td>

<td><a href="#" onclick="xbb_insertSingleTag('hr');return false;"
 class="toolbarButton"><img alt="[hr]" src="./images/buttons/hr.gif"
 id="img_hr" /></a></td>

<td><a href="#" onclick="xbb_insertTagWithAttribute('quote', '<?php echo $xbb_lang['quote_prompt']; ?>');return false;"
 class="toolbarButton"><img alt="[quote]" src="./images/buttons/quote.gif"
 id="img_quote" /></a></td>

<td><a href="#" onclick="xbb_submit();return false;"
 class="toolbarButton"><img alt="Preview" src="./images/buttons/preview.gif"
 id="img_preview" /></a></td>

<td><img src="./images/separator.gif" alt="" /></td>

<td><a href="#" onclick="xbb_codeList();return false;"
 class="toolbarButton"><img alt="[code]" src="./images/buttons/code.gif"
 id="img_code" /></a></td>

<td><a href="#" onclick="xbb_codeList();return false;"
 class="buttonMenu"><img src="./images/button_menu.gif" alt="" /></a></td>

<td><a href="#" onclick="xbb_sizeList();return false;"
 class="toolbarButton"><img alt="[size]" src="./images/buttons/size.gif"
 id="img_size" /></a></td>

<td><a href="#" onclick="xbb_sizeList();return false;"
 class="buttonMenu"><img src="./images/button_menu.gif" alt="" /></a></td>

<td><a href="#" onclick="xbb_colorList();return false;"
 class="toolbarButton"><img alt="[color]" src="./images/buttons/color.gif"
 id="img_color" /></a></td>

<td><a href="#" onclick="xbb_colorList();return false;"
 class="buttonMenu"><img src="./images/button_menu.gif" alt="" /></a></td>

<td><a href="#" onclick="xbb_fontList();return false;"
 class="toolbarButton"><img alt="[font]" src="./images/buttons/font.gif"
 id="img_font" /></a></td>

<td><a href="#" onclick="xbb_fontList();return false;"
 class="buttonMenu"><img src="./images/button_menu.gif" alt="" /></a></td>

<td><img src="./images/separator.gif" alt="" /></td>

<td><a href="#"
 onclick="xbb_insertLink('img', '<?php echo $xbb_lang['img_prompt']; ?>');return false;"
 class="toolbarButton"><img alt="[img]" src="./images/buttons/image.gif"
 id="img_img" /></a></td>

<td><a href="#"
 onclick="xbb_insertLink('url', '<?php echo $xbb_lang['url_prompt']; ?>');return false;"
 class="toolbarButton"><img alt="[url]" src="./images/buttons/link.gif"
 id="img_url" /></a></td>

<td><a href="#"
 onclick="xbb_insertLink('email', '<?php echo $xbb_lang['email_prompt']; ?>');return false;"
 class="toolbarButton"><img alt="[email]" src="./images/buttons/mail.gif"
 id="img_mail" /></a></td>

<td><img src="./images/separator.gif" alt="" /></td>

<td><a href="#"
 onclick="xbb_smilesList();return false;"
 class="toolbarButton"><img alt="Smiles" src="./images/buttons/smile.gif"
 id="img_smile" /></a></td>

<td><a href="#"
 onclick="xbb_aboutProgramm();return false;"
 class="toolbarButton"><img alt="About programm" src="./images/buttons/help.gif"
 id="img_help" /></a></td>

<td><img src="./images/right.gif" alt="" /></td>
</tr>
</table>

</td></tr>
<tr onclick="document.getElementById('hidden_div').style.display = 'none'">
<td id="td_iframe"><form name="xbb" action="preview.php" target="_blank" method="post"><iframe id="xbb_iframe" src="area.php" frameborder="0"></iframe><input name="xbb_state" type="hidden" value="<?php echo $state; ?>" /><textarea name="xbb_textarea" id="xbb_textarea" style="height:1px" onselect="storeCaret(this)"
onclick="storeCaret(this)" onkeyup="storeCaret(this)"
onchange="storeCaret(this)"></textarea></form></td>
</tr>
<tr style="background-image:url(./images/background.gif)"><td align="right">
<script type="text/javascript">
var xbb_iframe = document.getElementById(bb.iframeId);
var xbb_textarea = document.getElementById(bb.textareaId);
var height = (xbb_height - 66) + 'px';
if ('highlight' == bb.state) {
    xbb_textarea.style.display = 'none';
    xbb_iframe.style.height = height;
} else {
    xbb_iframe.style.height = '1px';
    xbb_textarea.style.height = height;
}
xbb_text2CurrentText();
if ('highlight' != bb.state) {
    xbb_textarea.value = bb.text;
}
</script>
<table border="0" cellpadding="0" cellspacing="0"
onclick="xbb_changeState()" style="background-image:url(./images/background.gif)">
<tr><td><img src="./images/left.gif" alt="" /></td>
<td align="center" width="150">
<a href="#" class="button" id="changeState">&nbsp;<?php
if ('highlight' == $state) {
	echo $xbb_lang['changestate_highlight'];
} else {
    echo $xbb_lang['changestate_plane'];
}
?>&nbsp;</a></td>
<td><img src="./images/right.gif" alt="" /></td>
</tr>
</table>

</td></tr></table>
<div style="position:absolute;top:0px;left:0;display:none"
id="hidden_div"></div>
</body>
</html>
