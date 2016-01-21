/******************************************************************************
 *                                                                            *
 *   bbcode.lib.js, v 0.00 2007/07/25 - This is part of xBB library           *
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

function bbcode(code) {
    /* РўРµРєСЃС‚ BBCode */
    this.text = code;
    /* Р РµР·СѓР»СЊС‚Р°С‚ СЃРёРЅС‚Р°РєСЃРёС‡РµСЃРєРѕРіРѕ СЂР°Р·Р±РѕСЂР° С‚РµРєСЃС‚Р° BBCode. */
    this.syntax = [];
    /* РЎРїРёСЃРѕРє РїРѕРґРґРµСЂР¶РёРІР°РµРјС‹С… С‚РµРіРѕРІ. */
    this.tags = [];
    /* Р¤Р»Р°Р¶РѕРє, РІРєР»СЋС‡Р°СЋС‰РёР№/РІС‹РєР»СЋС‡Р°СЋС‰РёР№ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРёРµ СЃСЃС‹Р»РєРё. */
    this.autolinks = true;
    /* РњР°СЃСЃРёРІ Р·Р°РјРµРЅ РґР»СЏ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРёС… СЃСЃС‹Р»РѕРє. */
    this.preg_autolinks = {
        pattern   : [
            /(\w+:\/\/[A-z0-9\.\?\+\-\/_=&%#:;]+[\w/=]+)/,
            /([^/])(www\.[A-z0-9\.\?\+\-/_=&%#:;]+[\w/=]+)/,
            /([\w]+[\w\-\.]+@[\w\-\.]+\.[\w]+)/,
        ],
        highlight : [
            '<' + 'span class="bb_autolink">$1<' + '/span>',
            '$1<' + 'span class="bb_autolink">$2<' + '/span>',
            '<' + 'span class="bb_autolink">$1<' + '/span>',
        ]
    };
    /* РџРѕРґСЃРІРµС‡РёРІР°РµРјС‹Рµ СЃРјР°Р№Р»РёРєРё Рё РїСЂРѕС‡РёРµ РјРЅРµРјРѕРЅРёРєРё. */
    this.mnemonics = [];
    /* РћСЃРЅРѕРІРЅС‹Рµ СЃРјР°Р№Р»РёРєРё, РїСЂРµРґР»Р°РіР°РµРјС‹Рµ РЅР° РІС‹Р±РѕСЂ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ. */
    this.smiles = [];
    /* РЎРїРёСЃРѕРє С€СЂРёС„С‚РѕРІ, РїСЂРµРґР»Р°РіР°РµРјС‹С… РЅР° РІС‹Р±РѕСЂ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ */
    this.fonts = [];
    /* РџР°Р»РёС‚СЂР° С†РІРµС‚РѕРІ, РїСЂРµРґР»Р°РіР°РµРјС‹С… РЅР° РІС‹Р±РѕСЂ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ */
    this.colors = [];
    /* Id РёС„СЂРµР№РјР° СЃ РїРѕРґСЃРІРµС‡РµРЅРЅС‹Рј bbcode */
    this.iframeId = 'xbb_iframe';
    /* Id textarea СЃ С‚РµРєСЃС‚РѕРј bbcode */
    this.textareaId = 'xbb_textarea';
    /*
    div, РёСЃРїРѕР»СЊР·СѓРµРјС‹Р№ РєР°Рє РєРѕРЅС‚РµР№РЅРµСЂ РґР°РЅРЅС‹С…,
    РїРµСЂРµРґР°РІР°РµРјС‹С… РёР· РѕРґРЅРѕРіРѕ С„СЂРµРјР° РІ РґСЂСѓРіРѕР№
    */
    this.transportDiv = parent.document.getElementById('xbb_transport_div');
    /*
    Р РµР¶РёРј, РІ РєРѕС‚РѕСЂРѕРј РІ РґР°РЅРЅС‹Р№ РјРѕРјРµРЅС‚ РЅР°С…РѕРґРёС‚СЃСЏ СЂРµРґР°РєС‚РѕСЂ. Р’РѕР·РјРѕР¶РЅС‹Рµ Р·РЅР°С‡РµРЅРёСЏ:
    'plain' (textarea) РёР»Рё 'highlight' (РїРѕРґСЃРІРµС‚РєР° СЃРёРЅС‚Р°РєСЃРёСЃР°)
    */
    this.state = 'plain';
    /* Р”Р»СЏ РЅСѓР¶Рґ РїР°СЂСЃРµСЂР°. - РџРѕР·РёС†РёСЏ РѕС‡РµСЂРµРґРЅРѕРіРѕ РѕР±СЂР°Р±Р°С‚С‹РІР°РµРјРѕРіРѕ СЃРёРјРІРѕР»Р°. */
    var _cursor = 0;
    /*
    get_token() - Р¤СѓРЅРєС†РёСЏ РїР°СЂСЃРёС‚ С‚РµРєСЃС‚ BBCode Рё РІРѕР·РІСЂР°С‰Р°РµС‚ РѕС‡РµСЂРµРґРЅСѓСЋ РїР°СЂСѓ

                        "С‡РёСЃР»Рѕ (С‚РёРї Р»РµРєСЃРµРјС‹) - Р»РµРєСЃРµРјР°"

    Р›РµРєСЃРµРјР° - РїРѕРґСЃС‚СЂРѕРєР° СЃС‚СЂРѕРєРё this.text, РЅР°С‡РёРЅР°СЋС‰Р°СЏСЃСЏ СЃ РїРѕР·РёС†РёРё _cursor
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
    this.get_token = function() {
        var token = '';
        var token_type = NaN;
        var char_type = NaN;
        var cur_char;
        while (true) {
            token_type = char_type;
            if (! this.text.charAt(_cursor)) {
                if (isNaN(char_type)) {
                    return false;
                } else {
                    break;
                }
            }
            cur_char = this.text.charAt(_cursor);
            switch (cur_char) {
                case '[':
                    char_type = 0;
                    break;
                case ']':
                    char_type = 1;
                    break;
                case '"':
                    char_type = 2;
                    break;
                case "'":
                    char_type = 3;
                    break;
                case "=":
                    char_type = 4;
                    break;
                case '/':
                    char_type = 5;
                    break;
                case ' ':
                    char_type = 6;
                    break;
                case "\t":
                    char_type = 6;
                    break;
                case "\n":
                    char_type = 6;
                    break;
                case "\r":
                    char_type = 6;
                    break;
                case "\0":
                    char_type = 6;
                    break;
                case "\x0B":
                    char_type = 6;
                    break;
                default:
                    char_type = 7;
            }
            if (isNaN(token_type)) {
                token = cur_char;
            } else if (5 >= token_type) {
                break;
            } else if (char_type == token_type) {
                token += cur_char;
            } else {
                break;
            }
            _cursor += 1;
        }
        if (this.in_array(token.toLowerCase(), this.tags)) {
            token_type = 8;
        }
        return [token_type, token];
    }

    this.parse = function(code) {
        if (code) { this.text = code; }
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
        var finite_automaton = {
         // РџСЂРµРґС‹РґСѓС‰РёРµ |   РЎРѕСЃС‚РѕСЏРЅРёСЏ РґР»СЏ С‚РµРєСѓС‰РёС… СЃРѕР±С‹С‚РёР№ (Р»РµРєСЃРµРј)   |
         //  СЃРѕСЃС‚РѕСЏРЅРёСЏ |  0 |  1 |  2 |  3 |  4 |  5 |  6 |  7 |  8 |
                   0 : [  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ]
                ,  1 : [  2 ,  3 ,  3 ,  3 ,  3 ,  4 ,  3 ,  3 ,  5 ]
                ,  2 : [  2 ,  3 ,  3 ,  3 ,  3 ,  4 ,  3 ,  3 ,  5 ]
                ,  3 : [  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ]
                ,  4 : [  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  7 ]
                ,  5 : [  2 ,  6 ,  3 ,  3 ,  8 ,  9 , 10 ,  3 ,  3 ]
                ,  6 : [  1 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ,  0 ]
                ,  7 : [  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ]
                ,  8 : [ 13 , 13 , 11 , 12 , 13 , 13 , 14 , 13 , 13 ]
                ,  9 : [  2 ,  6 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ,  3 ]
                , 10 : [  2 ,  6 ,  3 ,  3 ,  8 ,  9 ,  3 , 15 , 15 ]
                , 11 : [ 16 , 16 , 17 , 16 , 16 , 16 , 16 , 16 , 16 ]
                , 12 : [ 18 , 18 , 18 , 17 , 18 , 18 , 18 , 18 , 18 ]
                , 13 : [ 19 ,  6 , 19 , 19 , 19 , 19 , 17 , 19 , 19 ]
                , 14 : [  2 ,  3 , 11 , 12 , 13 , 13 ,  3 , 13 , 13 ]
                , 15 : [  2 ,  6 ,  3 ,  3 ,  8 ,  9 , 10 ,  3 ,  3 ]
                , 16 : [ 16 , 16 , 17 , 16 , 16 , 16 , 16 , 16 , 16 ]
                , 17 : [  2 ,  6 ,  3 ,  3 ,  3 ,  9 , 20 , 15 , 15 ]
                , 18 : [ 18 , 18 , 18 , 17 , 18 , 18 , 18 , 18 , 18 ]
                , 19 : [ 19 ,  6 , 19 , 19 , 19 , 19 , 20 , 19 , 19 ]
                , 20 : [  2 ,  6 ,  3 ,  3 ,  3 ,  9 ,  3 , 15 , 15 ]
            };
        // Р—Р°РєРѕРЅС‡РёР»Рё РѕРїРёСЃР°РЅРёРµ РєРѕРЅРµС‡РЅРѕРіРѕ Р°РІС‚РѕРјР°С‚Р°
        var mode = 0;
        this.syntax = [];
        var decomposition = {};
        var token_key = -1;
        var value = '';
        var previous_mode;
        var type;
        var name;
        _cursor = 0;
        var token = this.get_token();
        // РЎРєР°РЅРёСЂСѓРµРј РјР°СЃСЃРёРІ Р»РµРєСЃРµРј СЃ РїРѕРјРѕС‰СЊСЋ РїРѕСЃС‚СЂРѕРµРЅРЅРѕРіРѕ Р°РІС‚РѕРјР°С‚Р°:
        while (token) {
            previous_mode = mode;
            mode = finite_automaton[previous_mode][token[0]];
            if (-1 < token_key) {
                type = this.syntax[token_key].type;
            } else {
                type = false;
            }
            switch (mode) {
                case 0:
                    if ('text' == type) {
                        this.syntax[token_key].str += token[1];
                    } else {
                        this.syntax[++token_key] = {
                            type : 'text',
                            str  : token[1]
                        };
                    }
                    break;
                case 1:
                    decomposition = {
                        name   : '',
                        type   : '',
                        str    : '[',
                        layout : [[0, '[']]
                    };
                    break;
                case 2:
                    if ('text' == type) {
                        this.syntax[token_key].str += decomposition.str;
                    } else {
                        this.syntax[++token_key] = {
                            type : 'text',
                            str  : decomposition.str
                        };
                    }
                    decomposition = {
                        name   : '',
                        type   : '',
                        str    : '[',
                        layout : [[0, '[']]
                    };
                    break;
                case 3:
                    if ('text' == type) {
                        this.syntax[token_key].str += decomposition.str;
                        this.syntax[token_key].str += token[1];
                    } else {
                        this.syntax[++token_key] = {
                            type : 'text',
                            str  : decomposition.str + token[1]
                        };
                    }
                    decomposition = {};
                    break;
                case 4:
                    decomposition.type = 'close';
                    decomposition.str += '/';
                    decomposition.layout[decomposition.layout.length] = [1, '/'];
                    break;
                case 5:
                    decomposition.type = 'open';
                    name = token[1].toLowerCase();
                    decomposition.name = name;
                    decomposition.str += token[1];
                    decomposition.layout[decomposition.layout.length] = [2, token[1]];
                    if (! decomposition.attrib) {
                        decomposition.attrib = {};
                    }
                    decomposition.attrib[name] = '';
                    break;
                case 6:
                    if (! decomposition.name) {
                        decomposition.name = '';
                    }
                    if (13 == previous_mode || 19 == previous_mode) {
                        decomposition.layout[decomposition.layout.length] = [7, value];
                    }
                    decomposition.str += ']';
                    decomposition.layout[decomposition.layout.length] = [0, ']'];
                    this.syntax[++token_key] = decomposition;
                    decomposition = {};
                    break;
                case 7:
                    decomposition.name = token[1].toLowerCase();
                    decomposition.str += token[1];
                    decomposition.layout[decomposition.layout.length] = [2, token[1]];
                    break;
                case 8:
                    decomposition.str += '=';
                    decomposition.layout[decomposition.layout.length] = [3, '='];
                    break;
                case 9:
                    decomposition.type = 'open/close';
                    decomposition.str += '/';
                    decomposition.layout[decomposition.layout.length] = [1, '/'];
                    break;
                case 10:
                    decomposition.str += token[1];
                    decomposition.layout[decomposition.layout.length] = [4, token[1]];
                    break;
                case 11:
                    decomposition.str += '"';
                    decomposition.layout[decomposition.layout.length] = [5, '"'];
                    value = '';
                    break;
                case 12:
                    decomposition.str += "'";
                    decomposition.layout[decomposition.layout.length] = [5, "'"];
                    value = '';
                    break;
                case 13:
                    if (! decomposition.attrib) {
                        decomposition.attrib = {};
                    }
                    decomposition.attrib[name] = token[1];
                    value = token[1];
                    decomposition.str += token[1];
                    break;
                case 14:
                    decomposition.str += token[1];
                    decomposition.layout[decomposition.layout.length] = [4, token[1]];
                    break;
                case 15:
                    name = token[1].toLowerCase();
                    decomposition.str += token[1];
                    decomposition.layout[decomposition.layout.length] = [6, token[1]];
                    if (! decomposition.attrib) {
                        decomposition.attrib = {};
                    }
                    decomposition.attrib[name] = '';
                    break;
                case 16:
                    decomposition.str += token[1];
                    if (! decomposition.attrib) {
                        decomposition.attrib = {};
                    }
                    decomposition.attrib[name] += token[1];
                    value += token[1];
                    break;
                case 17:
                    decomposition.str += token[1];
                    decomposition.layout[decomposition.layout.length] = [7, value];
                    value = '';
                    decomposition.layout[decomposition.layout.length] = [5, token[1]];
                    break;
                case 18:
                    decomposition.str += token[1];
                    decomposition.attrib[name] += token[1];
                    value += token[1];
                    break;
                case 19:
                    decomposition.str += token[1];
                    decomposition.attrib[name] += token[1];
                    value += token[1];
                    break;
                case 20:
                    decomposition.str += token[1];
                    if (13 == previous_mode || 19 == previous_mode) {
                        decomposition.layout[decomposition.layout.length] = [7, value];
                    }
                    value = '';
                    decomposition.layout[decomposition.layout.length] = [4, token[1]];
                    break;
            }
            token = this.get_token();
        }
        if (decomposition.type) {
            if ('text' == type) {
                this.syntax[token_key].str += decomposition.str;
            } else {
                this.syntax[++token_key] = {
                    type : 'text',
                    str  : decomposition.str
                };
            }
        }
    }

    this.highlight = function() {
        var chars = [
            ['@l;' , '<span class="bb_spec_char">@l;</span>' ],
            ['@r;' , '<span class="bb_spec_char">@r;</span>' ],
            ['@q;' , '<span class="bb_spec_char">@q;</span>' ],
            ['@a;' , '<span class="bb_spec_char">@a;</span>' ],
            ['@at;', '<span class="bb_spec_char">@at;</span>']
        ];
        var link_search = this.preg_autolinks.pattern;
        var link_replace = this.preg_autolinks.highlight;
        var str = '';
        var elem;
        var val;
        for (var i_syntax in this.syntax) {
            elem = this.syntax[i_syntax].str;
            if ('text' == this.syntax[i_syntax].type) {
                elem = this.htmlspecialchars(elem);
                elem = this.strtr(elem, chars);
                for (var i_mnemonic in this.mnemonics) {
                    elem = elem.replace(
                        this.mnemonics[i_mnemonic],
                        '<span class="bb_mnemonic">' + this.mnemonics[i_mnemonic] + '</span>'
                    );
                }
                for (var i = 0; link_search[i]; ++i) {
                    elem = elem.replace(link_search[i], link_replace[i]);
                }
                str += elem;
            } else {
                str += '<span class="bb_tag">';
                var trim_val = '';
                for (var i_val in this.syntax[i_syntax].layout) {
                    val = this.syntax[i_syntax].layout[i_val];
                    switch (val[0]) {
                        case 0:
                            str += '<span class="bb_bracket">' + val[1] + '</span>';
                            break;
                        case 1:
                            str += '<span class="bb_slash">/</span>';
                            break;
                        case 2:
                            str += '<span class="bb_tagname">' + val[1] + '</span>';
                            break;
                        case 3:
                            str += '<span class="bb_equal">=</span>';
                            break;
                        case 4:
                            str += val[1];
                            break;
                        case 5:
                            trim_val = val[1].replace(/\s/, '');
                            if (! trim_val) {
                            	str += val[1];
                            } else {
                                str += '<span class="bb_quote">' + val[1] + '</span>';
                            }
                            break;
                        case 6:
                            str += '<span class="bb_attrib_name">'
                                + this.htmlspecialchars(val[1]) + '</span>';
                            break;
                        case 7:
                            trim_val = val[1].replace(/\s/, '');
                            if (! trim_val) {
                            	str += val[1];
                            } else {
                                str += '<span class="bb_attrib_val">'
                                    + this.strtr(this.htmlspecialchars(val[1]), chars)
                                    + '</span>';
                            }
                            break;
                        default:
                            str += val[1];
                    }
                }
                str += '</span>';
            }
        }
        str = this.nl2br(str);
        str = str.replace(/\s\s/, '&nbsp;&nbsp;');
        return str;
    }

    /*
    РўРµРєСЃС‚РѕРІРѕРµ СЃРѕРґРµСЂР¶РёРјРѕРµ СѓР·Р»Р° СЃ Р·Р°РјРµРЅРѕР№ <br /> РЅР° СЂР°Р·СЂС‹РІ СЃС‚СЂРѕРєРё Рё РѕРєСЂС‹Р¶РµРЅРёРµРј
    <p> СЂР°Р·СЂС‹РІР°РјРё СЃС‚СЂРѕРє.
    */
    this.innerText = function(node) {
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

    /* РђРЅР°Р»РѕРі С„СѓРЅРєС†РёРё in_array РІ PHP */
    this.in_array = function(needle, haystack) {
        for (var i = 0; haystack[i]; i++) {
            if (haystack[i] == needle) {
                return true;
            }
        }
        return false;
    }

    /* РђРЅР°Р»РѕРі С„СѓРЅРєС†РёРё nl2br РІ PHP */
    this.nl2br = function(str) {
        if (typeof(str) == "string") {
            return str.replace(/(\r\n)|(\n\r)|\r|\n/g, '<br />');
        }
        return str;
    }

    /* РђРЅР°Р»РѕРі С„СѓРЅРєС†РёРё htmlspecialchars РІ PHP */
    this.htmlspecialchars = function(str) {
        str = str.replace(/&/g, '&amp;');
        str = str.replace('/\"/g', '&quot;');
        str = str.replace("/\'/g", '&#039;');
        str = str.replace(/</g, '&lt;');
        str = str.replace(/>/g, '&gt;');
        return str
    }

    /*
    РђРЅР°Р»РѕРі С„СѓРЅРєС†РёРё strtr РІ PHP
    pairs = [['a', 'b'], ['c', 'd']];
    str1 = strtr("abcdabcdabcdabcd", pairs);
    str2 = strtr("abcdabcdabcdabcd", "dcba", "hgfe");
    */
    this.strtr = function(str, pairs, to) {
        if ((typeof(pairs)=="object") && (pairs.length)) {
            for (i in pairs) {
                str = str.replace(RegExp(pairs[i][0], "g"), pairs[i][1]);
            }
            return str;
        } else {
            pairs2 = new Array();
            for (i = 0; i < pairs.length; i++) {
                pairs2[i] = [pairs.substr(i,1), to.substr(i,1)];
            }
            return strtr(str, pairs2);
        }
    }

    this.parse();
}

/*
Р—Р°РєРѕРЅС‡РµРЅРѕ РѕРїРёСЃР°РЅРёРµ РєР»Р°СЃСЃР° bbcode.
РќРёР¶Рµ СЃР»РµРґСѓСЋС‚ РѕРїРёСЃР°РЅРёСЏ С„СѓРЅРєС†РёР№ РґР»СЏ СЂР°Р±РѕС‚С‹ СЃ textarea
*/

// Remember the current position.
function storeCaret(text) {
	// Only bother if it will be useful.
	if (typeof(text.createTextRange) != "undefined")
		text.caretPos = document.selection.createRange().duplicate();
}

// Replaces the currently selected text with the passed text.
function replaceText(text, textarea) {
	// Attempt to create a text range (IE).
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange) {
		var caretPos = textarea.caretPos;
		if (caretPos.text.charAt(caretPos.text.length - 1) == ' ') {
		    caretPos.text = text + ' ';
		} else {
		    caretPos.text = text;
		}
		caretPos.select();
	}
	// Mozilla text range replace.
	else if (typeof(textarea.selectionStart) != "undefined") {
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text + end;

		if (textarea.setSelectionRange)
		{
			textarea.focus();
			textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put it on the end.
	else {
		textarea.value += text;
		textarea.focus(textarea.value.length - 1);
	}
}

// Surrounds the selected text with text1 and text2.
function surroundText(text1, text2, textarea) {
	textarea = xbb_textarea;
	// Can a text range be created?
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange) {
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

		if (temp_length == 0) {
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
		}
		else
			textarea.focus(caretPos);
	}
	// Mozilla text range wrap.
	else if (typeof(textarea.selectionStart) != "undefined") {
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange) {
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put them on the end, then.
	else {
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}
}

function doinsert(text1, text2) {
    textarea = xbb_textarea;
	// Can a text range be created?
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
	{
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ?  caretPos.text + text1 + text2 + ' ' :  caretPos.text + text1 + text2;

		if (temp_length == 0)
		{
			caretPos.moveStart("character", 0);
			caretPos.moveEnd("character", 0);
			caretPos.select();
		}
		else
			textarea.focus(caretPos);
	}
	// Mozilla text range wrap.
	else if (typeof(textarea.selectionStart) != "undefined")
	{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange)
		{
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length + text2.length , newCursorPos + text1.length + text2.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put them on the end, then.
	else
	{
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}

}

function tag_url()
{
var FoundErrors = '';
var enterURL   = prompt(text_enter_url, "http://");
var enterTITLE = prompt(text_enter_url_name, "My WebPage");

if (!enterURL || enterURL=='http://') {FoundErrors = 1;}
else if (!enterTITLE) {FoundErrors = 1;}

if (FoundErrors) {return;}

doinsert ('[url=' + enterURL + ']'+enterTITLE, '[/url]');
}

function tag_email()
{
var emailAddress = prompt(text_enter_email, "");

if (!emailAddress) {return;}

doinsert("[email]"+emailAddress,"[/email]");
}

function tag_image()
{
var FoundErrors = '';
var enterURL   = prompt(text_enter_image, "http://");

if (!enterURL || enterURL=='http://' || enterURL.length<10) {return;}

doinsert("[img]"+enterURL,"[/img]");
}

function tag_list()
{
var listvalue = "init";
var thelist = "";

while ( (listvalue != "") && (listvalue != null) )
{
listvalue = prompt(list_prompt, "");
if ( (listvalue != "") && (listvalue != null) )
{
thelist = thelist+"[*]"+listvalue+"\n";
}
}

if ( thelist != "" )
{
doinsert( "[list]\n" + thelist, "[/list]\n");
}
}
