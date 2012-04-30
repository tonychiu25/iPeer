<?php
/* SVN FILE: $Id: personalize.php 727 2011-08-30 19:34:58Z john $ */

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
 * @lastmodified $Date: 2006/06/20 18:44:18 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 
/**
 * Personalize
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */
class Personalize extends AppModel
{
  var $name = 'Personalize';

  function updateAttribute($userId='', $attributeCode='', $attributeValue = null)
  {
    //$data = $this->find("user_id = ".$userId." AND attribute_code = '".$attributeCode."' ");
    $data = $this->find('first', array(
        'conditions' => array('user_id' => $userId, 'attribute_code' => $attributeCode)
    ));
    $tmpValue = '';
    if (isset($data) && $attributeValue == null) {
      $tmpValue = $data['Personalize']['attribute_value'];
      if ($tmpValue == 'true') {
        $tmpValue = 'none';
      } else {
         $tmpValue = 'true';
      }
      $data['Personalize']['attribute_code'] = $attributeCode;
      $data['Personalize']['user_id'] = $userId;
      $data['Personalize']['attribute_value']=$tmpValue;
    } else {
      $data['Personalize']['attribute_value'] = ($tmpValue == 'none') ? 'true':$attributeValue;
      $data['Personalize']['attribute_code'] = $attributeCode;
      $data['Personalize']['user_id'] = $userId;
    }
    $this->save($data);
  }

  function beforeSave()
  {
    $this->data[$this->name]['updated'] = date('Y-m-d H:i:s');
    return true;
  }
}

?>
