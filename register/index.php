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

  <div class="container-outer">
    <h1>KemanaUangku?</h1>

    <div class="container">
      <span>Create your account</span>

      <form action="../index.php" method="post">
        <div class="form-container">
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
          <button type="submit" name="submit">Sign Up</button>
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