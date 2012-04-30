<?php $readonly = isset($readonly) ? $readonly : false;?>
<?php $username_msg = $readonly ? '' : __('<br /><u>Remember:</u> Usernames must be at least 6 characters long and contain only:<li>letters and numbers</li>', true);?>
<table width="100%"  border="0" cellpadding="8" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>
    <?php echo $this->Form->create('User',
                                   array('id' => 'frm',
                                         'url' => array('action' => $this->action),
                                         'inputDefaults' => array('div' => false,
                                                                  'before' => '<td width="200px">',
                                                                  'after' => '</td>',
                                                                  'between' => '</td><td>')))?>
      <input type="hidden" name="required" id="required" value="username" />
      <table width="75%" border="0" align="center" cellpadding="4" cellspacing="2">
        <tr class="tableheader"><td colspan="3" align="center"><?php echo ucfirst($this->action)?> <?php __('User')?></td></tr>

        <!-- User Name -->
        <tr class="tablecell2">
          <?php echo $this->Form->input('username', array('id' => 'username', 'size'=>'50', 'class'=>'validate required TEXT_FORMAT username_msg Invalid_Text._At_Least_One_Word_Is_Required.', 'after' => $username_msg,
                                                          'error' => array( 'minLength' => __('Usernames must be at least 6 characters long', true), 
                                                          'character' => __('Usernames must be at least 6 characters long', true),
                                                          'unique' => __('Duplicate Username found. Please change the username.', true)),
                                                          'label' => __('Username', true).'&nbsp;<font color="red">*</font>',
                                                          'readonly' => $readonly));?>
          <?php echo $readonly ? '' : $ajax->observeField('username', array('update'=>'usernameErr', 'url'=>'checkDuplicateName/', 'frequency'=>1, 'loading'=>"Element.show('loading');", 'complete'=>"Element.hide('loading');stripe();")); ?>
          <td width="255"><div id='username_msg' class="error"></div><div id='usernameErr' class="error"></div></td>
        </tr>

        <?php if(!$readonly && !$isEdit):?>
        <!-- Password -->
          <tr class="tablecell2"><td  colspan="3">
          <?php __('A password will be automatically generated, and shown on the next page, after you click "Save".')?><br />
          <strong><?php __('Note:</strong> If using CWL logons, students should use CWL username/password for iPeer, instead of the generated one.')?>
          </td></tr>        
        <!-- Email Notification -->
        <tr class="tablecell2">
            <td><?php __('Send Email Notification?')?></td>
            <td><?php echo $this->Form->input('send_email_notification', array('type'=>'checkbox','label'=>false,'format' => array('input'))) ?></td>
            <td><?php __('If checked, send a notification email to user include username, password and instruction')?></td>
        </tr>
        <?php endif;?>

        <!-- First Name -->
        <tr class="tablecell2">
            <?php echo $this->Form->input('first_name', array('size'=>'50', 'class'=>'validate none TEXT_FORMAT first_name_msg Invalid_Text._At_Least_One_Word_Is_Required.',
                                                              'readonly' => $readonly, 'label'=>__('First Name', true))) ?>
            <td id="first_name_msg" class="error">&nbsp;</td>
        </tr>

        <!-- Last Name -->
        <tr class="tablecell2">
            <?php echo $this->Form->input('last_name', array('size'=>'50', 'class'=>'validate none TEXT_FORMAT last_name_msg Invalid_Text._At_Least_One_Word_Is_Required.',
                                                             'readonly' => $readonly, 'label' => __('Last Name', true)))?>
            <td id="last_name_msg" class="error">&nbsp;</td>
        </tr>


        <!-- Email  -->
        <tr class="tablecell2">
            <?php echo $this->Form->input('email', array('size'=>'50', 'class'=>'validate none EMAIL_FORMAT email_msg Invalid_Email_Format.',
                                                         'after' => '', 'label' => __('Email', true),
                                                         'error' => __('Invalid email format', true), 
                                                         'readonly' => $readonly)) ?>                                          
            <td id="email_msg" class="error">&nbsp;</td>
        </tr>

        <tr class="tablecell2">
          <?php echo $this->Form->input('Role.Role', array('disabled' => $readonly, 'label' => __('Role', true)));?>
          <?php //echo $this->Form->select('record_status', array('A' => 'Active', 'I' => 'Inactive'), null, array('empty' => false))?>
           <td id="role_msg" class="error">&nbsp;</td>
        </tr>

        <script language="JavaScript" type="text/javascript">
        function updateFields() {
          var options = $$('select#RoleRole option');
          var student_field_action = "hide";
          var nonstudent_field_action = "hide";
          var department_field_action = "hide";

          $F('RoleRole').each(function(selected){
            options.each(function(option) {
              if(option.value == selected) {
                if(option.text == 'student') {
                  student_field_action = "show";
                } else if (selected == 2 || selected == 3){
                  nonstudent_field_action = "show";
                  department_field_action = "show";
                } else {
                  nonstudent_field_action = "show";
                }
              }
            });
          });
          $$('tr.student_field').invoke(student_field_action);
          $$('tr.nonstudent_field').invoke(nonstudent_field_action);
          $$('tr.department_field').invoke(department_field_action);
        }
        $('RoleRole').observe('change', updateFields);
        </script>

        <!-- Department  -->
        <tr class="tablecell2 department_field" style="display:none;">
          <td>Department</td>
          <td><?php
          		if (empty($selectedDept)) {
          		  $selectedDept = null;
          		} 
           		echo $this->Form->select('selected_dept', $departmentList, $selectedDept, array('empty' => false)); 
              ?>
          </td>
          <td></td>
        </tr>

        <!-- Title  -->
        <tr class="tablecell2 nonstudent_field" style="display:none;">
          <?php echo $this->Form->input('title', array('size'=>'50', 'class'=>'validate none TEXT_FORMAT title_msg Invalid_Text._At_Least_One_Word_Is_Required.',
                                                       'readonly' => $readonly, 'label' => __('Title', true))) ?>
          <td id="title_msg" class="error">&nbsp;</td>
        </tr>

        <!-- student no-->
        <tr class="tablecell2 student_field" style="display:none;">
          <?php echo $this->Form->input('student_no', array('size'=>'50', 'class'=>'validate none',
                                                            'readonly' => $readonly, 'label' => __('Student No', true))) ?>
          <td id="student_no_msg" class="error">&nbsp;</td>
        </tr>
        
        <!-- student courses-->
        <?php if ($isStudent) : ?>
          <tr class="tablecell2"> <td width="130" id="courses_label"><?php __("This student's<br />Courses")?>:</td>
          <td colspan=2><?php
            // Render the course list, with check box selections
            echo $this->element("list/checkBoxList", array(
                "eachName" => "Course",
                "setName" => "Courses",
                "verbIn" => "add",
                "verbOut" => "remove",
                "list" => $simpleCoursesList,
                "readOnly" => $readonly,
                "selection" => $simpleEnrolledList)); ?>
          </td></tr>
        <?php endif; ?>

        <?php if($readonly):?>
        <tr class="tablecell2">
          <?php echo $this->Form->input('creator', array('size'=>'50', 'class'=>'validate none',
                                                         'readonly' => $readonly, 'label' => __('Creator', true))) ?>
          <td></td>
        </tr>

        <tr class="tablecell2">
          <?php echo $this->Form->input('updater', array('size'=>'50', 'class'=>'validate none',
                                                         'readonly' => $readonly, 'label' => __('Updater', true))) ?>
          <td></td>
        </tr>

        <tr class="tablecell2">
          <?php echo $this->Form->input('created', array('type' => 'text',
                                                         'size'=>'50', 'class'=>'validate none',
                                                         'readonly' => $readonly, 'label' => __('Created', true))) ?>
          <td></td>
        </tr>
        <tr class="tablecell2">
          <?php echo $this->Form->input('modified', array('type' => 'text',
                                                         'size'=>'50', 'class'=>'validate none',
                                                         'readonly' => $readonly, 'label' => __('Modified', true))) ?>
          <td></td>
        </tr>
        <?php endif;?>

        <!-- Back / Save -->
        <tr class="tablecell2">
            <td colspan="3" align="center">
            <input type="button" value="<?php __('Back')?>" onClick="javascript:window.location='javascript: history.go(-1)'";>
            <?php if (!$readonly) echo $this->Form->submit(__('Save', true), array('div' => false));?>
            </td>
        </tr>
        </table>

        <table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
        <tr>
            <td align="left"><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'corner_bot_left'))?></td>
            <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'corner_bot_right'))?></td>
        </tr>
        </table>
<?php echo $this->Form->end();?>
    </td></tr></table>
</td></tr>
</table>

<script language="JavaScript" type="text/javascript">
updateFields();
</script>
