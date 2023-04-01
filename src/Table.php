<?php

abstract class Table
{
    protected $timestamp;
    protected $table;
    protected $pdo;

    protected static $relationships = [];

    function __construct(PDO $pdo) {

        $this->pdo = $pdo;

        $this->table = strtolower(static::class);
    	$this->timestamp = $_SERVER['REQUEST_TIME'];

        $relationships = "";

        foreach(self::$relationships as $foreignKey => $relationship){
            $relationships .= ", FOREIGN KEY ($foreignKey) REFERENCES ". $relationship[0]->table ." (". $relationship[1] .") ON DELETE CASCADE";
        }

        $this->pdo->exec(
            "create table if not exists ". $this->table ." (
                ". static::migrate() . $relationships ."
            )"
        );
    }

    protected function select(string $string, string $where = ''){
        $query = "
            select ". $string ." from ". $this->table ." ". $where .";
	    ";

        $stmnt = $this->pdo->prepare($query);
        $stmnt->execute($values);

        return $stmnt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function insert(array $values){
        $query = "
            insert into ". $this->table ." 
            (". $this->insertArrayToString($values) .") 
            values
            (:". $this->insertArrayToMask($values) .")
	    ";
        $stmnt = $this->pdo->prepare($query);
        $stmnt->execute($values);

        return $this->pdo->lastInsertId();
    }

    protected function update(array $values, string $where){
        $query = "
            update ". $this->table ." set ". $this->updateArrayToMask($values) ." where ". $where ."
	    ";
        $stmnt = $this->pdo->prepare($query);
        $stmnt->execute($values);

        return true;
    }

    protected function delete(int $id){
        $query = "
            delete from ". $this->table ." where id=". $id ."
        ";
        $stmnt = $this->pdo->prepare($query);
        $stmnt->execute($values);

        return true;
    }

    public static function addForeignKey(Table $table, string $primaryKey, string $foreignKey): void {
        self::$relationships[$foreignKey] = [$table, $primaryKey];
    }

    private function insertArrayToString($values){
        return implode(', ', array_keys($values));
    }
    private function insertArrayToMask($values){
        return implode(', :', array_keys($values));
    }
    private function updateArrayToMask($values){
        $masks = [];

        foreach ($values as $key => $_){
            $masks[] = $key."=:".$key;
        }

        return implode(',', $masks);
    }

    abstract protected function migrate(): string;
}