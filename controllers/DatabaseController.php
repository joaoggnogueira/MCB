<?PHP

if (!defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
} else {
    define("DATABASE_CONTROLLER", true);
}

//No Anti-Injection, do not use directly
class DatabaseController {

    private static $DatabaseController = null;

    public static function get() {
        if (DatabaseController::$DatabaseController == null) {
            DatabaseController::$DatabaseController = new DatabaseController();
        }
        return DatabaseController::$DatabaseController;
    }

    private $connection;

    public function getConnection() {
        return $this->connection;
    }

    public function __construct() {
        $database_host = constant('DATABASE_HOST');
        $database_name = constant('DATABASE_NAME');
        $database_user = constant('DATABASE_USER');
        $database_password = constant('DATABASE_PASSWORD');
        try {
            $this->connection = new PDO(
                    "mysql:host=$database_host;dbname=$database_name", $database_user, $database_password
                    , array('charset' => 'utf8'));
        } catch (PDOException $e) {
            echo '<code>' . $e->getMessage() . '</code>';
            die();
        }
    }

    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    public function commit() {
        $this->connection->commit();
    }

    public function getLastInsertedId() {
        return $this->connection->lastInsertId();
    }

    public function getRecord($tablename, $where) {
        $query = "SELECT * FROM $tablename";

        $query .= " WHERE";
        $first = true;
        foreach ($where as $key => $value) {
            if ($first) {
                $first = false;
            } else {
                $query .= " AND ";
            }
            $query .= " `$key` = $value";
        }
        
        $stmt = $this->connection->prepare($query);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function listTable($tablename, $order_by, $where = array()) {

        $query = "SELECT * FROM $tablename";

        if (count($where) != 0) {
            $query .= " WHERE";
            $first = true;
            foreach ($where as $key => $value) {
                if ($first) {
                    $first = false;
                } else {
                    $query .= " AND ";
                }
                $query .= " `$key` = $value";
            }
        }

        if (isset($order_by) && is_string($order_by)) {
            $query .= " ORDER BY $order_by ASC";
        }
        $stmt = $this->connection->prepare($query);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $query
     * @return \QueryDatabase
     */
    public function query($query) {
        return new QueryDatabase($this->connection, $query);
    }

}

class QueryDatabase {

    /**
     * @var PDOStatement 
     */
    private $stmt;
    private $query;

    public function __construct($connection, $query) {
        $this->stmt = $connection->prepare($query);
        $this->query = $query;
    }

    public function bindInt($name, $value) {
        $work = (int) $value;
        $this->stmt->bindParam($name, $work, PDO::PARAM_INT);
    }

    public function bindString($name, $value) {
        $work = "" . $value;
        $this->stmt->bindParam($name, $work, PDO::PARAM_STR);
    }

    public function bindCurrentIp($name) {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        $this->stmt->bindParam($name, $ipaddress, PDO::PARAM_STR);
    }

    public function fetchAssoc() {
        if ($this->stmt->execute()) {
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function fetchAll() {
        if ($this->stmt->execute()) {
            return $this->stmt->fetchAll(PDO::FETCH_NUM);
        }
        return false;
    }

    public function fetchAllAssoc() {
        if ($this->stmt->execute()) {
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function execute() {
        return $this->stmt->execute();
    }

}
