<?php
session_start();
include_once("../../config/conn.php");

if (isset($_SESSION['login'])) {
  echo "<meta http-equiv='refresh' content='0; url=../..'>";
  die();
}

if (isset($_POST['klik'])) {
  $username = stripslashes($_POST['nama']);
  $password = $_POST['alamat'];
  if ($username == 'admin') {
    if ($password == 'admin') {
      $_SESSION['login'] = true;
      $_SESSION['id'] = null;
      $_SESSION['username'] = 'admin';
      $_SESSION['akses'] = 'admin';
      echo "<meta http-equiv='refresh' content='0; url=../admin'>";
      die();
    }
  } else {
    $cek_username = $pdo->prepare("SELECT * FROM dokter WHERE nama = '$username'; ");
    try{
        $cek_username->execute();
        if($cek_username->rowCount()==1){
            $baris = $cek_username->fetchAll(PDO::FETCH_ASSOC);
            if($password == $baris[0]['alamat']){
              $_SESSION['login'] = true;
              $_SESSION['id'] = $baris[0]['id'];
              $_SESSION['username'] = $baris[0]['nama'];
              $_SESSION['akses'] = 'dokter';
              echo "<meta http-equiv='refresh' content='0; url=../dokter/index.php'>";
              die();
            }
        }
    } catch(PDOException $e){
      $_SESSION['error'] = $e->getMessage();
      echo "<meta http-equiv='refresh' content='0;'>";
      die();
    }
  }
  $_SESSION['error'] = 'Username dan Password Tidak Cocok';
  echo "<meta http-equiv='refresh' content='0;'>";
  die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Poliklinik | Log in</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <style>
    body {
        font-family: 'Arial', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #e3f2fd; /* Warna pastel biru */
        background-image: linear-gradient(135deg, #e1f5fe 30%, #bbdefb 70%);
        animation: fadeInBg 2s ease-in-out;
    }

    .container {
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        display: flex;
        overflow: hidden;
        width: 800px;
        max-width: 100%;
        animation: slideIn 1s ease-out;
    }

    .left-box {
        background-color: #bbdefb; /* Warna pastel biru untuk sisi kiri */
        color: #2c3e50; /* Teks biru tua */
        padding: 40px 20px;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        animation: bounceIn 1s ease-out;
    }

    .right-box {
        padding: 40px 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.9); /* Transparansi putih */
        border-left: 3px solid #bbdefb;
    }

    .left-box h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 28px;
        margin-bottom: 20px;
        animation: fadeIn 1.2s ease-in;
    }

    .form-control {
        width: 100%;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 50px;
        box-sizing: border-box;
        transition: box-shadow 0.3s, transform 0.3s;
    }

    .form-control:focus {
        box-shadow: 0 0 8px rgba(33, 150, 243, 0.5);
        transform: scale(1.02);
        outline: none;
    }

    .btn-primary {
        background-color: #64b5f6; /* Warna pastel biru terang */
        color: #fff;
        padding: 15px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        width: 100%;
        box-sizing: border-box;
        transition: background-color 0.3s, transform 0.3s;
    }

    .btn-primary:hover {
        background-color: #42a5f5; /* Warna biru hover lebih gelap */
        transform: scale(1.05);
    }

    .alert {
        background-color: #e57373; /* Warna merah pastel */
        color: white;
        padding: 10px;
        border-radius: 50px;
        text-align: center;
        margin-bottom: 15px;
        animation: fadeIn 1s ease-in-out;
    }

    /* Animasi */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes fadeInBg {
        from {
            background-color: #bbdefb;
        }
        to {
            background-color: #e3f2fd;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="left-box">
    <h1 class="fw-bold">BK-Poliklinik</h1>
    <p>Login untuk Dokter</p>
  </div>
  <div class="right-box">
    <?php if (isset($message)) { ?>
      <div class="alert"><?php echo $message; ?></div>
    <?php } ?>
    <form method="POST">
      <input type="text" name="nama" class="form-control" placeholder="Username" required />
      <input type="password" name="alamat" class="form-control" placeholder="Password" required />
      <button type="submit" name="klik" class="btn-primary">Sign In</button>
    </form>
  </div>
</div>
</body>
</html>

