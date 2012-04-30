<?php
/* SVN FILE: $Id: courses_controller.php 734 2011-10-03 20:47:56Z compass $ */
/**
 * Enter description here ....
 *
 * @filesource
 * @copyright    Copyright (c) 2006, .
 * @link
 * @package
 * @subpackage
 * @since
 * @version      $Revision: 734 $
 * @modifiedby   $LastChangedBy$
 * @lastmodified $Date: 2006/07/20 18:10:32 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Controller :: Courses
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */

class CoursesController extends AppController
{
	var $name = 'Courses';
	var $uses =  array('GroupEvent', 'Course', 'Personalize', 'UserCourse', 'UserEnrol', 'Group', 'Event', 'User', 'DepartmentCourse', 'DepartmentUser', 'Department', 'DepartmentEvaluationTemplate');
	var $show;
	var $sortBy;
	var $direction;
	var $page;
	var $order;
	var $Sanitize;
	var $helpers = array('Html','Ajax', 'excel', 'Javascript','Time','Pagination', 'Js' => array('Prototype'));
	var $components = array('AjaxList', 'AccessControl');
	
  function __construct() {
		$this->Sanitize = new Sanitize;
		$this->show = empty($_GET['show'])? 'null': $this->Sanitize->paranoid($_GET['show']);
		if ($this->show == 'all') $this->show = 99999999;
		$this->sortBy = empty($_GET['sort'])? 'created': $this->Sanitize->paranoid($_GET['sort']);
		$this->direction = empty($_GET['direction'])? 'desc': $this->Sanitize->paranoid($_GET['direction']);
		$this->page = empty($_GET['page'])? '1': $this->Sanitize->paranoid($_GET['page']);
		$this->order = $this->sortBy.' '.strtoupper($this->direction);
 		$this->set('title_for_layout', 'Courses');
		parent::__construct();
	} 
	
  function setUpAjaxList() {
    // Set up Columns
    $columns = array(
        array("Course.id",            "",            "",      "hidden"),
        array("Course.homepage",      __("Web", true),         "4em",  "link",   "home.gif"),
        array("Course.course",        __("Course", true),      "15em",  "action", "Course Home"),
        array("Course.title",         __("Title", true),       "auto", "action", "Course Home"),
        array("Course.creator_id",           "",            "",     "hidden"),
        array("Course.record_status", __("Status", true),      "5em",  "map",     array("A" => __("Active", true), "I" => __("Inactive",true))),
        array("Course.creator",     __("Created by", true),  "10em", "action", "View Creator"));
        //array("Instructor.id",        "",            "",     "hidden"));


    // put all the joins together
    $joinTables = array();

    // For instructors: only list their own courses; for admin use all course
    $extraFilters = $this->Auth->user('role') == 'A' ?
     	    array('Course.id' => $this->Course->find('list', array('fields' => array('Course.id'), 
     	    													   'callbacks' => false))) :
            array('Course.id' => $this->UserCourse->find('list', array('conditions' => array('user_id' => $this->Auth->user('id')),
            														   'fields' => array('course_id'),
            														   'callbacks' => false)));
            
    // Set up actions
    $warning = __("Are you sure you want to delete this course permanently?", true);

    $actions = array(
        array(__("Course Home", true), "", "", "", "home", "Course.id"),
        array(__("View Record", true), "", "", "", "view", "Course.id"),
        array(__("Edit Course", true), "", "", "", "edit", "Course.id"),
        array(__("Delete Course", true), $warning, "", "", "delete", "Course.id"),
        array(__("View Creator", true), "",    "", "users", "view", "Course.creator_id"),
        array(__("View Instructor", true), "", "", "users", "view", "Instructor.id"));

    $recursive = 0;

    $this->AjaxList->setUp($this->Course, $columns, $actions,
        'Course.course', 'Course.course', $joinTables, $extraFilters, $recursive);
  }
  
  function daysLate($event, $submissionDate) {
   $days = 0; 
   $dueDate = $this->Event->find('first', array('conditions' => array('Event.id' => $event), 'fields' => array('Event.due_date')));
   $dueDate = new DateTime($dueDate['Event']['due_date']); 
   $submissionDate = new DateTime($submissionDate);
   $dateDiff = $dueDate->diff($submissionDate);
   if(!$dateDiff->format('%r')){
   $days = $dateDiff->format('%d');
   if($dateDiff->format('%i') || $dateDiff->format('%s')){$days++;}}
   return $days;  
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
  
  /**
   * Check if a user with ($userId) is allowed to access a course with ($courseId).
   * Non accessible users with be redirected to ($url) with an error message($errmsg) 
   * displayed.
   * 
   * @param string $errmsg : error message
   * @param string $url : redirect url
   */
  function _isCourseAccessible($courseId, $errmsg = "", $url = "") {
    $accessibleCourseId = User::getMyDeptCoursesList();
  	if (!in_array($courseId, $accessibleCourseId)) {
	  $this->Session->setFlash($errmsg);
	  $this->redirect($url);
	}
  }

  function view($id) {
  	if ($this->Auth->user('RolesUser') != 1) {
  	  $this->_isCourseAccessible( $id, "Invalid permission to view this course.", "/courses/index");
  	}
    $this->set('data', $this->Course->read(null, $id));
  }
  
  function home($id) { 
  	// Restrict-cross department/course access for admins and instructors respectivey
  	if ($this->Auth->user('RolesUser') != 1) {
	  $this->_isCourseAccessible( $id, "Invalid permission to access this course.", "/courses/index");
  	}
  	$course = $this->Course->find('first', array('conditions' => array('id' => $id),
  												 'callbacks' => false));
    $this->set('data', $course);
    $this->set('course_id', $id);
    $this->set('export_eval_link', 'courses/export/'.$id);
    
    $students = $course['Course']['student_count'];
    $this->set('studentCount', $students);

    $this->set('groupCount', count($course['Group']));
    $this->set('eventCount', count($course['Event']));

    $this->set('title_for_layout', $this->sysContainer->getCourseName($id));

    //Setup the Personalization list
    if (empty($this->userPersonalize->personalizeList)) {
      $personalizeData = $this->Personalize->find('all', array('conditions' => array('user_id' => $this->Auth->user('id'))));
      $this->userPersonalize->setPersonalizeList($personalizeData);
    }
    $this->set('userPersonalize', $this->userPersonalize);

    //Setup the courseId to session
    $this->Session->write('ipeerSession.courseId', $id);
    $this->render('home');
  }

  function add() {
    if (!empty($this->data)) {
      $formData = $this->data;
      if ($this->data = $this->Course->save($this->data)) {
        // add current user to the new course
        $this->Course->addInstructor($this->Course->id, $this->Auth->user('id'));
        $this->Course->addDepartment($this->Course->id, $formData['Course']['selected_dept']);
        $this->Session->setFlash('The course has been created.');
        //$this->sysContainer->setMyCourseList($myCourses);
        $this->redirect(array('action' => 'edit', $this->Course->id));
      }
      else{
        $this->Session->setFlash('Cannot add a course. Check errors below');
      }
    }
    
    $departments = $this->Department->getDepartmentById('list', $this->Auth->user('DepartmentList'), 'dept');
    $this->set('editView', 0);
    $this->set('departmentList', $departments);
    $this->set('course_id', 0);
    $this->set('data', $this->data);
    $this->render('edit');
  }

  function edit($id) {
    if(!is_numeric($id)) {
      $this->Session->setFlash(__('Invalid course ID.', true));
      $this->redirect('index');
    }
    /* Non-super admins must run a course accessible check before editing a course */
	if ($this->Auth->user('RolesUser') != 1) {
      $this->_isCourseAccessible( $id, "Invalid permission to edit this course.", "/courses/index");
	}
    
    $this->data['Course']['id'] = $id;
	if (!empty($this->data) && $this->Course->save($this->data)) {
      $this->Session->setFlash(__('The course was updated successfully.', true));
      $this->redirect('index');
    } else {
      $this->data = $this->Course->find('first', array('conditions' => array('id' => $id)));
      $departmentList = $this->Department->getDepartmentList();
      $this->set('editView', 1);
      $this->set('currentDepartments' , $this->data['Dept']);
      $this->set('departmentList', $departmentList);
      $this->set('instructors_rest', $this->Course->getAllInstructors('list', 
      							array('excludes' => $this->data['Instructor'])));
      $this->set('data', $this->data);
      $this->set('course_id', $this->data['Course']['id']);
      //$this->set('errmsg', $this->Course->errorMessage);
	}
  }

  function delete($id) {
	if ($this->Course->delete($id)) {
	  //Delete all corresponding data start here
	  $course = $this->Course->findById($id);

	  //Instructors: Instructor record will remain in database, but the join table records will be deleted
	  $instructors = $course['UserCourse'];
	  if (!empty($instructors)) {
	    foreach ($instructors as $index -> $value) {
	      $this->UserCourse-del($value['id']);
	    }
	  }

	  //Students: Students who enrolled in other courses will not be deleted;
	  //          Else, Student records will be deleted
	  $students = $course['UserEnrol'];
	  if (!empty($students)) {
	    foreach ($students as $index -> $value) {
	      $this->UserCourse-del($value['id']);

	      //Check whether there is other enrolled courses existed
	      $otherCourse = $this->UserCourse->getById($value['user_id']);
          if (empty($otherCourse)) {
	        $this->User->del($value['user_id']);
	      }
	    }
	  }

	  //Events: TODO
	  $events = $course['Event'];
	  if (!empty($events)) {
	  }
         //refresh my accessible courses on session
      $myCourses = $this->Course->findAccessibleCoursesListByUserIdRole($this->Auth->user('id'), $this->Auth->user('role'));
      $this->sysContainer->setMyCourseList($myCourses);
          // Finished all deletion of course related data
      $this->redirect('/courses/index/'.__('The course was deleted successfully.', true));
	  } else {
	    $this->set('errmsg', $this->Course->errorMessage);
	    $this->redirect('/courses/index');
	  }
  }

  function addInstructor() {
    if((!isset($this->passedArgs['instructor_id']) || !isset($this->passedArgs['course_id'])) &&
       (!isset($this->params['form']['instructor_id']) || !isset($this->params['form']['course_id']))) {
      $this->cakeError('error404');
    }

    $instructor_id = isset($this->passedArgs['instructor_id']) ? $this->passedArgs['instructor_id'] : $this->params['form']['instructor_id'];
    $course_id = isset($this->passedArgs['course_id']) ? $this->passedArgs['course_id'] : $this->params['form']['course_id'];

    if(!($instructor = $this->Course->Instructor->find('first', array('conditions' => array('Instructor.id' => $instructor_id))))) {
        $this->cakeError('error404');
    }

    if(!($course = $this->Course->find('first', array('conditions' => array('Course.id' => $course_id))))) {
        $this->cakeError('error404');
    }

    //$this->autoRender = false;
    $this->layout = false;
    $this->ajax = true;
		if($this->Course->addInstructor($course_id, $instructor_id)) {
      $this->set('instructor', $instructor['Instructor']);
      $this->set('course_id', $course_id);
      $this->render('/elements/courses/edit_instructor');
    } else {
      return __('Unknown error', true);
    }

  }

	function deleteInstructor() {
    if(!isset($this->passedArgs['instructor_id']) || !isset($this->passedArgs['course_id'])) {
      $this->cakeError('error404');
    }

    $this->autoRender = false;
    $this->ajax = true;
		if($this->Course->deleteInstructor($this->passedArgs['course_id'], $this->passedArgs['instructor_id'])) {
      return '';
    } else {
      return __('Unknown error', true);
      }
	}

  function checkDuplicateName()
  {
      $this->layout = 'ajax';
      $this->autoRender = false;

      $course = $this->Course->getCourseByCourse($this->data['Course']['course'], array('contain' => false));

      // check if the course is unique or the name is unchanged.
      return (empty($course) || (1 == count($course) && $this->params['named']['course_id'] == $course[0]['Course']['id'])) ?
        '' : __('Duplicated course.', true);
  }

	function update($attributeCode='',$attributeValue='')
	{
		$this->layout = false;
    $this->set('course_id', $this->Session->read('ipeerSession.courseId'));

		if ($attributeCode != '') {
      $this->params['data'] = $this->Personalize->updateAttribute($this->Auth->user('id'), $attributeCode,$attributeValue);
      $this->set('attributeCode',$attributeCode);

      $personalizeData = $this->Personalize->find('all', array('conditions' => array('user_id' => $this->Auth->user('id'))));
      $this->userPersonalize->setPersonalizeList($personalizeData);

      $this->set('userPersonalize', $this->userPersonalize);
      if ($attributeValue == '') {
        $this->render('update');
      }
		}
	}
}
?>