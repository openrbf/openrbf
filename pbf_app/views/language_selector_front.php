<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/* SVN FILE: $Id: language.php 188 2009-04-10 07:06:02Z Roland $ */
/**
 * |------------------------------------------------------------------------
 * | view element to display a language selection.
 * |
 * | This element shows the flags of all supported languages in a row
 * | and allows the user to select one of them.
 * | The flag images are expected in the img/lang subdirectory of the
 * | site's webroot. The image names must correspond to the keys of
 * | the array in the configuration entry $config['lang_avail'].
 * |
 * | Per language there should be two images for the selected and the
 * | unselected state, i.e. 'en.gif' and 'en_sel.gif'
 * |
 * | This library is free software; you can redistribute it and/or
 * | modify it under the terms of the GNU Lesser General Public
 * | License as published by the Free Software Foundation; either
 * | version 2.1 of the License, or (at your option) any later version.
 * |
 * | This library is distributed in the hope that it will be useful,
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * | Lesser General Public License for more details.
 * |
 * | You should have received a copy of the GNU Lesser General Public
 * | License along with this library; if not, write to the Free Software
 * | Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 * |
 */

/**
 * begin of user configurable items
 */
	// base directory of the flag images
$_img_dir = isset ( $img_dir ) ? $img_dir : base_url () . 'cside/images/lang';
// CSS class for each flag
$_img_css_class = isset ( $img_css_class ) ? $img_css_class : '';
// CSS style for each flag
$_img_css_style = isset ( $img_css_style ) ? $img_css_style : 'margin-right:4px';
// base value of the tabindex for the links
$_tabindex_start = isset ( $tabindex_start ) ? $tabindex_start : 20;

/**
 * end of user configurable items
 */

// render the img tag
if (! function_exists ( '_render_img_tag' )) {
	function _render_img_tag($img_dir, $title, $pic, $img_css_class, $img_css_style) {
		$fstr = "<img src='$img_dir/$pic'" . " alt='$title' title='$title'";
		if (! empty ( $img_css_class )) {
			$fstr .= " class='$img_css_class'";
		}
		if (! empty ( $img_css_style )) {
			$fstr .= " style='$img_css_style'";
		}
		$fstr .= " />";
		return $fstr;
	}
}

// get array of available languages
$_lang_avail = $this->config->item ( 'lang_uri_abbr' );
if ($_lang_avail !== false) {
	// get user's current language code
	$_sel_lang = $this->config->item ( 'language_abbr' ); // from the browser or the cookie... $_SERVER['HTTP_ACCEPT_LANGUAGE']. or
	                                                   // load the respective language file
	$this->lang->load ( 'lang' );
	if (! function_exists ( 'anchor' )) {
		$this->load->helper ( 'url' );
	}
	$_Output = Array ();
	$v = 0;
	foreach ( $_lang_avail as $_lang => $_language ) {
		// get language name in currently selected language
		$_lng = $this->lang->line ( 'lng_' . $_lang );
		if ($_sel_lang == $_lang) {
			// show selected language button
			$fstr = '<li>' . strtoupper ( $_lang ) . '</li>';
		} else {
			// show unselected language button
			
			$fstr = strtoupper ( $_lang );
			
			// just link to the same page again
			$selfuri = $this->uri->ruri_string ();
			$selfuri = str_replace ( '/index', '', $selfuri );
			if ($this->uri->total_rsegments () == 1) {
				$selfuri .= 'index';
			}
			// (the MY_Config::site_url() method appends the new language code)
			
			$fstr = '<li class="active">' . anchor ( site_url ( $_lang . $selfuri ), $fstr, array (
					'title' => ucwords ( $_language ),
					'tabindex' => ($v + $_tabindex_start) 
			) ) . '</li>';
			$v ++;
		}
		$_Output [] = $fstr;
	}
	
	$lang_links = '';
	
	foreach ( $_Output as $outkey => $outvar ) {
		// echo "<li>".$outvar."</li>";
		$lang_links .= $outvar . ' | ';
	}
	
	echo trim ( $lang_links, ' | ' );
}
