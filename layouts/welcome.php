<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Poliklinik</title>
    <link href="./dist/css/style.css" rel="stylesheet" />
    <style>
/* Warna Pastel Biru */
body {
    font-family: 'Arial', sans-serif;
    background-color: #e3f2fd; /* Biru pastel lembut */
    color: #2c3e50; /* Biru tua untuk teks */
    margin: 0;
    padding: 0;
}

.navbar {
    background-color: #bbdefb; /* Pastel biru untuk navbar */
    padding: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.navbar-brand {
    font-size: 1.5rem;
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s;
}

.navbar-brand:hover {
    color: #1976d2; /* Biru aksen lebih gelap */
}

.navbar-links a {
    color: #2c3e50;
    margin-left: 1rem;
    text-decoration: none;
    font-weight: bold;
    transition: transform 0.3s, color 0.3s;
}

.navbar-links a:hover {
    color: #1976d2;
    transform: scale(1.1);
}

/* Bagian Fitur */
.features {
    padding: 2rem 0;
    text-align: center;
}

.feature-item {
    margin-bottom: 2rem;
    padding: 2rem;
    background-color: #e1f5fe; /* Pastel biru lebih terang */
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    animation: fadeIn 1s ease-in-out;
}

.feature-title {
    font-size: 1.75rem;
    color: #2c3e50;
}

.feature-link {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background-color: #bbdefb;
    color: #2c3e50;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s, transform 0.3s;
}

.feature-link:hover {
    background-color: #1976d2;
    color: #fff;
    transform: scale(1.1);
}

/* Animasi */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10%);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Footer */
.footer {
    background-color: #bbdefb; /* Pastel biru */
    padding: 1rem 0;
    text-align: center;
}

.footer p {
    color: #2c3e50;
    margin: 0;
}

.social-links {
    margin-top: 0.5rem;
}

.social-link {
    color: #2c3e50;
    margin: 0 0.5rem;
    text-decoration: none;
    transition: color 0.3s, transform 0.3s;
}

.social-link:hover {
    color: #1976d2;
    transform: scale(1.2);
}
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Bimbingan Karir Poliklinik</a>
            <?php if ($muncul) : ?>
                <div class="navbar-links">
                    <a class="nav-link" href="http://<?= $_SERVER['HTTP_HOST'] ?>/BK_Arya/pages/<?= $arah ?>">Dashboard</a>
                </div>
            <?php endif ?>
        </div>
    </nav>

    <?php if (!$muncul) : ?>
        <section class="features" id="features">
            <div class="container">
                <div class="feature-item">
                    <h2 class="feature-title">Registrasi Sebagai Pasien</h2>
                    <p>Apabila Anda adalah seorang Pasien, silahkan Registrasi terlebih dahulu untuk melakukan pendaftaran sebagai Pasien!</p>
                    <a class="feature-link" href="http://<?= $_SERVER['HTTP_HOST'] ?>/BK_Arya/pages/auth/login-pasien.php">
                        Klik Link Berikut <i class="icon-arrow-right"></i>
                    </a>
                </div>
                <div class="feature-item">
                    <h2 class="feature-title">Login Sebagai Dokter</h2>
                    <p>Apabila Anda adalah seorang Dokter, silahkan Login terlebih dahulu untuk memulai melayani Pasien!</p>
                    <a class="feature-link" href="http://<?= $_SERVER['HTTP_HOST'] ?>/BK_Arya/pages/auth/login.php">
                        Klik Link Berikut <i class="icon-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    <?php endif ?>
    

    <footer class="footer">
        <div class="container">
            <p>&copy; BK Poliklinik 2024</p>
        </div>
    </footer>
</body>
</html>