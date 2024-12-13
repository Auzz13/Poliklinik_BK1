<?php
include_once("../../../config/conn.php");
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
  echo "<meta http-equiv='refresh' content='0; url=../auth/login.php'>";
  die();
}

$nama = $_SESSION['username'];
$akses = $_SESSION['akses'];

// Cek akses admin
if ($akses != 'admin') {
  echo "<meta http-equiv='refresh' content='0; url=../..'>";
  die();
}
?>

<?php
$title = 'Poliklinik | Obat';
// Breadcrumb section
ob_start();?>
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="<?= $base_admin; ?>">Home</a></li>
  <li class="breadcrumb-item active">Obat</li>
</ol>
<?php
$breadcrumb = ob_get_clean();
ob_flush();

// Title Section
ob_start();?>
Obat
<?php
$main_title = ob_get_clean();
ob_flush();

// Content section
ob_start();
?>

<!-- Form Obat -->
<form class="form col" method="POST" action="" name="myForm">
  <?php
  // Inisialisasi Variabel
  $nama_obat = '';
  $kemasan = '';
  $harga = '';
  
  // Jika Edit Data
  if (isset($_GET['id'])) {
    try {
      $stmt = $pdo->prepare("SELECT * FROM obat WHERE id = :id");
      $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($row) {
        $nama_obat = $row['nama_obat'];
        $kemasan = $row['kemasan'];
        $harga = $row['harga'];
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  ?>
    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
  <?php
  }
  ?>
  <div class="row mt-3">
    <label for="nama_obat" class="form-label fw-bold">Nama Obat</label>
    <input type="text" class="form-control" name="nama_obat" id="nama_obat" placeholder="Nama Obat" value="<?php echo htmlspecialchars($nama_obat); ?>" required>
  </div>
  <div class="row mt-3">
    <label for="kemasan" class="form-label fw-bold">Kemasan</label>
    <input type="text" class="form-control" name="kemasan" id="kemasan" placeholder="Kemasan" value="<?php echo htmlspecialchars($kemasan); ?>" required>
  </div>
  <div class="row mt-3">
    <label for="harga" class="form-label fw-bold">Harga</label>
    <input type="number" class="form-control" name="harga" id="harga" placeholder="Harga" value="<?php echo htmlspecialchars($harga); ?>" required>
  </div>
  <div class="row d-flex mt-3 mb-3">
    <button type="submit" class="btn btn-primary rounded-pill" name="simpan">Simpan</button>
  </div>
</form>

<!-- Tabel Obat -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Obat</h3>
  </div>
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Obat</th>
          <th>Kemasan</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $pdo->query("SELECT * FROM obat");
        $no = 1;
        while ($data = $result->fetch(PDO::FETCH_ASSOC)) {
        ?>
          <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($data['nama_obat']); ?></td>
            <td><?php echo htmlspecialchars($data['kemasan']); ?></td>
            <td>Rp. <?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
            <td>
              <a class="btn btn-success" href="index.php?page=obat&id=<?php echo $data['id']; ?>">Edit</a>
              <a class="btn btn-danger" href="index.php?page=obat&id=<?php echo $data['id']; ?>&aksi=hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php
// Proses Simpan Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
  try {
    if (isset($_POST['id']) && $_POST['id'] != '') {
      // Update Data
      $stmt = $pdo->prepare("UPDATE obat SET 
                              nama_obat = :nama_obat,
                              kemasan = :kemasan,
                              harga = :harga
                              WHERE id = :id");
      $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    } else {
      // Insert Data
      $stmt = $pdo->prepare("INSERT INTO obat(nama_obat, kemasan, harga) VALUES (:nama_obat, :kemasan, :harga)");
    }
    $stmt->bindParam(':nama_obat', $_POST['nama_obat'], PDO::PARAM_STR);
    $stmt->bindParam(':kemasan', $_POST['kemasan'], PDO::PARAM_STR);
    $stmt->bindParam(':harga', $_POST['harga'], PDO::PARAM_INT);
    $stmt->execute();

    // Redirect Setelah Simpan
    echo "<meta http-equiv='refresh' content='0; url=index.php?page=obat'>";
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Proses Hapus Data
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
  try {
    $stmt = $pdo->prepare("DELETE FROM obat WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    echo "<meta http-equiv='refresh' content='0; url=index.php?page=obat'>";
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}
?>

<?php
$content = ob_get_clean();
ob_flush();
include '../../../layouts/index.php';
?>