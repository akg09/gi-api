<?php
/*
*This file contains php api functions which gets and sets data from the database.
*Author By: Ankur Gupta
*Dated: 06 June 2015 
*/
$connection_data[] = array();
$connection_data['servername'] = "localhost";
$connection_data['database'] = "giconstruct";
$connection_data['username'] = "root";
$connection_data['password'] = "vertrigo";
$conn 		= array();

function establishConnection($servername,$username,$password,$database)
{
	global $conn;
	$conn = new mysqli($servername, $username, $password, $database);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
}
function getConnection()
{
	global $connection_data;
	$conn = establishConnection($connection_data['servername'],$connection_data['username'],$connection_data['password'],$connection_data['database']);
}
function closeConnection()
{
	global $conn;
	$conn->close();
}
function get_user($where=array())
{
	global $conn;
	$table_name = "user";
	$sql = getStatement($table_name,$where);
	$result = $conn->query($sql);
	return $result;
}
function set_user($where=array())
{
	global $conn;
	$table_name = "user";
	$new = 0;
	if(isset($where['user_guid']))
	{
		$user_guid = $where['user_guid'];
		unset($where['user_guid']);
	}
	if(isset($where['new']) && $where['new']== 1)
	{
		$new = 1;
		$user_guid = generateGuid();
		$where['user_guid'] = $user_guid;
	}
	$where = filterKeys($where,$conn);
	$sql = setStatement($table_name,$where,$new);
	if($new <> 1)
	{
		$sql = $sql." WHERE user_guid='".$user_guid."'";
	}
	//$sql = "INSERT INTO user (user_guid,first_name,last_name,email,mobile,password) VALUES('0XPkz','Ankur','Gupta','ankur.gupta.cse0015@gmail.com','09335178784','12345')";
	if ($conn->query($sql) === TRUE) {
		//echo "success";
	} else {
		//echo "error";
	}
	unset($where);
	$where['user_guid'] = $user_guid;
	$user = get_user($where);
	return $user;
}
function getStatement($table_name,$where)
{
	$sql = "SELECT * FROM `".$table_name."`";
	$statement = "";
	$counter = 1;
	foreach($where as $key=>$val)
	{
		if($counter == 1)
		{
			$statement = " WHERE ".$statement." ".$key."="."'".$val."'";
		}
		else
		{
			$statement = " AND ".$statement." ".$key."="."'".$val."'";
		}
		$counter++;
	}
	$sql = $sql." ".$statement;
	return $sql;
}
function setStatement($table_name,$where,$new=false)
{
	$keys = implode(",",array_keys($where));
	$values = implode(",",array_values($where));
	$statement = "";
	if(isset($new) && $new == 1)
	{
		$sql = "INSERT INTO ".$table_name." (".$keys.")"." VALUES(";
		foreach($where as $key=>$val)
		{
			$sql = $sql."'".$val."',";
		}
		$sql = substr($sql,0,strlen($sql)-1);
		$sql = $sql.")";
	}
	else
	{
		$sql = "UPDATE ".$table_name." SET ";
		foreach($where as $key=>$val)
		{
			$statement = $statement." ".$key.="="."'".$val."',";
		}
		$statement = substr($statement,0,strlen($statement)-1);
		$sql = $sql.$statement;
	}
	return $sql;
}
function filterKeys($where,$conn)
{
	$ret = array();
	$sql = "SHOW COLUMNS FROM user";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$list[] = $row;
		}
	}
	foreach($list as $key=>$val)
	{
		if(isset($where[$val['Field']]))
		{
			$ret[$val['Field']] = $where[$val['Field']];
		}
	}
	return $ret;
}
function generateGuid($length=4) 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
	$randomString = "0".$randomString;
    return $randomString;
}
?>