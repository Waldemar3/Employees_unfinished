<?php

require_once 'Table.php';

class Subordinates extends Table
{
	public function add($employeeId, $name){
		$employee = $this->getTableById('subordinate_id');

		$this->insert(['employee_id' => $employeeId, 'subordinate_id' => $employee->findIdByNameAndSurname($name)]);
	}

	public function read($employeeId){
		$employee = $this->getTableById('subordinate_id');

		$subordinates = $this->where("employee_id=".$employeeId)->select('subordinate_id');

		foreach($subordinates as $key => $subordinate){
			$subordinates[$key] = $subordinate['subordinate_id'];
		}

		return json_encode($employee->where("id")->in($subordinates)->select('id, name, surname'));
	}

	public function remove($id){
		return $this->where('subordinate_id='.$id)->delete();
	}
	
    protected function migrate(): string {
	    return "
			employee_id INT UNSIGNED NOT NULL,
			subordinate_id INT UNSIGNED NOT NULL
	    ";
	}
}