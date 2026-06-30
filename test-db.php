<?php
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "Connected to SQLite successfully\n";
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        echo "Table: " . $row['name'] . "\n";
    }
} catch (PDOException $e) {
    echo "SQLite connection failed: " . $e->getMessage() . "\n";
}
