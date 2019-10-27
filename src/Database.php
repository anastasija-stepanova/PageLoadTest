<?php

class Database
{
    private $pdo;

    public function __construct($host, $database, $userName, $password)
    {
        try {
            $this->pdo = new PDO("mysql:host=" . $host . ';dbname=' . $database, $userName, $password);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function executeQuery($query, $params = [])
    {
        $data = [];
        $stm = $this->pdo->prepare($query);
        if ($stm)
        {
            $stm->execute($params);
            while ($row = $stm->fetch(PDO::FETCH_ASSOC))
            {
                array_push($data, $row);
            }
            $stm->closeCursor();
            return $data;
        }
        else
        {
            return null;
        }
    }

    public function quote($str)
    {
        return $this->pdo->quote($str);
    }
}