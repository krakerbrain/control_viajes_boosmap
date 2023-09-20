<?php
require __DIR__ . '/config.php';

$date = date('Y-m-d H:i:s');
$backup_file_name = $_ENV['BD'] . '-' . $date . '-backup.sql';
$backup_file_path = '/public_html/backup/' . $backup_file_name;

// Create a backup of the database
$sql = "mysqldump -u $usuario -p $contrasenia $bd > $backup_file_path";

// Execute the SQL command
$result = $con->query($sql);

// Check if the command was successful
if ($result === false) {
    die("Error creating backup: " . $con->error);
}

// Close the connection to the database
$con->close();

// Success!
echo "Backup created successfully.";