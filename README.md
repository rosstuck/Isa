# What is it?
Isa is an odd little extension for Zend_Db_Table that offers named scopes.

# How do I use it?
Toss vendor/Isa in your vendor folder and have your table classes extend from
Isa_TableAbstract. Isa expects a PSR-0 style autoloader (PEAR, Zend, etc).

## Define a scope
Scopes are just defined as methods within your table class. There are two rules:

1. Follow the naming convention "_is<ScopeName>".
2. Within your scope, use the scope() method instead of Zend_Db_Table's select()

A simple example:

    class Table_Person extends Isa_TableAbstract {
        protected function _isLeader() {
            $this->scope()->where('leader = ?', 1);    
        }
        
        protected function _isFemale() {
            $this->scope()->where('gender = ?', 'f');
        }
    }

## Use a scope
The easiest way to fetch records is Isa's overloading of __call.

    $tablePerson = new Table_Person();
    $tablePerson->isLeader(); //returns a rowset with all leaders
    
You can also use the is() function, like so:

    $tablePerson->is('leader');

## Chain scopes
The is() function can also merge scopes together:

    $tablePerson->is('leader', 'female'); //returns all female leaders

## Scopes with params
Scope functions can also have parameters:

    //In your table class:
    protected function _isOfGender($gender) {
        $this->scope()->where('gender = ?', $gender);
    }
    
    //When you use it singly:
    $tablePerson->isOfGender('f');
    
The syntax changes a bit when using the is() function:

    //The scope name is the first element of the array
    $tablePerson->is(array('isOfGender', 'm'));
    
This isn't so useful when using one scope, but it's more for composing several:

    //Fetches all female zombie leaders
    $tablePerson->is('leader', 'zombie', array('ofGender', 'f'));

## Default scope
If you define a scope called "_isDefaultScope", it will be automatically applied
to every scope query you make. The main use cases are invariants like soft
deletes, for instance.

# Todo
- Accept scopes in a single array (applying scopes as filters in a search)
- Optionally return the select object instead of fetching
- Formalize composite scopes
- ?Cross table support for relations
- ?Move the scoping into an extended select object

# About
Thanks to Michiel van Leening and Jan Wolters.
