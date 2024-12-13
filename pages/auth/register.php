<?php
session_start();
include_once("../../config/conn.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Mendapatkan nilai dari form -- atribut name di input
  $nama = $_POST['nama'];
  $alamat = $_POST['alamat'];
  $no_ktp = $_POST['no_ktp'];
  $no_hp = $_POST['no_hp'];

  //   -------   SITUASI 1 -------

  // Cek apakah pasien sudah terdaftar berdasarkan nomor KTP
  $query_check_pasien = "SELECT id, nama ,no_rm FROM pasien WHERE no_ktp = '$no_ktp'";
  $result_check_pasien = mysqli_query($conn, $query_check_pasien);

  if (mysqli_num_rows($result_check_pasien) > 0) {
    $row = mysqli_fetch_assoc($result_check_pasien);

    if ( $row['nama'] != $nama) {
      // ketika nama tidak sesuai dengan no_ktp
      echo "<script>alert(`Nama pasien tidak sesuai dengan nomor KTP yang terdaftar.`);</script>";
      echo "<meta http-equiv='refresh' content='0; url=register.php'>";
      die();
  }
    $_SESSION['signup'] = true;
    $_SESSION['id'] = $row['id'];
    $_SESSION['username'] = $nama;
    $_SESSION['no_rm'] = $row['no_rm'];
    $_SESSION['akses'] = 'pasien';

    echo "<meta http-equiv='refresh' content='0; url=../pasien'>";
    die();
  }
  

  //   -------   SITUASI 2 -------

  // Query untuk mendapatkan nomor pasien terakhir - YYYYMM-XXX - 202312-004
  $queryGetRm = "SELECT MAX(SUBSTRING(no_rm, 8)) as last_queue_number FROM pasien";
  $resultRm = mysqli_query($conn, $queryGetRm);

  // Periksa hasil query
  if (!$resultRm) {
      die("Query gagal: " . mysqli_error($conn));
  }

  // Ambil nomor antrian terakhir dari hasil query
  $rowRm = mysqli_fetch_assoc($resultRm);
  $lastQueueNumber = $rowRm['last_queue_number'];

  // Jika tabel kosong, atur nomor antrian menjadi 0
  $lastQueueNumber = $lastQueueNumber ? $lastQueueNumber : 0;

  // ---

  // Mendapatkan tahun saat ini (misalnya, 202312)
  $tahun_bulan = date("Ym");

  // Membuat nomor antrian baru dengan menambahkan 1 pada nomor antrian terakhir
  $newQueueNumber = $lastQueueNumber + 1;

  // Menyusun nomor rekam medis dengan format YYYYMM-XXX
  $no_rm = $tahun_bulan . "-" . str_pad($newQueueNumber, 3, '0', STR_PAD_LEFT);


  // ---

  // Lakukan operasi INSERT
  $query = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES ('$nama', '$alamat', '$no_ktp', '$no_hp', '$no_rm')";

  // Eksekusi query
  if (mysqli_query($conn, $query)) {
    // Set session variables
    $_SESSION['signup'] = true;  //Menandakan langsung ke dashboard
    $_SESSION['id'] = mysqli_insert_id($conn); //mengambil id terakhir
    $_SESSION['username'] = $nama;
    $_SESSION['no_rm'] = $no_rm;
    $_SESSION['akses'] = 'pasien';

    // Redirect ke halaman dashboard
    echo "<meta http-equiv='refresh' content='0; url=../pasien'>";
    die();
  } else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
  }


  // Tutup koneksi database
  mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Poliklinik | Registration </title>
  <style>
    body {
      font-family: 'Source Sans Pro', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .register-box {
      width: 360px;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 5px;
      padding: 15px;
    }
    .card-header {
      text-align: center;
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .card-body {
      padding: 0 30px;
    }
    input.form-control {
      width: calc(100% - 30px);
      padding: 15px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      margin-bottom: 20px;
    }
    .btn-primary {
      background-color: #11B69F;
      color: #fff;
      padding: 10px;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      width: 100%;
      box-sizing: border-box;
    }
    .btn-primary:hover {
      background-color: #0a8d72;
    }
    .icheck-primary {
      display: flex;
      align-items: center;
      padding: 10px;
    }
    .icheck-primary input {
      margin-right: 5px;
    }
    .icheck-primary label {
      margin: 0;
    }
    .error-message {
      color: red;
      font-style: italic;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <b>Daftar</b> Poliklinik</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Register a new account</p>

      <!-- Form -->
      <form action="" method="post">
        <!-- Nama -->
        <input type="text" class="form-control" required placeholder="Full name" name="nama">
        <!-- Alamat -->
        <input type="text" class="form-control" required placeholder="Alamat" name="alamat">
        <!-- No KTP -->
        <input type="number" class="form-control" required placeholder="No KTP" name="no_ktp">
        <!-- No HP -->
        <input type="number" class="form-control" required placeholder="No HP" name="no_hp">
        <!-- Persetujuan -->
        <div class="icheck-primary">
          <input type="checkbox" id="agreeTerms" required name="terms" value="agree">
          <label for="agreeTerms">
           I agree to the <a href="#">terms</a>
          </label>
        </div>
        <!-- Tombol Register -->
        <button type="submit" class="btn btn-primary btn-block">Register</button>
      </form>
      <!-- End Form -->
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>
<!-- /.register-box -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
</body>
</html>
