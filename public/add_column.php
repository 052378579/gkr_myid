<?php
$mysqli = new mysqli("localhost", "root", "102013", "gkr_myid");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$sql = "ALTER TABLE cari_images ADD COLUMN image_hash VARCHAR(64) DEFAULT NULL";
if ($mysqli->query($sql) === TRUE) {
    echo "Column image_hash added successfully.";
} else {
    echo "Error: " . $mysqli->error;
}
$mysqli->close();
