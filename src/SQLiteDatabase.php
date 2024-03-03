<?php
require_once("Database.php");
require_once("MyException.php");

/**
 * Class Database
 * 
 * Manage all the db calls
 */
class SQLiteDatabase extends Database 
{

    /**
     * 
     */
    public function __construct($dbname) 
    {
        parent::__construct($dbname);
    }

    /**
     * connect  connect to the db
     * 
     * @dbname string name of database
     */
    protected function connect($dbname)
    {
        $this->_database = new SQLite3($dbname);
        if (!$this->_database) 
        {
            throw new MyException("Impossible to create the database");
        }
    }

    /**
     * escapeString escape a string to be able to add it in the db correctly
     */
    public function escapeString($str)
    {
        return $this->_database->escapeString($str);
    }

    /**
     * _executeQuery execute the given query
     */
    protected function _executeQuery($query) 
    {
        return $this->_database->query($query);
    }

    /**
     * tableExist       check if a given table exist
     * 
     * @param string $name  the name of the table
     * @return bool if the table exist
     */
    public function tableExist($name) : bool
    {
        $query = "SELECT count(*) as c FROM sqlite_master WHERE type='table' AND name='{$name}';";
        $res = $this->_executeQuery($query);
        if(!$res)
        {
            new MyException("Error selecting from db : query {$query}", __CLASS__, __FUNCTION__, __LINE__, false);
            return false;
        }
        return $res->fetchArray()["c"] > 0;
    }

    /**
     * checkCreateTable     create table if doesn't exist
     * 
     * @param string $name the name of the table
     */
    public function checkCreateTable($name) 
    {
        if(!$this->tableExist($name))
        {
            $query = "CREATE TABLE {$name} (id INTEGER PRIMARY KEY AUTOINCREMENT);";
            $res = $this->_executeQuery($query);
            if(!$res)
            {
                new MyException("Error creating table : query {$query}", __CLASS__, __FUNCTION__, __LINE__, false);
            }
        }
    }

    /**
     * columnExist  check if a column exist
     * 
     * @param string $tableName     the name of the table
     * @param string $columnName    the name of the column
     * @param string $type          the type of the column
     * 
     * @return bool if exist
     */
    public function columnExist($tableName, $columnName, $type) : bool
    {
        $query = "SELECT sql FROM sqlite_master WHERE type='table' AND name='{$tableName}';";
        $res = $this->_executeQuery($query);
        if(!$res)
        {
            new MyException("Error selecting from db : query {$query}", __CLASS__, __FUNCTION__, __LINE__, false);
        }
        $sql = $res->fetchArray()["sql"];
        $substr = " " . strval($columnName) . " " . $type;
        return strpos(strval($sql), $substr) !== false;
    }

    /**
     * checkCreateColumn    create the column if doesn't exist
     * 
     * @param string $tableName     the name of the table
     * @param string $columnName    the name of the column
     * @param string $type          the type of the column
     */
    public function checkCreateColumn($tableName, $columnName, $type) 
    {
        if(!$this->columnExist($tableName, $columnName, $type))
        {
            $query = "ALTER TABLE {$tableName} ADD COLUMN {$columnName} {$type}";
            $res = $this->_executeQuery($query);
            if(!$res)
            {
                new MyException("Error altering table : query {$query}", __CLASS__, __FUNCTION__, __LINE__, false);
            }
        }
    }

    /**
     * checkInsertValues        insert Values if not duplicates
     * 
     * @param string $tableName     the name of the table
     * @param array $queryColumns   an array with all the columns names
     * @param array $queryValues    an array with all the values
     */
    public function checkInsertValues($tableName, $queryColumns, $queryValues)
    {
        if ($this->toInsert($tableName, $queryColumns, $queryValues))
        {
            $insertQuery = "INSERT INTO " . $tableName . " (" . implode(",",$queryColumns) . ") VALUES (" . implode(",",$queryValues) . ");";
            $res = $this->_executeQuery($insertQuery);
            if(!$res)
            {
                new MyException("Error inserting data : query {$insertQuery}", __CLASS__, __FUNCTION__, __LINE__, false);
            }
        }
    }

    /**
     * toInsert        check if duplicates or param probolems
     * 
     * @param string $tableName     the name of the table
     * @param array $queryColumns   an array with all the columns names
     * @param array $queryValues    an array with all the values
     * 
     * @return bool data to be inserted
     */
    public function toInsert($tableName, $queryColumns, $queryValues) : bool
    {
        // prepare select query to check if duplicate
        $selectQuery = "SELECT " . implode(",",$queryColumns) . " FROM ". $tableName . " WHERE 1=1 ";
        if (count($queryColumns) == count($queryValues)) 
        {
            for ($i=0; $i < count($queryColumns); $i++) { 
                $selectQuery .= " AND " . $queryColumns[$i] . " like " . $queryValues[$i];
            }
        }
        else
        {
            new MyException("The columns number doesn't match the value count : col {$queryColumns} val {$queryValues}", __CLASS__, __FUNCTION__, __LINE__, false);
            return false;
        }

        $res = $this->_executeQuery($selectQuery);
        if (!$res) {
            new MyException("Error selecting from db : query {$selectQuery}", __CLASS__, __FUNCTION__, __LINE__, false);
            return false;
        }
        $fetch = $res->fetchArray();

        return empty($fetch);
    }

    /**
     * close the db connection
     */
    public function close() 
    {
        $this->_database->close();
    }

    /**
     * close the db at the end of the program
     */
    public function __destruct() 
    {
        $this->close();
    }
}

?>
