<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas Jobsheet - Pemrograman Web</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-app: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-app);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main Content Container */
        main.container {
            max-width: 960px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
            width: 100%;
            flex: 1;
        }

        .welcome-card {
            background-color: var(--bg-card);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--primary);
            padding: 1.75rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .welcome-card h1 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.35rem;
        }

        .welcome-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Grid Task Links */
        .jobsheet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.25rem;
        }

        .jobsheet-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 110px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .jobsheet-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.08);
        }

        .jobsheet-card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .jobsheet-card .arrow-link {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 1rem;
        }

        .jobsheet-card:hover .arrow-link {
            color: var(--primary-hover);
        }

        /* Footer */
        footer {
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-card);
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Main Content -->
    <main class="container">
        <div class="welcome-card">
            <h1>Daftar Jobsheet Praktikum</h1>
            <p>Pilih salah satu materi jobsheet di bawah ini untuk memeriksa hasil pengerjaan aplikasi web:</p>
        </div>

        <div class="jobsheet-grid">
            <a href="/Jobsheet1/index.html" class="jobsheet-card">
                <h2>Jobsheet 1</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet2/index.html" class="jobsheet-card">
                <h2>Jobsheet 2</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet3/index.html" class="jobsheet-card">
                <h2>Jobsheet 3</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet4/index.html" class="jobsheet-card">
                <h2>Jobsheet 4</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet5/index.html" class="jobsheet-card">
                <h2>Jobsheet 5</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet6/index.html" class="jobsheet-card">
                <h2>Jobsheet 6</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet7/index.php" class="jobsheet-card">
                <h2>Jobsheet 7</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet8/index.php" class="jobsheet-card">
                <h2>Jobsheet 8</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet8UpdateKonten/index.php" class="jobsheet-card">
                <h2>SIMKOS</h2>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SIMPUS-Mini &mdash; Praktikum Pemrograman Web</p>
    </footer>

</body>
</html>