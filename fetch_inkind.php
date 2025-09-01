<?php
include('config.php');

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT * FROM cash WHERE donor LIKE '%$query%' LIMIT 10";
    $result = mysqli_query($conn, $sql);

    $donors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $donors[] = $row;
    }

    echo json_encode($donors);
}
?>
