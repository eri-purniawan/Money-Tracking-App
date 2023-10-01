<?php

require "connect.php";

if (isset($_POST['submit'])) {

  $username = test_input($_POST['username']);
  $password = test_input($_POST['password']);
  $cpassword = test_input($_POST['cpassword']);

  $row = $conn->query("SELECT user FROM users WHERE user = '$username'");

  if ($row->rowCount() == 1) {
    $error = "Username already taken!";
    goto end;
  }

  $password = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO users VALUES ('', ?, ?)");

  $stmt->bindParam(1, $username, PDO::PARAM_STR);
  $stmt->bindParam(2, $password, PDO::PARAM_STR);
  $stmt->execute();

  if ($stmt->rowCount() == 1) {
    $Succes = TRUE;
  }
}

end:
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
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>KemanaUangku?</title>
  <link rel="icon" type="image/png" href="img/money_5776691.png" />
</head>

<body>

  <?php if (isset($Succes)) : ?>
    <div class="bg-alert">
      <div class="alert-container">
        <i class='bx bx-check bx-lg'></i>
        <p>Register Success</p>
        <a href="login.php">Login</a>
      </div>
      <div id="particles-js" class="particle"></div>
    </div>
  <?php endif; ?>

  <div class="container-outer">
    <h1>KemanaUangku?</h1>

    <div class="container">
      <span>Create your account</span>

      <form action="" method="post">
        <div id="error"></div>
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
            <div style="display: none;" id="warning"></div>
          </div>

          <div class="show-pass">
            <label for="checkbox">Show Password</label>
            <input type="checkbox" name="checkbox" id="checkbox">
          </div>

          <div class="form-list">
            <label for="cpassword"> Confirm Password</label>
            <input type="password" name="cpassword" id="cpassword">
          </div>
          <button type="submit" name="submit" id="btn">Sign Up</button>
        </div>
      </form>

    </div>
    <p>Already Have account? <a href="login.php">Sign in</a></p>
  </div>

  <div id="particles-js" class="particle"></div>

  <script src="js/particles.js"></script>
  <script src="js/form.js"></script>
  <script>
    const user = document.getElementById('username');
    const pass = document.getElementById('password');
    const cpass = document.getElementById('cpassword');
    const btn = document.getElementById('btn');
    const error = document.getElementById('error');

    checkVal(user);
    checkVal(pass);
    checkVal(cpass);

    btn.addEventListener('click', () => {
      if (user.value === '') {
        inputDisable(user);
        doAction();
      }

      if (pass.value === '') {
        inputDisable(pass);
        doAction();
      }

      if (cpass.value === '') {
        inputDisable(cpass);
        doAction();
      }

      if (pass.value !== cpass.value) {
        inputDisable(cpass);
        inputDisable(pass);
        btn.disabled = true;
        error.innerHTML = '<div class="error">Wrong confirm password</div>';
      }
    })

    function doAction() {
      btn.disabled = true;
      error.innerHTML = '<div class="error">Please fill the form!!</div>';
    }

    function checkVal(element) {
      element.addEventListener('keyup', () => {
        if (element.value === '') {
          inputDisable(element);
        } else {
          btn.disabled = false;
          inputEnable(element);
          error.innerHTML = '';
        }
      })
    }

    function inputDisable(element) {
      element.style.outline = '1px solid var(--red)';
      element.style.border = 'none';
    }

    function inputEnable(element) {
      element.style.outline = '1px solid var(--blue)';
      element.style.border = '1px solid var(--blue)';
    }

    const warn = document.getElementById('warning');
    pass.addEventListener('keyup', () => {
      let value = pass.value;
      if (value.length < 8) {
        warn.style.display = 'block';
        warn.innerHTML = "<p>Must contain 8 character</p>";
      } else {
        warn.innerHTML = "";
        warn.style.display = 'none';
      }
    })

    const check = document.getElementById('checkbox');
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