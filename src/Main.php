<?php
require_once("MyException.php");
require_once("Parser.php");

/**
 * Class Main
 * 
 * This is the class launched from the cmd.
 * It will manage the input/output of the user and call other classes to work with the data
 */
class Main
{

    /**
     * Constructor 
     * 
     * Manage the arguments and launch the parsing
     */
    public function __construct() 
    {
        $filename = $this->getFileName();
        $tableStructure = $this->getConfig();
        $config = !empty($tableStructure);
        $parser = new Parser($filename, $config, $tableStructure);
        $parser->parseXmlToDB();
    }

    /**
     * getFileName      get the filename of the data to collect from the cmd args
     * 
     * @return String $filename     the path of the existing file
     */
    function getFileName() : String 
    {
        // Check if command line arguments are provided
        if ($_SERVER['argc'] < 2) 
        {
            new MyException("No arguments provided", __CLASS__, __FUNCTION__, __LINE__, true);
        }

        // Extract command line arguments
        $arguments = array_slice($_SERVER['argv'], 1);
        //get file if exist
        $filename = $arguments[0];
        if(file_exists($filename))
        {
            return $filename;
        }
        else
        {
            new MyException("The file doesn't exist {$filename}", __CLASS__, __FUNCTION__, __LINE__, true);
        }
    }

    /**
     * getConfig      get the table configuration from the cmd args
     * 
     * @return array      the structure => array["tablename"]=>(array columns)
     */
    function getConfig() : array 
    {
        $tableStructure = [];
        // Check if command line arguments are provided
        if ($_SERVER['argc'] < 3) 
        {
            return $tableStructure;
        }
        if($_SERVER['argc']%2 != 0)
        {
            new MyException("The arguments count is wrong. The call should be\n php file.php file.xml <tablename> <columns> [<tablename> <columns> ...]", __CLASS__, __FUNCTION__, __LINE__, true);
        }

        for ($i=2; $i < $_SERVER['argc']; $i=$i+2) 
        { 
            $tableStructure[$_SERVER['argv'][$i]] = explode(",", $_SERVER['argv'][$i+1]);
        }
        return $tableStructure;
    }

    /**
     * Destructor
     */
    public function __destruct() 
    {
    }
}

new Main();

?>