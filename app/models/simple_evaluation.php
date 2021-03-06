<?php
/* SVN FILE: $Id$ */

/**
 * Enter description here ....
 *
 * @filesource
 * @copyright    Copyright (c) 2006, .
 * @link
 * @package
 * @subpackage
 * @since
 * @version      $Revision$
 * @modifiedby   $LastChangedBy$
 * @lastmodified $Date: 2006/09/25 17:31:54 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * SimpleEvaluation
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */
class SimpleEvaluation extends AppModel
{
  var $name = 'SimpleEvaluation';
  var $validate = array(
      'name' => VALID_NOT_EMPTY,
      'point_per_member' => VALID_NUMBER
  );

/*  var $hasMany = array(
                       'EvaluationSimple' => array(
                        'className' => 'EvaluationSimple',
                        'dependent' => true
                       )
  );*/
	//Overwriting Function - will be called before save operation
	function beforeSave(){

	    // Ensure the name is not empty
        if (empty($this->data[$this->name]['name'])) {
            $this->errorMessage = "Please enter a new name for this " . $this->name . ".";
            return false;
        }

        // Remove any signle quotes in the name, so that custom SQL queries are not confused.
        $this->data[$this->name]['name'] =
            str_replace("'", "", $this->data[$this->name]['name']);


        $allowSave = true;
        if (empty($this->data[$this->name]['id'])) {
            //check the duplicate username
            $allowSave = $this->__checkDuplicateTitle();
        }
        return $allowSave;
	}

  //Validation check on duplication of username
	function __checkDuplicateTitle() {
	  $duplicate = false;
    $field = 'name';
    $value = $this->data[$this->name]['name'];
    if ($result = $this->find($field . ' = "' . $value.'"', $field)){
      $duplicate = true;
     }

    if ($duplicate == true) {
      $this->errorMessage='Duplicate name found.  Please change the name of this Simple Evaluation';
      return false;
    }
    else {
      return true;
    }
	}


    /**
     * Returns the evaluations made by this user, and any other public ones.
     */
    function getBelongingOrPublic($userID) {
        return is_numeric($userID) ?
            $this->query("SELECT * FROM simple_evaluations as SimpleEvaluation where SimpleEvaluation.creator_id=" . $userID)
            : false;
    }
}

?>
