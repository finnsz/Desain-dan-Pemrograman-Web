<?php
// includes/session.php
// Session disimpan di database (PostgreSQL), bukan di file.
// Di Vercel (serverless) file session hilang / tidak dibagi antar request,
// sehingga user "otomatis logout" dan dilempar balik ke halaman login.

if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../config/database.php';

    // Buat tabel session otomatis kalau belum ada
    $pdo->exec("CREATE TABLE IF NOT EXISTS app_sessions (
        id VARCHAR(128) PRIMARY KEY,
        data TEXT NOT NULL DEFAULT '',
        updated_at TIMESTAMP NOT NULL DEFAULT NOW()
    )");

    class DbSessionHandler implements SessionHandlerInterface
    {
        private PDO $pdo;
        public function __construct(PDO $pdo) { $this->pdo = $pdo; }

        #[\ReturnTypeWillChange]
        public function open($path, $name) { return true; }

        #[\ReturnTypeWillChange]
        public function close() { return true; }

        #[\ReturnTypeWillChange]
        public function read($id)
        {
            $stmt = $this->pdo->prepare("SELECT data FROM app_sessions WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetchColumn();
            return $data === false ? '' : (string)$data;
        }

        #[\ReturnTypeWillChange]
        public function write($id, $data)
        {
            $stmt = $this->pdo->prepare(
                "INSERT INTO app_sessions (id, data, updated_at) VALUES (?, ?, NOW())
                 ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data, updated_at = NOW()"
            );
            return $stmt->execute([$id, $data]);
        }

        #[\ReturnTypeWillChange]
        public function destroy($id)
        {
            $stmt = $this->pdo->prepare("DELETE FROM app_sessions WHERE id = ?");
            return $stmt->execute([$id]);
        }

        #[\ReturnTypeWillChange]
        public function gc($max_lifetime)
        {
            $stmt = $this->pdo->prepare(
                "DELETE FROM app_sessions WHERE updated_at < NOW() - (? || ' seconds')::interval"
            );
            $stmt->execute([(int)$max_lifetime]);
            return $stmt->rowCount();
        }
    }

    session_set_save_handler(new DbSessionHandler($pdo), true);

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
