<?php
/* SVN FILE: $Id: groups_controller.php 727 2011-08-30 19:34:58Z john $ */

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
 * @lastmodified $Date: 2006/10/11 17:27:15 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Controller :: Groups
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */
 
define('IMPORT_USERNAME', 0);
define('IMPORT_GROUP_NUMBER', 1);
define('IMPORT_GROUP_NAME', 2);
 
class GroupsController extends AppController
{
  var $name = 'Groups';
	var $uses =  array('Group','GroupsMembers', 'User','UserEnrol','Personalize','GroupEvent');
  	var $show;
	var $sortBy;
	var $direction;
	var $page;
	var $order;
	var $helpers = array('Html','Ajax','Javascript','Time','Pagination');
	var $Sanitize;
  	var $components = array('AjaxList', 'ExportBaseNew', 'ExportCsv');


    function __construct()
    {
        $this->Sanitize = new Sanitize;
        $this->show = empty($_GET['show'])? 'null': $this->Sanitize->paranoid($_GET['show']);
        if ($this->show == 'all') {
            $this->show = 99999999;
        }
        $this->sortBy = empty($_GET['sort'])? 'created': $_GET['sort'];
        $this->direction = empty($_GET['direction'])? 'desc': $this->Sanitize->paranoid($_GET['direction']);
        $this->page = empty($_GET['page'])? '1': $this->Sanitize->paranoid($_GET['page']);
        $this->order = $this->sortBy.' '.strtoupper($this->direction);
        $this->pageTitle = 'Groups';
        parent::__construct();
    }

  function postProcess($data) {
    // Creates the custom in use column
    if ($data) {
      foreach ($data as $key => $entry) {
        $plural = ($entry['Group']['member_count']> 1) ? "s" : "";
        $data[$key]['Group']['group_name'] .= " <span style='color:#404080'>".
          '('.$entry['Group']['member_count'].' member'.$plural.')</span>';
      }
    }
    // Return the processed data back
    return $data;
  }

       // =-=-=-=-=-== New list routines =-=-=-=-=-===-=-
    function setUpAjaxList () {
        // Set up the ajax list component

        // Get the course data
        $userCourseList = $this->sysContainer->getMyCourseList();
        $coursesList = array();

        foreach ($userCourseList as $id => $course) {
            $coursesList{$id} = $course['course'];
        }

        // The columns to show
        $columns = array(
            array("Group.id",        "",         "",     "hidden"),
            array("Group.member_count",       "",         "",     "hidden"),
            array("Course.course",   __("Course", true),   "15em", "action", "Course Home"),
            array("Group.group_num", __("Group #", true),  "6em",  "number"),
            array("Group.group_name",__("Group Name", true),"auto", "action",
                    "View Group"),
            array("Group.creator_id",      "",         "",     "hidden"),
            array("Group.creator", __("Creator", true),  "10em", "action", "View Creator"),
            array("Group.created",  __("Date", true),     "10em", "date"),
            array("Group.course_id",        "",         "",     "hidden"),
            array("Course.id",        "",         "",     "hidden"),
        );

        $conditions = array('Group.course_id' => $this->Session->read('ipeerSession.courseId'));

        // The course to list for is the extra filter in this case
        $joinTables = array();/*
            array(
                array(  // Define the GUI aspecs
                    "id"            => "Group.course_id",
                    "description"   => "for Course:",
                    // What are the choises and the default values?
                    "list"  => $coursesList,
                    "default" => $this->Session->read('iPeerSession.courseId'),
                    // What table do we join to get these
                ),
                array(  "joinTable" => "users",
                        "joinModel" => "Creator",
                        "localKey" => "creator_id")
            );*/

        // For instructors: only list their own course events
        $extraFilters = "";
        if ($this->Auth->user('role') != 'A') {
            $extraFilters = " ( ";
            foreach ($coursesList as $id => $course) {
                $extraFilters .= "course_id=$id or ";
            }
            $extraFilters .= "1=0 ) "; // just terminates the or condition chain with "false"
        }

        // Define Actions
        $deleteUserWarning = __("Delete this group?\n", true).
                             __("(The students themselves will be unaffected).\n", true).
                             __("Proceed?", true);

        $recursive = 0;

        $actions = array(
            //   parameters to cakePHP controller:,
            //   display name, (warning shown), fixed parameters or Column ids
            array(__("View Group", true),  "", "", "",  "view", "Group.id"),
            array(__("Edit Group", true),  "", "", "",  "edit", "Group.id"),
            array(__("Course Home", true),  "", "", "courses", "home", "Group.course_id"),
            array(__("View Course", true),  "", "", "courses", "view", "Group.course_id"),
            array(__("View Creator", true),  "", "", "users","view", "Group.creator_id"),
            array(__("Delete Group", true),    $deleteUserWarning, "", "", "delete",       "Group.id")
        );

        $this->AjaxList->setUp($this->Group, $columns, $actions, "Group.group_num", "Group.group_name",
            $joinTables, $extraFilters, $recursive, "postProcess", null, null, null, $conditions);
    }

    function index() {
      $this->set('course_id', $this->Session->read('ipeerSession.courseId'));
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

    // Show a class list of groups
    function goToClassList($course) {
        if (is_numeric($course)) {
            $courses = $this->sysContainer->getMyCourseList();
            if (!empty($courses[$course])) {
                // We need to change the session state to point to this
                // course:
                // Initialize a basic non-funcional AjaxList
                $this->AjaxList->quickSetUp();
                // Clear the state first, we don't want any previous searches/selections.
                $this->AjaxList->clearState();
                // Set and update session state Variable
                $joinFilterSelections->{"Group.course_id"} = $course;
                $this->AjaxList->setStateVariable("joinFilterSelections", $joinFilterSelections);
            }
        }
        // Redirect to list after state modifications (or in case of error)
        $this->redirect("/groups/index/".$course);
    }

  function view ($id) {
    $this->data = $this->Group->read(null, $id);
		$this->set('data', $this->data);
    $this->set('course_id', $this->data['Group']['course_id']);
    $this->set('readonly', true);
    $this->set('members', $this->GroupsMembers->getMembers($id));
    $members = $this->GroupsMembers->getMembers($id);
    $this->User->recursive =0;
    $group_data = $this->User->find('all', array('conditions' => array('id'=>$members),
                                            'fields' => array('id', 'full_name', 'email')
        ));
    $this->set('group_data', $group_data);
  }

  function add ($course_id) {  	
    if (!empty($this->data)) {
      //$this->params = $this->Group->prepData($this->params);
      if ($this->Group->save($this->data)) {
        // add members into the groups_members table
        //$this->GroupsMembers->insertMembers($this->Group->id, $this->params['data']['Group']);
        $this->Session->setFlash(__('The groups were added successfully.', true));
        $this->redirect('index/'.$course_id);
      } else {
        // Error occured
        $this->Session->setFlash(__('Please correct the error below.', true));
      }
    }
    $user_data = $this->User->getEnrolledStudentsForList($course_id);

    //Check if student is already assigned in a group
    $groups = $this->Group->getGroupsByCouseId($course_id);
    $assigned_users = $this->GroupsMembers->getUserListInGroups($groups);
    foreach($assigned_users as $assigned_user){
      $user_data[$assigned_user] = $user_data[$assigned_user].'*';
    }

    $this->set('title_for_layout', $this->sysContainer->getCourseName($course_id).__(' > Groups', true));
    $this->data['Group']['course_id'] = $course_id;
    // gets all the students in db for the unfiltered students list
    $this->set('user_data', $user_data);
    $this->set('group_data', array());
    $this->set('course_id', $course_id);
    $this->set('group_num', $this->Group->getCourseGroupCount($course_id)+1);
    $this->render('edit');
  }

  function edit ($group_id) {
    if (!empty($this->data)) {
      //$this->data['Group']['id'] = $group_id;
      if ( $this->Group->save($this->data)) {
        //$this->GroupsMembers->updateMembers($this->Group->id, $data2save['data']['Group']);
        $this->Session->setFlash(__('The group was updated successfully.', true));
      } else {
        // Error occurs:
        $this->Session->setFlash(__('Error saving that group.', true));
      }
      $this->redirect('index/'.$this->data['Group']['course_id']);
    }

    $group = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
    
    if(!($this->data = $group)) {
      $this->Session->setFlash(__('Group Not Found.', true));
      $this->redirect('index/'.$this->Session->read('ipeerSession.courseId'));
    };

    $this->set('title_for_layout', $this->sysContainer->getCourseName($this->data['Group']['course_id']).__(' > Groups', true));

    // gets all students not listed in the group for unfiltered box
    $this->set('user_data', $this->Group->getStudentsNotInGroup($group_id, 'list'));
    //gets all students in the group
    $this->set('members', $this->Group->getMembersByGroupId($group_id, 'list'));
    $this->set('group_id', $group_id);
    $this->set('group_num',$this->data['Group']['group_num']);
    $this->set('course_id', $this->data['Group']['course_id']);
  }

  function delete ($id) {
    if ($this->Group->delete($id)) {
			$this->Session->setFlash(__('The group was deleted successfully.', true));
		} else {
      $this->Session->setFlash(__('Group delete failed.', true));
		}
    $this->redirect('index/'.$this->Session->read('ipeerSession.courseId'));
  }

  function checkDuplicateName()
  {
      $this->layout = 'ajax';
      $this->set('course_id', $this->rdAuth->courseId);
      $this->render('checkDuplicateName');
  }

  function getQueryAttribute($courseId = null)
  {
    $attributes = array('fields'=>'', 'condition'=>'', 'joinTable'=>array());
    $attributes['fields'] = 'Group.id, Group.group_num, Group.group_name, Group.course_id, Group.created, Group.creator_id, Group.modified, Group.updater_id';
    $joinTable = array();//array('INNER JOIN groups_members AS GroupsMembers ON Group.id = GroupsMembers.group_id');

    if (!empty($courseId)) {
      $attributes['condition'] .= ' Group.course_id = '.$courseId;
  	}
  	$attributes['joinTable']=$joinTable;

  	return $attributes;
  }

  function import() {
    // Just render :-)
    if (!empty($this->params['form'])) {
      $courseId = $this->params['form']['course_id'];
      $this->params['data']['Group']['course_id'] = $courseId;
      $filename = $this->params['form']['file']['name'];
      $tmpFile = $this->params['form']['file']['tmp_name'];

      //$uploadDir = $this->sysContainer->getParamByParamCode('system.upload_dir');
      $uploadDir = "../tmp/";
      //$uploadFile = APP.$uploadDir['parameter_value'] . $filename;
      $uploadFile = $uploadDir.$filename;

      //check for blank filename
      if (trim($filename) == "") {
        $this->set('errmsg',__('File required.', true));
        $this->set('user_data', $this->User->getEnrolledStudents($courseId));
      }
      //Return true if valid, else error msg
      $validUploads = $this->framework->validateUploadFile($tmpFile, $filename, $uploadFile);
      if ($validUploads === true) {
          // Get file into an array.
          $lines = file($uploadFile, FILE_SKIP_EMPTY_LINES);
          // Delete the uploaded file
          unlink($uploadFile);
          //Mass create groups
          $this->addGroupByImport($this->params['data'], $lines, $courseId);
          // We should never get to this line :-)
      } else {
          $this->set('errmsg', $validUploads);
      }
    }
   
    $courseId = $this->Session->read('ipeerSession.courseId');
    $this->set("courseId", $courseId);
  }

  // Takes an array of imported file lines, and creates groups from them
  function addGroupByImport($data, $lines, $courseId) {
      // Check for parameters
      if (empty($lines) || empty($courseId)) {
          return array();
      }

      // pre-process the lines in the file first
      for ($i = 0; $i < count($lines); $i++) {
          // Trim this line's white space
          $lines[$i] = trim($lines[$i]);
          // Clear all quotes - we rely on separators instead
          $lines[$i] = str_replace('"', '', $lines[$i]);
          // Replace tabs with commas - in case of different CSV formatting
          $lines[$i] = str_replace("\t", "", $lines[$i]);
          // Put a space between the commas, and their following column
          $lines[$i] = str_replace(",", ", ", $lines[$i]);
      }

      // Remove duplicate lines
      $lines = array_unique($lines);


      // Process the array into groups
      $users = array();
      for ($i = 0; $i < count($lines); $i++) {
          $entry = array();
          $entry['line'] =  $lines[$i];
          // To start, mark all entries as invalid and
          $entry['status'] = "Unchecked Entry";
          $entry['valid'] = false;
          $entry['added'] = false;
          // Split the line up by command
          $split = @split (',', $entry['line']);
          // If the count is not 3, there's probably a formatting error,
          //  so ignore this entry.
          if (count($split) < 3 ) {
              $entry['status'] = __("Too few columns in this line (", true) . count($split). ")," .
                  __(" expected 3.", true);
          } else if (count($split) > 3 ) {
              $entry['status'] = __("Too many columns in this line (", true) . count($split). ")," .
                  __(" expected 3.", true);
          } else {
              // assign the parts into their appropriate places
              $entry['username'] = trim($split[IMPORT_USERNAME]);
              $entry['group_num'] = trim($split[IMPORT_GROUP_NUMBER]);
              $entry['group_name'] = trim($split[IMPORT_GROUP_NAME]);

              // Check the entries for empty spots
              if (empty($entry['username'])) {
                  $entry['status'] = __("Username column is empty.", true);
              } else if (empty($entry['group_num'])) {
                  $entry['status'] = __("Group Number column is empty.", true);
              } else if (empty($entry['group_name'])) {
                  $entry['status'] = __("Group Name column is empty.", true);
              } else {
                  $userData = $this->User->findByUsername($entry['username']);
                  if (!is_array($userData)) {
                      $entry['status'] = __("User ", true). $entry[username].__(" is unknown. Please add this user first.", true);
                  } else {
                      $entry['id'] = $userData['User']['id'];
                      if (!$this->UserEnrol->isEnrolledInByUsername($entry['username'], $courseId)) {
                          $entry['status'] = __("User ", true). $entry[username].__(" is not enrolled in your selected course. ", true);
                          $entry['status'] .= __("Please entroll them first.", true);
                      } else {
                          // So, the user exists, and is enrolled in the course - they pass validation
                          $entry['status'] = __("Validated Entry", true);
                          $entry['valid'] = true;
                      }
                  }
              }
          }
          // Add this checked entry into the list
          array_push($users, $entry);
      }

      // Now, generate a list of groups to create
      $groups = array();
      foreach ($users as $key => $entry) {
          if ($entry['valid']) {
              // Check to see if this group is already in the group array.
              $newGroup = true;
              foreach ($groups as $group) {
                  if ($group['name'] == $entry['group_name'] &&
                      $group['number'] == $entry['group_num']) {
                      $newGroup = false;
                      break;
                  }
              }
              // If we have a new group, record it.
              if ($newGroup) {
                  $group = array();
                  $group['number'] = $entry['group_num'];
                  $group['name'] = $entry['group_name'];
                  $group['id'] = false;
                  $group['created'] = false;
                  $group['present'] = false;
                  $group['reason'] = __("Unchecked groups", true);
                  array_push($groups, $group);
              }
          } else {
              continue;
          }
      }

      // Check the groups' existance, and create them if they're missing.
      // Here I assume that the above code will not add a group without users.
      foreach ($groups as $key=>$group) {
          $groupId = $this->Group->findGroup($courseId, $group['number'], $group['name']);
          if (is_numeric($groupId)) {
              $groups[$key]['present'] = true;
              $groups[$key]['id'] = $groupId;
              $groups[$key]['reason'] = __("The group already exists. Students will be added to it.", true);
          } else {
              // DOESN'T WORK FOR SOME REASON...
              // Create the group's database array for storage
              $groupData = array();
              $groupData['Group'] = array();
              $groupData['Group']['id'] = null;
              $groupData['Group']['group_num'] = $group['number'];
              $groupData['Group']['group_name'] = $group['name'];
              $groupData['Group']['creator_id'] = $this->Auth->user('id');
              $groupData['Group']['course_id'] = $courseId;

              // Save the group to the database
              if ($this->Group->save($groupData)) {
                  $groups[$key]['present'] = true;
                  $groups[$key]['created'] = true;
                  $groups[$key]['id'] = $this->Group->id;
                  $groups[$key]['reason'] = __("This is a new group; it was created sucessfully.", true);
              } else {
                  $groups[$key]['reason'] = __("The group could not be created in the database!", true);
              }
          }
      }

      // Then, add the users to the created groups
      foreach ($users as $key => $user) {
          // For all valid users
          if ($user['valid']) {
              // Find the group we'd like to add this person to
              $groupId = false;
              foreach ($groups as $group) {
                  if ($group['number'] == $user['group_num'] &&
                      $group['name'] == $user['group_name']) {
                      $groupId = $group['id'];
                  }
              }
              if (is_numeric($groupId)) {
                  $alreadyAdded = $this->GroupsMembers->isMemberOf($user['id'], $groupId);
                  if ($alreadyAdded) {
                      // User Already in group
                      $users[$key]['status'] = __("User ", true). $user[username]. __("is already in group ", true);
                      $users[$key]['status'].= "$user[group_num] - $user[group_name]";
                  } else {
                      $groupMemberData = array();
                      $groupMemberData['id']= null;
                      $groupMemberData['user_id'] = $user['id'];
                      $groupMemberData['group_id'] = $groupId;
                      if ($this->GroupsMembers->save($groupMemberData)) {
                          $users[$key]['status'] = __("User added sucessfully to group ", true);
                          $users[$key]['status'].= "$user[group_num] - $user[group_name]";
                          $users[$key]['added'] = true;
                      } else {
                          $users[$key]['status'] = __("User ", true). $user[username].__(" could not be added to group ", true);
                          $users[$key]['status'].= "$user[group_num] - $user[group_name]";
                          $users[$key]['status'].= __("- the entry could not be created in the database.", true);
                      }
                  }
              } else {
                  // A group should have either existed, or was just created.
                  $users[$key]['status'] = __("Can't find the group for user", true). $user[username];
                  $users[$key]['status'] = __("This should never occur!", true);
              }

          }
      }

      // Set up the data for the view
      $results = array();
      $results['users_added'] = 0;
      $results['users_skipped'] = 0;
      $results['users_error'] = 0;

      $results['groups_added'] = 0;
      $results['groups_skipped'] = 0;
      $results['groups_error'] = 0;

      // Add up statistics of import
      foreach ($users as $key => $user) {

          // Look up extra user info:
          if (!empty($user['id'])) {
               $users[$key]['data'] = $this->User->findById($user['id']);
          } else {
              $users[$key]['data'] = false;
          }

          // Add the user to statistics
          if ($user['valid']) {
              if ($user['added']) {
                  $results['users_added']++;
              } else {
                  $results['users_skipped']++;
              }
          } else {
              $results['users_error']++;
          }

      }

      foreach ($groups as $key => $group) {
          if ($group['present']) {
              if ($group['created']) {
                  $results['groups_added']++;
              } else {
                  $results['groups_skipped']++;
              }
          } else {
              $results['groups_error']++;
          }
      }

      $results['groups'] = $groups;
      $results['users'] = $users;

      $this->set("results", $results);
      $this->render('import_results');
      exit;
  }

	function update($attributeCode='',$attributeValue='') {
		if ($attributeCode != '' && $attributeValue != '') //check for empty params
		{
			$this->params['data'] = $this->Personalize->updateAttribute($this->Auth->user('id'), $attributeCode, $attributeValue);
		}
	}
	
  function export($courseId) {	
    $this->set('courseId', $courseId);
    if (isset($this->params['form']) && !empty($this->params['form'])) {
      // check that at least one group has been selected
      if(empty($this->data['Member']['Member'])) {
        $this->Session->setFlash("Please select at least one group to export.");
        $this->redirect('');
      }
      $this->autoRender = false;
      $fileContent = '';
      $groups = $this->data['Member']['Member'];
      if(!empty($this->params['form']['include_group_names'])) {
        $fileContent .= "Group Name,";
      }
      if(!empty($this->params['form']['include_student_id'])) {
        $fileContent .= "Student #,";
      }
      if(!empty($this->params['form']['include_student_name'])) {
        $fileContent .= "Last Name, First Name,";
      }
      if(!empty($this->params['form']['include_student_email'])) {
        $fileContent .= "Email Address";
      }

      $fileContent .= "\n";

      foreach ($groups as $groupId) {
        $fileContent .= $this->ExportCsv->buildGroupExportCsvByGroup($this->params['form'], $groupId);
      }
      header('Content-Type: application/csv');
      header('Content-Disposition: attachment; filename=' .$this->params['form']['file_name']. '.csv');
      echo $fileContent;
    } else {
      // format data
      $unassignedGroups = $this->Group->find('list', array('conditions'=> array('course_id'=>$courseId), 'fields'=>array('group_name')));
      $this->set('unassignedGroups', $unassignedGroups);
    }
  }

/*    function getFilteredStudent()
    {
        $this->layout = 'ajax';
        $this->set('course_id', $this->rdAuth->courseId);
        $group_id = $this->params['form']['group_id'];
        $filter = 'filter' == $this->params['form']['filter'];
        $this->set('students', $this->Group->getFilteredStudents($group_id, $filter));
        $this->render('filteredStudents');
    }*/

  function sendGroupEmail ($courseId) {
    if (is_numeric($courseId)) {

    } else {

    }
  }
}
?>
