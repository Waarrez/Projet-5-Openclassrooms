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
        if ($this->conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $this->conn->connect_error);
        }
    }

    // Méthode pour exécuter une requête SQL
    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            die("Erreur lors de l'exécution de la requête : " . $this->conn->error);
        }
        return $result;
    }

    // Méthode pour fermer la connexion
    public function close(): void
    {
        $this->conn->close();
    }
}