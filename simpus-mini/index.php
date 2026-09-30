<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas Jobsheet - Pemrograman Web</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #f8f9fb;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --primary-light: #dbeafe;
            --accent-yellow: #D8EB13;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
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

        /* Main Container */
        main.container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            width: 100%;
            flex: 1;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .welcome-banner h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-banner p {
            font-size: 0.95rem;
            opacity: 0.95;
        }

        /* Section Title */
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary);
        }

        /* Jobsheet Grid */
        .jobsheet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
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
            min-height: 120px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .jobsheet-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            transition: height 0.2s ease;
        }

        .jobsheet-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.12);
        }

        .jobsheet-card:hover::before {
            height: 6px;
        }

        .jobsheet-card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .jobsheet-card .arrow-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 1rem;
            transition: gap 0.2s ease;
        }

        .jobsheet-card:hover .arrow-link {
            gap: 0.6rem;
            color: var(--primary-hover);
        }

        /* Coming Soon Card */
        .jobsheet-card.coming-soon {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }

        .jobsheet-card.coming-soon::before {
            background: linear-gradient(90deg, #9ca3af, #d1d5db);
        }

        .jobsheet-card.coming-soon h2 {
            color: var(--text-muted);
        }

        .jobsheet-card.coming-soon .arrow-link {
            color: var(--text-muted);
        }

        .jobsheet-card.coming-soon .badge-soon {
            display: inline-block;
            background-color: #fef3c7;
            color: #b45309;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            margin-top: 0.5rem;
        }

        /* SimKos Featured Section */
        .simkos-section {
            margin-bottom: 3rem;
        }

        .simkos-featured {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.15);
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            transition: all 0.3s ease;
        }

        .simkos-featured:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(59, 130, 246, 0.25);
        }

        .simkos-featured-header {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .simkos-featured-icon {
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .simkos-featured h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .simkos-featured p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0.25rem 0 0 0;
        }

        .simkos-featured-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .simkos-featured:hover .simkos-featured-cta {
            background-color: rgba(255, 255, 255, 0.3);
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

        /* Responsive */
        @media (max-width: 768px) {
            main.container {
                padding: 1.5rem 1rem;
            }

            .jobsheet-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
            }

            .simkos-featured {
                padding: 1.75rem;
            }

            .simkos-featured h2 {
                font-size: 1.25rem;
            }

            .simkos-featured-cta {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- Main Content -->
    <main class="container">
        <!-- SimKos Featured Section -->
        <div class="simkos-section">
            <div class="section-title">
                <i class="fas fa-star"></i>
                Proyek Utama
            </div>
            <a href="/SimKos/index.php" class="simkos-featured">
                <div class="simkos-featured-header">
                    <div class="simkos-featured-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h2>SIMKOS</h2>
                        <p>Sistem Informasi Manajemen Kos & Penghuni</p>
                    </div>
                </div>
                <div class="simkos-featured-cta">
                    Akses Dashboard <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>

        <!-- Jobsheet Section -->
        <div class="section-title">
            <i class="fas fa-book"></i>
            Jobsheet Pemrograman Web
        </div>
        <div class="jobsheet-grid">
            <a href="/Jobsheet1/index.html" class="jobsheet-card">
                <h2>Jobsheet 1</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet2/index.html" class="jobsheet-card">
                <h2>Jobsheet 2</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet3/index.html" class="jobsheet-card">
                <h2>Jobsheet 3</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet4/index.html" class="jobsheet-card">
                <h2>Jobsheet 4</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet5/index.html" class="jobsheet-card">
                <h2>Jobsheet 5</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet6/index.html" class="jobsheet-card">
                <h2>Jobsheet 6</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet7/index.php" class="jobsheet-card">
                <h2>Jobsheet 7</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet8/index.php" class="jobsheet-card">
                <h2>Jobsheet 8</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet9/index.php" class="jobsheet-card">
                <h2>Jobsheet 9</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <a href="/Jobsheet10/index.php" class="jobsheet-card">
                <h2>Jobsheet 10</h2>
                <div class="arrow-link">Buka <i class="fas fa-arrow-right"></i></div>
            </a>

            <div class="jobsheet-card coming-soon">
                <h2>Jobsheet 11</h2>
                <span class="badge-soon">Coming Soon</span>
            </div>

            <div class="jobsheet-card coming-soon">
                <h2>Jobsheet 12</h2>
                <span class="badge-soon">Coming Soon</span>
            </div>

            <div class="jobsheet-card coming-soon">
                <h2>Jobsheet 13</h2>
                <span class="badge-soon">Coming Soon</span>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SIMPUS-Mini &mdash; Praktikum Pemrograman Web</p>
    </footer>

</body>
</html>