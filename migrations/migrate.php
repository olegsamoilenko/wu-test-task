<?php

/**
 * Executes the SQL file to create the post_indexes table.
 * This is a simplified version of the migration script.
 */

$pdo = require __DIR__ . '/../src/db.php';

$sql = file_get_contents(__DIR__ . '/create_table.sql');

$pdo->exec($sql);

echo "Table was created\n";