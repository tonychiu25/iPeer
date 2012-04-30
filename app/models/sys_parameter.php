<?php
/* SVN FILE: $Id: sys_parameter.php 727 2011-08-30 19:34:58Z john $ */

/**
 * Enter description here ....
 *
 * @filesource
 * @copyright    Copyright (c) 2006, .
 * @link
 * @package
 * @subpackage
 * @since
 * @version      $Revision: 727 $
 * @modifiedby   $LastChangedBy$
 * @lastmodified $Date: 2006/06/20 18:44:19 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * SysParameter
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */
class SysParameter extends AppModel
{
  var $name = 'SysParameter';
  var $actsAs = array('Traceable');
  
	function findParameter ($paramCode='') {
 		//return $this->find("parameter_code = '".$paramCode."' ", array('id', 'parameter_code', 'parameter_value', 'parameter_type'));
            return $this->find('first', array(
                'conditions' => array('parameter_code' => $paramCode),
                'fields' => array('id', 'parameter_code', 'parameter_value', 'parameter_type')
            ));
  }

  function beforeSave()
  {
  	    $this->data[$this->name]['modified'] = date('Y-m-d H:i:s');
      // Ensure the name is not empty
    if (empty($this->data['SysParameter']['id'])) {
    	
    	
    	   $this->errorMessage = "Id is required";
      return false;
    } 
    
         if (!is_numeric($this->data['SysParameter']['id'])) {
    	
    	   $this->errorMessage = "Id must be a number";
      return false;
    }
    
    
        if (empty($this->data['SysParameter']['parameter_code'])) {
    	
    	   $this->errorMessage = "Parameter code is required";
      return false;
    } 
     if (empty($this->data['SysParameter']['parameter_type'])) {
      $this->errorMessage = "Parameter type is required";
      return false;
    }
  
    

      return true;
  }
}

?>
