<?php
/* SVN FILE: $Id: session.php,v 1.3 2006/06/20 18:46:33 zoeshum Exp $ */

/**
 * Short description for file.
 *
 * Long description for file
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
 * @subpackage		cake.cake.libs.controller.components
 * @since			CakePHP v 0.10.0.1232
 * @version			$Revision: 1.3 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006/06/20 18:46:33 $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller.components
 *
 */
class SessionComponent extends Object{

/**
 * Enter description here...
 *
 */
	function __construct($base = null) {
		$this->CakeSession = new CakeSession($base);
		parent::__construct();
	}
/**
 * Startup method.  Copies controller data locally for rendering flash messages.
 *
 */
	function startup(&$controller) {
		$this->base = $controller->base;
		$this->webroot = $controller->webroot;
		$this->here = $controller->here;
		$this->params = $controller->params;
		$this->action = $controller->action;
		$this->data = $controller->data;
		$this->plugin = $controller->plugin;
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->write('Controller.sessKey', 'session value');
 *
 * @param unknown_type $name
 * @param unknown_type $value
 * @return unknown
 */
	function write($name, $value) {
		return $this->CakeSession->writeSessionVar($name, $value);
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->read('Controller.sessKey');
 * Calling the method without a param will return all session vars
 *
 * @param unknown_type $name
 * @return unknown
 */
	function read($name = null) {
		return $this->CakeSession->readSessionVar($name);
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->del('Controller.sessKey');
 *
 * @param unknown_type $name
 * @return unknown
 */
	function del($name) {
		return $this->CakeSession->delSessionVar($name);
	}
/**
 * Enter description here...
 * @param unknown_type $name
 * @return unknown
 */
	function delete($name) {
		return $this->del($name);
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->check('Controller.sessKey');
 *
 * @param unknown_type $name
 * @return unknown
 */
	function check($name) {
		return $this->CakeSession->checkSessionVar($name);
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->error();
 *
 * @return string Last session error
 */
	function error() {
		return $this->CakeSession->getLastError();
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->setFlash('This has been saved');
 *
 * @param string $flashMessage Message to be flashed
 * @param string $layout Layout to wrap flash message in
 * @param array $params Parameters to be sent to layout as view variables
 * @param string $key Message key, default is 'flash'
 * @return string Last session error
 */
	function setFlash($flashMessage, $layout = 'default', $params = array(), $key = 'flash') {
		if ($layout == 'default') {
			$out = '<div id="' . $key . 'Message" class="message">' . $flashMessage . '</div>';
		} else if($layout == '' || $layout == null) {
			$out = $flashMessage;
		} else {
			$ctrl = null;
			$view = new View($ctrl);
			$view->base			= $this->base;
			$view->webroot		= $this->webroot;
			$view->here			= $this->here;
			$view->params		= $this->params;
			$view->action		= $this->action;
			$view->data			= $this->data;
			$view->plugin		= $this->plugin;
			$view->helpers		= array('Html');
			$view->layout		= $layout;
			$view->pageTitle	= '';
			$view->_viewVars	= $params;
			$out = $view->renderLayout($flashMessage);
		}
		$this->write('Message.' . $key, $out);
	}
/**
 * Use like this. $this->Session->flash();
 *
 * @param string $key Optional message key
 * @return null
 */
	function flash($key = 'flash') {
		if ($this->check('Message.' . $key)) {
			e($this->read('Message.' . $key));
			$this->del('Message.' . $key);
		} else {
			return false;
		}
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->renew();
 * This will renew sessions
 *
 * @return boolean
 */
	function renew() {
		$this->CakeSession->renew();
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->valid();
 * This will return true if session is valid
 * false if session is invalid
 *
 * @return boolean
 */
	function valid() {
		return $this->CakeSession->isValid();
	}
/**
 * Enter description here...
 *
 * Use like this. $this->Session->destroy();
 * Used to destroy Sessions
 *
 */
	function destroy() {
		$this->CakeSession->destroyInvalid();
	}
}

?>