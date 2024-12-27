<?php
include_once("../../../config/conn.php");
session_start();

if (isset($_SESSION['login'])) {
  $_SESSION['login'] = true;
} else {
  echo "<meta http-equiv='refresh' content='0; url=..'>";
  die();
}
$id_pasien = $_SESSION['id'];
$no_rm = $_SESSION['no_rm'];
$nama = $_SESSION['username'];
$akses = $_SESSION['akses'];

$url = $_SERVER['REQUEST_URI'];
$url = explode("/", $url);
$id_poli = $url[count($url) - 1];

if ($akses != 'pasien') {
  echo "<meta http-equiv='refresh' content='0; url=..'>";
  die();
}
?>

<?php
$title = 'Poliklinik | Tambah Jadwal Periksa';

// Breadcrumb Section
ob_start();?>
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="<?=$base_pasien;?>">Home</a></li>
  <li class="breadcrumb-item"><a href="<?=$base_pasien . '/poli';?>">Poli</a></li>
  <li class="breadcrumb-item active">Detail Poli</li>
</ol>
<?php
$breadcrumb = ob_get_clean();
ob_flush();

// Title Section
ob_start();?>
Detail Poli
<?php
$main_title = ob_get_clean();
ob_flush();

// Content Section
ob_start();?>

<div class="card">
  <div class="card-header bg-primary">
    <h3 class="card-title">Detail Poli</h3>
  </div>
  <div class="card-body">
  <?php
      $poli = $pdo->prepare("SELECT d.nama_poli as poli_nama,
                                    c.nama as dokter_nama, 
                                    b.hari as jadwal_hari, 
                                    b.jam_mulai as jadwal_mulai, 
                                    b.jam_selesai as jadwal_selesai,
                                    a.no_antrian as antrian,
                                    a.id as poli_id

                                    FROM daftar_poli as a

                                    INNER JOIN jadwal_periksa as b
                                      ON a.id_jadwal = b.id
                                    INNER JOIN dokter as c
                                      ON b.id_dokter = c.id
                                    INNER JOIN poli as d
                                      ON c.id_poli = d.id
                                    WHERE a.id = $id_poli");
      $poli->execute();
      if ($poli->rowCount() == 0) {
        echo "Tidak ada data";
      } else {
        while($p = $poli->fetch()) {
      ?>

        <center>
        <h5>Nama Poli</h5>
        <?= $p['poli_nama']?>
        <hr>

        <h5>Nama Dokter</h5>
        <?= $p['dokter_nama']?>
        <hr>

        <h5>Hari</h5>
        <?= $p['jadwal_hari']?>
        <hr>

        <h5>Mulai</h5>
        <?= $p['jadwal_mulai']?>
        <hr>

        <h5>Selesai</h5>
        <?= $p['jadwal_selesai']?>
        <hr>

        <h5>Nomor Antrian</h5>
        <button class="btn btn-success"><?= $p['antrian']?></button>
        <hr>

        </center>

      <?php
        }
      }
      ?>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Detail Riwayat Pasien</h3>
  </div>
  <div class="card-body">
    <?php
    $data2 = $pdo->query("SELECT 
                            p.nama AS 'nama_pasien',
                            pr.*, 
                            d.nama AS 'nama_dokter', 
                            dpo.keluhan AS 'keluhan', 
                            GROUP_CONCAT(o.nama_obat SEPARATOR ', ') AS 'obat' 
                          FROM periksa pr
                          LEFT JOIN daftar_poli dpo ON (pr.id_daftar_poli = dpo.id)
                          LEFT JOIN jadwal_periksa jp ON (dpo.id_jadwal = jp.id)
                          LEFT JOIN dokter d ON (jp.id_dokter = d.id)
                          LEFT JOIN pasien p ON (dpo.id_pasien = p.id)
                          LEFT JOIN detail_periksa dp ON (pr.id = dp.id_periksa)
                          LEFT JOIN obat o ON (dp.id_obat = o.id)
                          WHERE dpo.id_pasien = '$id_pasien' 
                          GROUP BY pr.id 
                          ORDER BY pr.tgl_periksa DESC;");
    ?>
    <?php if ($data2->rowCount() == 0) : ?>
      <h5>Tidak Ditemukan Riwayat Periksa</h5>
    <?php else : ?>
      <div class="grid-container">
        <div class="grid-item">No</div>
        <div class="grid-item">Tanggal Periksa</div>
        <div class="grid-item">Nama Pasien</div>
        <div class="grid-item">Nama Dokter</div>
        <div class="grid-item">Keluhan</div>
        <div class="grid-item">Catatan</div>
        <div class="grid-item">Obat</div>
        <div class="grid-item">Biaya Periksa</div>
        <?php $no = 1; ?>
        <?php while ($da = $data2->fetch()) : ?>
          <div class="grid-item"><?= $no++; ?></div>
          <div class="grid-item"><?= $da['tgl_periksa']; ?></div>
          <div class="grid-item"><?= $da['nama_pasien']; ?></div>
          <div class="grid-item"><?= $da['nama_dokter']; ?></div>
          <div class="grid-item"><?= $da['keluhan']; ?></div>
          <div class="grid-item"><?= $da['catatan']; ?></div>
          <div class="grid-item"><?= $da['obat']; ?></div>
          <div class="grid-item"><?= formatRupiah($da['biaya_periksa']); ?></div>
        <?php endwhile ?>
      </div>
    <?php endif ?>
  </div>
</div>

<a href="<?=$base_pasien . '/poli';?>" class="btn btn-primary btn-block">Kembali</a>

<?php
$content = ob_get_clean();
ob_flush();
?>

<?php include_once "../../../layouts/index.php"; ?>
