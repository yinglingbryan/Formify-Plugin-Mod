<?php 
	
abstract class FormifyObject {
	
	public static function create() {
		$db = Loader::db();
		$db->execute("INSERT INTO " . static::$table . " (" . static::$key . ") VALUES (0)");
		$o =  static::get($db->Insert_ID());
		return $o;
	}
	
	public static function get($id) {
		$db = Loader::db();
		$o = new static;
		
		if(is_array($id)) {
			$criteria = $id;
			
			if(count($criteria) > 0) {
				$i = 0;
				foreach($criteria as $column => $value) {
					if($i > 0) {
						$query .= " AND ";
					}
					$query .= $column . " = ?";
					$values[] = $value;
					$i++;
				}
			}
			
			$query = "SELECT " . static::$key . " FROM " . static::$table . " WHERE " . $query;
			$data = $db->getRow($query,$values);
			return self::get($data[static::$key]);
			
		} else {
			$data = $db->getRow("SELECT * FROM " . static::$table . " WHERE " . static::$key . "=?", array($id));
		}
		
		//Set properties
		if (($data[static::$key] == $id) && ($id != 0)) {
			foreach($data as $col => $val) {
				if($val != '') {
					$o->$col = $val;
				}
			}
			return $o;
		} else {
			return false;
		}
	}
	
	public static function search($criteria,$order='ASC',$orderBy='') {
		
		if($orderBy == '') {
			$orderBy = static::$key;
		}
		
		if(count($criteria) > 0) {
			$i = 0;
			foreach($criteria as $column => $value) {
				if($i > 0) {
					$query .= " AND ";
				}
				$query .= $column . " = ?";
				$values[] = $value;
				$i++;
			}
		}
		$query = "SELECT " . static::$key . " FROM " . static::$table . " WHERE " . $query . " ORDER BY " . $orderBy . " " . $order;
		
		$db = Loader::db();
		$records = $db->getAll($query,$values);
		$objects = array();
		
		if(count($records) > 0) {
			foreach($records as $record) {
				$o = static::get($record[static::$key]);
				$objects[] = $o;
			}
		}
		return $objects;
	}
	
	public static function all($size=0,$start=0,$order='ASC',$orderBy='') {
		
		if($orderBy == '') {
			$orderBy = static::$key;
		}
		
		$db = Loader::db();
		$records = $db->getAll("SELECT " . static::$key . " FROM " . static::$table . " ORDER BY " . $orderBy . " " . $order);
		$objects = array();
		
		if(count($records) > 0) {
			foreach($records as $record) {
				$o = static::get($record[static::$key]);
				$objects[] = $o;
			}
		}
		return $objects;
	}
	
	public function getID() {
		$key = static::$key;
		return $this->$key;
	}
	
	public function set($property,$value) {
		$db = Loader::db();
		
		if(($this->propertyIsAssignable($property)) && ($value !== false)) {
			$db->replace(static::$table,array(static::$key=>$this->getID(),$property=>"'" . addslashes($value) . "'"),static::$key);
		}
		$this->$property = $value;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("DELETE FROM " . static::$table . " WHERE " . static::$key . "=?",array($this->getID()));
	}
	
	public function propertyIsAssignable($property) {
		foreach(static::$assignableProperties as $ap) {
			if($ap == $property) {
				return true;
			}	
		}
		return false;
	}
	
}