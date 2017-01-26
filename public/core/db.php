<?php
$db = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$stats = new MongoDB\Driver\Command(["dbstats" => 1]);
$res = $db->executeCommand("test", $stats);
$stats = current($res->toArray());
print_r($stats);
?>
