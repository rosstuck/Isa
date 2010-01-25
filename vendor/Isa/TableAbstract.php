<?php
/**
 * Isa is an odd little extension for Zend_Db_Table that offers named scopes.
 * @author Ross Tuck
 * @package Isa
 */
class Isa_TableAbstract extends Zend_Db_Table_Abstract {
    
    /**
     * Stack holding the current list of selects we're creating
     * @var array
     */
    protected $_scopeStack = array();

    /**
     * Accepts a list of scopes and returns them as a db result. 
     *
     * Syntax examples:
     *  -is('popular')                           |Single scope
     *  -is('popular', 'smart')                  |Multiple scopes
     *  -is('popular', array('tallerThan', 150)) |With arguments
     *
     * @see __call()
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function is() {
        $this->_lockScope();
        
        $this->_addScope('defaultScope');
        foreach(func_get_args() as $scope) {
            $this->_addScope($scope);
        }

        $select = $this->_releaseScope();
        return $this->fetchAll($select);
    }
    
    /**
     * Add a single scope to the current chain in is() syntax form.
     * @param string|array $name String name of scope or array with scope name
     *                           as first element and args following.
     * @see is()
     * @return self Returns self for method chaining
     */
    protected function _addScope($name) {
        //Break scopes with arguments into separate pieces
        $args = array();
        if(is_array($name)) {
            $args = $name;
            $name = array_shift($args);
        }
    
        //Convert the scope name to the method name
        $funcName = '_is'.$name;
        if(!method_exists($this, $funcName)) {
            throw new Isa_Exception("Scope '$name' does not exist");
        }

        call_user_func_array(array($this, $funcName), $args);
        return $this;
    }

    /**
     * Gets or creates a select object for use in defining a scope. You must use 
     * this instead of $this->select() in your _is* function.
     * @return Zend_Db_Table_Select
     */
    protected function scope() {
        $scope = end($this->_scopeStack);
        if(!($scope instanceof Zend_Db_Table_Select)) {
            throw new Exception('Can not call scope() without calling _lockScope first.');
        }

        return $scope;
    }

    /**
     * Create a new select query to begin attaching scopes to.
     * @return Isa_TableAbstract Returns self for method chaining
     */
    protected function _lockScope() {
        $this->_scopeStack[] = $this->select();
        return $this;
    }
    
    /**
     * Unlocks the current chain of scopes and returns them as a query object.
     * @return Zend_Db_Table_Select
     */
    protected function _releaseScope() {
        return array_pop($this->_scopeStack);
    }


    /**
     * Placeholder for a real default scope, if the user so desires.
     */
    protected function _isDefaultScope() {
    }

    /**
     * Maps stray calls to scope functions.
     * @param string $name
     * @param array $args
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function __call($name, $args) {
        if(strtolower(substr($name, 0, 2)) === 'is') {
            //Unshifting name sets it as the scope. See is(), example 3.
            array_unshift($args, substr($name, 2));
            return $this->is($args);
        }
        
        throw new Isa_Exception("Unrecognized method '$name'");
    }
}
