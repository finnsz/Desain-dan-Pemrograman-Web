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
            --bg-sidebar: #0f172a;
            --border-color: #e2e8f0;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent-teal: #0d9488;
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

        /* Topbar Header */
        header.topbar {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        header.topbar .brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        header.topbar .user-info {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
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
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.25rem;
        }

        .jobsheet-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            text-decoration: none;
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .jobsheet-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.08);
        }

        .jobsheet-card .badge {
            align-self: flex-start;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .jobsheet-card.featured .badge {
            background-color: #dcfce7;
            color: #15803d;
        }

        .jobsheet-card h2 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .jobsheet-card p {
            font-size: 0.825rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .jobsheet-card .arrow-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.25rem;
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

    <!-- Topbar Header -->
    <header class="topbar">
        <div class="brand">
            <span>PEMROGRAMAN WEB</span>
        </div>
        <div class="user-info">Daftar Portofolio Tugas</div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="welcome-card">
            <h1>Daftar Jobsheet Практикум</h1>
            <p>Pilih salah satu materi jobsheet di bawah ini untuk memeriksa hasil pengerjaan aplikasi web:</p>
        </div>

        <div class="jobsheet-grid">
            <a href="/Jobsheet1/index.html" class="jobsheet-card">
                <div>
                    <span class="badge">HTML Basic</span>
                    <h2>Jobsheet 1</h2>
                    <p>Dasar Struktur HTML & Layout Sederhana</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet2/index.html" class="jobsheet-card">
                <div>
                    <span class="badge">CSS Styling</span>
                    <h2>Jobsheet 2</h2>
                    <p>Penerapan CSS Styling & Desain UI</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet3/index.html" class="jobsheet-card">
                <div>
                    <span class="badge">Responsive</span>
                    <h2>Jobsheet 3</h2>
                    <p>Flexbox, CSS Grid & Media Queries</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet4/index.html" class="jobsheet-card">
                <div>
                    <span class="badge">UX Design</span>
                    <h2>Jobsheet 4</h2>
                    <p>Wireframing & Desain Alur Userflow</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet5/index.html" class="jobsheet-card">
                <div>
                    <span class="badge">JavaScript</span>
                    <h2>Jobsheet 5</h2>
                    <p>Interaktivitas DOM & Validasi Form</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet6/index.html" class="jobsheet-card">
                <div>
                    <span class="badge">Async JS</span>
                    <h2>Jobsheet 6</h2>
                    <p>AJAX, Fetch API & Pengolahan Data JSON</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet7/index.php" class="jobsheet-card">
                <div>
                    <span class="badge">PHP Basic</span>
                    <h2>Jobsheet 7</h2>
                    <p>Backend PHP, Form Handler & Session</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet8/index.php" class="jobsheet-card">
                <div>
                    <span class="badge">Database</span>
                    <h2>Jobsheet 8</h2>
                    <p>Integrasi Database PostgreSQL & PDO</p>
                </div>
                <div class="arrow-link">Buka Tugas &rarr;</div>
            </a>

            <a href="/Jobsheet8UpdateKonten/index.php" class="jobsheet-card featured">
                <div>
                    <span class="badge">Modern UI & Cloud DB</span>
                    <h2>Jobsheet 8 (Update Konten)</h2>
                    <p>Aplikasi SIMKOS dengan Supabase & Modern Light Theme</p>
                </div>
                <div class="arrow-link">Buka Aplikasi &rarr;</div>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SIMPUS-Mini &mdash; Praktikum Pemrograman Web</p>
    </footer>

</body>
</html>