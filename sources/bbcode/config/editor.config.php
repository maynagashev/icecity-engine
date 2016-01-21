<?php

/******************************************************************************
 *                                                                            *
 *   editor.config.php, v 0.01 2007/07/25 - This is part of xBB library       *
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

// РЈРјРѕР»С‡Р°Р»СЊРЅС‹Рµ Р·РЅР°С‡РµРЅРёСЏ РЅР°СЃС‚СЂРѕРµРє xBBEditor-Р°

class Xbb {
    /*
    РџСѓС‚СЊ Рє Р±РёР±Р»РёРѕС‚РµРєРµ xBB
    */
    public $path = '.';
    /*
    РРґРµРЅС‚РёС„РёРєР°С‚РѕСЂ С‚РµРєСЃС‚Р°СЂРёРё
    */
    public $textarea_id = '';
    /*
    РЁРёСЂРёРЅР° РѕРєРЅР° СЂРµРґР°РєС‚РѕСЂР°
    */
    public $area_width = '700px';
    /*
    Р’С‹СЃРѕС‚Р° РѕРєРЅР° СЂРµРґР°РєС‚РѕСЂР°
    */
    public $area_height = '400px';
    /*
    РЈРјРѕР»С‡Р°Р»СЊРЅС‹Р№ СЂРµР¶РёРј СЂРµРґР°РєС‚РѕСЂР°.
    РџСЂРµРґСѓСЃРјРѕС‚СЂРµРЅС‹: 'plain' (С‚РµРєСЃС‚ РІ textarea), 'highlight' (С‚РµРєСЃС‚ РІ iframe)
    */
    public $state = 'plain';
    /*
    Р›РѕРєР°Р»РёР·Р°С†РёСЏ. РРјСЏ РґРѕР»Р¶РЅРѕ Р±С‹С‚СЊ РїСѓСЃС‚РѕР№ СЃС‚СЂРѕРєРѕР№ Р»РёР±Рѕ СЃРѕРІРїР°РґР°С‚СЊ СЃ РёРјРµРЅРµРј
    РєР°РєРѕР№ Р»РёР±Рѕ РїРѕРґРґРёСЂСЂРµРєС‚РѕСЂРёРё РІ i18n.
    */
    public $lang = '';
}