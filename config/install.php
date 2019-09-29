<?php

require_once('./Database.php');
try {
    $db = new Database();
    $connection = $db->getConnection();
    $sql = file_get_contents("data/database.sql");
    $connection->exec($sql);
    echo "Database and tables created successfully!";
} catch (PDOException $e) {
    echo $e->getMessage();
}