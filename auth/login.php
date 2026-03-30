<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
<div class="login-wrapper">
  <div class="login-box">
    <h2>Login Admin</h2>
    <form method="post" action="process_login.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</div>

<?php if(isset($_GET['error'])) echo "Login gagal"; ?>

</body>
</html>