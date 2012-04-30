<?php 
App::import('Lib', 'Toolkit');

class AppModel extends Model {
  var $errorMessage = array();
  var $insertedIds = array();

  function afterSave($created) {
    if($created) {
      $this->insertedIds[] = $this->getInsertID();
    }

    return true;
  }

  function __construct($id = false, $table = null, $ds = null) {
    parent::__construct($id, $table, $ds);
  }

  function save($data = null, $validate = true, $fieldList = array()) {
    //clear modified field value before each save
    if (isset($this->data) && isset($this->data[$this->alias]))
      unset($this->data[$this->alias]['modified']);
    if (isset($data) && isset($data[$this->alias]))
      unset($data[$this->alias]['modified']);

    return parent::save($data, $validate, $fieldList);
  }


  /**
    * showErrors itterates through the errors array
    * and returns a concatinated string of errors sepearated by
    * the $sep
    *
    * @param string $sep A seperated defaults to <br />
    * @return string
    * @access public
    */
  function showErrors($sep = "<br />"){
    $retval = "";
    foreach($this->errorMessage as $key => $error){
      if(!is_numeric($key)) {
        $error = $key.': '.$error;
      }
      $retval .= "$error $sep";
    }

    return $retval;
  }
  
  /** TODO : clean up model imports
   * Returns a list of accessible course_id based on the current user's
   * academic department affiliation... eg Math admins will return a list
   * of ids corresponding to all the math courses.
   */
  function getFilterCourseList($userId) {
  	$userModel = Classregistry::init('User');
  	$DepartmentUser = Classregistry::init('DepartmentUser');
  	$Department = Classregistry::init('Department');
  	$DepartmentCourse = Classregistry::init('DepartmentCourse');
  	$user = $userModel->getCurrentLoggedInUser();
	$user = $userModel->find('first', array('conditions' => array('User.id' => $userId)));
    $userRole = $user['Role'][0]['id'];
    $extraFilters = array();
    $courseList = array();
    
    switch($userRole) { 
      case '2' :  // Dept/Faculty admin only query courses in their dept/faculty
      	$departmentList = $DepartmentUser->getDepartmentListByUser($user['User']['id']);
      	foreach($departmentList as $deptId) {
      	  $children = $Department->getChildren($deptId);
      	  if(!empty($children)) {
      	  	// For faculty, we must query all assoicated depts/chidren
      	  	foreach ($children as $subDept) {
      	  	  $courseListTemp = $DepartmentCourse->getDeptCourseList($subDept['Department']['id']);
      	  	  $courseList = array_merge($courseList, $courseListTemp);
      	  	}
      	  } else {
      	  	// For departments, no sub-dept/childrens attached
		    $courseList = $Department->getDeptCourseList($deptId);
  		    foreach($courseList as $courseId) {
  		      //TODO set filter for department adminds	
  		    }
  		    
      	  }
      	  $queryData['conditions']['Course.id'] = $courseList;
      	}
      	$extraFilters = $courseList;
      	break;
      	
      default : break;
    }
  	
    
  	return $extraFilters;
  }
  
}
