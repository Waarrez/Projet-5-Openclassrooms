<?php

class BDD
{
    private string $servername;
    private string $username;
    private string $password;
    private string $dbname;
    private mysqli $conn; // Déclarez cette propriété pour stocker l'objet mysqli

    public function __construct($servername, $username, $password, $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->connect(); // Appelez la méthode connect pour établir la connexion à la base de données

        $this->conn->query("SET NAMES 'utf8mb4'");
    }

    private function connect(): void
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    }

    // Méthode pour exécuter une requête SQL
    public function query($sql): mysqli_result|bool
    {
        return $this->conn->query($sql);
    }

    // Méthode pour fermer la connexion
    public function close(): void
    {
        $this->conn->close();
    }
}