<?php
/* SVN FILE: $Id: rubrics_controller.php 727 2011-08-30 19:34:58Z john $ */

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
 * @lastmodified $Date: 2006/08/04 18:04:40 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Controller :: Rubrics
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */
class RubricsController extends AppController
{
	var $uses =  array('DepartmentEvaluationTemplate', 'Event', 'Rubric','RubricsLom','RubricsCriteria','RubricsCriteriaComment','Personalize', 'Department');
    var $name = 'Rubrics';
	var $show;
	var $sortBy;
	var $direction;
	var $page;
	var $order;
	var $helpers = array('Html','Ajax','Javascript','Time','Pagination');
	var $Sanitize;
  var $components = array('AjaxList','Output','sysContainer', 'userPersonalize', 'framework');

	function __construct()
	{
		$this->Sanitize = new Sanitize;
		$this->show = empty($_REQUEST['show'])? 'null': $this->Sanitize->paranoid($_REQUEST['show']);
		if ($this->show == 'all') $this->show = 99999999;
		$this->sortBy = empty($_GET['sort'])? 'name': $_GET['sort'];
		$this->direction = empty($_GET['direction'])? 'asc': $this->Sanitize->paranoid($_GET['direction']);
		$this->page = empty($_GET['page'])? '1': $this->Sanitize->paranoid($_GET['page']);
		$this->order = $this->sortBy.' '.strtoupper($this->direction);
    	$this->mine_only = (!empty($_REQUEST['show_my_tool']) && ('on' == $_REQUEST['show_my_tool'] || 1 == $_REQUEST['show_my_tool'])) ? true : false;
 		$this->set('title_for_layout', __('Rubrics', true));
    parent::__construct();
	}
	
  function postProcess($data) {
    // Creates the custom in use column
    if ($data) {
      foreach ($data as $key => $entry) {
        // is it in use?
        $inUse = (0 != $entry['Rubric']['event_count']);

        // Put in the custom column
        $data[$key]['!Custom']['inUse'] = $inUse ? __("Yes", true) : __("No", true);
      }
    }
    // Return the processed data back
    return $data;
  }

  function setUpAjaxList() {
    $myID = $this->Auth->user('id');
    
    // Set up Columns
    $columns = array(
            array("Rubric.id",          "",            "",      "hidden"),
            array("Rubric.name",        __("Name", true),        "auto",  "action", "View Rubric"),
            array("!Custom.inUse",      __("In Use", true),      "4em",   "number"),
            array("Rubric.availability",__("Availability", true), "6em",   "string"),
            array("Rubric.lom_max",     __("LOM", true),         "4em",   "number"),
            array("Rubric.criteria",    __("Criteria", true),    "4em",   "number"),
            array("Rubric.total_marks", __("Total", true),       "4em",   "number"),
            array("Rubric.event_count",   "",       "",        "hidden"),
            array("Rubric.creator_id",         "",            "",      "hidden"),
            array("Rubric.creator",   __("Creator",true),     "8em",   "action", "View Creator"),
            array("Rubric.created",     __("Creation Date", true),"10em", "date"));

    // Just list all and my evaluations for selections
    $userList = array($this->Auth->user('id') => "My Evaluations");

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

    // List only my own or
    // OLD Filter Code
    /*$myID = $this->Auth->user('id');
    $extraFilters = "(Rubric.creator_id=$myID or Rubric.availability='public')";*/

    // New filter Code
    $deptList = User::get('DepartmentList');
    $rubricIdList = $this->DepartmentEvaluationTemplate->getEvalTemplateListByTypeAndDept(3, $deptList);
    switch ($this->Auth->user('RolesUser')) {
      case 1: // superadmin can access all rubric that are public from any dept.
      	$extraFilters = array('availability' => 'public');
      	break;  
      
	  case 2: // Admins and instructors can access their own or dept public rubrics.
	  case 3:
		$publicRubricId = $this->Rubric->find('list', array('conditions' => array('availability' => 'public'),
															'fields' => array('id')));
		$rubricIds = array_intersect($rubricIdList, $publicRubricId);
		if (!empty($rubricIds)) {
		  $extraFilters = "Rubric.id =".array_pop($rubricIds);
		  foreach ($rubricIds as $id) {
		    $extraFilters .= " OR $id";
		  }
		  $extraFilters .= " OR creator_id = $myID";
		} else {
		  $extraFilters = "creator_id = $myID";
		}
		break;
		
	  default:break;
    }
    
    

    // Instructors can only edit their own evaluations
    $restrictions = "";
    if ($this->Auth->user('role') != 'A') {
      $restrictions = array("Rubric.creator_id" => array(
        $myID => true,
        "!default" => false));
    }

    // Set up actions
    $warning = __("Are you sure you want to delete this Rubric permanently?", true);
    $actions = array(
            array(__("View Rubric", true), "", "", "", "view", "Rubric.id"),
            array(__("Edit Rubric", true), "", $restrictions, "", "edit", "Rubric.id"),
            array(__("Copy Rubric", true), "", "", "", "copy", "Rubric.id"),
            array(__("Delete Rubric", true), $warning, $restrictions, "", "delete", "Rubric.id"),
            array(__("View Creator", true), "",    "", "users", "view", "Creator.id"));

    // No recursion in results
    $recursive = 0;

    // Set up the list itself
    $this->AjaxList->setUp($this->Rubric, $columns, $actions,
                           "Rubric.name", "Rubric.name", $joinTables, $extraFilters, $recursive, "postProcess");
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
    if ($layout != '') {
      $this->layout = $layout;
    }

    $deptId = $this->DepartmentEvaluationTemplate->find('first', array('conditions' => array('evaluation_template_type_id' => 3,
    																						 'evaluation_template_id' => $id)));
    $departmentName = $this->Department->getDepartmentById('first', $deptId['DepartmentEvaluationTemplate']['department_id'], 
    																	array('dept'));
    $this->data = $this->Rubric->find('first', array('conditions' => array('id' => $id),
                                                     'contain' => array('RubricsCriteria.RubricsCriteriaComment',
                                                                        'RubricsLom')));
    $this->set('data', $this->data);
    $this->set('dept', $departmentName['Department']['dept']);
    $this->set('readonly', true);
    $this->set('evaluate', false);
    $this->set('action', __('View Rubric', true));
    $this->render('view');
  }
  
  function add($layout='') {
    if ($layout != '') {
      $this->layout = $layout;
    }
    $deptList = $this->Department->getDepartmentById('list', $this->Auth->user('DepartmentList'), array('dept'));
    $this->set('departmentList', $deptList);
    $this->set('editView', 0);
    
    if(!empty($this->data)) {
      $this->set('action', __('Add Rubric (Step 2)', true));
      $this->set('data', $this->data);

      if(isset($this->params['form']['submit'])) {
        if($this->__processForm()) {
          // Save the associated dept with this rubric
		  $deptEvalTemplate['department_id'] = $this->data['Rubric']['selected_dept'];
      	  $deptEvalTemplate['evaluation_template_type_id'] = 3;
      	  $deptEvalTemplate['evaluation_template_id'] = $this->Rubric->id;
      	  if ($this->DepartmentEvaluationTemplate->save($deptEvalTemplate)) { 	
            $this->Session->setFlash(__('The rubric was added successfully.', true));
            $this->redirect('index');
      	  }
        }
      }
    } else {
      $this->set('action', __('Add Rubric', true));
    }
    $this->render('edit');
  }

	function edit($id) {
		if (empty($this->data)) {
      $this->data = $this->Rubric->find('first', array('conditions' => array('id' => $id),
                                                       'contain' => array('RubricsCriteria.RubricsCriteriaComment',
                                                                          'RubricsLom')));
      $this->set('data', $this->data);
		} else {
			//check to see if user has clicked preview
			if(!empty($this->params['form']['preview'])) {
				$this->set('data', $this->data);
			} else {
        if($this->__processForm()) {
          $this->Session->setFlash(__('The rubric evaluation was updated successfully'));
          $this->redirect('index');
        }
      }
    }
    $evalDept = $this->DepartmentEvaluationTemplate->find('first', array('conditions' => array('evaluation_template_type_id' => 3,
    																						   'evaluation_template_id' => $id)));
    $dept = $this->Department->find('first', array('conditions' => array('id' => $evalDept['DepartmentEvaluationTemplate']['department_id']),
    											   'recursive' => 0));
	$this->set('editView', 1);
	$this->set('currentDept', $dept['Department']['dept']);   
    $this->set('action', __('Edit Rubric', true));
    $this->render('edit');
	}

  function __processForm() {
    if (!empty($this->data)) {
      $this->Output->filter($this->data);//always filter
      //Save Data
      
      //$this->log($this->data);
      if ($this->Rubric->saveAllWithCriteriaComment($this->data)) {
        $this->data['Rubric']['id'] = $this->Rubric->id;
        return true;
      } else {
        $this->Session->setFlash($this->Rubric->errorMessage, 'error');
      }
    }
    return false;
  }

  function copy($id) {
    $this->set('data', $this->data);
    $this->set('action', __('Copy Rubric', true));
    $evalDept = $this->DepartmentEvaluationTemplate->find('first', array('conditions' => array('evaluation_template_type_id' => 3,
    																						   'evaluation_template_id' => $id)));
    $dept = $this->Department->find('first', array('conditions' => array('id' => $evalDept['DepartmentEvaluationTemplate']['department_id']),
    											   'recursive' => 0));
	$this->set('editView', 1);
	$this->set('currentDept', $dept['Department']['dept']);   
    $this->render('edit');
  }

	function delete($id) {
    // Deny Deleting evaluations in use:
    if ($this->Rubric->getEventCount($id)) {
      $this->Session->setFlash(__('This evaluation is in use. Please remove all the events assosiated with this evaluation first.', true), 
                               'error');
    } else {
      if ($this->Rubric->delete($id, true) && $this->DepartmentEvaluationTemplate->deleteRowByEvaluation(3, $id)) {
        /*$this->RubricsLom->deleteLOM($id);
          $this->RubricsCriteria->deleteCriterias($id);
          $this->RubricsCriteriaComment->deleteCriteriaComments($id);
        //$this->set('data', $this->Rubric->find('all',null, null, 'id'));
        $this->index();*/
        $this->Session->setFlash(__('The rubric was deleted successfully.', true));
      }
    }
    $this->redirect('index');
	}
	
	function test(){
		$this->log(__("Test Success", true));
	}
	
	function setForm_RubricName($name){
		$this->data['Rubric']['name'] = $name;
		//$this->log($this->data['Rubric']['name']);
	}

/*  function previewRubric()
  {
    $this->layout = 'ajax';
		$this->render('preview');
  }

	function renderRows($row=null, $criteria_weight=null )
	{
		$this->layout = 'ajax';
		$this->render('row');
	}


	function update($attributeCode='',$attributeValue='') {
		if ($attributeCode != '' && $attributeValue != '') //check for empty params
  		$this->params['data'] = $this->Personalize->updateAttribute($this->Auth->user('id'), $attributeCode, $attributeValue);
	}*/
}

?>
