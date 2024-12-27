<?php
include_once "../../../config/conn.php"; // Pastikan file koneksi benar
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['login']) || $_SESSION['akses'] != 'dokter') {
    echo "<meta http-equiv='refresh' content='0; url=..'>";
    die();
}

$nama = $_SESSION['username'];
$akses = $_SESSION['akses'];
$id_dokter = $_SESSION['id'];

// Ambil ID jadwal dari URL
$url = $_SERVER['REQUEST_URI'];
$url = explode("/", $url);
$id = $url[count($url) - 1];

// Ambil data jadwal periksa berdasarkan ID
$result = query("SELECT * FROM jadwal_periksa WHERE id = $id");
if (!$result || count($result) == 0) {
    echo "<script>alert('Jadwal tidak ditemukan.'); document.location.href = '../';</script>";
    exit;
}
$jadwal = $result[0];

// Fungsi untuk memperbarui status aktif
function updateJadwalPeriksaStatus($id, $status) {
    global $conn; // Variabel koneksi

    // Nonaktifkan semua jadwal jika status baru adalah aktif
    if ($status === 'Y') {
        $conn->query("UPDATE jadwal_periksa SET aktif = 'T'");
    }

    // Perbarui status jadwal yang dipilih
    $query = "UPDATE jadwal_periksa SET aktif = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Proses submit form
if (isset($_POST["submit"])) {
    if (!isset($_POST['aktif']) || !in_array($_POST['aktif'], ['Y', 'T'])) {
        echo "<script>alert('Status tidak valid.'); document.location.href = './edit.php/$id';</script>";
        exit;
    }

    $status = $_POST['aktif'];

    // Validasi: Setidaknya satu jadwal harus aktif
    if ($status === "T") {
        $query = "SELECT COUNT(*) as active_count FROM jadwal_periksa WHERE id != ? AND aktif = 'Y'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data['active_count'] == 0) {
            echo "
                <script>
                    alert('Setidaknya satu jadwal harus aktif.');
                    document.location.href = './edit.php/$id';
                </script>";
            exit;
        }
    }

    if (updateJadwalPeriksaStatus($id, $status)) {
        echo "
            <script>
                alert('Data berhasil diubah');
                document.location.href = '../';
            </script>";
    } else {
        echo "
            <script>
                alert('Gagal mengubah data');
                document.location.href = '../jadwal_periksa/edit.php';
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

// Title Section
ob_start(); ?>
Edit Jadwal Periksa
<?php
$main_title = ob_get_clean();

// Content Section
ob_start(); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Status Jadwal Periksa</h3>
    </div>
    <div class="card-body">
        <form action="" id="editJadwal" method="POST">
            <input type="hidden" name="id_dokter" value="<?=$id_dokter?>">
            <div class="form-group">
                <label for="hari">Hari</label>
                <input type="text" id="hari" class="form-control" value="<?=$jadwal['hari']?>" disabled>
            </div>
            <div class="form-group">
                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" id="jam_mulai" class="form-control" value="<?=date('H:i', strtotime($jadwal['jam_mulai']))?>" disabled>
            </div>
            <div class="form-group">
                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" id="jam_selesai" class="form-control" value="<?=date('H:i', strtotime($jadwal['jam_selesai']))?>" disabled>
            </div>
            <div class="form-group">
                <!-- Radio Button Input -->
                <label for="aktif">Status</label>
                <div class="form-check">
                    <input type="radio" id="aktif1" class="form-check-input" name="aktif" value="Y" <?php if($jadwal['aktif'] == "Y"){echo "checked";} ?>>
                    <label for="aktif1" class="form-check-label">Aktif</label>
                </div>
                <div class="form-check">
                    <input type="radio" id="tidak-aktif" class="form-check-input" name="aktif" value="T" <?php if($jadwal['aktif'] == "T"){echo "checked";} ?>>
                    <label for="tidak-aktif" class="form-check-label">Tidak Aktif</label>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" name="submit" id="submitButton" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();

// JS Section
$js = '';
?>

<?php include_once "../../../layouts/index.php"; ?>
