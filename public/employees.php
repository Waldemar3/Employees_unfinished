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

    $POST = function() use($employees, $subordinates) {
        if($_POST['method'] == 'update' && $_POST['entity'] == 'employee'){
            $request = $employees->change($_POST, $_POST['id']);
        }elseif($_POST['method'] == 'delete' && $_POST['entity'] == 'employee'){
            $request = $employees->remove($_POST['id']);
        }elseif($_POST['method'] == 'add' && $_POST['entity'] == 'subordinate'){
            $request = $subordinates->add($_POST['id'], $_POST['name']);
        }elseif($_POST['method'] == 'read' && $_POST['entity'] == 'subordinate'){
            $request = $subordinates->read($_POST['id']);
        }elseif($_POST['method'] == 'delete' && $_POST['entity'] == 'subordinate'){
            $request = $subordinates->remove($_POST['id']);
        }else{
            $request = $employees->create($_POST);
        }
        return $request;
    };

    switch($_SERVER['REQUEST_METHOD']){
        case 'GET': $request = $GET(); break;
        case 'POST': $request = $POST(); break;
    }

    echo $request;
}catch(\Exception $e){
    header('HTTP/1.0 400');
    echo $e->getMessage();
}
