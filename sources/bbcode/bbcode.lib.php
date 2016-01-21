<?php

/******************************************************************************
 *                                                                            *
 *   bbcode.lib.php, v 0.29 2007/07/18 - This is part of xBB library          *
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

class bbcode {
	/*
	РРјСЏ С‚РµРіР°, РєРѕС‚РѕСЂРѕРјСѓ СЃРѕРїРѕСЃС‚Р°РІР»РµРЅ СЌРєР·РµРјРїР»СЏСЂ РєР»Р°СЃСЃР°.
	РџСѓСЃС‚Р°СЏ СЃС‚СЂРѕРєР°, РµСЃР»Рё СЌРєР·РµРјРїР»СЏСЂ РЅРµ СЃРѕРїРѕСЃС‚Р°РІР»РµРЅ РЅРёРєР°РєРѕРјСѓ С‚РµРіСѓ.
	*/
    public $tag = '';
    /*
    РњР°СЃСЃРёРІ Р·РЅР°С‡РµРЅРёР№ Р°С‚СЂРёР±СѓС‚РѕРІ С‚РµРіР°, РєРѕС‚РѕСЂРѕРјСѓ СЃРѕРїРѕСЃС‚Р°РІР»РµРЅ СЌРєР·РµРјРїР»СЏСЂ РєР»Р°СЃСЃР°.
    РџСѓСЃС‚, РµСЃР»Рё СЌРєР·РµРјРїР»СЏСЂ РЅРµ СЃРѕРїРѕСЃС‚Р°РІР»РµРЅ РЅРёРєР°РєРѕРјСѓ С‚РµРіСѓ.
    */
    public $attrib = array();
    /*
    РўРµРєСЃС‚ BBCode
    */
    public $text = '';
    /*
    РњР°СЃСЃРёРІ, - СЂРµР·СѓР»СЊС‚Р°С‚ СЃРёРЅС‚Р°РєСЃРёС‡РµСЃРєРѕРіРѕ СЂР°Р·Р±РѕСЂР° С‚РµРєСЃС‚Р° BBCode. РћРїРёСЃР°РЅРёРµ СЃРјРѕС‚СЂРёС‚Рµ
    РІ РґРѕРєСѓРјРµРЅС‚Р°С†РёРё.
    */
    public $syntax = array();
    /*
    Р”РµСЂРµРІРѕ СЃРµРјР°РЅС‚РёС‡РµСЃРєРѕРіРѕ СЂР°Р·Р±РѕСЂР° С‚РµРєСЃС‚Р° BBCode. РћРїРёСЃР°РЅРёРµ СЃРјРѕС‚СЂРёС‚Рµ РІ
    РґРѕРєСѓРјРµРЅС‚Р°С†РёРё.
    */
    public $tree = array();
    /*
    РЎРїРёСЃРѕРє РїРѕРґРґРµСЂР¶РёРІР°РµРјС‹С… С‚РµРіРѕРІ СЃ СѓРєР°Р·Р°РЅРёРµРј СЃРїРµС†РёР°Р»РёР·РёСЂРѕРІР°РЅРЅС‹С… РєР»Р°СЃСЃРѕРІ.
    РЎРјРѕС‚СЂРёС‚Рµ С„Р°Р№Р» config/parser.config.php
    */
    public $tags = array();
    /*
    РЎРјР°Р№Р»РёРєРё Рё РїСЂРѕС‡РёРµ РјРЅРµРјРѕРЅРёРєРё. РњР°СЃСЃРёРІ: 'РјРЅРµРјРѕРЅРёРєР°' => 'РµРµ_Р·Р°РјРµРЅР°'.
    */
    public $mnemonics = array();
    /*
    Р¤Р»Р°Р¶РѕРє, РІРєР»СЋС‡Р°СЋС‰РёР№/РІС‹РєР»СЋС‡Р°СЋС‰РёР№ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРёРµ СЃСЃС‹Р»РєРё.
    РЎРјРѕС‚СЂРёС‚Рµ С„Р°Р№Р» config/parser.config.php
    */
    public $autolinks = true;
    /*
    РњР°СЃСЃРёРІ Р·Р°РјРµРЅ РґР»СЏ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРёС… СЃСЃС‹Р»РѕРє.
    РЎРјРѕС‚СЂРёС‚Рµ С„Р°Р№Р» config/parser.config.php
    */
    public $preg_autolinks = array(
        'pattern' => array(),
        'replacement' => array(),
        'highlight' => array(),
    );
    public $is_close = false;
    public $lbr = 0;
    public $rbr = 0;
    /* РЎС‚Р°С‚РёСЃС‚РёС‡РµСЃРєРёРµ СЃРІРµРґРµРЅРёСЏ РїРѕ РѕР±СЂР°Р±РѕС‚РєРµ BBCode */
    public $stat = array(
        'time_parse' => 0,  // Р’СЂРµРјСЏ РїР°СЂСЃРёРЅРіР°
        'time_html' => 0,   // Р’СЂРµРјСЏ РіРµРЅРµСЂР°С†РёРё HTML-Р°
        'count_tags' => 0,  // Р§РёСЃР»Рѕ С‚РµРіРѕРІ BBCode
        'count_level' => 0  // Р§РёСЃР»Рѕ СѓСЂРѕРІРЅРµР№ РІР»РѕР¶РµРЅРЅРѕСЃС‚Рё С‚РµРіРѕРІ BBCode
    );
    /*
    РњРѕРґРµР»СЊ РїРѕРІРµРґРµРЅРёСЏ С‚РµРіР° (РІ РїР»Р°РЅРµ РІР»РѕР¶РµРЅРЅРѕСЃС‚Рё), РєРѕС‚РѕСЂРѕРјСѓ СЃРѕРїРѕСЃС‚Р°РІР»РµРЅ СЌРєР·РµРјРїР»СЏСЂ
    РґР°РЅРЅРѕРіРѕ РєР»Р°СЃСЃР°. РњРѕРґРµР»Рё РїРѕРІРµРґРµРЅРёСЏ РјРѕРіСѓС‚ Р±С‹С‚СЊ СЃР»РµРґСѓСЋС‰РёРјРё:
    'a'       - СЃСЃС‹Р»РєРё, СЏРєРѕСЂСЏ
    'caption' - Р·Р°РіРѕР»РѕРІРєРё С‚Р°Р±Р»РёС†
    'code'    - Р»РёРЅРµР№РЅС‹Рµ РєРѕРЅС‚РµР№РЅРµСЂС‹ РєРѕРґР°
    'div'     - Р±Р»РѕС‡РЅС‹Рµ СЌР»РµРјРµРЅС‚С‹
    'hr'      - РіРѕСЂРёР·РѕРЅС‚Р°Р»РЅС‹Рµ Р»РёРЅРёРё
    'img'     - РєР°СЂС‚РёРЅРєРё
    'li'      - СЌР»РµРјРµРЅС‚С‹ СЃРїРёСЃРєРѕРІ
    'p'       - Р±Р»РѕС‡РЅС‹Рµ СЌР»РµРјРµРЅС‚С‹ С‚РёРїР° Р°Р±Р·Р°С†РµРІ Рё Р·Р°РіРѕР»РѕРІРєРѕРІ
    'pre'     - Р±Р»РѕС‡РЅС‹Рµ РєРѕРЅС‚РµР№РЅРµСЂС‹ РєРѕРґР°
    'span'    - Р»РёРЅРµР№РЅС‹Рµ СЌР»РµРјРµРЅС‚С‹
    'table'   - С‚Р°Р±Р»РёС†С‹
    'td'      - СЏС‡РµР№РєРё С‚Р°Р±Р»РёС†
    'tr'      - СЃС‚СЂРѕРєРё С‚Р°Р±Р»РёС†
    'ul'      - СЃРїРёСЃРєРё
    РљРѕРЅРєСЂРµС‚РЅРѕРµ СЃРѕРґРµСЂР¶Р°РЅРёРµ РІ РїРѕРЅСЏС‚РёРµ "РјРѕРґРµР»СЊ РїРѕРІРµРґРµРЅРёСЏ" РІРєР»Р°РґС‹РІР°РµС‚СЃСЏ РЅР°СЃС‚СЂРѕР№РєР°РјРё
    РІ С„Р°Р№Р»Рµ config/parser.config.php
    */
    public $behaviour = 'div';
    /*
    РўРµРєСѓС‰Р°СЏ РґРёСЂСЂРµРєС‚РѕСЂРёСЏ.
    */
    private $_current_path;
    /*
    Р”Р»СЏ РЅСѓР¶Рґ РїР°СЂСЃРµСЂР°. - РџРѕР·РёС†РёСЏ РѕС‡РµСЂРµРґРЅРѕРіРѕ РѕР±СЂР°Р±Р°С‚С‹РІР°РµРјРѕРіРѕ СЃРёРјРІРѕР»Р°.
    */
    private $_cursor;
    /*
    РњР°СЃСЃРёРІ РѕР±СЉРµРєС‚РѕРІ, - РїСЂРµРґСЃС‚Р°РІРёС‚РµР»РµР№ РѕС‚РґРµР»СЊРЅС‹С… С‚РµРіРѕРІ.
    */
    private $_tag_objects = array();
    /*
    РњР°СЃСЃРёРІ РїР°СЂ: 'РјРѕРґРµР»СЊ_РїРѕРІРµРґРµРЅРёСЏ_С‚РµРіРѕРІ' => РјР°СЃСЃРёРІ_РјРѕРґРµР»РµР№_РїРѕРІРµРґРµРЅРёР№_С‚РµРіРѕРІ.
    РќР°РєР»Р°РґС‹РІР°РµС‚ РѕРіСЂР°РЅРёС‡РµРЅРёСЏ РЅР° РІР»РѕР¶РµРЅРЅРѕСЃС‚СЊ С‚РµРіРѕРІ. РўРµРіРё СЃ РјРѕРґРµР»СЏРјРё РїРѕРІРµРґРµРЅРёСЏ, РЅРµ
    СѓРєР°Р·Р°РЅРЅС‹РјРё РІ РјР°СЃСЃРёРІРµ СЃРїСЂР°РІР°, РІР»РѕР¶РµРЅРЅС‹Рµ РІ С‚РµРі СЃ РјРѕРґРµР»СЊСЋ РїРѕРІРµРґРµРЅРёСЏ, СѓРєР°Р·Р°РЅРЅРѕР№
    СЃР»РµРІР°, Р±СѓРґСѓС‚ РёРіРЅРѕСЂРёСЂРѕРІР°С‚СЊСЃСЏ РєР°Рє РЅРµРїСЂР°РІРёР»СЊРЅРѕ РІР»РѕР¶РµРЅРЅС‹Рµ.
    РЎРјРѕС‚СЂРёС‚Рµ С„Р°Р№Р» config/parser.config.php
    */
    private $_children;
    /*
    РњР°СЃСЃРёРІ РїР°СЂ: 'РјРѕРґРµР»СЊ_РїРѕРІРµРґРµРЅРёСЏ_С‚РµРіРѕРІ' => РјР°СЃСЃРёРІ_РјРѕРґРµР»РµР№_РїРѕРІРµРґРµРЅРёР№_С‚РµРіРѕРІ.
    РќР°РєР»Р°РґС‹РІР°РµС‚ РѕРіСЂР°РЅРёС‡РµРЅРёСЏ РЅР° РІР»РѕР¶РµРЅРЅРѕСЃС‚СЊ С‚РµРіРѕРІ.
    РўРµРі, РїСЂРёРЅР°РґР»РµР¶РµС‰РёР№ СѓРєР°Р·Р°РЅРЅРѕР№ СЃР»РµРІР° РјРѕРґРµР»Рё РїРѕРІРµРґРµРЅРёСЏ С‚РµРіРѕРІ РґРѕР»Р¶РµРЅ Р·Р°РєСЂС‹С‚СЊСЃСЏ,
    РєР°Рє С‚РѕР»СЊРєРѕ РЅР°С‡РёРЅР°РµС‚СЃСЏ С‚РµРі, РїСЂРёРЅР°РґР»РµР¶РµС‰РёР№ РєР°РєРѕР№ С‚Рѕ РёР· РјРѕРґРµР»РµР№ РїРѕРІРµРґРµРЅРёСЏ,
    СѓРєР°Р·Р°РЅРЅС‹С… СЃРїСЂР°РІР°.
    РЎРјРѕС‚СЂРёС‚Рµ С„Р°Р№Р» config/parser.config.php
    */
    private $_ends;

    /* РљРѕРЅСЃС‚СЂСѓРєС‚РѕСЂ РєР»Р°СЃСЃР° */
    function bbcode($code = '') {
        $this->_current_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        include $this->_current_path . 'config' . DIRECTORY_SEPARATOR . 'parser.config.php';
        include $this->_current_path . 'config' . DIRECTORY_SEPARATOR . 'tags.php';
        $this->tags = $tags;
        $this->_children = $children;
        $this->_ends = $ends;
        $this->parse($code);
    }

    /*
    get_token() - Р¤СѓРЅРєС†РёСЏ РїР°СЂСЃРёС‚ С‚РµРєСЃС‚ BBCode Рё РІРѕР·РІСЂР°С‰Р°РµС‚ РѕС‡РµСЂРµРґРЅСѓСЋ РїР°СЂСѓ

                        "С‡РёСЃР»Рѕ (С‚РёРї Р»РµРєСЃРµРјС‹) - Р»РµРєСЃРµРјР°"

    Р›РµРєСЃРµРјР° - РїРѕРґСЃС‚СЂРѕРєР° СЃС‚СЂРѕРєРё $this -> text, РЅР°С‡РёРЅР°СЋС‰Р°СЏСЃСЏ СЃ РїРѕР·РёС†РёРё
                                $this -> _cursor
    РўРёРїС‹ Р»РµРєСЃРµРј РјРѕРіСѓС‚ Р±С‹С‚СЊ СЃР»РµРґСѓСЋС‰РёРµ:

    0 - РѕС‚РєСЂС‹РІСЋС‰Р°СЏ РєРІР°РґСЂР°С‚РЅР°СЏ СЃРєРѕР±РєР° ("[")
    1 - Р·Р°РєСЂС‹РІР°СЋС‰Р°СЏ РєРІР°РґСЂР°С‚РЅР°СЏ cРєРѕР±РєР° ("]")
    2 - РґРІРѕР№РЅР°СЏ РєР°РІС‹С‡РєР° ('"')
    3 - Р°РїРѕСЃС‚СЂРѕС„ ("'")
    4 - СЂР°РІРµРЅСЃС‚РІРѕ ("=")
    5 - РїСЂСЏРјРѕР№ СЃР»СЌС€ ("/")
    6 - РїРѕСЃР»РµРґРѕРІР°С‚РµР»СЊРЅРѕСЃС‚СЊ РїСЂРѕР±РµР»СЊРЅС‹С… СЃРёРјРІРѕР»РѕРІ
        (" ", "\t", "\n", "\r", "\0" РёР»Рё "\x0B")
    7 - РїРѕСЃР»РµРґРѕРІР°С‚РµР»СЊРЅРѕСЃС‚СЊ РїСЂРѕС‡РёС… СЃРёРјРІРѕР»РѕРІ, РЅРµ СЏРІР»СЏСЋС‰Р°СЏСЃСЏ РёРјРµРЅРµРј С‚РµРіР°
    8 - РёРјСЏ С‚РµРіР°
    */
    function get_token() {
        $token = '';
        $token_type = false;
        $char_type = false;
        while (true) {
            $token_type = $char_type;
            if (! isset($this -> text{$this -> _cursor})) {
                if (false === $char_type) {
                    return false;
                } else {
                    break;
                }
            }
            $char = $this -> text{$this -> _cursor};
            switch ($char) {
                case '[':
                    $char_type = 0;
                    break;
                case ']':
                    $char_type = 1;
                    break;
                case '"':
                    $char_type = 2;
                    break;
                case "'":
                    $char_type = 3;
                    break;
                case "=":
                    $char_type = 4;
                    break;
                case '/':
                    $char_type = 5;
                    break;
                case ' ':
                    $char_type = 6;
                    break;
                case "\t":
                    $char_type = 6;
                    break;
                case "\n":
                    $char_type = 6;
                    break;
                case "\r":
                    $char_type = 6;
                    break;
                case "\0":
                    $char_type = 6;
                    break;
                case "\x0B":
                    $char_type = 6;
                    break;
                default:
                    $char_type = 7;
            }
            if (false === $token_type) {
                $token = $char;
            } elseif (5 >= $token_type) {
                break;
            } elseif ($char_type == $token_type) {
                $token .= $char;
            } else {
                break;
            }
            $this -> _cursor += 1;
        }
        if (isset($this -> tags[strtolower($token)])) {
            $token_type = 8;
        }
        return array($token_type, $token);
    }

    function parse($code = '') {
        $time_start = $this->_getmicrotime();
        if (is_array($code)) {
            $is_tree = false;
            foreach ($code as $key => $val) {
                if (isset($val['val'])) {
                	$this->tree = $code;
                	$this->syntax = $this->get_syntax();
                	$is_tree = true;
                	break;
                }
            }
            if (! $is_tree) {
                $this->syntax = $code;
                $this->get_tree();
            }
            $this->text = '';
            foreach ($this->syntax as $val) {
                $this->text .= $val['str'];
            }
            $this->stat['time_parse'] = $this->_getmicrotime() - $time_start;
            return $this->syntax;
        } elseif ($code) {
        	$this->text = $code;
        }
        /*
        РСЃРїРѕР»СЊР·СѓРµРј РјРµС‚РѕРґ РєРѕРЅРµС‡РЅС‹С… Р°РІС‚РѕРјР°С‚РѕРІ
        РЎРїРёСЃРѕРє РІРѕР·РјРѕР¶РЅС‹С… СЃРѕСЃС‚РѕСЏРЅРёР№ Р°РІС‚РѕРјР°С‚Р°:
        0  - РќР°С‡Р°Р»Рѕ СЃРєР°РЅРёСЂРѕРІР°РЅРёСЏ РёР»Рё РЅР°С…РѕРґРёРјСЃСЏ РІРЅРµ С‚РµРіР°. РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        1  - Р’СЃС‚СЂРµС‚РёР»Рё СЃРёРјРІРѕР» "[", РєРѕС‚РѕСЂС‹Р№ СЃС‡РёС‚Р°РµРј РЅР°С‡Р°Р»РѕРј С‚РµРіР°. РћР¶РёРґР°РµРј РёРјСЏ
             С‚РµРіР°, РёР»Рё СЃРёРјРІРѕР» "/".
        2  - РќР°С€Р»Рё РІ С‚РµРіРµ РЅРµРѕР¶РёРґР°РІС€РёР№СЃСЏ СЃРёРјРІРѕР» "[". РЎС‡РёС‚Р°РµРј РїСЂРµРґС‹РґСѓС‰СѓСЋ СЃС‚СЂРѕРєСѓ
             РѕС€РёР±РєРѕР№. РћР¶РёРґР°РµРј РёРјСЏ С‚РµРіР°, РёР»Рё СЃРёРјРІРѕР» "/".
        3  - РќР°С€Р»Рё РІ С‚РµРіРµ СЃРёРЅС‚Р°РєСЃРёС‡РµСЃРєСѓСЋ РѕС€РёР±РєСѓ. РўРµРєСѓС‰РёР№ СЃРёРјРІРѕР» РЅРµ СЏРІР»СЏРµС‚СЃСЏ "[".
             РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        4  - РЎСЂР°Р·Сѓ РїРѕСЃР»Рµ "[" РЅР°С€Р»Рё СЃРёРјРІРѕР» "/". РџСЂРµРґРїРѕР»Р°РіР°РµРј, С‡С‚Рѕ РїРѕРїР°Р»Рё РІ
             Р·Р°РєСЂС‹РІР°СЋС‰РёР№ С‚РµРі. РћР¶РёРґР°РµРј РёРјСЏ С‚РµРіР° РёР»Рё СЃРёРјРІРѕР» "]".
        5  - РЎСЂР°Р·Сѓ РїРѕСЃР»Рµ "[" РЅР°С€Р»Рё РёРјСЏ С‚РµРіР°. РЎС‡РёС‚Р°РµРј, С‡С‚Рѕ РЅР°С…РѕРґРёРјСЃСЏ РІ
             РѕС‚РєСЂС‹РІР°СЋС‰РµРј С‚РµРіРµ. РћР¶РёРґР°РµРј РїСЂРѕР±РµР» РёР»Рё "=" РёР»Рё "/" РёР»Рё "]".
        6  - РќР°С€Р»Рё Р·Р°РІРµСЂС€РµРЅРёРµ С‚РµРіР° "]". РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        7  - РЎСЂР°Р·Сѓ РїРѕСЃР»Рµ "[/" РЅР°С€Р»Рё РёРјСЏ С‚РµРіР°. РћР¶РёРґР°РµРј "]".
        8  - Р’ РѕС‚РєСЂС‹РІР°СЋС‰РµРј С‚РµРіРµ РЅР°С€Р»Рё "=". РћР¶РёРґР°РµРј РїСЂРѕР±РµР» РёР»Рё Р·РЅР°С‡РµРЅРёРµ Р°С‚СЂРёР±СѓС‚Р°.
        9  - Р’ РѕС‚РєСЂС‹РІР°СЋС‰РµРј С‚РµРіРµ РЅР°С€Р»Рё "/", РѕР·РЅР°С‡Р°СЋС‰РёР№ Р·Р°РєСЂС‹С‚РёРµ С‚РµРіР°. РћР¶РёРґР°РµРј
             "]".
        10 - Р’ РѕС‚РєСЂС‹РІР°СЋС‰РµРј С‚РµРіРµ РЅР°С€Р»Рё РїСЂРѕР±РµР» РїРѕСЃР»Рµ РёРјРµРЅРё С‚РµРіР° РёР»Рё РёРјРµРЅРё
             Р°С‚СЂРёР±СѓС‚Р°. РћР¶РёРґР°РµРј "=" РёР»Рё РёРјСЏ РґСЂСѓРіРѕРіРѕ Р°С‚СЂРёР±СѓС‚Р° РёР»Рё "/" РёР»Рё "]".
        11 - РќР°С€Р»Рё '"' РЅР°С‡РёРЅР°СЋС‰СѓСЋ Р·РЅР°С‡РµРЅРёРµ Р°С‚СЂРёР±СѓС‚Р°, РѕРіСЂР°РЅРёС‡РµРЅРЅРѕРµ РєР°РІС‹С‡РєР°РјРё.
             РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        12 - РќР°С€Р»Рё "'" РЅР°С‡РёРЅР°СЋС‰РёР№ Р·РЅР°С‡РµРЅРёРµ Р°С‚СЂРёР±СѓС‚Р°, РѕРіСЂР°РЅРёС‡РµРЅРЅРѕРµ Р°РїРѕСЃС‚СЂРѕС„Р°РјРё.
             РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        13 - РќР°С€Р»Рё РЅР°С‡Р°Р»Рѕ РЅРµР·Р°РєР°РІС‹С‡РµРЅРЅРѕРіРѕ Р·РЅР°С‡РµРЅРёСЏ Р°С‚СЂРёР±СѓС‚Р°. РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        14 - Р’ РѕС‚РєСЂС‹РІР°СЋС‰РµРј С‚РµРіРµ РїРѕСЃР»Рµ "=" РЅР°С€Р»Рё РїСЂРѕР±РµР». РћР¶РёРґР°РµРј Р·РЅР°С‡РµРЅРёРµ
             Р°С‚СЂРёР±СѓС‚Р°.
        15 - РќР°С€Р»Рё РёРјСЏ Р°С‚СЂРёР±СѓС‚Р°. РћР¶РёРґР°РµРј РїСЂРѕР±РµР» РёР»Рё "=" РёР»Рё "/" РёР»Рё "]".
        16 - РќР°С…РѕРґРёРјСЃСЏ РІРЅСѓС‚СЂРё Р·РЅР°С‡РµРЅРёСЏ Р°С‚СЂРёР±СѓС‚Р°, РѕРіСЂР°РЅРёС‡РµРЅРЅРѕРіРѕ РєР°РІС‹С‡РєР°РјРё.
             РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        17 - Р—Р°РІРµСЂС€РµРЅРёРµ Р·РЅР°С‡РµРЅРёСЏ Р°С‚СЂРёР±СѓС‚Р°. РћР¶РёРґР°РµРј РїСЂРѕР±РµР» РёР»Рё РёРјСЏ СЃР»РµРґСѓСЋС‰РµРіРѕ
             Р°С‚СЂРёР±СѓС‚Р° РёР»Рё "/" РёР»Рё "]".
        18 - РќР°С…РѕРґРёРјСЃСЏ РІРЅСѓС‚СЂРё Р·РЅР°С‡РµРЅРёСЏ Р°С‚СЂРёР±СѓС‚Р°, РѕРіСЂР°РЅРёС‡РµРЅРЅРѕРіРѕ Р°РїРѕСЃС‚СЂРѕС„Р°РјРё.
             РћР¶РёРґР°РµРј С‡С‚Рѕ СѓРіРѕРґРЅРѕ.
        19 - РќР°С…РѕРґРёРјСЃСЏ РІРЅСѓС‚СЂРё РЅРµР·Р°РєР°РІС‹С‡РµРЅРЅРѕРіРѕ Р·РЅР°С‡РµРЅРёСЏ Р°С‚СЂРёР±СѓС‚Р°. РћР¶РёРґР°РµРј С‡С‚Рѕ
             СѓРіРѕРґРЅРѕ.
        20 - РќР°С€Р»Рё РїСЂРѕР±РµР» РїРѕСЃР»Рµ Р·РЅР°С‡РµРЅРёСЏ Р°С‚СЂРёР±СѓС‚Р°. РћР¶РёРґР°РµРј РёРјСЏ СЃР»РµРґСѓСЋС‰РµРіРѕ
             Р°С‚СЂРёР±СѓС‚Р° РёР»Рё "/" РёР»Рё "]".

        РћРїРёСЃР°РЅРёРµ РєРѕРЅРµС‡РЅРѕРіРѕ Р°РІС‚РѕРјР°С‚Р°:
        */
        $finite_automaton = array(
               // РџСЂРµРґС‹РґСѓС‰РёРµ |   РЎРѕСЃС‚РѕСЏРЅРёСЏ РґР»СЏ С‚РµРєСѓС‰РёС… СЃРѕР±С‹С‚РёР№ (Р»РµРєСЃРµРј)   |
               //  СЃРѕСЃС‚РѕСЏРЅРёСЏ |  0 |  1 |  2 |  3 |  4 |  5 |  6 |  7 |  8 |
                   0 => array(  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ,  1 => array(  2 ,  3 ,  3 ,  3 ,  3 ,  4 ,  3 ,  3 ,  5 )
                ,  2 => array(  2 ,  3 ,  3 ,  3 ,  3 ,  4 ,  3 ,  3 ,  5 )
                ,  3 => array(  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ,  4 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  7 )
                ,  5 => array(  2 ,  6 ,  3 ,  3 ,  8 ,  9 , 10 ,  3 ,  3 )
                ,  6 => array(  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 )
                ,  7 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 )
                ,  8 => array( 13 , 13 , 11 , 12 , 13 , 13 , 14 , 13 , 13 )
                ,  9 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 )
                , 10 => array(  2 ,  6 ,  3 ,  3 ,  8 ,  9 ,  3 , 15 , 15 )
                , 11 => array( 16 , 16 , 17 , 16 , 16 , 16 , 16 , 16 , 16 )
                , 12 => array( 18 , 18 , 18 , 17 , 18 , 18 , 18 , 18 , 18 )
                , 13 => array( 19 ,  6 , 19 , 19 , 19 , 19 , 17 , 19 , 19 )
                , 14 => array(  2 ,  3 , 11 , 12 , 13 , 13 ,  3 , 13 , 13 )
                , 15 => array(  2 ,  6 ,  3 ,  3 ,  8 ,  9 , 10 ,  3 ,  3 )
                , 16 => array( 16 , 16 , 17 , 16 , 16 , 16 , 16 , 16 , 16 )
                , 17 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  9 , 20 , 15 , 15 )
                , 18 => array( 18 , 18 , 18 , 17 , 18 , 18 , 18 , 18 , 18 )
                , 19 => array( 19 ,  6 , 19 , 19 , 19 , 19 , 20 , 19 , 19 )
                , 20 => array(  2 ,  6 ,  3 ,  3 ,  3 ,  9 ,  3 , 15 , 15 )
            );
        // Р—Р°РєРѕРЅС‡РёР»Рё РѕРїРёСЃР°РЅРёРµ РєРѕРЅРµС‡РЅРѕРіРѕ Р°РІС‚РѕРјР°С‚Р°
        $mode = 0;
        $this->syntax = array();
        $decomposition = array();
        $token_key = -1;
        $value = '';
        $this ->_cursor = 0;
        // РЎРєР°РЅРёСЂСѓРµРј РјР°СЃСЃРёРІ Р»РµРєСЃРµРј СЃ РїРѕРјРѕС‰СЊСЋ РїРѕСЃС‚СЂРѕРµРЅРЅРѕРіРѕ Р°РІС‚РѕРјР°С‚Р°:
        while ($token = $this -> get_token()) {
            $previous_mode = $mode;
            $mode = $finite_automaton[$previous_mode][$token[0]];
            if (-1 < $token_key) {
                $type = $this -> syntax[$token_key]['type'];
            } else {
                $type = false;
            }
            switch ($mode) {
            case 0:
                if ('text' == $type) {
                    $this -> syntax[$token_key]['str'] .= $token[1];
                } else {
                    $this -> syntax[++$token_key] = array(
                            'type' => 'text',
                            'str' => $token[1]
                        );
                }
                break;
            case 1:
                $decomposition = array(
                    'name'   => '',
                    'type'   => '',
                    'str'    => '[',
                    'layout' => array(array(0, '['))
                );
                break;
            case 2:
                if ('text' == $type) {
                    $this -> syntax[$token_key]['str'] .= $decomposition['str'];
                } else {
                    $this -> syntax[++$token_key] = array(
                        'type' => 'text',
                        'str' => $decomposition['str']
                    );
                }
                $decomposition = array(
                    'name'   => '',
                    'type'   => '',
                    'str'    => '[',
                    'layout' => array(array(0, '['))
                );
                break;
            case 3:
                if ('text' == $type) {
                    $this -> syntax[$token_key]['str'] .= $decomposition['str'];
                    $this -> syntax[$token_key]['str'] .= $token[1];
                } else {
                    $this -> syntax[++$token_key] = array(
                        'type' => 'text',
                        'str' => $decomposition['str'].$token[1]
                    );
                }
                $decomposition = array();
                break;
            case 4:
                $decomposition['type'] = 'close';
                $decomposition['str'] .= '/';
                $decomposition['layout'][] = array(1, '/');
                break;
            case 5:
                $decomposition['type'] = 'open';
                $name = strtolower($token[1]);
                $decomposition['name'] = $name;
                $decomposition['str'] .= $token[1];
                $decomposition['layout'][] = array(2, $token[1]);
                $decomposition['attrib'][$name] = '';
                break;
            case 6:
                if (! isset($decomposition['name'])) {
                    $decomposition['name'] = '';
                }
                if (13 == $previous_mode || 19 == $previous_mode) {
                    $decomposition['layout'][] = array(7, $value);
                }
                $decomposition['str'] .= ']';
                $decomposition['layout'][] = array( 0, ']' );
                $this -> syntax[++$token_key] = $decomposition;
                $decomposition = array();
                break;
            case 7:
                $decomposition['name'] = strtolower($token[1]);
                $decomposition['str'] .= $token[1];
                $decomposition['layout'][] = array(2, $token[1]);
                break;
            case 8:
                $decomposition['str'] .= '=';
                $decomposition['layout'][] = array(3, '=');
                break;
            case 9:
                $decomposition['type'] = 'open/close';
                $decomposition['str'] .= '/';
                $decomposition['layout'][] = array(1, '/');
                break;
            case 10:
                $decomposition['str'] .= $token[1];
                $decomposition['layout'][] = array(4, $token[1]);
                break;
            case 11:
                $decomposition['str'] .= '"';
                $decomposition['layout'][] = array(5, '"');
                $value = '';
                break;
            case 12:
                $decomposition['str'] .= "'";
                $decomposition['layout'][] = array(5, "'");
                $value = '';
                break;
            case 13:
                $decomposition['attrib'][$name] = $token[1];
                $value = $token[1];
                $decomposition['str'] .= $token[1];
                break;
            case 14:
                $decomposition['str'] .= $token[1];
                $decomposition['layout'][] = array(4, $token[1]);
                break;
            case 15:
                $name = strtolower($token[1]);
                $decomposition['str'] .= $token[1];
                $decomposition['layout'][] = array(6, $token[1]);
                $decomposition['attrib'][$name] = '';
                break;
            case 16:
                $decomposition['str'] .= $token[1];
                $decomposition['attrib'][$name] .= $token[1];
                $value .= $token[1];
                break;
            case 17:
                $decomposition['str'] .= $token[1];
                $decomposition['layout'][] = array(7, $value);
                $value = '';
                $decomposition['layout'][] = array(5, $token[1]);
                break;
            case 18:
                $decomposition['str'] .= $token[1];
                $decomposition['attrib'][$name] .= $token[1];
                $value .= $token[1];
                break;
            case 19:
                $decomposition['str'] .= $token[1];
                $decomposition['attrib'][$name] .= $token[1];
                $value .= $token[1];
                break;
            case 20:
                $decomposition['str'] .= $token[1];
                if ( 13 == $previous_mode || 19 == $previous_mode ) {
                    $decomposition['layout'][] = array(7, $value);
                }
                $value = '';
                $decomposition['layout'][] = array(4, $token[1]);
                break;
            }
        }
        if (count($decomposition)) {
            if ('text' == $type) {
                $this -> syntax[$token_key]['str'] .= $decomposition['str'];
            } else {
                $this -> syntax[++$token_key] = array(
                    'type' => 'text',
                    'str' => $decomposition['str']
                );
            }
        }
        $this->get_tree();
        $this->stat['time_parse'] = $this->_getmicrotime() - $time_start;
        return $this->syntax;
    }

    function specialchars($string) {
        $chars = array(
            '[' => '@l;',
            ']' => '@r;',
            '"' => '@q;',
            "'" => '@a;',
            '@' => '@at;'
        );
        return strtr($string, $chars);
    }

    function unspecialchars($string) {
        $chars = array(
            '@l;'  => '[',
            '@r;'  => ']',
            '@q;'  => '"',
            '@a;'  => "'",
            '@at;' => '@'
        );
        return strtr($string, $chars);
    }

    /*
    Р¤СѓРЅРєС†РёСЏ РїСЂРѕРІРµСЂСЏРµС‚, РґРѕР»Р¶РµРЅ Р»Рё С‚РµРі СЃ РёРјРµРЅРµРј $current Р·Р°РєСЂС‹С‚СЊСЃСЏ, РµСЃР»Рё
    РЅР°С‡РёРЅР°РµС‚СЃСЏ С‚РµРі СЃ РёРјРµРЅРµРј $next.
    */
    function must_close_tag($current, $next) {
        if (isset($this -> tags[$current])) {
            $this->_includeTagFile($current);
            $class_vars = get_class_vars($this -> tags[$current]);
            $current_behaviour = $class_vars['behaviour'];
        } else {
            $current_behaviour = $this->behaviour;
        }
        if (isset($this -> tags[$next])) {
            $this->_includeTagFile($next);
            $class_vars = get_class_vars($this -> tags[$next]);
            $next_behaviour = $class_vars['behaviour'];
        } else {
            $next_behaviour = $this->behaviour;
        }
        $must_close = false;
        if (isset($this->_ends[$current_behaviour])) {
            $must_close = in_array($next_behaviour, $this->_ends[$current_behaviour]);;
        }
        return $must_close;
    }

    /*
    Р’РѕР·РІСЂР°С‰Р°РµС‚ true, РµСЃР»Рё С‚РµРі СЃ РёРјРµРЅРµРј $parent РјРѕР¶РµС‚ РёРјРµС‚СЊ РЅРµРїРѕСЃСЂРµРґСЃС‚РІРµРЅРЅС‹Рј
    РїРѕС‚РѕРјРєРѕРј С‚РµРі СЃ РёРјРµРЅРµРј $child. Р’ РїСЂРѕС‚РёРІРЅРѕРј СЃР»СѓС‡Р°Рµ - false.
    Р•СЃР»Рё $parent - РїСѓСЃС‚Р°СЏ СЃС‚СЂРѕРєР°, С‚Рѕ РїСЂРѕРІРµСЂСЏРµС‚СЃСЏ, СЂР°Р·СЂРµС€РµРЅРѕ Р»Рё $child РІС…РѕРґРёС‚СЊ РІ
    РєРѕСЂРµРЅСЊ РґРµСЂРµРІР° BBCode.
    */
    function isPermissiblyChild($parent, $child) {
        $parent = (string) $parent;
        $child = (string) $child;
        if (isset($this -> tags[$parent])) {
            $this->_includeTagFile($parent);
            $class_vars = get_class_vars($this -> tags[$parent]);
            $parent_behaviour = $class_vars['behaviour'];
        } else {
            $parent_behaviour = $this->behaviour;
        }
        if (isset($this -> tags[$child])) {
            $this->_includeTagFile($child);
            $class_vars = get_class_vars($this -> tags[$child]);
            $child_behaviour = $class_vars['behaviour'];
        } else {
            $child_behaviour = $this->behaviour;
        }
        $permissibly = true;
        if (isset($this->_children[$parent_behaviour])) {
            $permissibly = in_array(
                $child_behaviour, $this->_children[$parent_behaviour]
            );
        }
        return $permissibly;
    }

    function normalize_bracket($syntax) {
        $structure = array();
        $structure_key = -1;
        $level = 0;
        $open_tags = array();
        foreach ($syntax as $syntax_key => $val) {
            unset($val['layout']);
            switch ($val['type']) {
                case 'text':
                    $val['str'] = $this -> unspecialchars($val['str']);
                    $type = (-1 < $structure_key)
                        ? $structure[$structure_key]['type'] : false;
                    if ('text' == $type) {
                        $structure[$structure_key]['str'] .= $val['str'];
                    } else {
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = $level;
                    }
                    break;
                case 'open/close':
                    $val['attrib'] = array_map(
            	        array(&$this, 'unspecialchars'), $val['attrib']
            	    );
                    foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
                        if ($this -> must_close_tag($ultimate, $val['name'])) {
                            $structure[++$structure_key] = array(
                                    'type'  => 'close',
                                    'name'  => $ultimate,
                                    'str'   => '',
                                    'level' => --$level
                                );
                            unset($open_tags[$ult_key]);
                        } else {
                        	break;
                        }
                    }
                    $structure[++$structure_key] = $val;
                    $structure[$structure_key]['level'] = $level;
                    break;
                case 'open':
                    $this->_includeTagFile($val['name']);
                    $val['attrib'] = array_map(
            	        array(&$this, 'unspecialchars'), $val['attrib']
            	    );
                    foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
                        if ($this -> must_close_tag($ultimate, $val['name'])) {
                            $structure[++$structure_key] = array(
                                    'type'  => 'close',
                                    'name'  => $ultimate,
                                    'str'   => '',
                                    'level' => --$level
                                );
                            unset($open_tags[$ult_key]);
                        } else { break; }
                    }
                    $class_vars = get_class_vars($this -> tags[$val['name']]);
                    if ($class_vars['is_close']) {
                        $val['type'] = 'open/close';
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = $level;
                    } else {
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = $level++;
                        $open_tags[] = $val['name'];
                    }
                    break;
                case 'close':
                    if (! count($open_tags)) {
                        $type = (-1 < $structure_key)
                            ? $structure[$structure_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $structure[$structure_key]['str'] .= $val['str'];
                        } else {
                            $structure[++$structure_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => 0
                                );
                        }
                        break;
                    }
                    if (! $val['name']) {
                        end($open_tags);
                        list($ult_key, $ultimate) = each($open_tags);
                        $val['name'] = $ultimate;
                        $structure[++$structure_key] = $val;
                        $structure[$structure_key]['level'] = --$level;
                        unset($open_tags[$ult_key]);
                        break;
                    }
                    if (! in_array($val['name'],$open_tags)) {
                        $type = (-1 < $structure_key)
                            ? $structure[$structure_key]['type'] : false;
                        if ('text' == $type) {
                            $structure[$structure_key]['str'] .= $val['str'];
                        } else {
                            $structure[++$structure_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
                        if ($ultimate != $val['name']) {
                            $structure[++$structure_key] = array(
                                    'type'  => 'close',
                                    'name'  => $ultimate,
                                    'str'   => '',
                                    'level' => --$level
                                );
                            unset($open_tags[$ult_key]);
                        } else {
                        	break;
                        }
                    }
                    $structure[++$structure_key] = $val;
                    $structure[$structure_key]['level'] = --$level;
                    unset($open_tags[$ult_key]);
            }
        }
        foreach (array_reverse($open_tags,true) as $ult_key => $ultimate) {
            $structure[++$structure_key] = array(
                    'type'  => 'close',
                    'name'  => $ultimate,
                    'str'   => '',
                    'level' => --$level
                );
            unset($open_tags[$ult_key]);
        }
        return $structure;
    }

    function get_tree() {
        /* РџСЂРµРІСЂР°С‰Р°РµРј $this -> syntax РІ РїСЂР°РІРёР»СЊРЅСѓСЋ СЃРєРѕР±РѕС‡РЅСѓСЋ СЃС‚СЂСѓРєС‚СѓСЂСѓ */
        $structure = $this -> normalize_bracket($this -> syntax);
        /* РћС‚СЃР»РµР¶РёРІР°РµРј, РёРјРµСЋС‚ Р»Рё СЌР»РµРјРµРЅС‚С‹ РЅРµСЂР°Р·СЂРµС€РµРЅРЅС‹Рµ РїРѕРґСЌР»РµРјРµРЅС‚С‹.
           РЎРѕРѕС‚РІРµС‚СЃС‚РІРµРЅРЅРѕ СЌС‚РѕРјСѓ РёСЃРїСЂР°РІР»СЏРµРј $structure. */
        $normalized = array();
        $normal_key = -1;
        $level = 0;
        $open_tags = array();
        $not_tags = array();
        $this -> stat['count_tags'] = 0;
        foreach ($structure as $structure_key => $val) {
            switch ($val['type']) {
                case 'text':
                    $type = (-1 < $normal_key)
                        ? $normalized[$normal_key]['type'] : false;
                    if ('text' == $type) {
                        $normalized[$normal_key]['str'] .= $val['str'];
                    } else {
                        $normalized[++$normal_key] = $val;
                        $normalized[$normal_key]['level'] = $level;
                    }
                    break;
                case 'open/close':
                    $this->_includeTagFile($val['name']);
                    $is_open = count($open_tags);
                    end($open_tags);
                    $parent = $is_open ? current($open_tags) : $this->tag;
                    $permissibly = $this->isPermissiblyChild($parent, $val['name']);
                    if (! $permissibly) {
                        $type = (-1 < $normal_key)
                            ? $normalized[$normal_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $normalized[$normal_key]['str'] .= $val['str'];
                        } else {
                            $normalized[++$normal_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    $normalized[++$normal_key] = $val;
                    $normalized[$normal_key]['level'] = $level;
                    $this -> stat['count_tags'] += 1;
                    break;
                case 'open':
                    $this->_includeTagFile($val['name']);
                    $is_open = count($open_tags);
                    end($open_tags);
                    $parent = $is_open ? current($open_tags) : $this->tag;
                    $permissibly = $this->isPermissiblyChild($parent, $val['name']);
                    if (! $permissibly) {
                        $not_tags[$val['level']] = $val['name'];
                        $type = (-1 < $normal_key)
                            ? $normalized[$normal_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $normalized[$normal_key]['str'] .= $val['str'];
                        } else {
                            $normalized[++$normal_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    $normalized[++$normal_key] = $val;
                    $normalized[$normal_key]['level'] = $level++;
                    $ult_key = count($open_tags);
                    $open_tags[$ult_key] = $val['name'];
                    $this -> stat['count_tags'] += 1;
                    break;
                case 'close':
                    $not_normal = isset($not_tags[$val['level']])
                        && $not_tags[$val['level']] = $val['name'];
                    if ($not_normal) {
                        unset($not_tags[$val['level']]);
                        $type = (-1 < $normal_key)
                            ? $normalized[$normal_key]['type'] : false;
                        if ( 'text' == $type ) {
                            $normalized[$normal_key]['str'] .= $val['str'];
                        } else {
                            $normalized[++$normal_key] = array(
                                    'type'  => 'text',
                                    'str'   => $val['str'],
                                    'level' => $level
                                );
                        }
                        break;
                    }
                    $normalized[++$normal_key] = $val;
                    $normalized[$normal_key]['level'] = --$level;
                    $ult_key = count($open_tags) - 1;
                    unset($open_tags[$ult_key]);
                    $this -> stat['count_tags'] += 1;
                    break;
            }
        }
        unset($structure);
        // Р¤РѕСЂРјРёСЂСѓРµРј РґРµСЂРµРІРѕ СЌР»РµРјРµРЅС‚РѕРІ
        $result = array();
        $result_key = -1;
        $open_tags = array();
        $val_key = -1;
        $this -> stat['count_level'] = 0;
        foreach ($normalized as $normal_key => $val) {
            switch ($val['type']) {
                case 'text':
                    if (! $val['level']) {
                        $result[++$result_key] = array(
                            'type' => 'text',
                            'str' => $val['str']
                        );
                        break;
                    }
                    $open_tags[$val['level']-1]['val'][] = array(
                            'type' => 'text',
                            'str' => $val['str']
                        );
                    break;
                case 'open/close':
                    if (! $val['level']) {
                        $result[++$result_key] = array(
                            'type'   => 'item',
                            'name'   => $val['name'],
                            'attrib' => $val['attrib'],
                            'val'    => array()
                        );
                        break;
                    }
                    $open_tags[$val['level']-1]['val'][] = array(
                        'type'   => 'item',
                        'name'   => $val['name'],
                        'attrib' => $val['attrib'],
                        'val'    => array()
                    );
                    break;
                case 'open':
                    $open_tags[$val['level']] = array(
                        'type'   => 'item',
                        'name'   => $val['name'],
                        'attrib' => $val['attrib'],
                        'val'    => array()
                    );
                    break;
                case 'close':
                    if (! $val['level']) {
                        $result[++$result_key] = $open_tags[0];
                        unset($open_tags[0]);
                        break;
                    }
                    $open_tags[$val['level']-1]['val'][] = $open_tags[$val['level']];
                    unset($open_tags[$val['level']]);
                    break;
            }
            if ($val['level'] > $this -> stat['count_level']) {
                $this -> stat['count_level'] += 1;
            }
        }
        $this -> tree = $result;
        return $result;
    }

    function get_syntax($tree = false) {
        if (! is_array($tree)) {
            $tree = $this -> tree;
        }
        $syntax = array();
        foreach ($tree as $elem) {
            if ('text' == $elem['type']) {
            	$syntax[] = array(
            	    'type' => 'text',
            	    'str' => $this -> specialchars($elem['str'])
            	);
            } else {
                $sub_elems = $this -> get_syntax($elem['val']);
                $str = '';
                $layout = array(array(0, '['));
                foreach ($elem['attrib'] as $name => $val) {
                    $val = $this -> specialchars($val);
                    if ($str) {
                    	$str .= ' ';
                    	$layout[] = array(4, ' ');
                    	$layout[] = array(6, $name);
                    } else {
                        $layout[] = array(2, $name);
                    }
                    $str .= $name;
                    if ($val) {
                    	$str .= '="'.$val.'"';
                    	$layout[] = array(3, '=');
                    	$layout[] = array(5, '"');
                    	$layout[] = array(7, $val);
                    	$layout[] = array(5, '"');
                    }
                }
                if (count($sub_elems)) {
                	$str = '['.$str.']';
                } else {
                    $str = '['.$str.' /]';
                    $layout[] = array(4, ' ');
                    $layout[] = array(1, '/');
                }
                $layout[] = array(0, ']');
                $syntax[] = array(
                    'type' => count($sub_elems) ? 'open' : 'open/close',
                    'str' => $str,
                    'name' => $elem['name'],
                    'attrib' => $elem['attrib'],
                    'layout' => $layout
                );
                foreach ($sub_elems as $sub_elem) { $syntax[] = $sub_elem; }
                if (count($sub_elems)) {
                	$syntax[] = array(
                	    'type' => 'close',
                	    'str' => '[/'.$elem['name'].']',
                	    'name' => $elem['name'],
                	    'layout' => array(
                	        array(0, '['),
                	        array(1, '/'),
                	        array(2, $elem['name']),
                	        array(0, ']')
                	    )
                	);
                }
            }
        }
        return $syntax;
    }

    function insert_smiles($text) {
        $text = htmlspecialchars($text,ENT_NOQUOTES);
        if ($this -> autolinks) {
            $search = $this -> preg_autolinks['pattern'];
            $replace = $this -> preg_autolinks['replacement'];
            $text = preg_replace($search, $replace, $text);
        }
        $text = str_replace('  ', '&nbsp;&nbsp;', nl2br($text));
        $text = strtr($text, $this -> mnemonics);
        return $text;
    }

    function highlight() {
        $time_start = $this -> _getmicrotime();
        $chars = array(
            '@l;'  => '<span class="bb_spec_char">@l;</span>',
            '@r;'  => '<span class="bb_spec_char">@r;</span>',
            '@q;'  => '<span class="bb_spec_char">@q;</span>',
            '@a;'  => '<span class="bb_spec_char">@a;</span>',
            '@at;' => '<span class="bb_spec_char">@at;</span>'
        );
        $search = $this -> preg_autolinks['pattern'];
        $replace = $this -> preg_autolinks['highlight'];
        $str = '';
        foreach($this -> syntax as $elem) {
            if ('text' == $elem['type']) {
                $elem['str'] = strtr(htmlspecialchars($elem['str']), $chars);
                foreach ($this -> mnemonics as $mnemonic => $value) {
                    $elem['str'] = str_replace(
                        $mnemonic,
                        '<span class="bb_mnemonic">'.$mnemonic.'</span>',
                        $elem['str']
                    );
                }
                $elem['str'] = preg_replace($search, $replace, $elem['str']);
                $str .= $elem['str'];
            } else {
                $str .= '<span class="bb_tag">';
                foreach ($elem['layout'] as $val) {
                    switch ($val[0]) {
                        case 0:
                            $str .= '<span class="bb_bracket">'.$val[1]
                                .'</span>';
                            break;
                        case 1:
                            $str .= '<span class="bb_slash">/</span>';
                            break;
                        case 2:
                            $str .= '<span class="bb_tagname">'.$val[1]
                                .'</span>';
                            break;
                        case 3:
                            $str .= '<span class="bb_equal">=</span>';
                            break;
                        case 4:
                            $str .= $val[1];
                            break;
                        case 5:
                            if (! trim($val[1])) {
                            	$str .= $val[1];
                            } else {
                                $str .= '<span class="bb_quote">'.$val[1]
                                    .'</span>';
                            }
                            break;
                        case 6:
                            $str .= '<span class="bb_attrib_name">'
                                .htmlspecialchars($val[1]).'</span>';
                            break;
                        case 7:
                            if (! trim($val[1])) {
                            	$str .= $val[1];
                            } else {
                                $str .= '<span class="bb_attrib_val">'
                                    .strtr(htmlspecialchars($val[1]), $chars)
                                    .'</span>';
                            }
                            break;
                        default:
                            $str .= $val[1];
                    }
                }
                $str .= '</span>';
            }
        }
        $str = nl2br($str);
        $str = str_replace('  ', '&nbsp;&nbsp;', $str);
        $this -> stat['time_html'] = $this -> _getmicrotime() - $time_start;
        return $str;
    }

    function get_html($elems = null) {
        $time_start = $this -> _getmicrotime();
        if (! is_array($elems)) {
            $elems =& $this -> tree;
        }
        $result = '';
        $lbr = 0;
        $rbr = 0;
        foreach ($elems as $elem) {
            if ('text' == $elem['type']) {
                $elem['str'] = $this -> insert_smiles($elem['str']);
                for ($i=0; $i < $rbr; ++$i) {
                    $elem['str'] = ltrim($elem['str']);
                    if ('<br />' == substr($elem['str'], 0, 6)) {
                        $elem['str'] = substr_replace($elem['str'], '', 0, 6);
                    }
                }
                $result .= $elem['str'];
            } else {
            	$this->_includeTagFile($elem['name']);
            	$handler = $this->tags[$elem['name']];
                /* РЈР±РёСЂР°РµРј Р»РёС€РЅРёРµ РїРµСЂРµРІРѕРґС‹ СЃС‚СЂРѕРє */
                $class_vars = get_class_vars($handler);
                $lbr = $class_vars['lbr'];
                $rbr = $class_vars['rbr'];
                for ($i=0; $i < $lbr; ++$i) {
                    $result = rtrim($result);
                    if ('<br />' == substr($result, -6)) {
                        $result = substr_replace($result, '', -6, 6);
                    }
                }
                /* РћР±СЂР°Р±Р°С‚С‹РІР°РµРј СЃРѕРґРµСЂР¶РёРјРѕРµ СЌР»РµРјРµРЅС‚Р° */
                $tag = $this->_tag_objects[$handler];
                $tag->autolinks = $this->autolinks;
                $tag->tags = $this->tags;
                $tag->mnemonics = $this->mnemonics;
                $tag->tag = $elem['name'];
                $tag->attrib = $elem['attrib'];
                $tag->tree = $elem['val'];
                $result .= $tag -> get_html();
            }
        }
        $result = preg_replace(
            "'\s*<br \/>\s*<br \/>\s*'si", "\n<br />&nbsp;<br />\n", $result
        );
        $this->stat['time_html'] = $this->_getmicrotime() - $time_start;
        return $result;
    }

    /* Р¤СѓРЅРєС†РёСЏ РїСЂРµРѕР±СЂР°Р·СѓРµС‚ СЃС‚СЂРѕРєСѓ URL СЃ С†РµР»СЊСЋ Р·Р°С‰РёС‚С‹ РѕС‚ javascript-РёРЅСЉРµРєС†РёРё. */
    function checkUrl($url) {
        if (! $url) { return ''; }
        $protocols = array(
            'ftp://', 'file://', 'http://', 'https://', 'mailto:', 'svn://',
            '#',      '/',       '?',       './',       '../',     'www.'
        );
        $is_http = false;
        foreach ($protocols as $val) {
            if ($val == substr($url, 0, strlen($val))) {
                $is_http = true;
                if ('www.' == $val) {
                	$url = 'http://'.$url;
                }
                break;
            }
        }
        if (! $is_http) { $url = './'.$url; }
        $url = htmlentities($url, ENT_QUOTES);
        $url = str_replace('.', '&#'.ord('.').';', $url);
        $url = str_replace(':', '&#'.ord(':').';', $url);
        $url = str_replace('(', '&#'.ord('(').';', $url);
        $url = str_replace(')', '&#'.ord(')').';', $url);
        return $url;
    }

    /*
    Р¤СѓРЅРєС†РёСЏ РІРѕР·РІСЂР°С‰Р°РµС‚ С‚РµРєСѓС‰РёР№ UNIX timestamp СЃ РјРёРєСЂРѕСЃРµРєСѓРЅРґР°РјРё РІ С„РѕСЂРјР°С‚Рµ float
    */
    function _getmicrotime() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $usec + (float) $sec;
    }

    /*
    Р¤СѓРЅРєС†РёСЏ РїСЂРѕРІРµСЂСЏРµС‚, РґРѕСЃС‚СѓРїРµРЅ Р»Рё РєР»Р°СЃСЃ - РѕР±СЂР°Р±РѕС‚С‡РёРє С‚РµРіР° СЃ РёРјРµРЅРµРј $tagName Рё,
    РµСЃР»Рё РЅРµС‚, РїС‹С‚Р°РµС‚СЃСЏ РїРѕРґРєР»СЋС‡РёС‚СЊ С„Р°Р№Р» СЃ СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓСЋС‰РёРј РєР»Р°СЃСЃРѕРј. Р•СЃР»Рё СЌС‚Рѕ РЅРµ
    РІРѕР·РјРѕР¶РЅРѕ, РїРµСЂРµРЅР°Р·РЅР°С‡Р°РµС‚ С‚РµРіСѓ РѕР±СЂР°Р±РѕС‚С‡РёРє, - СЃРѕРїРѕСЃС‚Р°РІР»СЏРµС‚ РµРјСѓ РєР»Р°СЃСЃ bbcode.
    Р—Р°С‚РµРј РёРЅРёС†РёР°Р»РёР·РёСЂСѓРµС‚ РѕР±СЉРµРєС‚ РѕР±СЂР°Р±РѕС‚С‡РёРєР° (РµСЃР»Рё РѕРЅ РµС‰Рµ РЅРµ РёРЅРёС†РёР°Р»РёР·РёСЂРѕРІР°РЅ).
    */
    function _includeTagFile($tagName) {
        if (! class_exists($this->tags[$tagName])) {
            $tag_file = $this->_current_path
                . str_replace('_', DIRECTORY_SEPARATOR, $this->tags[$tagName])
                . '.php';
            if (is_file($tag_file)) {
                include_once $tag_file;
            } else {
            	$this->tags[$tagName] = 'bbcode';
            }
        }
        $handler = $this->tags[$tagName];
        if (! isset($this->_tag_objects[$handler])) {
            $this->_tag_objects[$handler] = new $handler;
        }
        return true;
    }
}
