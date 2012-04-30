<?php
/* SVN FILE: $Id: simpleevaluations_controller.php 727 2011-08-30 19:34:58Z john $ */

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
 * @lastmodified $Date: 2006/09/13 18:19:23 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Controller :: Simpleevaluations
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */
App::import('Lib', 'neat_string');

class SimpleevaluationsController extends AppController
{
    var $name = 'SimpleEvaluations';
	var $show;
	var $sortBy;
	var $direction;
	var $page;
	var $order;
	var $helpers = array('Html','Ajax','Javascript','Time','Pagination');
	var $NeatString;
	var $Sanitize;
	var $uses = array('SimpleEvaluation', 'Event', 'Personalize', 'User', 'DepartmentUser', 'DepartmentCourse', 'Department', 'DepartmentEvaluationTemplate','Course');
	var $components = array('AjaxList');

//	var $components = array('EvaluationSimpleHelper');

	function __construct() {
		$this->Sanitize = new Sanitize;
		$this->NeatString = new NeatString;
		$this->show = empty($_REQUEST['show'])? 'null': $this->Sanitize->paranoid($_REQUEST['show']);
		if ($this->show == 'all') $this->show = 99999999;
		$this->sortBy = empty($_GET['sort'])? 'name': $_GET['sort'];
		$this->direction = empty($_GET['direction'])? 'asc': $this->Sanitize->paranoid($_GET['direction']);
		$this->page = empty($_GET['page'])? '1': $this->Sanitize->paranoid($_GET['page']);
		$this->order = $this->sortBy.' '.strtoupper($this->direction);
 		$this->set('title_for_layout', __('Simple Evaluations', true));
        $this->mine_only = (!empty($_REQUEST['show_my_tool']) && ('on' == $_REQUEST['show_my_tool'] || 1 == $_REQUEST['show_my_tool'])) ? true : false;

		parent::__construct();
	}

  function postProcess($data) {

    // Creates the custom in use column
    if ($data) {
      foreach ($data as $key => $entry) {
        // is it in use?
        $inUse = (0 != $entry['SimpleEvaluation']['event_count']);

        // Put in the custom column
        $data[$key]['!Custom']['inUse'] = $inUse ? "Yes" : "No";
      }
    }
    // Return the processed data back
    return $data;
  }

  function setUpAjaxList() {
    $myID = $this->Auth->user('id');

    // Set up Columns
    $columns = array(
            array("SimpleEvaluation.id",   "",       "",        "hidden"),
            array("SimpleEvaluation.event_count",   "",       "",        "hidden"),
            array("SimpleEvaluation.name", __("Name", true),   "12em",    "action",   "View Evaluation"),
            array("SimpleEvaluation.description", __("Description", true),"auto",  "action", "View Evaluation"),
            array("!Custom.inUse", __("In Use", true),          "4em",    "number"),
            array("SimpleEvaluation.point_per_member", __("Points/Member", true), "10em", "number"),
            array("SimpleEvaluation.creator_id",           "",            "",     "hidden"),
            array("SimpleEvaluation.creator",     __("Creator", true),  "10em", "action", "View Creator"),
            array("SimpleEvaluation.created", __("Creation Date", true), "10em", "date"));

    $userList = array($myID => "My Evaluations");

    // Join with Users
    $jointTableCreator =
      array("id"         => "Creator.id",
            "localKey"   => "creator_id",
            "description" => __("Evaluations to show:", true),
            "default" => $myID,
            "list" => $userList,
            "joinTable"  => "users",
            "joinModel"  => "Creator");
    // put all the joins together
    $joinTables = array($jointTableCreator);
  
    $extraFilters = "SimpleEvaluation.id";
    // Instructors can only edit their own evaluations
    $restrictions = "";
    if ($this->Auth->user('role') != 'A') {
      $restrictions = array(
                            "SimpleEvaluation.creator_id" => array(
                                                  $myID => true,
                                                  "!default" => false));
    }

    // Set up actions
    $warning = __("Are you sure you want to delete this evaluation permanently?", true);
    $actions = array(
                     array(__("View Evaluation", true), "", "", "", "view", "SimpleEvaluation.id"),
                     array(__("Edit Evaluation", true), "", $restrictions, "", "edit", "SimpleEvaluation.id"),
                     array(__("Copy Evaluation", true), "", "", "", "copy", "SimpleEvaluation.id"),
                     array(__("Delete Evaluation", true), $warning, $restrictions, "", "delete", "SimpleEvaluation.id"),
                     array(__("View Creator", true), "",    "", "users", "view", "SimpleEvaluation.creator_id"));

    // No recursion in results
    $recursive = 0;

    // Set up the list itself
    $this->AjaxList->setUp($this->SimpleEvaluation, $columns, $actions,
                           "SimpleEvaluation.name", "SimpleEvaluation.name", $joinTables, $extraFilters, $recursive, "postProcess");
  }
  
  /**
   * Checks whether userId is allowed to access simple evaluation with $simpleEvalId;
   * If not set error message set to $errmsg and redirect to $url.
   * 
   * @param $errmsg : error message displayed if not accessible.
   * @param $url : redirect destination if not accessible.
   */
  function _checkSimpleEvalAccessible($userId, $simpleEvalId, $errmsg = "", $url = "") {
  	$accessibleSimpleEvalList = $this->DepartmentEvaluationTemplate->getEvalTemplateListByTypeAndDept(1, $this->Auth->user('DepartmentList'));
  	if (!in_array($simpleEvalId, $accessibleSimpleEvalList)) {
      $this->Session->setFlash($errmsg);
      $this->redirect($url);
  	}
  }

  function index() {
    // Set up the basic static ajax list variables
    $this->setUpAjaxList();
    // Set the display list
    $this->set('paramsForList', $this->AjaxList->getParamsForList());
  }

  function ajaxList() {
    // Set up the list
    $this->setUpAjaxList();
    // Process the request for data
    $this->AjaxList->asyncGet();
  }

  function view($id, $layout='') {
  	/* Non-super admins must run an accessible check before viewing evaluation */
  	if ($this->Auth->user('RolesUser') != 1) {
  	$this->_checkSimpleEvalAccessible($this->Auth->user('id'), $id, 
  		"Invalid permission to view this simple evaluation.", "/simpleevaluations/index");
  	}
  	
  	if ($layout != '') {
	  $this->layout = $layout;
	  if ($layout == 'pop_up') $this->set('popUp', 1);
	}
	  $data = $this->SimpleEvaluation->read(null, $id);
		$this->set('data', $data);
  }

  function add($layout='') {
    if ($layout != '') {
      $this->layout = $layout;
    }

    if(!empty($this->data)) {
      if($this->__processForm()) {
        $this->Session->setFlash(__("The evaluation was added successfully.", true));
        $this->redirect('index');
      } else {
        $this->Session->setFlash($this->SimpleEvaluation->errorMessage);
        $this->set('data', $this->data);
      }
    }
    
	$deptList = $this->Department->getDepartmentById('list', $this->Auth->user('DepartmentList'), array('dept'));
	$this->set('editView', 0);
	$this->set('departmentList', $deptList);
    $this->render('edit');
  }

  function __processForm() {
    if (!empty($this->data)) {
      $this->Output->filter($this->data);//always filter
      //Save Data
      if ($this->SimpleEvaluation->save($this->data)) {
      	// Save associated dept to the simple eval template.
      	$deptEvalTemplate['department_id'] = $this->data['SimpleEvaluation']['selected_dept'];
      	$deptEvalTemplate['evaluation_template_type_id'] = 1;
      	$deptEvalTemplate['evaluation_template_id'] = $this->SimpleEvaluation->id;
        $this->data['SimpleEvaluation']['id'] = $this->SimpleEvaluation->id;
        if($this->DepartmentEvaluationTemplate->save($deptEvalTemplate)) 
          return true;
      }
    }

    return false;
  }

  function edit($id) {
    if(!is_numeric($id)) {
      $this->Session->setFlash(__('Invalid ID.', true));
      $this->redirect('index');
    }
    /*Non-super admins must run a permission check before edit*/
    if ($this->Auth->user('RolesUser') != 1) {
    $this->_checkSimpleEvalAccessible($this->Auth->user('id'), $id,
     "Invalid permission to edit simple evaluation.", "/simpleevaluations/index");
    }
    
    $evalDept = $this->DepartmentEvaluationTemplate->find('first', array('conditions' => array('evaluation_template_type_id' => 1,
    																						   'evaluation_template_id' => $id)));
    $dept = $this->Department->find('first', array('conditions' => array('id' => $evalDept['DepartmentEvaluationTemplate']['department_id']),
    											   'recursive' => 0));
    
    $this->data['SimpleEvaluation']['id'] = $id;
	$this->set('editView', 1);
	$this->set('currentDept', $dept['Department']['dept']);
	
		if ($this->__processForm()) {
      $this->Session->setFlash(__('The simple evaluation was updated successfully.', true));
      $this->redirect('index');
    } else {
			$this->data = $this->SimpleEvaluation->find('first', array('conditions' => array('id' => $id),
                                                                 'contain' => false,
																 'callbacks' => false));

			$this->Output->filter($this->data);//always filter
			//converting nl2br back so it looks better
			$this->Output->br2nl($this->data);
		} 
	}

  function copy($id) {
  	/* Non-super admins must run a permission check prior to copy */
  	if ($this->Auth->user('RolesUser') != 1) {
    $this->_checkSimpleEvalAccessible($this->Auth->user('id'), $id,
     "Invalid permission to copy simple evaluation.", "/simpleevaluations/index");
  	}
    $this->render = false;
    $this->data = $this->SimpleEvaluation->read(null, $id);
    $this->data['SimpleEvaluation']['id'] = null;
    $this->data['SimpleEvaluation']['name'] = 'Copy of '.$this->data['SimpleEvaluation']['name'];
    //converting nl2br back so it looks better
    $this->Output->br2nl($this->data);
    $this->render('edit');
	}

  function delete($id) {
    // Non-super admins must run a access level check prior to delete 
	if ($this->Auth->user('RolesUser') != 1) {
    $this->_checkSimpleEvalAccessible($this->Auth->user('id'), $id, 
    	"Invalid deletion on inaccessible evaluation.", "/simpleevaluations/index");
	}
	
    // Deny Deleting evaluations in use:
    if ($this->SimpleEvaluation->getEventCount($id)) {
      $message = __("This evaluation is now in use, and can NOT be deleted.<br />", true);
      $message.= __("Please remove all the events assosiated with this evaluation first.", true);
      $this->Session->setFlash($message);
      $this->redirect('index');
    }
    
    // Delete the SimpleEvaluation and its associated row in "DepartmentEvaluationTemplate" table.
    if ($this->SimpleEvaluation->delete($id) && $this->DepartmentEvaluationTemplate->deleteRowByEvaluation(1, $id)) {
	  $this->Session->setFlash(__('The evaluation was deleted successfully.', true));
	} else {
      $this->Session->setFlash(__('Evaluation delete failed.', true));
	}
    $this->redirect('index');
  }
}

?>