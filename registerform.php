<!-- php starts here  -->
<?php
require_once "db.php";

// variable created globally
$fnameErr = $lnameErr = $emailErr = $genderErr = $cityErr = $fileErr = $upload = $passErr = $confirmPassErr = "";
$fname = $lname = $email = $password = $confirmpass = $gender = $city = "";
$edit_id = null;

require_once "global_func.php";

// Check if edit_id is present in the URL
if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];

    // Fetch existing data for editing
    $editDataQuery = "SELECT * FROM userFormData WHERE id = $edit_id";
    $editDataResult = mysqli_query($conn, $editDataQuery);

    try {
        if (!$editDataResult) {
            throw new Exception("Error fetching data for editing: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($editDataResult) > 0) {
            $editData = mysqli_fetch_assoc($editDataResult);
            $fname = $editData['FirstName'];
            $lname = $editData['LastName'];
            $email = $editData['Email'];
            $password = $_POST["password"];
            $confirmpass = $_POST["confirmpass"];
            $gender = $editData['Gender'];
            $city = $editData['City'];
        } else {
            throw new Exception("No data found for editing with ID: $edit_id");
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fnameval = $_POST["firstname"];
    $lnameval = $_POST["lastname"];
    $emailval = $_POST["email"];
    $passval = $_POST["password"];
    $confirmpassval = $_POST["confirmpass"];
    $genderval = $_POST["gender"];
    $cityval = $_POST['city'];

    $fname = trimInputVal($fnameval);
    $lname = trimInputVal($lnameval);
    $email = trimInputVal($emailval);
    $password = password_hash(trimInputVal($passval), PASSWORD_DEFAULT);
    $confirmpass = trimInputVal($confirmpassval);
    $gender = trimInputVal($genderval);
    $city = trimInputVal($cityval);

    $chName = checkName($fnameval, $fnameErr, "First name is required.");
    $chLName = checkName($lnameval, $lnameErr, "Last name is required.");
    $chEmail = checkEmail($emailval, $emailErr);
    $chpass = checkPass($passval, $passErr, "Password is required.");
    $chconfirmpass = checkConfirmPass($confirmpassval, $passval, $confirmPassErr);
    $chMulty = checkMultiple($genderval, $genderErr, "Please select your gender.");
    $chMultyCity = checkMultiple($cityval, $cityErr, "Please select your city.");
    $chFile = checkFile();

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $edit_id = mysqli_real_escape_string($conn, $_POST['id']);
        try {
            if (validate()) {
                // Update existing entry using prepared statement
                $sql = "UPDATE userFormData SET FirstName=?, LastName=?, Gender=?, City=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $sql);

                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . mysqli_error($conn));
                }

                // Bind parameters to the prepared statement
                mysqli_stmt_bind_param($stmt, "ssssi", $fname, $lname, $gender, $city, $edit_id);

                // Execute the prepared statement
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    $_SESSION['user_name'] = $email;
                    echo "<h2>Data Updated Successfully</h2>";

                    header("Location: admin.php");
                    exit();
                } else {
                    echo "Error updating record: " . mysqli_error($conn);
                }
                // Close the prepared statement
                mysqli_stmt_close($stmt);
            }
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage();
        }
    } else {
        try {
            if (validate()) {
                $user = "user";
                // Prepare the SQL statement
                $sql = "INSERT INTO userFormData (FirstName, LastName, Email, Password, Gender, City, File, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . mysqli_error($conn));
                }
                
                // Bind parameters to the prepared statement
                mysqli_stmt_bind_param($stmt, "ssssssss", $fname, $lname, $email, $password, $gender, $city, $filename,  $user);

                // Execute the prepared statement
                $result = mysqli_stmt_execute($stmt);
                var_dump($result);

                if (!$result) {
                    throw new Exception("Error inserting record: " . mysqli_stmt_error($stmt));
                }

                // Close statement
                mysqli_stmt_close($stmt);

                // Close connection
                mysqli_close($conn);
                header("Location: login.php");
                exit();
            }
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage();
        }
    }
} else {
    try {
        // If unauthorized access, throw an exception
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            throw new Exception("Invalid Request Method");
        }
    } catch (Exception $e) {
        // Handle unauthorized access exception
        echo "Error: " . $e->getMessage();
    }
}
?>
<!-- php ends here  -->