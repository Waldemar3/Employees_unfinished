<?php

require_once 'Table.php';

class Employees extends Table
{
	private $regexp = [
        'name' => '/^[А-яA-z0-9 ]{3,32}$/',
        'email' => '/^\\S+@\\S+\\.\\S+$/',
        'phone_number' => '/^\\+?[1-9][0-9]{7,14}$/',
        'text' => '/^[А-яA-z0-9 ]{3,255}$/',
    ];

	public function readAll(){
		return json_encode($this->select('id, name, surname'));
	}

	public function read($id){
		return json_encode($this->select('*', 'where id='.$id));
	}

	public function create(array $employee){
		if(empty($employee)){
            throw new \Exception("Тело запроса пустое");
        }

		return $this->insert([
			'name' => $this->validateName($employee['name']),
			'surname' => $this->validateName($employee['surname']),
			'job' => $this->validateJob($employee['job']),
			'email' => $this->validateEmail($employee['email']),
			'phone_number' => $this->validatePhoneNumber($employee['phone_number']),
			'notes' => $this->validateNotes($employee['notes']),
			'timestamp' => $this->timestamp,
		]);
	}

	public function change(array $employee, int $id){
		return $this->update([
			'name' => $this->validateName($employee['name']),
			'surname' => $this->validateName($employee['surname']),
			'job' => $this->validateJob($employee['job']),
			'email' => $this->validateEmail($employee['email']),
			'phone_number' => $this->validatePhoneNumber($employee['phone_number']),
			'notes' => $this->validateNotes($employee['notes']),
			'timestamp' => $this->timestamp,
		], 'id='.$id);
	}

	public function remove(int $id){
		return $this->delete($id);
	}

	public function findIdByNameAndSurname(string $name){
		$name = explode(" ", $name);
		return $this->select('id', "where name='".$this->validateName($name[0])."' and surname='".$this->validateName($name[1])."'");
	}

	private function validateName($name){
        if(!preg_match($this->regexp['name'], $name)){
            throw new \Exception("Имя или фамилия имеет неправильный формат");
        }

        return $name;
    }

    private function validateEmail($email){
        if(!preg_match($this->regexp['email'], $email)){
            throw new \Exception("Неверный формат Email");
        }
        return $email;
    }

    private function validatePhoneNumber($phoneNumber){
        if(!preg_match($this->regexp['phone_number'], $phoneNumber)){
            throw new \Exception("Неверный формат номера телефона");
        }
        return $phoneNumber;
    }

    private function validateNotes($notes){
        if(!preg_match($this->regexp['text'], $notes)){
            throw new \Exception("Неверный формат заметок");
        }
        return $notes;
    }

    private function validateJob($job){
        if(!preg_match($this->regexp['text'], $job)){
            throw new \Exception("Неверный формат должности");
        }
        return $job;
    }

    protected function migrate(): string {
	    return "
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			surname VARCHAR(255) NOT NULL,
			email VARCHAR(255) NOT NULL,
			phone_number VARCHAR(255) NOT NULL,
			job VARCHAR(255) NOT NULL,
			notes TEXT NOT NULL,
			timestamp INT UNSIGNED NOT NULL,

			PRIMARY KEY (id)
		";
	}
}