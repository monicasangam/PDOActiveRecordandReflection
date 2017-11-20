<?php
ini_set('display_errors', on);
error_reporting(E_ALL);

define('DATABASE','ms792' );
define('USERNAME', 'ms792');
define('PASSWORD', 'bSzrOJUh');
define('CONNECTION', 'sql1.njit.edu');


class dbConnection
{
    protected static $database;

    private function __construct()
    {
        try
        {
            self::$database = new PDO('mysql:host=' . CONNECTION . ';dbName=' . DATABASE, USERNAME, PASSWORD);
            self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo "Connection error: ". $e->getMessage();
        }
    }

    public static function getConnection()
    {
        if(!self::$database)
        {
            new dbConnection();
        }
	    self::$database->query("use ms792");

        return self::$database;
    }
}

class collection
{
    protected $model;

    public function __construct($modelName)
    {
        $this->model = $modelName;
    }

    public function findAll()
    {
        $database = dbConnection::getConnection();
        $sql = 'SELECT * FROM ' . $this->model->tableName;
	    $statement = $database->prepare($sql);
	    $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS,$this->model);
        $recordsSet = $statement->fetchAll();
        return $recordsSet;
    }

    public function findOne($id)
    {
        $database = dbConnection::getConnection();
        $sql = 'SELECT * FROM '. $this->model->tableName .' where id= '.$id;
        $statement = $database->prepare($sql);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS,$this->model);
        $recordSet = $statement->fetchAll();
        return $recordSet;
    }
}
class accounts extends collection
{
    public function __construct($modelName)
    {
        $this->model = 'accounts';
    }
}

class todos extends collection
{
    public function __construct($modelName)
    {
        $this->model = 'todos';
    }
}

class model
{
    var $tableName;
    var $columnNames;

    public function __construct()
    {
        $this->tableName = 'accounts';
        $this->columNames = array ("email","fname","lname","phone","birthday","gender","password");
        echo("1 test values for columnNames = " . $this->columnNames."<br>");
    }

    public function save($id, $columnValues)
    {
        //if($id='')
        //{
           $sql = $this->insert($columnValues);
        //}
        //else
        //{
        //    $sql = $this->update($id, $columnValues);
       // }
        $database = dbConnection::getConnection();
        $statement = $database->prepare($sql);
        $statement->execute($sql);
        $last_id = $database->lastInsertId();

        echo ("New record created successfully.Last inserted ID is: ". $database->lastInsertId(). "<br>");

        return $last_id;

    }

    public function insert($columnValues)
    {
        $insertSql = "INSERT INTO ".$this->tableName." (";

        for($index=0 ; $index < 7;$index=$index+1) {
            $columnName=$this->columNames[$index];
            if($index==0)
                $insertSql = $insertSql . $columnName;
            else
                $insertSql = $insertSql . "," . $columnName;
        }

        $insertSql = $insertSql. ") values (";

        for($index=0 ; $index < 7;$index=$index+1) {
            $columnValue = "'".$columnValues[$index]."'";
            if($index==0)
                $insertSql = $insertSql .  $columnValue;
            else
                $insertSql = $insertSql . "," . $columnValue;
        }
        $insertSql = $insertSql . ")";
        echo("insertSql 4".$insertSql."<br>");
        return $insertSql;
    }

    public function updateAll($id, $columnValues)
    {
        update($id, $this->columnNames, $columnValues);
    }

    public function update($id, $columnNames, $columnValues)
    {
        $updateSql = "Update ".$this->tableName." set ";

        for($index = 0; $index < sizeof($this->columnNames);$index=$index+1) {
            if($this->columnValues[$index] == is_empty) {
                if ($index = 0) {
                    $updateSql = $updateSql + $this->columnNames[$index] + " = " + $this->columnValues[$index];
                } else {
                    $updateSql = $updateSql + "," + $this->columnNames[$index] + " = " + $this->columnValues[$index];
                }
            }
        }
        $updateSql = $updateSql + " where id = "+ $this->id;

    }

    public function delete($id)
    {
        $database = dbConnection::getConnection();
        $sql = "DELETE FROM ".$this->tableName." where id= ".$id;
        echo 'Record deleted successfully from accounts table.'."<br>";

    }
}
class account extends model
{
    public function __construct()
    {
        $this->tableName = 'accounts';
        $this->columNames = array ("email","fname","lname","phone","birthday","gender","password");
        echo("0 test values for columnNames = " . $this->columnNames ."<br>");
    }

}

class todo extends model
{
    public function __construct()
    {
        $this->tableName = 'todos';
        $this->columNames = array ("ownerEmail","ownerId","createdDate","dueDate","message");
    }
}

class htmlTable
{
    public function makeTable($data)
    {
        echo '<table>';

        foreach ($data as $data)
        {
            echo "<tr>";

            foreach ($data as $column) {

                echo "<td>$column</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}


$account1 = new account();
$account1ColumnValues = array ("peter@njit.com","Peter","Smith","212-555-1212","03-MAY-10","Male","abc123");
$id1 = $account1->save("", $account1ColumnValues);
print("id1 = ".$last_id."<br>");

$account1ColumnValues = array ("sam@njit.com","Sam","Jung","609-555-1212","03-APR-12","Male","pqr345");
$id2 = $account1->save("",$account1ColumnValues);
print("id2 = ".$last_id."<br>");

$account1ColumnValues = array ("carol@njit.com","Carol","Barley","212-555-1212","03-DEC-11","Female","aaa123");
$id3 = $account1->save("",$account1ColumnValues);
print("id3 = ".$last_id."<br>");

$account1ColumnValues = array ("param@njit.com","Param","Singh","212-555-3333","03-JAN-10","Male","aaabb123");
$id4 = $account1->save("",$account1ColumnValues);
print("id4 = ".$last_id."<br>");

/*
$account1ColumnValues = array ("peter1@njit.com","Peter1","Smith1","212-555-1111","03-MAY-10","Male","abc123");
$account1->updateAll($id1, $account1ColumnValues);

$account1ColumnNames = array ("email","fname","lname","phone");
$account1ColumnValues = array ("sam1@njit.com","Sam1","Jung1","212-555-2222");
$account1->update($id2, $account1ColumnNames, $account1ColumnValues);

$account1->delete($id3);

$accounts = new accounts($acount1);

$records = $accounts->findAll();
print($records);

$record = $accounts->findOne($id1);
print($record);

$record = $accounts->findOne($id2);
print($record);

$record = $accounts->findOne($id3);
print($record);

$record = $accounts->findOne($id4);
print($record);
*/




