<?php

require "../connect.php";

if (isset($_POST['submit'])) {

  $username = test_input($_POST['username']);
  $password = test_input($_POST['password']);
  $cpassword = test_input($_POST['cpassword']);

  $row = $conn->query("SELECT user FROM users WHERE user = '$username'");

  if (empty($username) || empty($password) || empty($cpassword)) {
    $error = "Please fill requared data bellow!";
    goto end;
  }

  if ($row->rowCount() == 1) {
    $error = "Username already taken!";
    goto end;
  }

  if ($password != $cpassword) {
    $error = "Wrong confirm password!";
    goto end;
  }

  $password = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->query("INSERT INTO users VALUES ('', '$username', '$password')");

  if ($stmt->rowCount() == 1) {
    $Succes = TRUE;
  }
}

function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

end:
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Victor+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../login/style.css">
  <title>Register</title>
</head>

<body>

  <?php if (isset($Succes)) : ?>
    <div class="bg-alert">
      <div class="alert-container">
        <i class='bx bx-check bx-lg'></i>
        <p>Register Success</p>
        <a href="../login/index.php">Login</a>
      </div>
      <div id="particles-js" class="particle"></div>
    </div>
  <?php endif; ?>

  <div class="container-outer">
    <h1>KemanaUangku?</h1>

    <div class="container">
      <span>Create your account</span>

      <form action="" method="post">
        <div class="form-container">
          <?php if (isset($error)) : ?>
            <p><?= $error ?></p>
          <?php endif; ?>
          <div class="form-list">
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
          </div>

          <div class="form-list">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
          </div>

          <div class="form-list">
            <label for="cpassword"> Confirm Password</label>
            <input type="password" name="cpassword" id="cpassword">
          </div>
          <button type="submit" name="submit" id="btn">Sign Up</button>
        </div>
      </form>

    </div>
    <p>Already Have account? <a href="../login/index.php">Sign in</a></p>
  </div>

  <div id="particles-js" class="particle"></div>

  <script src="../Asset/particles.js"></script>
  <script src="../login/script.js"></script>
</body>

</html>