<?php
require_once(__DIR__ . "/../src/Parser.php");
require_once(__DIR__ . "/../src/MyException.php");


/**
 * Class Test
 * 
 * used to test the program
 */
class Test
{

    /**
     * Constructor
     * 
     * used to call all the test cases and count the results
     */
    public function __construct() 
    {
        $count = 0;
        $total = 0;
        $failed = "";
        $this->trackTest("testStandardInsertTotal", $count, $failed, $total);
        $this->trackTest("testStandardInsert", $count, $failed, $total);
        $this->trackTest("testDifferentColumns", $count, $failed, $total);
        $this->trackTest("testDifferentsTables", $count, $failed, $total);
        $this->trackTest("testErrorLog", $count, $failed, $total);
        echo $count . "/" . $total . "\n";
        echo "Failed : " . $failed . "\n";

    }

    private function trackTest($name, &$count, &$failed,  &$total)
    {
        $total++;
        if(call_user_func(array($this,$name)))
        {
            $count++;
        } 
        else
        {
            $failed .= $name . ", ";
        }   
    }

    /**
     * testStandardInsertTotal 
     * 
     * check that the total number of lines is always 3. No matter how many times we parse it
     */
    private function testStandardInsertTotal() : bool
    {
        $passed = true;
        $xmlParser = new Parser(__DIR__ . "/../TestData/standardInsert.xml", false);
        $xmlParser->parseXmlToDB();
        $xmlParser->parseXmlToDB();
        $xmlParser->parseXmlToDB();

        $db = new SQLite3("db_test_1");

        //check the total number of lines
        $query = "SELECT COUNT(*) AS C FROM item";
        $res = $db->query($query);
        $passed &= $res->fetchArray()["C"] == 3;
        return $passed;
    }

    /**
     * testStandardInsert
     * 
     * for each element in our XML file check it is actually inserted
     */
    private function testStandardInsert() : bool
    {
        $passed = true;
        $xmlParser = new Parser(__DIR__ . "/../TestData/standardInsert.xml", false);
        $xmlParser->parseXmlToDB();

        $db = new SQLite3("db_test_1");

        //check the 3 entry
        $query = "SELECT COUNT(*) AS C FROM item WHERE sku like '20' and Instock like 'Yes' and Facebook like '1' and IsKCup like '0'";
        $res = $db->query($query);
        $passed &= $res->fetchArray()["C"] == 1;
        $query2 = "SELECT COUNT(*) AS C FROM item WHERE sku like '5000081171' and Instock like 'Yes' and Facebook like '1' and IsKCup like '0'";
        $res2 = $db->query($query2);
        $passed &= $res2->fetchArray()["C"] == 1;
        $query3 = "SELECT COUNT(*) AS C FROM item WHERE sku like '7602C' and Instock like 'Yes' and Facebook like '0' and IsKCup like '1'";
        $res3 = $db->query($query3);
        $passed &= $res3->fetchArray()["C"] == 1;
    
        return $passed;
    }
    
    /**
     * testDifferentColumns
     * 
     * test that all the elements are inserted with all the columns
     */
    private function testDifferentColumns() : bool
    {
        $passed = true;
        $xmlParser = new Parser(__DIR__ . "/../TestData/moreColumns.xml", false);
        $xmlParser->parseXmlToDB();

        $db = new SQLite3("db_test_2");

        //check the 3 entry
        $query = "SELECT COUNT(*) AS C FROM item WHERE sku like '20' and Instock like 'Yes' and IsKCup like '0'";
        $res = $db->query($query);
        $passed &= $res->fetchArray()["C"] == 1;
        $query2 = "SELECT COUNT(*) AS C FROM item WHERE sku like '5000081171' and Instock like 'Yes' and Facebook like '1' and IsKCup like '0'";
        $res2 = $db->query($query2);
        $passed &= $res2->fetchArray()["C"] == 1;
        $query3 = "SELECT COUNT(*) AS C FROM item WHERE sku like '7602C' and Facebook like '0' and IsKCup like '1'";
        $res3 = $db->query($query3);
        $passed &= $res3->fetchArray()["C"] == 1;
    
        return $passed;
    }

    
    /**
     * testDifferentsTables
     * 
     * test that the elements are inserted in differents tables
     */
    private function testDifferentsTables() : bool
    {
        $passed = true;
        $xmlParser = new Parser(__DIR__ . "/../TestData/moreTables.xml", false);
        $xmlParser->parseXmlToDB();

        $db = new SQLite3("db_test_3");

        //check the total number of lines for both tables
        $query = "SELECT COUNT(*) AS C FROM item";
        $res = $db->query($query);
        $passed &= $res->fetchArray()["C"] == 2;
        $query1 = "SELECT COUNT(*) AS C FROM abc";
        $res1 = $db->query($query1);
        $passed &= $res1->fetchArray()["C"] == 1;
        return $passed;
    
    }

    /**
     * testErrorLog
     * 
     * test that the elements are inserted in differents tables
     */
    private function testErrorLog() : bool
    {
        $filePath = __DIR__."/../Log/error_log";
        if (file_exists($filePath)) 
        {
            unlink($filePath);
        }
        $e = new MyException("Test exception", __CLASS__,__FUNCTION__,__LINE__,false);
        if (file_exists($filePath)) 
        {
            $fileContent = file_get_contents($filePath);
            if (strpos($fileContent, "Test exception") !== false)
            {
                return true;
            }
        }
        return false;
    
    }
}

new Test();
?>