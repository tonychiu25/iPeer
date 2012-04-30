<?phpclass DepartmentUser extends AppModel{  var $name = 'DepartmentUser';    /**   * Returns the department ids corresponding to every department that the user belong in; ignores factuly and root lv.    */  function getDepartmentListByUser($userId = null) {  	$department = ClassRegistry::init('Department');  		$deptList = $this->find('list', array('conditions' => array('user_id' => $userId), 'fields' => array('dept_id')));	$returnDeptList = array();	// Some deptId may actaully be faculty id; hence extract the associated dept for the faculty if nexessary.  	foreach ($deptList as $deptId) {	  $dept = $department->getDepartmentById('first', $deptId);	  // Query only depts, filter our faculty and root level (refer to "Department" table)	  $childDept = $department->find('list', array('conditions' => array('parent_id >' => 1,	  												 					 'lft >=' => $dept['Department']['lft'],																		 'rght <=' => $dept['Department']['rght']),	  											    'fields' => array('id'),	  												'recursive' => 0));	  $returnDeptList = array_merge($childDept, $returnDeptList);	}		return $returnDeptList;  }    /**   * Returns a departmentUser row based on the deptId and userId.   */  function getDeptUserByUserDeptId($type = '' ,$deptId = null, $userId = null) {  	return $this->find($type, array('conditions' => array('dept_id' => $deptId, 'user_id' => $userId)));  }    /**   * Returns a departmentUser row based on user_id.   */  function getDeptUserByUser($type = '', $userId = '') {  	return $this->find($type, array('conditions' => array('user_id' => $userId)));  }}?>