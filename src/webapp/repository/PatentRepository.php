<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Patent;
use tdt4237\webapp\models\PatentCollection;
use tdt4237\webapp\Auth;

class PatentRepository
{

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makePatentFromRow(array $row)
    {
        $patent = new Patent($row['patentId'], $row['company'], $row['title'], $row['description'], $row['date'], $row['file']);
        $patent->setPatentId($row['patentId']);
        $patent->setCompany($row['company']);
        $patent->setTitle($row['title']);
        $patent->setDescription($row['description']);
        $patent->setDate($row['date']);
        $patent->setFile($row['file']);

        return $patent;
    }


    public function find($patentId)
    {
        $sql  = "SELECT * FROM patent WHERE patentId = ?";
        $result = $this->pdo->prepare($sql);
        $result->execute(array($patentId));
        $row = $result->fetch();

        if($row === false) {
            return false;
        }


        return $this->makePatentFromRow($row);
    }

    public function all()
    {
        $sql   = "SELECT * FROM patent";
        $results = $this->pdo->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in patent all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PatentCollection(
            array_map([$this, 'makePatentFromRow'], $fetch)
        );
    }

    public function deleteByPatentid($patentId)
    {
        $sql  = "DELETE FROM patent WHERE patentid = ?";
        $result = $this->pdo->prepare($sql);
        return $result->execute(array($patentId));
    }


    public function save(Patent $patent)
    {
        $title          = $patent->getTitle();
        $company        = $patent->getCompany();
        $description    = $patent->getDescription();
        $date           = $patent->getDate();
        $file           = $patent->getFile();

        /*$query = "INSERT INTO patent(company, date, title, description, file) VALUES(?, ?, ?, ?, ?)";
        $result = $this->pdo->prepare($query);
        $values = [$company, $date, $title, $description, $file];
        return $result->execute($values);*/



        if ($patent->getPatentId() === null) {
            $query = "INSERT INTO patent (company, date, title, description, file) "
                . "VALUES ('$company', '$date', '$title', '$description', '$file')";
        }

        $this->pdo->exec($query);
        return $this->pdo->lastInsertId();
    }
}
