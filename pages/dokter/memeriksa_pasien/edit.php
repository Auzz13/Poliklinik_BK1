<?php
include_once "../../../config/conn.php";
session_start();

if (!isset($_SESSION['login'])) {
    echo "<meta http-equiv='refresh' content='0; url=../auth/login.php'>";
    die();
}

$nama = $_SESSION['username'];
$akses = $_SESSION['akses'];
$id_dokter = $_SESSION['id'];

if ($akses != 'dokter') {
    echo "<meta http-equiv='refresh' content='0; url=..'>";
    die();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    die("<script>alert('ID tidak valid'); window.history.back();</script>");
}

// Mengambil data pasien dan periksa berdasarkan ID
$pasiens = query("SELECT
                    pasien.id AS id_pasien,
                    pasien.nama AS nama_pasien,
                    periksa.tgl_periksa AS tgl_periksa,
                    periksa.catatan AS catatan,
                    periksa.biaya_periksa AS biaya_periksa,
                    daftar_poli.id AS id_daftar_poli,
                    daftar_poli.no_antrian AS no_antrian,
                    daftar_poli.keluhan AS keluhan,
                    daftar_poli.status_periksa AS status_periksa
                FROM pasien
                INNER JOIN daftar_poli ON pasien.id = daftar_poli.id_pasien
                INNER JOIN periksa ON daftar_poli.id = periksa.id_daftar_poli
                WHERE periksa.id = '$id'");

if (!$pasiens) {
    die("<script>alert('Data tidak ditemukan'); window.history.back();</script>");
}

$pasiens = $pasiens[0];

// Mengambil daftar obat
$obat = query("SELECT * FROM obat");
$selected_obat = query("SELECT id_obat FROM detail_periksa WHERE id_periksa = '$id'");
$selected_obat_ids = array_column($selected_obat, 'id_obat');

// Menangani pengiriman form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl_periksa = $_POST['tgl_periksa'];
    $catatan = $_POST['catatan'];
    $obat = $_POST['obat'] ?? [];
    $id_obat = [];
    $biaya_periksa = 150000; // Biaya pemeriksaan tetap
    $total_biaya_obat = 0;

    // Menghitung total biaya obat yang dipilih
    foreach ($obat as $data) {
        list($obat_id, $harga) = explode('|', $data);
        $id_obat[] = $obat_id;
        $total_biaya_obat += (int)$harga;
    }
    $total_biaya = $biaya_periksa + $total_biaya_obat;

    // Menyimpan data pemeriksaan
    $query = "UPDATE periksa SET tgl_periksa = '$tgl_periksa', catatan = '$catatan', biaya_periksa = '$total_biaya' WHERE id = '$id'";
    $result = mysqli_query($conn, $query);

    // Menghapus data obat lama dan menambahkan data obat baru
    $query2 = "DELETE FROM detail_periksa WHERE id_periksa = '$id'";
    $result2 = mysqli_query($conn, $query2);

    // Menyimpan obat yang baru dipilih
    $query3 = "INSERT INTO detail_periksa (id_obat, id_periksa) VALUES ";
    foreach ($id_obat as $obat_id) {
        $query3 .= "('$obat_id', '$id'),";
    }
    $query3 = rtrim($query3, ',');
    $result3 = mysqli_query($conn, $query3);

    // Jika semua query berhasil, alihkan ke halaman daftar pemeriksaan pasien
    if ($result && $result2 && $result3) {
        header("Location: ../memeriksa_pasien/"); // Arahkan ke halaman daftar pemeriksaan pasien
        exit(); // Jangan lupa exit untuk menghentikan eksekusi lebih lanjut
    } else {
        echo "<script>alert('Data gagal diubah');</script>";
    }
}
?>

<?php
$title = 'Poliklinik | Edit Periksa Pasien';

ob_start();
?>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="<?= $base_dokter; ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= $base_dokter . '/memeriksa_pasien'; ?>">Daftar Periksa</a></li>
    <li class="breadcrumb-item active">Edit Periksa</li>
</ol>
<?php
$breadcrumb = ob_get_clean();
ob_flush();

// Title Section
ob_start();
?>
Edit Periksa Pasien
<?php
$main_title = ob_get_clean();
ob_flush();

// Content Section
ob_start();
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Periksa</h3>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="form-group">
                <label for="nama_pasien">Nama Pasien</label>
                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="<?= $pasiens['nama_pasien'] ?>" disabled>
            </div>

            <div class="form-group">
                <label for="tgl_periksa">Tanggal Periksa</label>
                <input type="datetime-local" class="form-control" id="tgl_periksa" name="tgl_periksa" value="<?= $pasiens['tgl_periksa'] ?>" required>
            </div>

            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="4"><?= $pasiens['catatan'] ?></textarea>
            </div>

            <div class="form-group">
                <label for="id_obat">Obat</label>
                <select class="form-control" name="obat[]" multiple id="id_obat">
                    <?php foreach ($obat as $obats) : ?>
                        <option value="<?= $obats['id']; ?>|<?= $obats['harga']; ?>" <?= in_array($obats['id'], $selected_obat_ids) ? 'selected' : '' ?>>
                            <?= $obats['nama_obat']; ?> - <?= $obats['kemasan']; ?> - Rp.<?= number_format($obats['harga'], 0, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Total Harga</label>
                <input type="text" class="form-control" id="harga" name="harga" readonly value="<?= $pasiens['biaya_periksa'] ?>">
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" id="simpan_periksa" name="simpan_periksa">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#id_obat').select2();
        $('#id_obat').on('change', function() {
            var selectedValues = $(this).val();
            var total = 150000;

            if (selectedValues) {
                selectedValues.forEach(function(value) {
                    var parts = value.split('|');
                    if (parts.length === 2) {
                        total += parseInt(parts[1]);
                    }
                });
            }

            $('#harga').val(total.toLocaleString());
        });
    });
</script>
<?php
$content = ob_get_clean();
include_once("../../../layouts/index.php");
?>
