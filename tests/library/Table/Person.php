<?php
/**
 * Sample table class, used in our tests
 * @author Ross Tuck
 * @package Isa
 */
class Table_Person extends Isa_TableAbstract {

    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    protected $_name = 'person';

    protected function _isDefaultScope() {
        $this->scope()->where('human = ?', 1);
    }

    protected function _isMale() {
        $this->scope()->where('gender = ?', self::GENDER_MALE);
    }

    protected function _isFemale() {
        $this->scope()->where('gender = ?', self::GENDER_FEMALE);
    }

    protected function _isLeader() {
        $this->scope()->where('leader = ?', 1);    
    }

    protected function _isOfGender($gender) {
        $this->scope()->where('gender = ?', strtolower($gender));
    }

    protected function _isNestedScopeTest() {
        $ross = $this->isJerk();
        $this->scope()->where('id != ?', $ross[0]['id']);
    }
    
    protected function _isJerk() {
        $this->isMale();//unused, just to stretch the test

        $this->_addScope('male');
        $this->scope()->where('birthdate = ?', '1984-11-10'); //is ross
    }

    protected function _isMaleLeader() {
        $this->_addScope(array('ofGender', self::GENDER_MALE));
        $this->_addScope('leader');
    }
}
