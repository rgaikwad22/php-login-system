<!doctype html>
<!-- If multi-language site, reconsider usage of html lang declaration here. -->
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Login System</title>

  <!-- 120 word description for SEO purposes goes here. Note: Usage of lang tag. -->
  <meta name="description" lang="en" content="">

  <!-- Keywords to help with SEO go here. Note: Usage of lang tag.  -->
  <meta name="keywords" lang="en" content="">

  <!-- View-port Basics: http://mzl.la/VYREaP -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- Place favicon.ico in the root directory: mathiasbynens.be/notes/touch-icons -->
  <link rel="shortcut icon" href="favicon.ico" />

  <!--font-awesome link for icons-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Default style-sheet is for 'media' type screen (color computer display).  -->
  <link rel="stylesheet" media="screen" href="css/style.css">
</head>
<?php
session_start();
require_once "db.php";

$unameval = $passval = $usernameErr = $passErr = $invalidUserErr = "";

require_once "global_func.php";
function generateSecureToken() {
  return bin2hex(random_bytes(32));
}

if (isset($_COOKIE['security_token'])) {
  if ($_COOKIE['security_token'] === $_SESSION['security_token']) {
    if ($_SESSION['user_role'] === 'admin') {
      header('Location: admin.php');
    } else if ($_SESSION['user_role'] === 'user') {
      header('Location: user.php');
    }else {
      header('Location: home.php');
    }
    exit();
  }
}

if (isset($_POST['username']) && isset($_POST['password'])) {
  $unameval = $_POST["username"];
  $passval = $_POST["password"];

  $uname = trimInputVal($unameval);
  $pass = trimInputVal($passval);

  $check_name = checkUserName($uname, $usernameErr, "email is required.");
  $check_pass = checkPass($pass, $passErr, "Password is required.");

  try {
    $slectDataQuery = "SELECT Email, Password, role FROM userFormData WHERE Email = '$uname'";
    $selectDataResult = mysqli_query($conn, $slectDataQuery);
    if (!$selectDataResult) {
      throw new Exception("Error fetching data for editing: " . mysqli_error($conn));
    }
    $selectData = mysqli_fetch_assoc($selectDataResult);
    if (mysqli_num_rows($selectDataResult) === 1) {
        $valid_user_username = $selectData['Email'];
        $valid_user_password  = $selectData['Password'];
        $valid_user_role = $selectData['role'];
    } else {
        throw new Exception("No data found");
    }
  } catch (Exception $e) {
      echo "Exception: " . $e->getMessage();
  }

  if (validate2()) {
    if ($uname === $valid_user_username && password_verify($pass, $valid_user_password)) {
      $_SESSION['is_authenticated'] = true;
      if ($valid_user_role === 'user') {
        $_SESSION['user_role'] = $valid_user_role;
      }else {
        $_SESSION['user_role'] = $valid_user_role;
      }
      $_SESSION['user_name'] = $uname;
      $_SESSION['security_token'] = generateSecureToken();

      setcookie('security_token', $_SESSION['security_token'], time() + (86400 * 30), '/');

      header('Location: home.php');
      exit(); 
    }else {
      $invalidUserErr = "Invalid username or password.";
    }
  }
}

function validate2() {
  global $check_name, $check_pass;

  if ($check_name && $check_pass) {
      return true;
  }
  return false;
}

?>

<body>
  <!--container starts here-->
  <div class="container">
    <!--main starts here-->
    <main>
      <section class="form-section">
        <div class="wrapper">
          <h1 class="section-heading">Login Page</h1>
          <p style='color: red;'><?php echo $invalidUserErr ?></p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form"
            enctype="multipart/form-data">
            <div class="input-grp">
              <label for="user-name">Email: <span class="requred">*</span></label>
              <input type="email" id="user-name" name="username" class="input-write" placeholder="Enter your username"
                value="<?php echo $unameval ?>">
              <span class="error">
                <?php echo $usernameErr; ?>
              </span>
            </div>
            <div class="input-grp">
              <label for="password">Password : <span class="requred">*</span></label>
              <input type="password" id="password" name="password" class="input-write" placeholder="Enter your password"
                value="">
              <span class="error">
                <?php echo $passErr; ?>
              </span>
            </div>
            <input type="submit" name="log-in" value="Login" class="btn submit-btn">
            <span class="register-link">Don't have an account? <a href="register.php">Register!</a></span>
          </form>
        </div>
      </section>
    </main>
    <!--main ends here-->
  </div>
  <!--container ends here-->
  <script src="assets/js/script.js"></script>
</body>

</html>