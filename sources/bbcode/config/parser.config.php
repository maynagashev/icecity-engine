<?php
/******************************************************************************
 *                                                                            *
 *   parser.config.php, v 0.02 2007/07/18 - This is part of xBB library       *
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

/*
Р¤Р°Р№Р» СЃРѕРґРµСЂР¶РёС‚ СѓРјРѕР»С‡Р°Р»СЊРЅС‹Рµ РЅР°СЃС‚СЂРѕР№РєРё РґР»СЏ РєР»Р°СЃСЃР° bbcode.

/* Р¤Р»Р°Р¶РѕРє, РІРєР»СЋС‡Р°СЋС‰РёР№/РІС‹РєР»СЋС‡Р°СЋС‰РёР№ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРёРµ СЃСЃС‹Р»РєРё */
$this->autolinks = true;

/* РњР°СЃСЃРёРІ Р·Р°РјРµРЅ РґР»СЏ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРёС… СЃСЃС‹Р»РѕРє */
$this->preg_autolinks = array(
    'pattern' => array(
        "'[\w\+]+://[A-z0-9\.\?\+\-/_=&%#:;]+[\w/=]+'si",
        "'([^/])(www\.[A-z0-9\.\?\+\-/_=&%#:;]+[\w/=]+)'si",
        "'[\w]+[\w\-\.]+@[\w\-\.]+\.[\w]+'si",
    ),
    'replacement' => array(
        '<a href="$0" target="_blank">$0</a>',
        '$1<a href="http://$2" target="_blank">$2</a>',
        '<a href="mailto:$0">$0</a>',
    ),
    'highlight' => array(
        '<span class="bb_autolink">$0</span>',
        '$1<span class="bb_autolink">$2</span>',
        '<span class="bb_autolink">$0</span>',
    ),
);

// Р¤РѕСЂРјРёСЂСѓРµРј РЅР°Р±РѕСЂ СЃРјР°Р№Р»РёРєРѕРІ
$path = substr($this->_current_path, strlen($_SERVER['DOCUMENT_ROOT']));
$path = str_replace('\\', '/', $path) . 'images/smiles/';
$pak = file($this->_current_path . 'images/smiles/Set_Smiles_YarNET.pak');
$smiles = array();
foreach ($pak as $val) {
    $val = trim($val);
    if (! $val || '#' == $val{0}) { continue; }
    list($gif, $alt, $symbol) = explode('=+:',$val);
    $smiles[$symbol] = '<img src="' . $path . htmlspecialchars($gif) . '" alt="'
        . htmlspecialchars($alt) . '" />';
}
// Р—Р°РґР°РµРј РЅР°Р±РѕСЂ СЃРјР°Р№Р»РёРєРѕРІ
$this->mnemonics = $smiles;
?>
