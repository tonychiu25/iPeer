<table width="100%" border="0" cellpadding="8" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>
      <table width="95%" border="0" align="center" cellpadding="4" cellspacing="4">
      <tr>
        <td valign="top" colspan="3">
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
              <tr>
                <td><?php echo $html->image('layout/corner_top_left.gif',array('align'=>'middle','alt'=>'corner_top_left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_top_right.gif',array('align'=>'middle','alt'=>'corner_top_right'))?></td>
              </tr>
          </table>
	        <table width="100%"  border="0" cellpadding="4" cellspacing="2">
            <tr>
              <td colspan="6" align="center" class="tableheader"><?php echo $data['Course']['course']; ?> - <?php echo $data['Course']['title']; ?></td>
            </tr>
            <tr>
              <td width="100" class="tablesubheader">Instructor:</td>
              <td class="tablecell2" colspan="5">
              <?php $params = array('controller'=>'courses', 'courseInstructors'=>$data['UserCourse']);
              echo $this->renderElement('courses/course_instructors', $params);?>
              </td>
            </tr>
            <tr>
              <td width="100" class="tablesubheader">Class Size:</td>
              <td width="300" class="tablecell2"><?php
                if ($studentCount == 0) {
                  echo 'No student is enrolled';
                } else {
                  echo $studentCount.' students';
                } ?></td>
              <td width="100" valign="top" class="tablesubheader">Class Statistics: </td>
              <td class="tablecell2" colspan="3"><?php
                if ($groupCount == 0) {
                  echo 'No group has been made';
                } else {
                  echo $groupCount.' groups';
                } ?>
                <br><?php
                if ($eventCount == 0) {
                  echo 'No Evaluation Event has been created';
                } else {
                  echo $eventCount.' Evaluation Events';
                } ?></td>
            </tr>
            <!--<?php if (!(isset($rdAuth->customIntegrateCWL) && $rdAuth->customIntegrateCWL)) { ?>
            <tr>
              <td class="tablesubheader">Self Enrollment:</td>
              <td class="tablecell2"><?php echo $data['Course']['self_enroll'] == 'off'? 'Not Enabled' : 'Enabled'; ?></td>
              <td class="tablesubheader">Self Enrollment Password: </td>
              <td width="300" class="tablecell2"><?php echo $data['Course']['password']==null? 'N/A' : $data['Course']['password']; ?></td>
            </tr>
            <tr>
              <td class="tablesubheader">Self Enrollment URL: </td>
              <td width="150" class="tablecell2" colspan="3"><?php echo $data['Course']['homepage']==null? 'N/A' : $data['Course']['homepage']; ?></td>
            </tr>
          <?php }?>-->
          </table>
		      <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
              <tr>
                <td><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
              </tr>
            </table>
        </td>
      </tr>

        <tr>
          <td valign="top" width="50%">
            <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
              <tr>
                <td><?php echo $html->image('layout/corner_top_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_top_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
              </tr>
            </table>
		        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td style="padding-bottom:0px">
             <?php
                $submenu = 'Course.SubMenu.Student.Show';
                $submenuTitle = 'Students';
                $params = array('controller'=>'courses', 'userPersonalize'=>$userPersonalize,
                    'submenu'=>$submenu, 'submenuTitle'=>$submenuTitle, 'courseId'=>$rdAuth->courseId);
                echo $this->renderElement('courses/ajax_course_submenu', $params);
            ?>
            </td></tr>
            <tr><td>
            <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
              <tr>
                <td><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
              </tr>
            </table>
            <br /></td></tr>
             <tr><td >
             <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5"><tr>
                <td><?php echo $html->image('layout/corner_top_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_top_right.gif',array('align'=>'middle','alt'=>'right'))?></td></tr>
             </table>

             </td></tr>
             <tr><td>

            <?php
              $submenu = 'Course.SubMenu.Group.Show';
              $submenuTitle = 'Groups';
              $params = array('controller'=>'courses', 'userPersonalize'=>$userPersonalize, 'submenu'=>$submenu, 'submenuTitle'=>$submenuTitle, 'courseId'=>$rdAuth->courseId);
              echo $this->renderElement('courses/ajax_course_submenu', $params);?>
                 </td>
               </tr>
            </table>
            <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
              <tr>
                <td><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
              </tr>
            </table>
        </td>
        <!--
        <td valign="top">
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
            <tr>
              <td><?php echo $html->image('layout/corner_top_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
              <td align="right"><?php echo $html->image('layout/corner_top_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
            </tr>
          </table>
		      <table width="100%"  border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td>
          <?php
            $submenu = 'Course.SubMenu.Rubric.Show';
            $submenuTitle = 'Rubrics';
            $params = array('controller'=>'courses', 'userPersonalize'=>$userPersonalize, 'submenu'=>$submenu, 'submenuTitle'=>$submenuTitle, 'courseId'=>$rdAuth->courseId);
            echo $this->renderElement('courses/ajax_course_submenu', $params);

            $submenu = 'Course.SubMenu.SimpleEvals.Show';
            $submenuTitle = 'Simple Evaluations';
            $params = array('controller'=>'courses', 'userPersonalize'=>$userPersonalize, 'submenu'=>$submenu, 'submenuTitle'=>$submenuTitle, 'courseId'=>$rdAuth->courseId);
            echo $this->renderElement('courses/ajax_course_submenu', $params); ?>
      		    </td>
            </tr>
          </table>
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
            <tr>
              <td><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
              <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
            </tr>
          </table>
       </td>
       -->
       <td valign="top">
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
            <tr>
              <td><?php echo $html->image('layout/corner_top_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
              <td align="right"><?php echo $html->image('layout/corner_top_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
            </tr>
          </table>
		      <table width="100%"  border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td>
          <?php
            $submenu = 'Course.SubMenu.EvalEvents.Show';
            $submenuTitle = 'Evaluation Events';
            $params = array('controller'=>'courses', 'userPersonalize'=>$userPersonalize, 'submenu'=>$submenu, 'submenuTitle'=>$submenuTitle, 'courseId'=>$rdAuth->courseId);
            echo $this->renderElement('courses/ajax_course_submenu', $params);
        ?>

            </td></tr>
            <tr><td>
            <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
              <tr>
                <td><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
              </tr>
            </table>
            <br /></td></tr>
             <tr><td >
             <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5"><tr>
                <td><?php echo $html->image('layout/corner_top_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
                <td align="right"><?php echo $html->image('layout/corner_top_right.gif',array('align'=>'middle','alt'=>'right'))?></td></tr>
             </table>

             </td></tr>
             <tr><td>
        <?php
            if (!empty($access['SURVEY'])) {
              $submenu = 'Course.SubMenu.TeamMaker.Show';
              $submenuTitle = 'Surveys (Team Maker)';
              $params = array('controller'=>'courses', 'userPersonalize'=>$userPersonalize, 'submenu'=>$submenu, 'submenuTitle'=>$submenuTitle, 'courseId'=>$rdAuth->courseId);
              echo $this->renderElement('courses/ajax_course_submenu', $params);
            }
            ?>

      		    </td>
            </tr>
          </table>
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#E5E5E5">
            <tr>
              <td><?php echo $html->image('layout/corner_bot_left.gif',array('align'=>'middle','alt'=>'left'))?></td>
              <td align="right"><?php echo $html->image('layout/corner_bot_right.gif',array('align'=>'middle','alt'=>'right'))?></td>
            </tr>
          </table>
       </td>
      </tr>
    </table>

  </td>
</tr>
</table>
