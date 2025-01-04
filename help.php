<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }

        .hero {
            background: linear-gradient(to right, #1f4037, #99f2c8);
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

        .container {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        ul {
            list-style-type: square;
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 10px;
        }

        .faq {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-button {
            background: linear-gradient(to right, #ff6a00, #ee0979);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: linear-gradient(to left, #ff6a00, #ee0979);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        footer {
            background: #f1f1f1;
            padding: 20px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <header class="hero">
        <h1>Bantuan</h1>
        <p>Temukan jawaban atas pertanyaan umum atau hubungi kami untuk dukungan lebih lanjut.</p>
    </header>

    <!-- Main Content -->
    <main class="container">
        <section>
            <h2 class="section-title">Pertanyaan Umum (FAQ)</h2>
            <div class="faq">
                <h5>Apa itu layanan kami?</h5>
                <p>Layanan kami membantu Anda mengelola aktivitas online dengan mudah dan efisien.</p>

                <h5>Bagaimana cara menghubungi dukungan pelanggan?</h5>
                <p>Anda dapat menghubungi kami melalui email di <a href="mailto:support@example.com">support@example.com</a> atau telepon di (123) 456-7890.</p>

                <h5>Apakah data saya aman?</h5>
                <p>Ya, kami menggunakan enkripsi tingkat tinggi untuk melindungi data Anda.</p>

                <h5>Bagaimana cara memulihkan kata sandi?</h5>
                <p>Klik tombol "Lupa Kata Sandi" di halaman login dan ikuti petunjuk yang diberikan.</p>
            </div>
        </section>

        <section class="my-5">
            <h2 class="section-title">Panduan Pengguna</h2>
            <p>
                Berikut adalah langkah-langkah untuk memulai menggunakan layanan kami:
            </p>
            <ul>
                <li>Daftarkan akun Anda di halaman pendaftaran.</li>
                <li>Login ke akun Anda dengan email dan kata sandi.</li>
                <li>Mulai gunakan fitur-fitur yang tersedia untuk kebutuhan Anda.</li>
                <li>Jika menghadapi masalah, kunjungi bagian FAQ atau hubungi dukungan kami.</li>
            </ul>
        </section>

        <section class="my-5">
            <h2 class="section-title">Dukungan Teknis</h2>
            <p>
                Tim kami siap membantu Anda dengan segala kendala teknis yang Anda hadapi. Jangan ragu untuk menghubungi kami jika 
                Anda memerlukan bantuan.
            </p>
        </section>

        <!-- Back Button -->
        <div class="text-center my-5">
            <a href="index.php" class="btn back-button">Kembali</a>
        </div>
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
