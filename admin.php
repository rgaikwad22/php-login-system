<?php
session_start();

if (!isset($_SESSION['is_authenticated']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once "db.php";

$uname = $_SESSION['user_name'];

$sql = "SELECT id, FirstName, LastName, Email, Gender, File, City FROM userFormData";
$result = $conn->query($sql);

if (!$result) {
    throw new Exception("Error fetching data: " . $conn->error);
}
?>
<h2>Welcome to the Admin Page</h2>
<h2><a href='logout.php'>Logout</a></h2>

<?php
if ($result->num_rows > 0):
?>

<p>Hello, <?php echo $uname; ?>!</p>
<table border='1'>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Gender</th>
        <th>File</th>
        <th>City</th>
        <th>Actions</th>
    </tr>
    <?php
        while ($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td><?php echo $row['FirstName']; ?></td>
        <td><?php echo $row['LastName']; ?></td>
        <td><?php echo $row['Email']; ?></td>
        <td><?php echo $row['Gender']; ?></td>
        <td><?php echo $row['File']; ?></td>
        <td><?php echo $row['City']; ?></td>
        <td>
            <a href='register.php?id=<?php echo $row['id']; ?>'>Edit</a> | 
            <a href='delete.php?id=<?php echo $row['id']; ?>'>Delete</a>
        </td>
    </tr>
    <?php
        endwhile;
    ?>
    </table>
<?php
else:
    echo "No data available";
endif;
?>

<?php
$conn->close();
?>

<!-- try {
    // Fetch data from the database

    // Display the fetched data in a table
    echo "<h2>Welcome to the Admin Page</h2>";
    echo "<h2><a href='logout.php'>Logout</a></h2>";
    if ($result->num_rows > 0) {
        echo "<p>Hello, $uname!</p>";
        echo "<table border='1'>";
        echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Gender</th><th>File</th><th>City</th><th>Actions</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['FirstName']}</td>";
            echo "<td>{$row['LastName']}</td>";
            echo "<td>{$row['Email']}</td>";
            echo "<td>{$row['Gender']}</td>";
            echo "<td>{$row['File']}</td>";
            echo "<td>{$row['City']}</td>";
            echo "<td><a href='register.php?id={$row['id']}'>Edit</a> | <a href='delete.php?id={$row['id']}'>Delete</a></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No data available";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
} -->
