<?php

require_once 'Table.php';

class Subordinates extends Table
{
	public function add($employeeId, $name){
		$employee = self::$relationships['subordinate_id'][0];

		$this->insert(['employee_id' => $employeeId, 'subordinate_id' => $employee->findIdByNameAndSurname($name)[0]['id']]);
	}

	public function read($employeeId){
		$employee = self::$relationships['subordinate_id'][0];

		$subordinates = $this->select('subordinate_id', "where employee_id=".$employeeId);

		foreach($subordinates as $key => $subordinate){
			$subordinates[$key] = $subordinate['subordinate_id'];
		}

		return $employee->select('*', "where id in (". implode(',', $subordinates) .")");
	}

	public function remove($id){
		return $this->delete($id);
	}
	
    protected function migrate(): string {
	    return "
			employee_id INT UNSIGNED NOT NULL,
			subordinate_id INT UNSIGNED NOT NULL
	    ";
	}
}