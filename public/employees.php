<?php

require_once 'db-connection.php';
require_once '../src/Employees.php';
require_once '../src/Subordinates.php';

try{
    $employees = new Employees($pdo);

    Subordinates::addForeignKey($employees, 'id', 'employee_id');
    Subordinates::addForeignKey($employees, 'id', 'subordinate_id');
    $subordinates = new Subordinates($pdo);

    $GET = function() use($employees, $subordinates) {
        if(!empty($_GET['id'])){
            $request = $employees->read($_GET['id']);
        }else{
            $request = $employees->readAll();
        }
        return $request;
    };

    switch($_SERVER['REQUEST_METHOD']){
        case 'GET': $request = $GET(); break;
        case 'POST': $request = $employees->create($_POST); break;
    }

    echo $request;
}catch(\Exception $e){
    header('HTTP/1.0 400');
    echo $e->getMessage();
}
