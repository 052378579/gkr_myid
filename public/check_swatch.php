<?php
$db = \Config\Database::connect();
$query = $db->query("SHOW COLUMNS FROM tbl_barang");
print_r($query->getResultArray());

