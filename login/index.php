<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Victor+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <title>Login</title>
</head>

<body>

  <div class="container-outer">
    <h1>KemanaUangku?</h1>

    <div class="container">
      <span>Login to your account</span>

      <form action="" method="post">
        <div class="form-container">
          <div class="form-list">
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
          </div>

          <div class="form-list">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
          </div>
          <button type="submit" name="submit">Login</button>
        </div>
      </form>

    </div>
    <p>Don't have account yet? <a href="../register/index.php">Sign up</a></p>
  </div>

  <div id="particles-js" class="particle"></div>

  <script src="../Asset/particles.js"></script>
  <script src="script.js"></script>
</body>

</html>