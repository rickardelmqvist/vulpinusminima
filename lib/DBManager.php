<?php
class DBManager{
    private $conn = NULL;
    private $result = NULL;
    function __construct() {
        $this->conn = new mysqli("mysql-server", DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    function query($sql) {
        $this->result = $this->conn->query($sql);        
        if( $this->conn->error) {
            echo "Query Failure: ".$this->conn->error;
            echo "*** ".$sql." ***";
        }
        
    }
    function get_num_rows(){
        return $this->result->num_rows;  
    }
    
    function fetch_assoc(){
        return $this->result->fetch_assoc();
    }
    
    function get_last_id(){
        return $this->conn->insert_id;
    }

}

?>