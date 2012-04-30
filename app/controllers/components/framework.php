<?php
/* SVN FILE: $Id: framework.php 727 2011-08-30 19:34:58Z john $ */
/*
 * rdAuth Component for ipeerSession
 *
 * @author      gwoo <gwoo@rd11.com>
 * @version     0.10.5.1797
 * @license		OPPL
 *
 */
class frameworkComponent
{
	var $components = array('sysContainer');

  /**
   * validateUploadFile() - validates the uploaded file format
   *
   * @var $uses
   */
  function validateUploadFile($tmpFile, $filename, $uploadFile) {
    $result = true;
  	$fileParts = pathinfo('dir/' . $filename);
    if (empty($fileParts['extension'])) {
      $result = __("No filename extension. Must be csv.", true);
      return $result;
    }
    //echo "tem file is ".$tmpFile.' file name is  '.$fileName.' upload file is '.$uploadFile.'<br>';
  	$fileExtension = strtolower($fileParts['extension']); 
  	if ($fileExtension == 'txt' || $fileExtension == 'csv')  {
  		if (!move_uploaded_file($tmpFile, $uploadFile)) {
  			$result = __("Error reading file", true);
  		}
  	} else {
  		$result = __("iPeer does not support the file type '.", true) . $fileExtension .
  					 __("'. Please use only text files (.txt) or comma seperated values files (.csv).", true);
  	}
  	return $result;
  }

  // returns the difference between two times
  function getTimeDifference($t1, $t2, $format='days') {
  	$seconds = strtotime($t1) - strtotime($t2);
  	$minutes = $seconds / 60;
  	$hours = $minutes / 60;
  	$days = $hours / 24;

  	if ($format == __('days', true)) {
  		return $days;
  	}
  	else if ($format == __('hours', true)) {
  		return $hours;
  	}
  	else if ($format == __('minutes', true)) {
  		return $minutes;
  	}
  	else if ($format == __('seconds', true)) {
  		return $seconds;
  	}
  	else {
  		return 0;
  	}
  }

  // returns the current date and time, in the format to be stored in the database
  function getTime($t=0, $f='Y-m-d H:i:s') {
  	if ($t == 0) {
  		$t = time();
  	}
  	return date($f, $t);
  }

  function getUser($userId) {
    
    $this->User = new User;
    return ($this->User->find('id = '.$userId));
  }
}
