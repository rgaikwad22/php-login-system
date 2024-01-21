<?php
session_start();
require_once "db.php";

try {
    // Check if an ID is provided in the URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Delete the record based on the provided ID
        $sql = "DELETE FROM userFormData WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            if ($_SESSION['user_role'] == "admin") {
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit();
        } else {
            throw new Exception("Error deleting record: " . $conn->error);
        }
    } else {
        throw new Exception("ID not provided");
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}

// Close the database connection
$conn->close();

?>