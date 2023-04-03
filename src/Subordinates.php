<?php namespace App;

class Subordinates extends Table
{
	public function add($employeeId, $name){
		$subordinate = $this->getTableById('subordinate_id')->findByNameAndSurname($name);

		if($employeeId == $subordinate['id']){
			throw new \Exception("Вы не можете добавить самого себя");
		}

		$query = "
			insert into subordinates 
			(employee_id, subordinate_id) 
			select :employee_id, :subordinate_id where not exists 
			(select * from subordinates where employee_id=:subordinate_id and subordinate_id=:employee_id) limit 1
		";
        $stmnt = $this->pdo->prepare($query);
        $stmnt->execute(['employee_id' => $employeeId, 'subordinate_id' => $subordinate['id']]);

		if($stmnt->rowCount() == 0){
			throw new \Exception("Данный пользователь уже находится в подчинении");
		}

		return json_encode($subordinate);
	}

	public function read($employeeId){
		$employee = $this->getTableById('subordinate_id');

		$subordinates = $this->where("employee_id=".$employeeId)->select('subordinate_id');

		if(empty($subordinates)){
			return 0;
		}

		foreach($subordinates as $key => $subordinate){
			$subordinates[$key] = $subordinate['subordinate_id'];
		}

		return json_encode($employee->where("id")->in($subordinates)->select('id, name, surname'));
	}

	public function remove($id){
		return $this->where('subordinate_id='.$id)->delete();
	}

	public function fixture($ids, $employees){
		for ($i = 0; $i < count($employees)*2; $i++) {
			$this->add($ids[array_rand($ids)], $employees[array_rand($employees)]);
		}
	}
	
    protected function migrate(): string {
	    return "
			employee_id INT UNSIGNED NOT NULL,
			subordinate_id INT UNSIGNED NOT NULL
	    ";
	}
}