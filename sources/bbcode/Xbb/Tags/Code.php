<?php

/******************************************************************************
 *                                                                            *
 *   Code.php, v 0.06 2007/04/29 - This is part of xBB library                *
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

/* РљР»Р°СЃСЃ РґР»СЏ С‚РµРіРѕРІ РїРѕРґСЃРІРµС‚РєРё СЃРёРЅС‚Р°РєСЃРёСЃР° Рё РґР»СЏ С‚РµРіРѕРІ [code] Рё [pre] */
class Xbb_Tags_Code extends bbcode {
    /* Р§РёСЃР»Рѕ СЂР°Р·СЂС‹РІРѕРІ СЃС‚СЂРѕРє, РєРѕС‚РѕСЂС‹Рµ РґРѕР»Р¶РЅС‹ Р±С‹С‚СЊ РёРіРЅРѕСЂРёСЂРѕРІР°РЅС‹ РїРµСЂРµРґ С‚РµРіРѕРј */
    public $lbr = 0;
    /* Р§РёСЃР»Рѕ СЂР°Р·СЂС‹РІРѕРІ СЃС‚СЂРѕРє, РєРѕС‚РѕСЂС‹Рµ РґРѕР»Р¶РЅС‹ Р±С‹С‚СЊ РёРіРЅРѕСЂРёСЂРѕРІР°РЅС‹ РїРѕСЃР»Рµ С‚РµРіР° */
    public $rbr = 1;
    public $behaviour = 'pre';
    /* РђР»СЊС‚РµСЂРЅР°С‚РёРІРЅС‹Рµ РЅР°Р·РІР°РЅРёСЏ СЏР·С‹РєРѕРІ Рё РёС… С‚СЂР°РЅСЃР»СЏС†РёСЏ РІ РѕР±РѕР·РЅР°С‡РµРЅРёСЏ GeSHi */
    public $lang_synonym = array(
        'c++'    => 'cpp',
        'c#'     => 'csharp',
        'html'   => 'html4strict',
        'html4'  => 'html4strict',
        'js'     => 'javascript',
        'ocaml'  => 'ocaml-brief',
        'oracle' => 'oracle8',
        't-sql'  => 'tsql',
        'vb.net' => 'vbnet',
    );
    /* РћР±СЉРµРєС‚ GeSHi */
    private $_geshi;
    /* РљРѕРЅСЃС‚СЂСѓРєС‚РѕСЂ РєР»Р°СЃСЃР° */
    function Xbb_Tags_Code() {
        $geshi_path = realpath(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'geshi.php'
        );
        @include_once $geshi_path;
        @$this->_geshi =  new GeSHi('', 'text');
        $this->_geshi->set_header_type(GESHI_HEADER_NONE);
    }
    /* РћРїРёСЃС‹РІР°РµРј РєРѕРЅРІРµСЂС‚Р°С†РёСЋ РІ HTML */
    function get_html($tree = null) {
        // РќР°С…РѕРґРёРј СЏР·С‹Рє РїРѕРґСЃРІРµС‚РєРё
        switch ($this->tag) {
            case 'code':
                $language = $this->attrib['code'];
                break;
            case 'pre':
                $language = $this->attrib['pre'];
                break;
            default:
                $language = $this->tag;
        }
        if (! $language) { $language = 'text'; }
        if (isset($this->lang_synonym[$language])) {
            $language = $this->lang_synonym[$language];
        }
        @$this->_geshi->set_language($language);
        // РќР°С…РѕРґРёРј РїРѕРґСЃРІРµС‡РёРІР°РµРјС‹Р№ РєРѕРґ
        $source = '';
        foreach ($this->tree as $item) {
            if ('item' == $item['type']) { continue; }
            $source .= $item['str'];
        }
        $this->_geshi->set_source($source);
        // РЈСЃС‚Р°РЅР°РІР»РёРІР°РµРј РЅСѓРјРµСЂР°С†РёСЋ СЃС‚СЂРѕРє
        if (isset($this->attrib['num'])) {
            $this->_geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
            if ('' !== $this -> attrib['num']) {
                $num = (int) $this->attrib['num'];
                $this->_geshi->start_line_numbers_at($num);
            }
        }
        // Р—Р°РґР°РµРј РІРµР»РёС‡РёРЅСѓ С‚Р°Р±СѓР»СЏС†РёРё
        if (isset($this->attrib['tab'])) {
        	$this->attrib['tab'] = (int) $this->attrib['tab'];
        	if ($this->attrib['tab']) {
        	    $this->_geshi -> set_tab_width($this->attrib['tab']);
        	}
        }
        // РЈСЃС‚Р°РЅР°РІР»РёРІР°РµРј РІС‹РґРµР»РµРЅРёРµ СЃС‚СЂРѕРє
        if (isset($this->attrib['extra'])) {
            $extra = explode(',', $this->attrib['extra']);
            foreach ($extra as $key => $val) {
                $extra[$key] = (int) $val;
            }
            $this->_geshi->highlight_lines_extra($extra);
        }
        // Р¤РѕСЂРјРёСЂСѓРµРј Р·Р°РіРѕР»РѕРІРѕРє
        $result = '<span class="bb_code_lang">'
            . $this->_geshi->get_language_name() . '</span>';
        if (isset($this->attrib['title'])) {
            $result = htmlspecialchars($this->attrib['title']);
        }
        // РџРѕР»СѓС‡Р°РµРј РїРѕРґСЃРІРµС‡РµРЅРЅС‹Р№ РєРѕРґ
        $result = '<div class="bb_code"><div class="bb_code_header">' .$result
            . '</div>' . $this->_geshi->parse_code();
        // Р¤РѕСЂРјРёСЂСѓРµРј РїРѕРґРїРёСЃСЊ РїРѕРґ РєРѕРґРѕРј
        if (isset($this->attrib['footer'])) {
            $content = htmlspecialchars($this->attrib['footer']);
            $content = '<div class="bb_code_footer">' . $content . '</div>';
            $result .= $content;
        }
        // Р’РѕР·РІСЂР°С‰Р°РµРј СЂРµР·СѓР»СЊС‚Р°С‚
        return $result . '</div>';
    }
}
?>
