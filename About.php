<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }

        .hero {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .hero p {
            font-size: 1.2rem;
            margin-top: 15px;
        }

        .btn-back {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .btn-back:hover {
            background: linear-gradient(to right, #feb47b, #ff7e5f);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .card-team {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card-team:hover {
            transform: translateY(-10px);
        }

        .team-img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #6a11cb;
        }

        .timeline-item {
            margin-bottom: 20px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 20px;
            height: 20px;
            background: #6a11cb;
            border-radius: 50%;
        }

        footer {
            background: #f8f9fa;
            padding: 20px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <!-- Button Back -->
    <div class="container mt-3">
        <a href="index.php" class="btn btn-back">
            ← Kembali
        </a>
    </div>

    <!-- Hero Section -->
    <header class="hero">
        <h1>Tentang Kami</h1>
        <p>Pelajari lebih lanjut tentang siapa kami dan apa yang kami perjuangkan.</p>
    </header>

    <!-- Visi dan Misi -->
    <main class="container my-5">
        <h2 class="section-title">Visi dan Misi Kami</h2>
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Visi</h5>
                <p>
                    Menjadi pemimpin dalam menyediakan solusi inovatif yang mengubah kehidupan masyarakat
                    dan mendukung perkembangan komunitas global.
                </p>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Misi</h5>
                <ul>
                    <li>Menyediakan produk dan layanan berkualitas tinggi.</li>
                    <li>Berkomitmen pada keberlanjutan dan tanggung jawab sosial.</li>
                    <li>Mendukung pertumbuhan dan perkembangan masyarakat.</li>
                </ul>
            </div>
        </div>

        <!-- Sejarah -->
        <section class="timeline my-5">
            <h2 class="section-title">Sejarah Kami</h2>
            <div class="timeline-item">
                <h5>Tahun 2010</h5>
                <p>Didirikan dengan visi untuk menciptakan solusi digital yang revolusioner.</p>
            </div>
            <div class="timeline-item">
                <h5>Tahun 2015</h5>
                <p>Meluncurkan produk pertama kami yang mendapatkan pengakuan global.</p>
            </div>
            <div class="timeline-item">
                <h5>Tahun 2020</h5>
                <p>Menjadi pemimpin pasar di bidang teknologi inovatif.</p>
            </div>
        </section>

        <!-- Tim Kami -->
        <section class="my-5">
            <h2 class="section-title">Tim Kami</h2>
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="card card-team p-3">
                        <img src="https://via.placeholder.com/120" alt="CEO" class="team-img mx-auto">
                        <h5 class="mt-3">John Doe</h5>
                        <p>CEO</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="card card-team p-3">
                        <img src="https://via.placeholder.com/120" alt="CTO" class="team-img mx-auto">
                        <h5 class="mt-3">Jane Smith</h5>
                        <p>CTO</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="card card-team p-3">
                        <img src="https://via.placeholder.com/120" alt="CMO" class="team-img mx-auto">
                        <h5 class="mt-3">Michael Brown</h5>
                        <p>CMO</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>© 2025 Perusahaan Anda. Semua Hak Dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
