<?php
/**
 * Tests for Isa's scope functionality
 * @author Ross Tuck
 * @package Isa
 */
class Isa_ScopeTest extends Isa_TestCase {

    public function setUp() {
        $this->_tablePerson = new Table_Person();
    }

    public function testScopeAsStatic() {
        $men = $this->_tablePerson->isMale();
        $this->assertEquals(2, count($men));
    }

    public function testScopeAsPublicFunction() {
        $men = $this->_tablePerson->isMale();
        $this->assertEquals(2, count($men));
    }

    /**
     * @expectedException Isa_Exception
     */
    public function testNonexistantScopeThrowsException() {
        $this->_tablePerson->isZombie();
    }
    
    /**
     * @expectedException Isa_Exception
     */
    public function testNonexistantFunction() {
        $this->_tablePerson->getTwitterName();
    }

    
    public function testScopeBuilderWithOneScope() {
        $this->_isTheQueen(
            $this->_tablePerson->is('female')
        );
    }
    
    public function testScopeBuilderWithMultipleScopes() {
        $this->_isTheQueen(
            $this->_tablePerson->is('leader', 'female')
        );
    }
    
    public function testScopeWithParameter() {
        $women = $this->_tablePerson->isOfGender('f');
        $this->_isTheQueen($women);
    }
    
    public function testStaticScopeWithParameter() {
        $women = $this->_tablePerson->isOfGender('f');
        $this->_isTheQueen($women);
    }

    public function testScopeBuilderWithParameter() {
        $men = $this->_tablePerson->is('leader', array('ofGender', 'm'));

        $honestAbe = $men[0];
        $this->assertEquals(1, $men->count());
        $this->assertEquals($honestAbe->name, 'Abe Lincoln');
    }
    
    public function testConflictingScopesGivesEmptyResult() {
        $noone = $this->_tablePerson->is('male', 'female');
        $this->assertEquals(0, $noone->count());
    }

    public function testNestedScopes() {
        $leaders = $this->_tablePerson->isNestedScopeTest();

        $this->assertEquals(2, $leaders->count());
        $this->assertEquals($leaders[0]['name'], 'Abe Lincoln');
        $this->assertEquals($leaders[1]['name'], 'Queen Elizabeth');
    }
    
    public function testCompositeScope() {
        $maleLeaders = $this->_tablePerson->isMaleLeader();
        $this->assertEquals($maleLeaders[0]['name'], 'Abe Lincoln');
    }
    
    protected function _isTheQueen($women) {
        $this->assertEquals(1, $women->count());

        $queen = $women[0];
        $this->assertEquals($queen->name, 'Queen Elizabeth');
    }
}
