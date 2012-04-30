<?php
/* SVN FILE: $Id: evaltools_controller.php 727 2011-08-30 19:34:58Z john $ */

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
 * @lastmodified $Date: 2006/07/17 18:38:41 $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Controller :: Users
 *
 * Enter description here...
 *
 * @package
 * @subpackage
 * @since
 */
class EvaltoolsController extends AppController
{
/**
 * This controller does not use a model
 *
 * @var $uses
 */
  var $uses =  array('SimpleEvaluation', 'Rubric', 'Mixeval', 'Survey', 'EmailTemplate');
  var $page;
  var $Sanitize;
  var $functionCode = 'EVAL_TOOL';

  function __construct() {
    $this->Sanitize = new Sanitize;
    $this->set('title_for_layout', __('Evaluation Tools', true));
    parent::__construct();
  }

  function index($evaltool = null) {
    //Disable the autorender, base the role to render the custom home
    $this->autoRender = false;

    //General Evaluation Tools Rendering for Admin and Instructor
    switch ($evaltool) {
      case "simpleevaluations" :
        $this->redirect('/simpleevaluations/index/');
      break;

      case "rubrics" :
        $this->redirect('/rubrics/index/');
      break;

      case "surveys" :
        $this->redirect('/surveys/index/');
      break;

      case "emailtemplates" :
        $this->redirect('/emailtemplates/index/');
      break;

      default:
        $this->showAll();
        $this->render('index');
      break;
    }
  }

  function showAll() {
    $simpleEvalData = $this->SimpleEvaluation->find('all', array('conditions' => array('creator_id' => $this->Auth->user('id')), 'callbacks' => false));
    $this->set('simpleEvalData', $simpleEvalData);

    $rubricData = $this->Rubric->find('all', array('conditions' => array('creator_id' => $this->Auth->user('id'))));
    $this->set('rubricData', $rubricData);

    $mixevalData = $this->Mixeval->find('all', array('conditions' => array('creator_id' => $this->Auth->user('id'))));
    $this->set('mixevalData', $mixevalData);

    $surveyData = $this->Survey->find('all', array('conditions' => array('Survey.creator_id' => $this->Auth->user('id')),
                                                   'contain' => array('Course')));
    $this->set('surveyData', $surveyData);

    $emailTemplates = $this->EmailTemplate->getMyEmailTemplate($this->Auth->user('id'));
    $this->set('emailTemplates', $emailTemplates);
  }
}
?>
