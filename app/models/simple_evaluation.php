<?php
/* SVN FILE: $Id: simple_evaluation.php 727 2011-08-30 19:34:58Z john $ */

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
App::import('Model', 'EvaluationBase');

class SimpleEvaluation extends EvaluationBase
{
  const TEMPLATE_TYPE_ID = 1;
  var $name = 'SimpleEvaluation';
  // use default table
  var $useTable = null;
  var $hasMany = array(
    'Event' =>
      array('className'   => 'Event',
            'conditions'  => array('Event.event_template_type_id' => self::TEMPLATE_TYPE_ID),
            'order'       => '',
            'foreignKey'  => 'template_id',
            'dependent'   => true,
            'exclusive'   => false,
            'finderSql'   => ''
           ),
      );
      
  /**
   * Apply a filter to the simple evaluation ajax list in simpleEvaluation/index; 
   * eg math admins will return a list correspond to only math simple evaluations. 
   */
  function beforeFind($queryData) {
  	$this->User = ClassRegistry::init('User');
  	$user = $this->User->getCurrentLoggedInUser();
	// filter only applicable to non-super admins
  	$deptEvalTemplate = ClassRegistry::init('DepartmentEvaluationTemplate');
  	$accessibleSimpleEvalList = $deptEvalTemplate->getEvalTemplateListByTypeAndDept(1, $user['DepartmentList']);
  	if ($user['RolesUser'] != 1) {
  	  $queryData['conditions']['SimpleEvaluation.id'] = $accessibleSimpleEvalList;
  	}
  	
  	return $queryData;
  }
}

?>
