<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami</title>
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

        .container {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .form-container {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            margin-bottom: 20px;
            font-weight: bold;
        }

        .btn-submit {
            background: linear-gradient(to right, #11998e, #38ef7d);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(to left, #11998e, #38ef7d);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
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
        <h1>Hubungi Kami</h1>
        <p>Kami di sini untuk membantu Anda. Silakan isi formulir di bawah ini untuk menghubungi kami.</p>
    </header>

    <!-- Main Content -->
    <main class="container">
        <section>
            <div class="form-container">
                <h2>Formulir Kontak</h2>
                <form action="submit_contact.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama Anda" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subjek</label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Masukkan subjek pesan Anda" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Tulis pesan Anda di sini" required></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-submit">Kirim Pesan</button>
                    </div>
                </form>
            </div>
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
