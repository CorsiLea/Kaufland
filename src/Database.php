<?php
require_once("MyException.php");

/**
 * Class Database
 * 
 * Manage all the db calls
 */
abstract class Database 
{
    /**
     * @var SQLite3
     */
    private $_database;

    /**
     * Constructor
     */
    public function __construct($dbname) 
    {
        $this->connect($dbname);
    }

    abstract protected function connect($dbname);

    /**
     * escapeString escape a string to be able to add it in the db correctly
     */
    abstract public function escapeString($str);

    /**
     * _executeQuery execute the given query
     */
    abstract protected function _executeQuery($query);

    /**
     * tableExist       check if a given table exist
     * 
     * @param string $name  the name of the table
     * @return bool if the table exist
     */
    abstract public function tableExist($name) : bool;

    /**
     * checkCreateTable     create table if doesn't exist
     * 
     * @param string $name the name of the table
     */
    abstract public function checkCreateTable($name);

    /**
     * columnExist  check if a column exist
     * 
     * @param string $tableName     the name of the table
     * @param string $columnName    the name of the column
     * @param string $type          the type of the column
     * 
     * @return bool if exist
     */
    abstract public function columnExist($tableName, $columnName, $type) : bool;

    /**
     * checkCreateColumn    create the column if doesn't exist
     * 
     * @param string $tableName     the name of the table
     * @param string $columnName    the name of the column
     * @param string $type          the type of the column
     */
    abstract public function checkCreateColumn($tableName, $columnName, $type);

    /**
     * checkInsertValues        insert Values if not duplicates
     * 
     * @param string $tableName     the name of the table
     * @param array $queryColumns   an array with all the columns names
     * @param array $queryValues    an array with all the values
     */
    abstract public function checkInsertValues($tableName, $queryColumns, $queryValues);

    /**
     * toInsert        check if duplicates or param probolems
     * 
     * @param string $tableName     the name of the table
     * @param array $queryColumns   an array with all the columns names
     * @param array $queryValues    an array with all the values
     * 
     * @return bool data to be inserted
     */
    abstract public function toInsert($tableName, $queryColumns, $queryValues) : bool;

    /**
     * close the db connection
     */
    abstract public function close();

    /**
     * close the db at the end of the program
     */
    public function __destruct() 
    {
        $this->close();
    }
}

?>
