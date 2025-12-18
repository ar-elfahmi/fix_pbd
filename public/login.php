<?php
session_start();
require '../app/config/Koneksi.php';
require '../app/controlles/ClassUser.php';

$User = new User();
$alert = '';

if(isset($_POST["login"])){
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    $result = $User->LoginQuery($username, $password);
    
    if($result){
        $_SESSION["login"] = true;
        $_SESSION["iduser"] = $result["iduser"];
        $_SESSION["username"] = $result["username"];
        $_SESSION["idrole"] = $result["idrole"];
        
        // Redirect ke dashboard admin
        header("Location: ../app/roles/admin/Components/DashbordAdmin.php");
        exit;
    } else {
        $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            Username atau password salah!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Manajemen Inventori</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }
    .login-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 15px 15px 0 0;
      padding: 2rem;
      text-align: center;
    }
    .login-body {
      padding: 2rem;
    }
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      color: white;
      padding: 0.75rem;
      font-weight: 600;
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
      color: white;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <h2><i class="fas fa-store me-2"></i>Sistem Inventori</h2>
      <p class="mb-0">Silakan login untuk melanjutkan</p>
    </div>
    <div class="login-body">
      <?= $alert ?>
      <form method="post">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>
        
        <div class="d-grid">
          <button type="submit" name="login" class="btn btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>Login
          </button>
        </div>
      </form>
      
      <div class="text-center mt-3">
        <small class="text-muted">Default login: admin1 / pass123</small>
      </div>
    </div>
  </div>
  
  <!-- Bootstrap JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>