<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light" style="background-color: #5DADE2; color: #FFFFFF;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item d-none d-sm-inline-block">
            <a href="http://<?= $_SERVER['HTTP_HOST']?>/BK_Arya/pages/auth/destroy.php" class="nav-link" style="color: #FFFFFF;">Logout</a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4" style="background-color: #85C1E9;">
    <!-- Brand Logo -->
    <a href="http://<?= $_SERVER['HTTP_HOST']?>/BK_Arya/pages/dokter/" class="brand-link" style="background-color: #2874A6; color: #FFFFFF;">
        <span class="brand-text font-weight-light">BK_Poliklinik</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="background-color: #85C1E9;">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image"></div>
            <div class="info">
                <a href="#" class="d-block" style="color: #34495E;">Selamat Datang <?= ucwords($_SESSION['username']) ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php if ($_SESSION['akses'] == 'admin') : ?>
                    <li class="nav-item">
                        <a href="<?= $base_admin ?>" class="nav-link">
                            <i class="nav-icon fas fa-th" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_admin.'/dokter' ?>" class="nav-link">
                            <i class="nav-icon fas fa-user-md" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Dokter</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_admin.'/pasien' ?>" class="nav-link">
                            <i class="nav-icon fas fa-user-injured" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Pasien</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_admin.'/poli' ?>" class="nav-link">
                            <i class="nav-icon fas fa-hospital" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Poli</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_admin.'/obat' ?>" class="nav-link">
                            <i class="nav-icon fas fa-pills" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Obat</p>
                        </a>
                    </li>
                <?php elseif ($_SESSION['akses'] == 'dokter') : ?>
                    <li class="nav-item">
                        <a href="<?= $base_dokter ?>" class="nav-link">
                            <i class="nav-icon fas fa-th" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_dokter . '/jadwal_periksa' ?>" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Jadwal Periksa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_dokter . '/memeriksa_pasien' ?>" class="nav-link">
                            <i class="nav-icon fas fa-stethoscope" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Memeriksa Pasien</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_dokter . '/riwayat_pasien' ?>" class="nav-link">
                            <i class="nav-icon fas fa-notes-medical" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Riwayat Pasien</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $base_dokter . '/profil' ?>" class="nav-link">
                            <i class="nav-icon fas fa-user" style="color: #2874A6;"></i>
                            <p style="color: #34495E;">Profil</p>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a href="<?= $base_pasien . '/poli' ?>" class="nav-link">
                            <p style="color: #34495E;">Poli</p>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
