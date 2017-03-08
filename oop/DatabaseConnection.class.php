<?php
/**
* PDO Database Connection Class
* 
* @author 			Jules Rau <admin@jules-rau.de>
* @copyright 		Jules Rau
* @version 	1.0	07.03.2017
*/

class DatabaseConnection{
	/**
	* @var 		object
	* @access 	private
	*/
    private $pdo;
	
	/**
	* @var 		object
	* @access 	private
	*/
    private $sQuery;
	
	/**
	* @var 		array
	* @access 	private
	*/
    private $settings;
	
	/**
	* @var 		boolean
	* @access 	private
	*/
    private $connected = false;
	
	/**
	* @var 		array
	* @access 	private
	*/
    private $parameters;
	
	/**
	* Constructs the Object
	* 
	* @access 	public
	* @return 	void
	*/
    public function __construct(){
        $this->Connect();
        $this->parameters = array();
    }
	
	/** 
	* Initialize the request 
	* 
	* @access 	
	* @param 	string	$qry
	* @param 	string 	$params
	* @return 	void
	* @see 		init()
	*/
    private function init($qry, $params = ""){
        if(!$this->connected){ 
			$this->connect();
        }
		
		/* try to execute query */
		try{
            $this->sQuery = $this->pdo->prepare($qry);
            $this->bindMore($params);
			
            if(!empty($this->params))
                foreach($this->params as $param => $value){
                    $type = PDO::PARAM_STR;
                    switch ($value[1]){
                        case is_int($value[1]) :   	$type = PDO::PARAM_INT;  break;
                        case is_bool($value[1]) : 	$type = PDO::PARAM_BOOL; break;
                        case is_null($value[1]) :  	$type = PDO::PARAM_NULL; break;
						case is_string($value[1]) : $type = PDO::PARAM_STR;	 break;
                    }
                    $this->sQuery->bindValue($value[0], $value[1], $type);
                }
            $this->sQuery->execute();
        }catch(PDOException $e){
            echo "<script>alert('".$this->setException($e->getMessage(), $qry)."');</script>";
        }
        $this->params = array();
    }
	
	/** 
	* Connect to databaseserver with ODBC 
	* 
	* @access 	private
	* @return 	void
	* @see 		connect()
	*/
    private function connect(){
		if(file_exists("ini/DatabaseSettings.ini"))
			$this->settings = parse_ini_file("ini/DatabaseSettings.ini");
		elseif(file_exists("../ini/DatabaseSettings.ini"))
			$this->settings = parse_ini_file("../ini/DatabaseSettings.ini");
			
        $dsn = "mysql:";
		$dsn .= "dbname=".$this->settings["dbname"].";";
		$dsn .= "host=".$this->settings["host"].";";
		
		/* Try to make connection to database */
        try{
            $this->pdo = new PDO($dsn, $this->settings["user"], $this->settings["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connected = true;
        }catch(PDOException $e){
            echo $this->setException($e->getMessage());
        }
    }
	
	/** 
	* Close connection to databaseserver 
	* 
	* @access 	public
	* @return 	void
	* @see 		closeConnection()
	*/
	public function closeConnection(){ 
		$this->pdo = null; 
	} 
	
	/** 
	* Get last insert Id 
	* 
	* @access 	public
	* @return 	void
	* @see 		lastInsertId()
	*/
    public function lastInsertId(){
		return $this->pdo->lastInsertId(); 
	}
	
	/** 
	* Begin transaction width database 
	* 
	* @access 	public
	* @return 	void
	* @see 		beginTransaction()
	*/
    public function beginTransaction(){
		return $this->pdo->beginTransaction(); 
	}
	
	/** 
	* Execute/commit database request
	* 
	* @access 	public
	* @return 	void
	* @see 		executeTransaction()
	*/
    public function executeTransaction(){
		return $this->pdo->commit(); 
	}
	
	/** 
	* Rollback PDO
	* 
	* @access 	public
	* @return 	void
	* @see 		rollBack()
	*/
	public function rollBack(){
		return $this->pdo->rollBack(); 
	}
	
	/** 
	* Print actual exception
	* 
	* @access 	private
	* @param 	string 	$msg
	* @param 	string 	$sql
	* @return 	string
	* @see 		setException()
	*/
	private function setException($msg, $sql = ""){
        $exception = "Unhandled Exception. <br>";
        $exception .= $msg . "<br>";
		$exception .= "Raw SQL : " . $sql;
		
        return $exception;
    }
	
	/** 
	* Bind request (with parameters) 
	* 
	* @access 	public
	* @param 	array 	$arr
	* @return 	void
	* @see 		bindMore()
	*/
    public function bindMore($arr){
        if(empty($this->parameters) && is_array($arr)){
            $columns = array_keys($arr);
            foreach($columns as $i => &$col){ $this->bind($col, $arr[$col]); }
        }
    }
	
	/** 
	* Query request 
	* 
	* @access 	
	* @param 	string 	$qry
	* @param 	array 	$params
	* @param 	string 	$fetch
	* @return 	mixed
	* @see 		
	*/
	public function query($qry, $params = null, $fetch = PDO::FETCH_ASSOC){
        $qry = trim(str_replace("\r", " ", $qry));
        $this->init($qry, $params);
        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $qry));
        $statement = strtoupper($rawStatement[0]);
        
        if($statement === 'SELECT' || $statement === 'SHOW')
            return $this->sQuery->fetchAll($fetch);
        elseif($statement === 'INSERT' || $statement === 'UPDATE' || $statement === 'DELETE')
            return $this->sQuery->rowCount();
        else
            return NULL;
    }
	
	/** 
	* Bind request (friendly) 
	* 
	* @access 	public
	* @param  	string 	$param
	* @param  	string 	$val
	* @return 	void
	* @see 		bind()
	*/
    public function bind($param, $val){
		$this->parameters[sizeof($this->parameters)] = [":" . $param , $val];
	}
	
	/** 
	* Column request 
	* 
	* @access 	public
	* @param  	string 	$qry
	* @param  	array 	$params
	* @return 	mixed
	* @see 		column()
	*/
	public function column($qry, $params = null){
        $this->init($qry, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);
        $column = null;
		
        foreach($Columns as $cells){ $column[] = $cells[0]; }
		
		return $column;
    }
	
	/** 
	* Single row request 
	* 
	* @access 	public
	* @param  	string 	$qry
	* @param  	array  	$params
	* @param  	string 	$fetch
	* @return 	array
	* @see 		row()
	*/
    public function row($qry, $params = null, $fetch = PDO::FETCH_ASSOC){
        $this->init($qry, $params);
        $result = $this->sQuery->fetch($fetch);
        $this->sQuery->closeCursor();
        return $result;
    }
	
	/** 
	* Single value request 
	* 
	* @access 	public
	* @param  	string 	$qry
	* @param  	array  	$params
	* @return 	array
	* @see 		single()
	*/
	public function single($qry, $params = null){
        $this->init($qry, $params);
        $result = $this->sQuery->fetchColumn();
        $this->sQuery->closeCursor();
        return $result;
    }
}
?>