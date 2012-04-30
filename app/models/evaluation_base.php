<?php

class EvaluationBase extends AppModel {
  var $name = 'EvaluationBase';
  var $actsAs = array('ExtendAssociations', 'Containable', 'Habtamable', 'Traceable');
  var $useTable = false;
  // suppress the warning when using "cake schema generate"
  const TEMPLATE_TYPE_ID = 0;

  function __construct($id = false, $table = null, $ds = null) {
    parent::__construct($id, $table, $ds);
    $c = get_class( $this );
    $this->virtualFields['event_count'] = sprintf('SELECT count(*) as count FROM events as event WHERE event.event_template_type_id = %d AND event.template_id = %s.id', constant($c.'::TEMPLATE_TYPE_ID'), $this->alias);
  }

  function beforeSave(){
    // Ensure the name is not empty
    if (empty($this->data[$this->name]['name'])) {
      $this->errorMessage = "Please enter a new name for this " . $this->name . ".";
      return false;
    }

    // Remove any signle quotes in the name, so that custom SQL queries are not confused.
    $this->data[$this->name]['name'] =
      str_replace("'", "", $this->data[$this->name]['name']);

    //check the duplicate name
    if (empty($this->data[$this->name]['id']) && !$this->__checkDuplicateName()) {
      return false;
    }
       //check if questions are entered
    if(!empty($this->data['Question'])&&$this->name =='Mixeval') {
     foreach ($this->data['Question'] as $row) {
    	 if ($row['question_type']== 'S' &&(empty($row['Description'] ) || (count($row['Description'])) < 2)) {
    	 	$this->errorMessage = "Please add at least two descriptors for each of the Lickert questions.";
    	 	 return false;
    	 }
     }
    }
    
    if(empty($this->data['Question'])&&($this->name =='Mixeval')){
       $this->errorMessage = "Please add at least one question for this " . $this->name . ".";
       return false;
     }
    return parent::beforeSave();
  }
  
  //Validation check on duplication of name
	function __checkDuplicateName() {
    $result = $this->find('first', array('conditions' => array('name' => $this->data[$this->name]['name'])));
    if ($result) {
      $this->errorMessage='Duplicate name found. Please change the name.';
      return false;
    }

    return true;
  }

  function getBelongingOrPublic($user_id) {
  	
  	/*$userRole = User::get('RolesUser');
  	$userDeptList = User::get('DepartmentList');
  	$evaluationType = $this->name;
    $evalTemplateId = $evaluationType::TEMPLATE_TYPE_ID;
    $userRole = User::get('RolesUser'); 
    $deptEvalTemplate = ClassRegistry::init('DepartmentEvaluationTemplate');
  	
	// Query all accessible evaluation template based on user's affliated academic dept.
    $accessibleEvalTemplateId = $deptEvalTemplate->getEvalTemplateListByTypeAndDept($evalTemplateId, $userDeptList);
    $conditions = array('creator_id' => $user_id);  
    if ($userRole == 2) {
      $conditions = array('OR' => array_merge(array('id' => $accessibleEvalTemplateId), $conditions));	       
    } else if ($userRole == 3) {
      if ($evaluationType != "SimpleEvaluation") {
        $conditions = array('OR' => array_merge(array('id' => array(3),
        											  'availability' => 'public'), $conditions));
      }
    }

    return $this->find('list', array('conditions' => $conditions, 
    								 'fields' => array('name'),
    								 'callbacks' => false));*/

  	$deptEvalTemplateModel = ClassRegistry::init('DepartmentEvaluationTemplate');
  	$evaluationType = $this->name;
    $evalTemplateId = $evaluationType::TEMPLATE_TYPE_ID; 
  	
    if(!is_numeric($user_id)) {
      return false;
    }
    
    $templateId  = $deptEvalTemplateModel->getEvalTemplateListByTypeAndDept($evalTemplateId, User::get('DepartmentList'));
    $conditions = array('creator_id' => $user_id);
    $conditions = array('id' => array_merge($templateId, $conditions));
    if($this->name != 'SimpleEvaluation') {
      $conditions = array('OR' => array_merge(array('availability' => 'public'), $conditions));
    }
    return $this->find('list', array('conditions' => $conditions, 'fields' => array('name')));
  }

  function getEventCount($evaluation_id) {
    $eval = $this->read('event_count', $evaluation_id);
    return $eval[$this->alias]['event_count'];
  }
}
