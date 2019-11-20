<?php
$conn = mysqli_connect("localhost:33060", "root", "root","center88_DB");
if (!$conn){
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT *
        FROM center88_member
        WHERE mb_id='" . $_POST["set_id"] . "'";
if(mysqli_num_rows(mysqli_query($conn, $sql)) > 0){
    echo "false";
}
else {
    echo "true";
}
?>