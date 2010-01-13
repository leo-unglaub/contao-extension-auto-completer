<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005-2009 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  leo@leo-unglaub.net, CyberSpectrum 2009 
 * @author     Leo Unglaub <leo@leo-unglaub.net>, Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    auto_completer 
 * @license    GNU/GPL 
 * @filesource
 */


 
	// push the AC-Module into the miscellaneous array
	array_insert(
		$GLOBALS['FE_MOD']['miscellaneous'], 
		1, 
		array (
			'auto_completer' => 'ac_auto_completer'
		)
	);


	/**
	 * Register some Hooks
	 *  - register the AJAX-Hook
	 *  - register the parseFrendendTemplate-Hook to add the .js-Script into the header 
	 * 	  if an search module is on the page
	 */
	$GLOBALS['TL_HOOKS']['dispatchAjax'][] = array('ac_auto_completer_response', 'ac_response');
	$GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('ac_auto_completer_searchform', 'hook_search');
	
	/**
	 * Define our own hook to allow other modules to use the autocompletion feature
	 */
	if(!isset($GLOBALS['TL_CONFIG']['auto_completer'])) {
        $GLOBALS['TL_CONFIG']['auto_completer']=array();
	}
		
    $GLOBALS['TL_CONFIG']['auto_completer']['searchindex']=array
    (
    	'hooklookup' => array('ac_search_index', 'keywords'),
    	'hookconfig' => array('ac_search_index', 'config'),
    );
        
        
        
	/**
     * Documentation
     * 
     * This is a example HOOK registration in a modules config.php File
     * 
     */
     
    // $GLOBALS['TL_CONFIG']['auto_completer']['mymodulename']=array
    // (
    // 'hooklookup' => array('mymodulename', 'keywords'),
    // 'hookconfig' => array('mymodulename', 'config'),
    // );
	
    


?>