<?php
require_once("SQLiteDatabase.php");
require_once("MyException.php");

/**
 * Class Parser
 * 
 * In this class the xml file is parsed and the data inserted in the Database
 */
class Parser
{
    /**
     * @var SimpleXMLElement of the file
     */
    private $_xml;
    /**
     * @var bool use a given config or insert the data automatically
     */
    private $_config;
    /**
     * @var array [tableName]=>array(columns)
     */
    private $_tablesStructure;

    /**
     * @var Database
     */
    private $_db;

    /**
     * Constructor
     */
    public function __construct($filename, $useConfig, $tablesStructure = array()) 
    {
        $use_errors = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($filename);
        if (!$xml) {
            throw new MyException("Cannot load xml source", __CLASS__,__FUNCTION__,__LINE__, true);
        }
        libxml_clear_errors();
        libxml_use_internal_errors($use_errors);

        $this->_xml = $xml;
        $this->_config = $useConfig;
        $this->_tablesStructure = $tablesStructure;

        //check file has db
        if(!$this->_xml->getName())
        {
            new MyException("No DB defined", __CLASS__, __FUNCTION__, __LINE__, true);
        }
        // create DB - if more db option add the config and a switch or if
        $this->_db = new SQLiteDatabase($this->_xml->getName()); 
    }

    /**
     * parseXmlToDB define wich kind of parsing is needed
     */
    function parseXmlToDB() 
    {
        if ($this->_config) 
        {
            return $this->parseXMLToDB_config();
        }
        return $this->parseXMLToDB_auto();
    }

    /**
     * parseXMLToDB_auto
     * 
     * insert into db the xml data with the following scheme
     * <db><table><column>data</column> [...] </table> [...] </db>
     */
    function parseXMLToDB_auto()
    {
        foreach ($this->_xml->children() as $table) 
        {
            //check if table exist or create table
            $tableName = $table->getName();
            $this->_db->checkCreateTable($tableName);

            //query column preparation
            $queryColumns = array();
            //query values preparation
            $queryValues = array();

            foreach ($table->children() as $column => $value) 
            {
                array_push($queryColumns,$column);
                array_push($queryValues,"'" . $this->_db->escapeString($value->__toString()) . "'");
                //check if column exist or add it
                $this->_db->checkCreateColumn($tableName, $column, "text");
            }
            // insert values to table
            $this->_db->checkInsertValues($tableName, $queryColumns, $queryValues);
        }
    }

    /**
     * parseXMLToDB_config
     * 
     * insert into db only the configured xml data
     * <db><table><column>data</column> [...] </table> [...] </db>
     */
    function parseXMLToDB_config() 
    {
        foreach ($this->_xml->children() as $table) 
        {
            //check if table exist or create table
            $tableName = $table->getName();
            
            // only insert designated tables
            if(isset($this->_tablesStructure[$tableName]))
            {
                $tableStructure = $this->_tablesStructure[$tableName];
                $this->_db->checkCreateTable($tableName);

                //query column preparation
                $queryColumns = array();
                //query values preparation
                $queryValues = array();

                foreach ($table->children() as $column => $value) 
                {
                    //add only designated column
                    if(in_array($column, $tableStructure))
                    {
                        array_push($queryColumns,$column);
                        array_push($queryValues,"'" . $this->_db->escapeString($value->__toString()) . "'");
                        //check if column exist or add it
                        $this->_db->checkCreateColumn($tableName, $column, "text");

                    }
                }
                // insert values to table
                $this->_db->checkInsertValues($tableName, $queryColumns, $queryValues);
            }
        }
    }
    
    /**
     * Destructor
     */
    public function __destruct() 
    {
    }
}

?>