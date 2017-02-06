<html>
<body>

New equipment is added to database with following values: <br>
<br>
Department Tag : <?php echo $_POST["department_tag"]; ?> <br>
GT Tag : <?php echo $_POST["gt_tag"]; ?> <br>
Equipment Type : <?php echo $_POST["equipment_type"]; ?> <br>
Equipment Name : <?php echo $_POST["equipment_name"]; ?> <br>
Status : <?php echo $_POST["status"]; ?> <br>
Loaned To : <?php echo $_POST["loaned_to"]; ?> <br>

<?php
    $url = "http://localhost/core/add-equipment";
    $ch = curl_init($url);
    
    $data = "department_tag=" . $_POST["department_tag"] . "&gt_tag=" . $_POST["gt_tag"]
        . "&equipment_type=" . $_POST["equipment_type"] . "&equipment_name=" . $_POST["equipment_name"]
        . "&status=" . $_POST["status"] . "&loaned_to=" . $_POST["loaned_to"];
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<br>";
    echo "REST API returned status code: $statuscode";
?>

</body>
</html>
