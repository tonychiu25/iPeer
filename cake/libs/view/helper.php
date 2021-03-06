<?php
/* SVN FILE: $Id: helper.php,v 1.3 2006/06/20 18:46:38 zoeshum Exp $ */

/**
 * Backend for helpers.
 *
 * Internal methods for the Helpers.
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake.libs.view
 * @since			CakePHP v 0.2.9
 * @version			$Revision: 1.3 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006/06/20 18:46:38 $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Backend for helpers.
 *
 * Long description for class
 *
 * @package		cake
 * @subpackage	cake.cake.libs.view
 */
class Helper extends Object {

/**
 * Holds tag templates.
 *
 * @access public
 * @var array
 */
	var $tags = array();
/**
 * Parses tag templates into $this->tags.
 *
 * @return void
 */
	function loadConfig() {
		$config = fileExistsInPath(CAKE . 'config' . DS . 'tags.ini.php');
		$cakeConfig = $this->readConfigFile($config);

		if (file_exists(APP . 'config' . DS . 'tags.ini.php')) {
			$appConfig = $this->readConfigFile(APP . 'config' . DS . 'tags.ini.php');
			$cakeConfig = am($cakeConfig, $appConfig);
		}
		return $cakeConfig;
	}
/**
 * Decides whether to output or return a string.
 *
 * Based on AUTO_OUTPUT and $return's value, this method decides whether to
 * output a string, or return it.
 *
 * @param  string  $str	String to be output or returned.
 * @param  boolean $return Whether this method should return a value or
 *                         output it. This overrides AUTO_OUTPUT.
 * @return mixed	Either string or boolean value, depends on AUTO_OUTPUT
 *                and $return.
 */
	function output($str, $return = false) {
		if (AUTO_OUTPUT && $return === false) {
			echo $str;
			return true;
		} else {
			return $str;
		}
	}
/**
 * Assigns values to tag templates.
 *
 * Finds a tag template by $keyName, and replaces $values's keys with
 * $values's keys.
 *
 * @param  string $keyName Name of the key in the tag array.
 * @param  array  $values  Values to be inserted into tag.
 * @return string Tag with inserted values.
 */
	function assign($keyName, $values) {
		return str_replace('%%' . array_keys($values) . '%%', array_values($values), $this->tags[$keyName]);
	}
/**
 * Returns an array of settings in given INI file.
 *
 * @param string $fileName
 * @return array
 */
	function readConfigFile($fileName) {
		$fileLineArray = file($fileName);

		foreach($fileLineArray as $fileLine) {
			$dataLine = trim($fileLine);
			$firstChar = substr($dataLine, 0, 1);

			if ($firstChar != ';' && $dataLine != '') {
				if ($firstChar == '[' && substr($dataLine, -1, 1) == ']') {
					// [section block] we might use this later do not know for sure
					// this could be used to add a key with the section block name
					// but it adds another array level
				} else {
					$delimiter = strpos($dataLine, '=');

					if ($delimiter > 0) {
						$key = strtolower(trim(substr($dataLine, 0, $delimiter)));
						$value = trim(substr($dataLine, $delimiter + 1));

						if (substr($value, 0, 1) == '"' && substr($value, -1) == '"') {
							$value = substr($value, 1, -1);
						}

						$iniSetting[$key] = stripcslashes($value);
					} else {
						$iniSetting[strtolower(trim($dataLine))] = '';
					}
				}
			} else {
			}
		}

		return $iniSetting;
	}
/**
 * After render callback.  Overridden in subclasses.
 *
 * @return void
 */
	function afterRender() {
	}
}

?>