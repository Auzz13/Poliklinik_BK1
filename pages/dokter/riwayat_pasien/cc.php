<?php
include_once("../../../config/conn.php");
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    echo "<meta http-equiv='refresh' content='0; url=../auth/login.php'>";
    die();
}

// Ambil data pengguna
$nama = $_SESSION['username']; // Nama dokter dari sesi login
$akses = $_SESSION['akses'];

// Cek akses
if ($akses != 'dokter') {
    echo "<meta http-equiv='refresh' content='0; url=../..'>";
    die();
}

// Cek apakah `pasien_id` ada
if (!isset($_GET['pasien_id'])) {
    echo "<meta http-equiv='refresh' content='0; url=index.php'>";
    die();
}

$pasien_id = $_GET['pasien_id'];

// Mulai output buffering untuk konten
ob_start();
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Riwayat Pasien</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= $base_dokter; ?>">Home</a></li>
                    <li class="breadcrumb-item active">Riwayat Pasien</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Riwayat Pasien</h3>
        </div>
        <div class="card-body">
            <?php
            // Query untuk mendapatkan data riwayat pasien sesuai dengan nama dokter yang login
            $data2 = $pdo->prepare("
                SELECT 
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
                WHERE dpo.id_pasien = :pasien_id AND d.nama = :nama_dokter
                GROUP BY pr.id
                ORDER BY pr.tgl_periksa DESC
            ");
            $data2->execute(['pasien_id' => $pasien_id, 'nama_dokter' => $nama]);

            if ($data2->rowCount() == 0) {
                echo "<h5>Tidak Ditemukan Riwayat Periksa</h5>";
            } else {
                echo "<div class='grid-container'>";
                echo "<div class='grid-item'>No</div>";
                echo "<div class='grid-item'>Tanggal Periksa</div>";
                echo "<div class='grid-item'>Nama Pasien</div>";
                echo "<div class='grid-item'>Nama Dokter</div>";
                echo "<div class='grid-item'>Keluhan</div>";
                echo "<div class='grid-item'>Catatan</div>";
                echo "<div class='grid-item'>Obat</div>";
                echo "<div class='grid-item'>Biaya Periksa</div>";

                $no = 1;
                while ($da = $data2->fetch()) {
                    echo "<div class='grid-item'>{$no}</div>";
                    echo "<div class='grid-item'>{$da['tgl_periksa']}</div>";
                    echo "<div class='grid-item'>{$da['nama_pasien']}</div>";
                    echo "<div class='grid-item'>{$da['nama_dokter']}</div>";
                    echo "<div class='grid-item'>{$da['keluhan']}</div>";
                    echo "<div class='grid-item'>{$da['catatan']}</div>";
                    echo "<div class='grid-item'>{$da['obat']}</div>";
                    echo "<div class='grid-item'>" . formatRupiah($da['biaya_periksa']) . "</div>";
                    $no++;
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>
</section>
<?php
// Assign buffered output ke `$content`
$content = ob_get_clean();
include '../../../layouts/index.php';
?>
