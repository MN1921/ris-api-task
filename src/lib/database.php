<?php

class Database {

    public $pdo;

    public function __construct($config) {
        $host = $config["host"];
        $port = $config["port"];
        $dsn = "pgsql:host=$host;port=$port";
        try {
            $this->pdo = new PDO($dsn, $config["user"], $config["password"], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            die("Exception: " . $e->getMessage() . "\n");
        }   
    }

    public function prepareSql($sql) {
        return $this->pdo->prepare($sql);
    }
}


function getDatabase() {
    $config = [
        "host" => "postgres",
        "port" => 5432,
        "user" => "postgres",
        "password" => "postgres",
    ];
    return new Database($config);
}

// try {
//     $dsn = "pgsql:host=$host;port=$port";
//     $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

//     echo "Connected to PostgreSQL successfully!";
//     echo "\n";
    
//     // $stmt = $pdo->prepare("SELECT id, name FROM your_table WHERE category = :category");
//     // $category = 'electronics';
//     // $stmt->bindParam(':category', $category);

//     // $stmt = $pdo->prepare("SELECT version();");
//     // $stmt->execute();

//     // // Fetch results
//     // while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//     //     echo $row['version'];
//     //     echo "\n";
//     // }

//     // $stmt = $pdo->prepare("SELECT * FROM table;");
//     // $stmt->execute();


// } catch (PDOException $e) {
//     die("Exception: " . $e->getMessage() . "\n");
// }
?>