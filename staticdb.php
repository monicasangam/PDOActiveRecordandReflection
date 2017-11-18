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
    static public function create()
    {
        $model = new static::$modelName;
        return $model;
    }

    static public function findAll()
    {
        $database = dbConnection::getConnection();
	$tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
	$statement = $database->prepare($sql);
	$statement->execute();
	$class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS,$class);
        $recordsSet = $statement->fetchAll();
        return $recordsSet;
    }

    static public function findOne($id)
    {
        $database = dbConnection::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM '. $tableName .' where id= 2';
        $statement = $database->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS,$class);
        $recordSet = $statement->fetchAll();
        return $recordSet;
    }
}
class accounts extends collection
{
    protected static $modelName = 'accounts';
}

class todos extends collection
{
    protected  static $modelName = 'todos';
}

class model
{
    protected $tableName;
    public function save()
    {
        if($this->id='')
        {
            $sql = $this->insert();
        }
        else
        {
            $sql = $this->update();
        }
        $database = dbConnection::getConnection();
        $statement = $database->prepare($sql);
        $statement->execute();

        $tableName = get_called_class();


        $columnString = implode(',',$statement);
        $valueString = ":".implode(',:',$statement);

        echo "INSERT INTO $tableName (".$columnString.")VALUES(".$valueString.")</br>";

        echo 'I just saved record: ' .$this->id;
    }

    private function insert()
    {
        $sql = 'mary';
        return $sql;
    }

    private function update()
    {
        $sql = 'mary';
        return $sql;
        echo 'I just updated this record'. $this->id;
    }

    public function delete()
    {
        echo 'I just deleted this record'. $this->id;
    }
}
class account extends model
{

}

class todo extends model
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

$obj =	accounts::create();
$records = $obj->findAll();
print_r($records);
echo '<br>';
echo '<br>';

$obj =  accounts::create();
$records = $obj->findOne(2);
print_r($records);
echo '<br>';
echo '<br>';

$obj = todos::create();
$records = $obj->findAll();
print_r($records);
echo '<br>';
echo '<br>';

$obj =  todos::create();
$records = $obj->findOne(3);
print_r($records);
echo '<br>';
echo '<br>';
