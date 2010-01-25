<?php
 /**
 * Ugly setup code lifted straight from the Zend manual
 * @author Ross Tuck
 * @package Isa
 */
abstract class Isa_TestCase extends Zend_Test_PHPUnit_DatabaseTestCase {
    private $_connectionMock;

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection() {
        if($this->_connectionMock == null) {
            $connection = Zend_Db_Table_Abstract::getDefaultAdapter();
            $this->_connectionMock = $this->createZendDbConnection(
                $connection, 'zfunittests'
            );
            Zend_Db_Table_Abstract::setDefaultAdapter($connection);
        }
        return $this->_connectionMock;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet() {
        return $this->createFlatXmlDataSet(
            __DIR__.'/../../data/people.xml'
        );
    }
}
