<?php
#CRUD:
#-Create
#-Read
#-Update
#-Delete

class BasicGateway {
    protected $db = null;
    protected $table_name = null;

    public function __construct($db) {
        $this->db = $db;
    }

    public function readAll() {
        $statement = "
        SELECT * FROM $this->table_name
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function readById($id) {
        $query = "
        SELECT * FROM $this->table_name
        WHERE id = ?
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array($id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function create($input) {
        $query = "
        INSERT INTO $this->table_name
            (". implode(', ', array_keys($input)) .")
        VALUES
            (". implode(', ', array_values($input)) .")
        ";

        echo $query;

        try {
            $statement = $this->db->prepare($query);
            $statement->execute();

            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function update($id, $input) {
        $query = "
        UPDATE $this->table_name
        SET ". implode(',', array_map(function($x) {return $x.'=:'.$x;}, array_keys($input))) ."
        WHERE id = {$id}
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute($input);

            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id) {
        $query = "
        DELETE FROM $this->table_name
        WHERE id = {$id}
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute();

            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}
?>