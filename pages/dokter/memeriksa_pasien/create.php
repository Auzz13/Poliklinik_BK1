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
$obat = query("SELECT * FROM obat");

$pasiens = query("SELECT
                    p.nama AS nama_pasien,
                    dp.id AS id_daftar_poli
                FROM pasien p
                INNER JOIN daftar_poli dp ON p.id = dp.id_pasien
                WHERE p.id = '$id'")[0];

$biaya_periksa = 150000;
$total_biaya_obat = 0;
?>

<?php
$title = 'Poliklinik | Periksa Pasien';

// Breadcrumb section
ob_start(); ?>
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="<?= $base_dokter; ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= $base_dokter . '/memeriksa_pasien'; ?>">Daftar Periksa</a></li>
    <li class="breadcrumb-item active">Periksa Pasien</li>
</ol>
<?php
$breadcrumb = ob_get_clean();
ob_flush();

// Title Section
ob_start(); ?>
Periksa Pasien
<?php
$main_title = ob_get_clean();
ob_flush();
// Content section
ob_start();
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Periksa Pasien</h3>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <!-- Kolom input untuk menambahkan data -->
            <div class="form-group">
                <label for="nama_pasien">Nama Pasien</label>
                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="<?= $pasiens["nama_pasien"] ?>" disabled>
            </div>

            <div class="form-group">
                <label for="tgl_periksa">Tanggal Periksa</label>
                <input type="datetime-local" class="form-control" id="tgl_periksa" name="tgl_periksa">
            </div>

            <div class="form-group">
                <label for="catatan">Catatan</label>
                <input type="text" class="form-control" id="catatan" name="catatan">
            </div>

            <div class="form-group">
                <label for="id_obat">Obat</label>
                <select class="form-control" name="obat" id="id_obat">
                    <option value="">Pilih Obat</option>
                    <?php foreach ($obat as $obats) : ?>
                        <option value="<?= $obats['id']; ?>|<?= $obats['harga']; ?>">
                            <?= $obats['nama_obat']; ?> - <?= $obats['kemasan']; ?> - Rp.<?= number_format($obats['harga'], 0, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="total_harga">Total Harga</label>
                <input type="text" class="form-control" id="harga" name="harga" readonly value="Rp <?= number_format($biaya_periksa, 0, ',', '.'); ?>">
            </div>

            <!-- Tombol untuk mengirim form -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" id="simpan_periksa" name="simpan_periksa">
                    <i class="fa fa-save"></i> Simpan</button>
            </div>
        </form>
        <?php
        if (isset($_POST['simpan_periksa'])) {
            $tgl_periksa = $_POST['tgl_periksa'];
            $catatan = $_POST['catatan'];
            $obat = $_POST['obat'];
            $id_daftar_poli = $pasiens['id_daftar_poli'];
            $total_biaya_obat = 0;

            $query = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES
                    ($id_daftar_poli, '$tgl_periksa', '$catatan', '$biaya_periksa')";
            $result = mysqli_query($conn, $query);

            $periksa_id = mysqli_insert_id($conn);

            if (!empty($obat)) {
                list($id_obat, $harga_obat) = explode('|', $obat);
                $total_biaya_obat += (float) $harga_obat;

                $query2 = "INSERT INTO detail_periksa (id_obat, id_periksa) VALUES ($id_obat, $periksa_id)";
                $result2 = mysqli_query($conn, $query2);

                $total_biaya = $biaya_periksa + $total_biaya_obat;
                $query_update = "UPDATE periksa SET biaya_periksa = '$total_biaya' WHERE id = $periksa_id";
                mysqli_query($conn, $query_update);
            }

            $query3 = "UPDATE daftar_poli SET status_periksa = '1'
                        WHERE id = $id_daftar_poli";
            $result3 = mysqli_query($conn, $query3);

            if ($result && $result3) {
                echo "
          <script>
            alert('Data berhasil diubah');
            document.location.href = '../ ';
          </script>
        ";
            } else {
                echo "
          <script>
            alert('Data gagal diubah');
            document.location.href = '../edit.php/$id';
          </script>
        ";
            }
        }
        ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#id_obat').on('change', function() {
            var selectedValue = $(this).val();
            var sum = 150000; // Biaya periksa default
            if (selectedValue) {
                var parts = selectedValue.split("|");
                if (parts.length === 2) {
                    sum += parseFloat(parts[1]);
                }
            }
            // Format harga dengan pemisah ribuan
            $('#harga').val('Rp ' + sum.toLocaleString('id-ID'));
        });
    });
</script>
<?php
$content = ob_get_clean();
ob_flush();

include_once "../../../layouts/index.php";
?>
