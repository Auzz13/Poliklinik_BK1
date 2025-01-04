<?php
include_once "../../../config/conn.php";
session_start();

if (isset($_SESSION['login'])) {
    $_SESSION['login'] = true;
} else {
    echo "<meta http-equiv='refresh' content='0; url=..'>";
    die();
}

$nama = $_SESSION['username'];
$akses = $_SESSION['akses'];
$id_dokter = $_SESSION['id'];

if ($akses != 'dokter') {
    echo "<meta http-equiv='refresh' content='0; url=..'>";
    die();
}

$url = $_SERVER['REQUEST_URI'];
$url = explode("/", $url);
$id = $url[count($url) - 1];

$jadwal = query("SELECT * FROM jadwal_periksa WHERE id = $id")[0];

// Input data ke database
if (isset($_POST["submit"])) {
    $hari = $_POST["hari"];
    $jam_mulai = $_POST["jam_mulai"];
    $jam_selesai = $_POST["jam_selesai"];
    $aktif = $_POST["aktif"];

    // Validasi input kosong
    if (empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        echo "
          <script>
              alert('Data tidak boleh kosong');
              document.location.href = './edit.php/$id';
          </script>";
        die;
    }

    // Jika status diubah menjadi aktif, set semua jadwal lainnya menjadi tidak aktif
    if ($aktif === 'Y') {
        $queryReset = "UPDATE jadwal_periksa SET aktif = 'T' WHERE id != '$id'";
        mysqli_query($conn, $queryReset);
    }

    // Update jadwal dengan data baru
    $queryUpdate = "
        UPDATE jadwal_periksa
        SET hari = '$hari', jam_mulai = '$jam_mulai', jam_selesai = '$jam_selesai', aktif = '$aktif'
        WHERE id = '$id'
    ";
    $result = mysqli_query($conn, $queryUpdate);

    if ($result) {
        echo "
          <script>
              alert('Data berhasil diubah');
              document.location.href = '../';
          </script>";
    } else {
        echo "
          <script>
              alert('Terjadi kesalahan saat mengubah data');
              document.location.href = './edit.php/$id';
          </script>";
    }
}
?>

<?php
$title = 'Poliklinik | Edit Jadwal Periksa';

// Breadcrumb Section
ob_start(); ?>
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="<?=$base_dokter;?>">Home</a></li>
  <li class="breadcrumb-item"><a href="<?=$base_dokter . '/jadwal_periksa';?>">Jadwal Periksa</a></li>
  <li class="breadcrumb-item active">Edit Jadwal Periksa</li>
</ol>
<?php
$breadcrumb = ob_get_clean();
ob_flush();

// Title Section
ob_start(); ?>
Edit Jadwal Periksa
<?php
$main_title = ob_get_clean();
ob_flush();
ob_start();
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Edit Jadwal Periksa</h3>
  </div>
  <div class="card-body">
    <form action="" id="tambahJadwal" method="POST">
      <input type="hidden" name="id_dokter" value="<?= $id_dokter ?>">

      <!-- Pilih Hari (Read-Only) -->
      <div class="form-group">
        <label for="hari">Hari</label>
        <select name="hari" id="hari" class="form-control" disabled>
          <option hidden>-- Pilih Hari --</option>
          <?php
          $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
          foreach ($hari as $h): ?>
            <option value="<?= $h ?>" <?= ($h == $jadwal['hari']) ? "selected" : ""; ?>>
              <?= $h ?>
            </option>
          <?php endforeach; ?>
        </select>
        <!-- Kirim data Hari secara hidden -->
        <input type="hidden" name="hari" value="<?= $jadwal['hari'] ?>">
      </div>

      <!-- Input Jam Mulai (Read-Only) -->
      <div class="form-group">
        <label for="jam_mulai">Jam Mulai</label>
        <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" 
               value="<?= date('H:i', strtotime($jadwal['jam_mulai'])) ?>" readonly>
      </div>

      <!-- Input Jam Selesai (Read-Only) -->
      <div class="form-group">
        <label for="jam_selesai">Jam Selesai</label>
        <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" 
               value="<?= date('H:i', strtotime($jadwal['jam_selesai'])) ?>" readonly>
      </div>

      <!-- Radio Status -->
      <div class="form-group">
        <label for="aktif">Status</label>
        <div class="form-check">
          <input type="radio" id="aktif1" class="form-check-input" name="aktif" value="Y"
                 <?= ($jadwal['aktif'] == "Y") ? "checked" : ""; ?>>
          <label for="aktif1" class="form-check-label">Aktif</label>
        </div>
        <div class="form-check">
          <input type="radio" id="tidak-aktif" class="form-check-input" name="aktif" value="T"
                 <?= ($jadwal['aktif'] == "T") ? "checked" : ""; ?>>
          <label for="tidak-aktif" class="form-check-label">Tidak Aktif</label>
        </div>
      </div>

      <!-- Tombol Simpan -->
      <div class="d-flex justify-content-end">
        <button type="submit" name="submit" id="submitButton" class="btn btn-primary">
          <i class="fa fa-save"></i> Simpan
        </button>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
ob_flush();
?>
<?php include_once "../../../layouts/index.php"; ?>
