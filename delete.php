<?php
if (isset($_GET['id'])) {
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "phpecom";

    $conn = mysqli_connect($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = $_GET['id'];

    $sql = "DELETE FROM test WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
    header("Location: indexcrud.php"); 
    exit();
}
?>
