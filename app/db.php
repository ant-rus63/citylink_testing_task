<?php
class DB{

    private  $dbHost;
    private  $dbUser;
    private  $dbPassw;
    private  $dbName;
    private $mysqli;


    public function __construct($host, $user, $password, $dbname){
        $this->dbHost = $host;
        $this->dbName = $dbname;
        $this->dbUser = $user;
        $this->dbPassw = $password;
        $this->mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPassw, $this->dbName);
    }


    public function resultArray($sql, $paramsArray, $paramsTypes){
        $result = array();
        $row = array();
        $implodedArray = '';
        if ($stmt = $this->mysqli->prepare($sql)) {
            for($i=0 ; $i<count($paramsArray); $i++){
                $implodedArray .= '$paramsArray['.$i.'],';
            }
            $implodedArray = substr($implodedArray, 0 , strlen($implodedArray)-1);
            eval('$stmt->bind_param("'.$paramsTypes.'" ,'. $implodedArray.');');
            $stmt->execute();
            $row = array();
            $this->stmt_bind_assoc($stmt, $row);
            while ($stmt->fetch()) {
                $result[] = $row;
            }
            $stmt->close();
        }
        $this->mysqli->close();
        return $result;
    }


    private function stmt_bind_assoc (&$stmt, &$out) {
        $data = mysqli_stmt_result_metadata($stmt);
        $fields = array();
        $out = array();

        $fields[0] = $stmt;
        $count = 1;

        while($field = mysqli_fetch_field($data)) {
            $fields[$count] = &$out[$field->name];
            $count++;
        }
        @call_user_func_array(mysqli_stmt_bind_result, $fields);
    }


    public function resultRow($sql, $paramsArray, $paramsTypes){

    }


    public function insert(){

    }


    public function del(){

    }

}
?>