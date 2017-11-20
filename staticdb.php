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
    private $model;

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

        //echo("model = ".get_class($this->model)."<br>");

        $statement->setFetchMode(PDO::FETCH_CLASS,get_class($this->model));
        $recordsSet = $statement->fetchAll();
        return $recordsSet;
    }

    public function findOne($id)
    {
        $database = dbConnection::getConnection();
        $sql = 'SELECT * FROM '. $this->model->tableName .' where id= '.$id;
        $statement = $database->prepare($sql);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS,get_class($this->model));
        $recordSet = $statement->fetchAll();
        return $recordSet;
    }
}
class accounts extends collection
{
    public function __construct($modelName)
    {
        $this->model = $modelName;
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
        $this->columnNames = array ("email","fname","lname","phone","birthday","gender","password");
        echo("1 test values for columnNames = " . $this->columnNames."<br>");
    }

    public function save($id, $columnValues)
    {
        $database = dbConnection::getConnection();
        if($id=='')
        {
            $sql = $this->insert($columnValues);

            $database->beginTransaction();
            $statement = $database->prepare($sql);

            echo("sql=".$sql."<br>");
            $statement->execute();
            $last_id = $database->lastInsertId();
            $database->commit();

            echo("last_id = " .$last_id."<br>");
            return $last_id;
        }
        else
        {
            $database->beginTransaction();
            $sql = $this->updateAll($id,$columnValues);
            $statement = $database->prepare($sql);
            $database->exec($statement);
            //$statement->execute();
            $database->commit();
        }

    }

    public function insert($columnValues)
    {
        echo ("columnName in insert = ".$this->columnNames[0]. "<br>");
        $insertSql = "INSERT INTO ".$this->tableName." (";

        for($index=0 ; $index < sizeof($this->columnNames);$index=$index+1) {
            $columnName=$this->columnNames[$index];
            if($index==0)
                $insertSql = $insertSql . $columnName;
            else
                $insertSql = $insertSql . "," . $columnName;
        }

        $insertSql = $insertSql. ") values (";

        for($index=0 ; $index < sizeof($columnValues);$index=$index+1) {
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
        echo ("columnName in updateAll = ".$this->columnNames[0]. "<br>");
        $this->update($id, $this->columnNames, $columnValues);
    }

    public function update($id, $columnNames, $columnValues)
    {
        $updateSql = "Update ".$this->tableName." set ";
        echo ("update SQL1 = ".$updateSql);

        for($index = 0; $index < sizeof($columnValues);$index=$index+1) {
            echo ("index = ".$index. "<br>");
            echo ("columnName = ".$columnNames[$index]. "<br>");
            echo ("columnValue = ".$columnValues[$index]. "<br>");
            if(!is_null($columnValues[$index]) && !empty($columnValues[$index])) {
                if ($index == 0) {
                    $updateSql = $updateSql . $columnNames[$index] . " = '" . $columnValues[$index] . "'";
                } else {
                    $updateSql = $updateSql . "," . $columnNames[$index] . " = '" . $columnValues[$index] . "'";
                }
            }
        }

        $updateSql = $updateSql . " where id = " . $id;

        echo ("update SQL2 = ".$updateSql);
        //return $updateSql;
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
        $this->columnNames = array ("email","fname","lname","phone","birthday","gender","password");
    }

}

class todo extends model
{
    public function __construct()
    {
        $this->tableName = 'todos';
        $this->columnNames = array ("ownerEmail","ownerId","createdDate","dueDate","message");
    }
}

class modelX
{
    public $tableName;
}

class accountX extends modelX
{
    public $id;
    public $email;
    public $fname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;

    public function __construct()
    {
        $this->tableName = 'accounts';
    }
}

class todoX extends modelX
{
    public $id;
    public $ownerEmail;
    public $ownerId;
    public $createdDate;
    public $dueDate;
    public $message;

    public function __construct()
    {
        $this->tableName = 'todos';
    }
}
class htmlTable
{
    public function makeTable($data)
    {
        echo '<table>';

        foreach ($data as $rowData)
        {
            //echo($rowData);
            echo "<tr>";
            foreach ($rowData as $key=>$value) {
               echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }


}

/*
echo '<h1>Insert into Accounts Table</h1>';
$account1 = new account();

$account1ColumnValues = array ("peter@njit.com","Peterxx","Smith","212-555-1212","03-MAY-10","Male","abc123");
$id1 = $account1->save("", $account1ColumnValues);
print("id1 = ".$id1."<br>");


$account1ColumnValues = array ("sam@njit.com","Sam","Jung","609-555-1212","03-APR-12","Male","pqr345");
$id2 = $account1->save("",$account1ColumnValues);
print("id2 = ".$id2."<br>");

$account1ColumnValues = array ("carol@njit.com","Carol","Barley","212-555-1212","03-DEC-11","Female","aaa123");
$id3 = $account1->save("",$account1ColumnValues);
print("id3 = ".$id3."<br>");

$account1ColumnValues = array ("param@njit.com","Param","Singh","212-555-3333","03-JAN-10","Male","aaabb123");
$id4 = $account1->save("",$account1ColumnValues);
print("id4 = ".$id4."<br>");


echo '<h1>Update record (all columns) in Accounts Table</h1>';

$account1ColumnValues = array ("peter1@njit.com","Petery","Smith1","212-555-1111","03-MAY-10","Male","abc123");
$account1->save(20, $account1ColumnValues);

echo '<h1>Update record (custom columns) in Accounts Table</h1>';
$account1ColumnNames = array ("email","fname","lname","phone");
$account1ColumnValues = array ("sam1@njit.com","Sam1","Jung1","212-555-2222");
$account1->update($id2, $account1ColumnNames, $account1ColumnValues);

echo '<h1>Delete record  in Accounts Table</h1>';

$account1->delete($id3);
*/

/*("ownerEmail","ownerId","createdDate","dueDate","message")

echo '<h1>Insert into ToDo Table</h1>';
$todo1 = new todo();

$toDoColumnValues = array ("peter@njit.com","1","03-MAY-10","03-MAY-10","test1");
$id1 = $todo1->save("", $toDoColumnValues);
print("id1 = ".$id1."<br>");


$toDoColumnValues = array ("carol@njit.com","1","03-MAY-10","03-MAY-10","test1");
$id2 = $todo1->save("",$toDoColumnValues);
print("id2 = ".$id2."<br>");

$toDoColumnValues = array ("sam@njit.com","1","03-MAY-10","03-MAY-10","test1");
$id3 = $todo1->save("",$toDoColumnValues);
print("id3 = ".$id3."<br>");

$toDoColumnValues = array ("sim@njit.com","1","03-MAY-10","03-MAY-10","test1");
$id4 = $todo1->save("",$toDoColumnValues);
print("id4 = ".$id4."<br>");


echo '<h1>Update record (all columns) in ToDo Table</h1>';

$toDoColumnValues = array ("peter1@njit.com","Petery","212-555-1111","03-MAY-10","Male","abc123");
$todo1->save(20, $toDoColumnValues);

echo '<h1>Update record (custom columns) in ToDo Table</h1>';
$toDoColumnNames = array ("ownerEmail","ownerId","createdDate","dueDate","message");
$toDoColumnValues = array ("sim@njit.com","1","03-MAY-10","03-MAY-10","test1");
$todo1->update($id2, $toDoColumnNames, $toDoColumnValues);

echo '<h1>Delete record  in ToDo Table</h1>';

$todo1->delete($id3);
*/




$accountX = new accountX();
$accounts = new collection($accountX);

$todoX = new todoX();
$todos = new collection($todoX);

$records = $accounts->findAll();
echo '<h1>Select all the Records in Accounts Table</h1>';

$formatter = new htmlTable();
$formatter->makeTable($records);
echo '<br>';
echo '<br>';

$records = $accounts->findOne(20);
echo '<h1>Select One Record from Accounts Table</h1>';
echo '<h2>Select Record Id : 1</h2>';
$formatter->makeTable($records);
echo '<br>';
echo '<br>';

$records = $todos->findAll();
echo '<h1>Select all the Records in Todos Table</h1>';
$formatter->makeTable($records);
echo '<br>';
echo '<br>';

$records = $todos->findOne(1);
echo '<h1>Select One Record from Todos Table</h1>';
echo '<h2>Select Record Id : 1</h2>';
$formatter->makeTable($records);

echo '<br>';
echo '<br>';




