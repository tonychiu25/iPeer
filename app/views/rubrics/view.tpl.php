<table width="100%"  border="0" cellpadding="8" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>	<div>
  <table width="95%" border="0" align="center" cellpadding="4" cellspacing="2">
    <tr class="tableheader">
      <td align="center" colspan="4">Rubric Settings</td>
      </tr>
    <tr class="tablecell">
      <td width="86">Name:</td>
      <td width="389" colspan="3"><?php echo $data['Rubric']['name']; ?></td>
    </tr>
    <tr class="tablecell">
      <td>Level of Mastery: </td>
      <td colspan="3"><?php echo $data['Rubric']['lom_max']; ?></td>
    </tr>
    <tr class="tablecell">
      <td>Criteria: </td>
      <td colspan="3"><?php echo $data['Rubric']['criteria']; ?></td>
    </tr>
    <tr class="tablecell">
      <td>Rubric Availability:</td>
      <td colspan="3"><?php echo $data['Rubric']['availability']; ?></td>
    </tr>
    <tr class="tablecell2">
      <td id="creator_label"><small>Creator:</small></td>
      <td align="left"><?php
      $params = array('controller'=>'rubrics', 'userId'=>$data['Rubric']['creator_id']);
      echo $this->renderElement('users/user_info', $params);
      ?></td>
      <td id="updater_label"><small>Updater:</small></td>
      <td align="left"><?php
      $params = array('controller'=>'rubrics', 'userId'=>$data['Rubric']['updater_id']);
      echo $this->renderElement('users/user_info', $params);
      ?></td>
    </tr>
    <tr class="tablecell2">
      <td id="created_label"><small>Create Date:</small></td>
      <td align="left"><?php if (!empty($data['Rubric']['created'])) echo '<small>'.$data['Rubric']['created'].'</small>'; ?></td>
      <td id="updated_label"><small>Update Date:</small></td>
      <td align="left"><?php if (!empty($data['Rubric']['modified'])) echo '<small>'.$data['Rubric']['modified'].'</small>'; ?></td>
    </tr>
    <tr class="tablecell">
      <td colspan="4" align="center"><input type="button" name="Back" value="Back" onClick="parent.location='<?php echo $this->webroot.$this->themeWeb.$this->params['controller']; ?>'"></td>
     </tr>
  </table>
  <table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
    <tr>
      <td align="left"><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'corner_bot_left'))?></td>
      <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'corner_bot_right'))?></td>
    </tr>
  </table>
  </div>
</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="title">
  <tr>
	<td><?php echo $html->image('layout/icon_ipeer_logo.gif',array('border'=>'0','alt'=>'ipeer_logo'))?> Rubric Preview </td>
	<td><div align="right"><a href="#rpreview" onclick="$('rpreview').toggle(); toggle1(this);"><?php echo empty($this->data) ? '[-]' : '[-]'; ?></a></div></td>
  </tr>
</table>

<div id="rpreview" style="display: block; background: #FFF;">
<br>
<?php
$params = array('controller'=>'rubrics','data'=>$this->controller->RubricHelper->compileViewData($data), 'evaluate'=>0);
echo $this->renderElement('rubrics/ajax_rubric_view', $params);
?>
</div>
