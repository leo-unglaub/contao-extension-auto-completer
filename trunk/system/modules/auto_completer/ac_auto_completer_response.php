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

// temporarily we want to redirect all errors to an exception to prevent TL from dying as we want to 
// be able to recover from something that might be broken in one of our hooks.
function ac_auto_completer_exception_error_handler($errno, $errstr, $errfile, $errline ) {
	if ($errno != E_NOTICE)
		throw new ErrorException($errstr . " No: " . $errno  . " File: " . $errfile . " Line: " . $errline, 0, $errno, $errfile, $errline);
}

/**
 * Class ac_auto_completer_response - returns the JSON reply to the FE on ajax requests.
 *
 * @copyright  leo@leo-unglaub.net, CyberSpectrum 2009 
 * @author     Leo Unglaub <leo@leo-unglaub.net>, Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    Controller
 */
class ac_auto_completer_response extends Frontend
{
	/* 
	 * called from ajax hook - sets Content-Type header to application/json
	 * @return JSON-array
	 */
	public function ac_response()
	{
		// check if ajax-request is from the auto completer
		if ($this->Input->get('req_script') == 'ac_auto_completer')
		{
			// are we hooked?
			if($this->Input->get('hook'))
			{
				$reply=$this->ac_response_hooked_keywords();
			} else {
				// non hooked, fallback to table tl_search_index matching.
				$reply=$this->ac_response_simple_keywords();
			}
			// convert result into json and return
			//header('Content-type: application/json');
			return json_encode($reply);
		}
	}

	/* 
	 * called from ac_response() - calls the auto completer hook that is requested.
	 * @return array
	 */
	protected function ac_response_hooked_keywords()
	{
		try
		{
			set_error_handler("ac_auto_completer_exception_error_handler");
			$hook=$GLOBALS['TL_CONFIG']['auto_completer'][$this->Input->get('hook')]['hooklookup'];
			if(is_array($hook))
			{
				// try to load it.
				$this->import($hook[0]);
				$reply = $this->$hook[0]->$hook[1]();
			} else
				$reply = array('-internal error-');
		} catch(Exception $e) {
			// class could not be imported, return empty array instead of 
			// suffering a slow and horribly death as no match is better than
			// an exception on the Frontend.
			$reply = array($e->getMessage() . '-internal error-' . $hook[1]);
		}
		restore_error_handler();
		if(!is_array($reply))
			$reply = array('-internal error-');
		return $reply;
	}
	
	/* 
	 * called from ac_response() - applies search on tl_search_index.
	 * @return array
	 */
	protected function ac_response_simple_keywords() {
		$this->import('Database');	
		
		// add support for low case keywords
		// maybe better solved by backend parameter
		$ac_search = strtolower($this->Input->post('value'));
		$ac_search_module = $this->Input->get('searchmodid');
		
		$sql = 'SELECT DISTINCT word FROM tl_search_index WHERE word LIKE ?';
		// Check if there is a module id passed, if so, we want to collect the badwords from there.
		if($ac_search_module)
		{
			// we need the module blacklist now.
			$blacklist=$this->Database->prepare("SELECT auto_complete_ignore_words FROM tl_module WHERE id=?")
									  ->limit(1)
									  ->execute($ac_search_module);
			// if we got a list, add it to the sql.
			if($blacklist->numRows) {
				if (!empty($GLOBALS['TL_CONFIG']['auto_completer_global_ignore_words'])) {
					$ac_global_ignore_list = $GLOBALS['TL_CONFIG']['auto_completer_global_ignore_words'] . (strlen($blacklist->auto_complete_ignore_words) ? ',' : '');
				} else {
					$ac_global_ignore_list = '';
				}
				$sql .= ' AND NOT FIND_IN_SET(word, "'.$ac_global_ignore_list . $blacklist->auto_complete_ignore_words . '")';
			}
		}
		$sql .= ' ORDER BY relevance DESC';
		// execute the search-query
		$ac_values = $this->Database->prepare($sql)
									->execute("$ac_search%");
		return $ac_values->fetchEach('word');
	}
}

?>