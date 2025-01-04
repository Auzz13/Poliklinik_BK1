
<?php
// Sertakan koneksi ke database
include_once("../../../config/conn.php");

// Mulai sesi
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['login'])) {
    echo "<meta http-equiv='refresh' content='0; url=../auth/login.php'>";
    die();
}

// Ambil data pengguna dari sesi
$nama = $_SESSION['username'];
$akses = $_SESSION['akses'];

// Pastikan pengguna memiliki akses sebagai dokter
if ($akses != 'dokter') {
    echo "<meta http-equiv='refresh' content='0; url=..'>";
    die();
}

// Ambil ID daftar poli dari parameter GET
$id_daftar_poli = isset($_GET['id']) ? $_GET['id'] : null;

// Validasi ID daftar poli
if (!$id_daftar_poli) {
    die("<script>alert('ID daftar poli tidak valid'); window.history.back();</script>");
}

// Query data pasien dan keluhan berdasarkan ID daftar poli
$pasiens = query("SELECT
                    pasien.id AS id_pasien,
                    pasien.nama AS nama_pasien,
                    daftar_poli.keluhan AS keluhan,
                    daftar_poli.id AS id_daftar_poli
                FROM daftar_poli
                INNER JOIN pasien ON pasien.id = daftar_poli.id_pasien
                WHERE daftar_poli.id = '$id_daftar_poli'");

// Jika data tidak ditemukan, tampilkan pesan kesalahan
if (!$pasiens) {
    die("<script>alert('Data tidak ditemukan untuk ID daftar poli: $id_daftar_poli'); window.history.back();</script>");
}

// Ambil data pasien pertama dari query
$pasiens = $pasiens[0];

// Query daftar obat
$obat = query("SELECT * FROM obat");

// Set biaya periksa
$biaya_periksa = 150000;
$total_biaya_obat = 0;

// Konfigurasi layout
$title = 'Poliklinik | Periksa Pasien';
$breadcrumb = '<ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../dokter">Home</a></li>
                    <li class="breadcrumb-item"><a href="../dokter/memeriksa_pasien">Daftar Periksa</a></li>
                    <li class="breadcrumb-item active">Periksa Pasien</li>
               </ol>';
$main_title = 'Periksa Pasien';

ob_start();
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Periksa Pasien</h3>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="form-group">
                <label for="nama_pasien">Nama Pasien</label>
                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="<?= $pasiens['nama_pasien'] ?>" disabled>
            </div>

            <div class="form-group">
                <label for="keluhan">Keluhan</label>
                <input type="text" class="form-control" id="keluhan" name="keluhan" value="<?= $pasiens['keluhan'] ?>" disabled>
            </div>

            <div class="form-group">
                <label for="tgl_periksa">Tanggal Periksa</label>
                <input type="datetime-local" class="form-control" id="tgl_periksa" name="tgl_periksa" required>
            </div>

            <div class="form-group">
                <label for="catatan">Catatan</label>
                <input type="text" class="form-control" id="catatan" name="catatan">
            </div>

            <div class="form-group">
                <label for="id_obat">Obat</label>
                <select class="form-control" name="obat[]" multiple id="id_obat">
                    <?php foreach ($obat as $obats) : ?>
                        <option value="<?= $obats['id']; ?>|<?= $obats['harga']; ?>">
                            <?= $obats['nama_obat']; ?> - <?= $obats['kemasan']; ?> - Rp.<?= number_format($obats['harga'], 0, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Total Harga</label>
                <input type="text" class="form-control" id="harga" name="harga" readonly>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" id="simpan_periksa" name="simpan_periksa">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </form>

        <?php
        if (isset($_POST['simpan_periksa'])) {
            $tgl_periksa = $_POST['tgl_periksa'];
            $catatan = $_POST['catatan'];
            $obat = $_POST['obat'];
            $id_daftar_poli = $pasiens['id_daftar_poli'];
            $id_obat = [];
            
            foreach ($obat as $data) {
                list($obat_id, $harga) = explode('|', $data);
                $id_obat[] = $obat_id;
                $total_biaya_obat += (int)$harga;
            }
            $total_biaya = $biaya_periksa + $total_biaya_obat;

            $query = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES ('$id_daftar_poli', '$tgl_periksa', '$catatan', '$total_biaya')";
            $result = mysqli_query($conn, $query);

            $periksa_id = mysqli_insert_id($conn);
            $query2 = "INSERT INTO detail_periksa (id_obat, id_periksa) VALUES ";
            foreach ($id_obat as $id) {
                $query2 .= "('$id', '$periksa_id'),";
            }
            $query2 = rtrim($query2, ',');
            $result2 = mysqli_query($conn, $query2);

            $query3 = "UPDATE daftar_poli SET status_periksa = '1' WHERE id = '$id_daftar_poli'";
            $result3 = mysqli_query($conn, $query3);

            if ($result && $result2 && $result3) {
                echo "<script>alert('Data berhasil disimpan');document.location.href = '../../dokter';</script>";
            } else {
                echo "<script>alert('Data gagal disimpan');</script>";
            }
        }
        ?>
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
            $('#harga').val(total);
        });
    });
</script>
<?php
$content = ob_get_clean();
include_once("../../../layouts/index.php");
?>
