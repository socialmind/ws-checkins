<?php
/**
 * Database Handler.
 *
 * @version v0.7, August 02, 2013
 * @copyright W3Programmers (http://www.w3programmers.com)
 * 
 * @author Adnan Shawkat Tanim (http://www.w3programmers.com/author/tanim/)
 * @author Apostolos Kritikos <apostolos@websthetics.gr>
 * 
 * @package ws-checkins
 * 
 * @description Class that handles the database using PHP Data Objects (PDO) 
 * 
 * NOTE: The source code of this class which was originally implemented by Adnan Shawkat Tanim 
 * was altered by Websthetics ( http://websthetics.gr ) in order to suit the purpose of  ws-checkins 
 * project.
 * (via: http://www.w3programmers.com/crud-with-pdo-and-oop-php/)
 */

class DB {
	
	/** */
	private $host="localhost";

	/** */
	private $user="root";

	/** */
	private $db="primax";

	/** */
	private $pass="";

	/** */
	private $conn;
	
	/** 
	 * Constructor. It creates an instance of @link DB.
	 */
	public function __construct(){
		
		$this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db,$this->user,$this->pass);
	}
	
	/** 
	 *
	 */
	public function showData($table){
		
		$sql="SELECT * FROM $table";
		$q      = $this->conn->query($sql) or die("failed!");
		
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$data[]=$r;
		}
		return $data;	
	}
	
	/** 
	 *
	 */
	public function getById($id,$table){
		
		$sql="SELECT * FROM $table WHERE id = :id";
		$q = $this->conn->prepare($sql);
		$q->execute(array(':id'=>$id));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		return $data;	
	}
	
	/** 
	 *
	 */
	public function update($id,$name,$email,$mobile,$address,$table){

		$sql = "UPDATE $table 
		        SET name=:name,email=:email,mobile=:mobile,address=:address
				WHERE id=:id";
		$q = $this->conn->prepare($sql);
		$q->execute(array(':id'=>$id,':name'=>$name,':email'=>$email,':mobile'=>$mobile,':address'=>$address));		
		return true;
		
	}
	
	/** 
	 *
	 */
	public function insertData($name,$email,$mobile,$address,$table){
		
		$sql = "INSERT INTO $table SET name=:name,email=:email,mobile=:mobile,address=:address";
		$q = $this->conn->prepare($sql);
		$q->execute(array(':name'=>$name,':email'=>$email,':mobile'=>$mobile,':address'=>$address));
		return true;
	}
	
	/** 
	 *
	 */
	public function deleteData($id,$table){
	
		$sql="DELETE FROM $table WHERE id=:id";
		$q = $this->conn->prepare($sql);
		$q->execute(array(':id'=>$id));
		return true;	
	}	
}
?>