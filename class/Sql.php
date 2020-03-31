<?php 

class Sql extends PDO {
    // Attributes
    private $conn;

    // Methods

    /**
     * When we do this process of putting data into the database, we need to prepare a statement with a query,
     * binding the parameters and then execute. To optimize this whole process, we have the following methods:
     * 
     * "query": with the parameters 'rawQuery' and 'params' (which is, by default, an array), we have the 'stmt'
     * variable (statement) that, with the attribute conn (connection) is prepared ('prepare()') with the
     * 'rawQuery' parameter.
     * 
     * Now we need to bind the parameter with the query. For that, we have the 'setParams()', who receives the
     * statement and the parameters. On a loop, we will be binding the parameters with the statement using the
     * method 'setParam()', who receives the statement, the key, the value and makes de bind of those.
     */

    public function query($rawQuery, $params = array()) {
        $stmt = $this->conn->prepare($rawQuery);
        $this->setParams($stmt, $params);
        $stmt->execute();
        return $stmt;
    }

    private function setParams($statement, $parameters = array()) {
        foreach ($parameters as $key => $value) {
            $this->setParam($statement, $key, $value);
        }
    }

    private function setParam($statement, $key, $value) {
        $statement->bindParam($key, $value);
    }

    public function select($rawQuery, $params = array()):array {
        $stmt = $this->query($rawQuery, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters, Setters and Construct

    /**
     * When the object is initialized, the connection will happen automatically.
     */
    public function __construct() {
        $this->conn = new PDO("mysql:dbname=dbphp7;host=localhost", "root", "");
    }
}

?>