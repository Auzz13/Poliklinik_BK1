<?php
include_once("../../../config/conn.php");
session_start();

if (isset($_SESSION['login'])) {
    $_SESSION['login'] = true;
} else {
    echo "<meta http-equiv='refresh' content='0; url=../auth/login.php'>";
    die();
}

$nama = $_SESSION['username'];
$akses = $_SESSION['akses'];

if ($akses != 'dokter') {
    echo "<meta http-equiv='refresh' content='0; url=..'>";
    die();
}

// Query pasien dengan menggunakan daftar poli
$pasien = query("SELECT
                  periksa.id AS id_periksa,
                  pasien.id AS id_pasien,
                  periksa.catatan AS catatan,
                  daftar_poli.no_antrian AS no_antrian, 
                  pasien.nama AS nama_pasien, 
                  daftar_poli.keluhan AS keluhan,
                  daftar_poli.id AS id_daftar_poli,
                  daftar_poli.status_periksa AS status_periksa
                FROM pasien 
                INNER JOIN daftar_poli ON pasien.id = daftar_poli.id_pasien
                LEFT JOIN periksa ON daftar_poli.id = periksa.id_daftar_poli");

$periksa = query("SELECT * FROM periksa");
$obat = query("SELECT * FROM obat");
?>

<?php
$title = 'Poliklinik | Daftar Periksa Pasien';

// Breadcrumb section
ob_start(); ?>
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="<?= $base_dokter; ?>">Home</a></li>
  <li class="breadcrumb-item active">Daftar Periksa</li>
</ol>
<?php
$breadcrumb = ob_get_clean();
ob_flush();

// Title Section
ob_start(); ?>
Daftar Periksa Pasien
<?php
$main_title = ob_get_clean();
ob_flush();

// Content section
ob_start();
?>
<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead>
        <tr>
          <th style="width: 8%">No Urut</th>
          <th style="width: 40%">Nama Pasien</th>
          <th style="width: 40%">Keluhan</th>
          <th style="width: 15%">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pasien as $pasiens) : ?>
          <tr>
            <td id="id" class="text-center"><?= $pasiens["no_antrian"] ?></td>
            <td><?= $pasiens["nama_pasien"] ?></td>
            <td><?= $pasiens["keluhan"] ?></td>

            <td>
              <?php if ($pasiens["status_periksa"] == 0) { ?>
                <a href="create.php?id=<?= $pasiens['id_daftar_poli'] ?>" class="btn btn-primary"><i class="fas fa-stethoscope"></i> Periksa </a>
              <?php } else { ?>
                <a href="edit.php?id=<?= $pasiens['id_periksa'] ?>" class="btn btn-warning"><i class="fa fa-edit"></i> Edit </a>
              <?php } ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();
ob_flush();

// Include layout
include_once "../../../layouts/index.php";
?>
