<?php
// $Id: socket_test.php 449 2011-02-21 22:52:09Z compass $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../socket.php');
Mock::generate('SimpleSocket');

class TestOfSimpleStickyError extends UnitTestCase {
    
    function testSettingError() {
        $error = new SimpleStickyError();
        $this->assertFalse($error->isError());
        $error->_setError('Ouch');
        $this->assertTrue($error->isError());
        $this->assertEqual($error->getError(), 'Ouch');
    }
    
    function testClearingError() {
        $error = new SimpleStickyError();
        $error->_setError('Ouch');
        $this->assertTrue($error->isError());
        $error->_clearError();
        $this->assertFalse($error->isError());
    }
}
?>