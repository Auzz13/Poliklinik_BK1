<?php
// Sertakan file koneksi
include_once("../../../config/conn.php");
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['login'])) {
    echo "<meta http-equiv='refresh' content='0; url=../auth/login.php'>";
    die();
}

// Ambil informasi dari sesi login
$nama = $_SESSION['username']; // Nama dokter dari sesi
$akses = $_SESSION['akses'];  // Hak akses dari sesi

// Periksa apakah akses pengguna adalah dokter
if ($akses != 'dokter') {
    echo "<meta http-equiv='refresh' content='0; url=..'>";
    die();
}

// Query pasien berdasarkan nama dokter yang login
try {
    $stmt = $pdo->prepare("
        SELECT
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
        LEFT JOIN periksa ON daftar_poli.id = periksa.id_daftar_poli
        INNER JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id
        INNER JOIN dokter ON jadwal_periksa.id_dokter = dokter.id
        WHERE dokter.nama = :nama_dokter
        ORDER BY daftar_poli.no_antrian ASC
    ");
    $stmt->execute(['nama_dokter' => $nama]);
    $pasien = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<?php
// Set judul halaman
$title = 'Poliklinik | Daftar Periksa Pasien';

// Breadcrumb section
ob_start(); ?>
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="<?= $base_dokter; ?>">Home</a></li>
  <li class="breadcrumb-item active">Daftar Periksa</li>
</ol>
<?php
$breadcrumb = ob_get_clean();

// Title Section
$main_title = "Daftar Periksa Pasien";

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
        <?php if (count($pasien) > 0): ?>
          <?php foreach ($pasien as $pasiens): ?>
            <tr>
              <td class="text-center"><?= htmlspecialchars($pasiens["no_antrian"]) ?></td>
              <td><?= htmlspecialchars($pasiens["nama_pasien"]) ?></td>
              <td><?= htmlspecialchars($pasiens["keluhan"]) ?></td>
              <td>
                <?php if ($pasiens["status_periksa"] == 0): ?>
                  <a href="create.php?id=<?= $pasiens['id_daftar_poli'] ?>" class="btn btn-primary">
                    <i class="fas fa-stethoscope"></i> Periksa
                  </a>
                <?php else: ?>
                  <a href="edit.php?id=<?= $pasiens['id_periksa'] ?>" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Edit
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center">Tidak ada pasien terdaftar</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();

// Include layout
include_once "../../../layouts/index.php";
?>
