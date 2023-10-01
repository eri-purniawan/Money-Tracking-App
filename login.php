<?php

session_start();

require "connect.php";

if (isset($_SESSION['login'])) {
  header('Location: index.php ');
  exit;
}

if (isset($_POST['submit'])) {

  $username = test_input($_POST['username']);
  $password = test_input($_POST['password']);

  $row = $conn->prepare("SELECT * FROM users WHERE user = :username");
  $row->bindParam('username', $username, PDO::PARAM_STR);
  $row->execute();

  if ($row->rowCount() == 1) {

    $result = $row->fetchAll(PDO::FETCH_ASSOC);
    $row_pass = password_verify($password, $result[0]['pass']);

    passCheck($row_pass);
    $error =  passCheck($row_pass);
  } else {
    $error = 'Username or password is incorect!';
  }
}

function passCheck($data)
{
  $error = 'Username or password is incorect!';
  global $result;
  if ($data === TRUE) {
    $_SESSION['login'] = TRUE;
    $_SESSION['user_id'] = $result[0]['id'];
    header('Location: index.php');
  } else {
    return $error;
  }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description" content="Aplikasi penelusuran pengeluaran pada keuangan berbasis web">
  <meta name="keywords" content="Uang, kemana, aplikasi">
  <meta name="author" content="Eri Purniawan">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Victor+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/form.css?v=1">
  <title>KemanaUangku?</title>
  <link rel="icon" type="image/png" href="img/money_5776691.png" />
</head>

<body>

  <div class="container-outer">
    <h1>KemanaUangku?</h1>

    <div class="container">
      <span>Login to your account</span>

      <form action="" method="post">
        <?php if (isset($error)) : ?>
          <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <div class="form-container">
          <div class="form-list">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" autocomplete="no">
          </div>

          <div class="form-list">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
          </div>

          <div class="show-pass">
            <label for="checkbox">Show Password</label>
            <input type="checkbox" name="checkbox" id="checkbox">
          </div>

          <button type="submit" name="submit">Login</button>
        </div>
      </form>

    </div>
    <p>Don't have account yet? <a href="register.php"><span>Sign up</span></a></p>
  </div>

  <div id="particles-js" class="particle"></div>

  <script src="js/particles.js"></script>
  <script src="js/form.js"></script>
  <script>
    const check = document.getElementById('checkbox');
    const pass = document.getElementById('password');
    check.addEventListener('click', () => {
      if (pass.type == 'password') {
        pass.type = 'text';
      } else {
        pass.type = 'password';
      }
    })
  </script>
</body>

</html>