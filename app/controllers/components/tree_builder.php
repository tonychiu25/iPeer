<?phpclass TreeBuilderComponent extends Object {  var $tree;  var $FacultyAco;  var $name = 'TreeBuilder';  var $actsAs = array('Tree');  function __construct() {    $this->Sanitize = new Sanitize;    $this->FacultyAco = ClassRegistry::init('FacultyAco');  }    function addChildNode($parent_id=null, $faculty=null) {	$data['parent_id'] = $parent_id;	$data['faculty'] = $faculty;	if($this->FacultyAco->save($data)) {	  return true;	} else {	  throw new Exception("Invalude parent_id");	}  }    function modifyNode($nodeId=null, $newFaculty=null) {  	$this->FacultyAco->id = $nodeId;  	if($this->FacultyAco->save(array('faculty' => $newFaculty))) {  	  return true;  	} else {  	  throw new Exception("Invalide parent_id");	  	}  }    }?>