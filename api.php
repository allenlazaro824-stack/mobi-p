<?php

declare(strict_types=1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/catalog.php';

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'signup':
            signup();
            break;
        case 'signin':
            signin();
            break;
        case 'me':
            me();
            break;
        case 'catalog':
            echo json_encode(['catalog' => fullCatalog()]);
            break;
        case 'logout':
            session_destroy();
            echo json_encode(['ok' => true]);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function signup(): void
{
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $name = trim((string)($data['name'] ?? ''));
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $password = (string)($data['password'] ?? '');
    $profilePhoto = (string)($data['profilePhoto'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        http_response_code(422);
        echo json_encode(['error' => 'Name, email and password are required.']);
        return;
    }

    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO users(name, email, password_hash, profile_photo) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $profilePhoto]);
    $userId = (int)$pdo->lastInsertId();

    seedDownloads($pdo, $userId);

    $_SESSION['user_id'] = $userId;
    $_SESSION['name'] = $name;
    $_SESSION['photo'] = $profilePhoto;

    echo json_encode([
        'ok' => true,
        'user' => ['id' => $userId, 'name' => $name, 'profilePhoto' => $profilePhoto],
        'catalog' => fullCatalog(),
    ]);
}

function signin(): void
{
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $password = (string)($data['password'] ?? '');

    $stmt = db()->prepare('SELECT id, name, password_hash, profile_photo FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, (string)$user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        return;
    }

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name'] = (string)$user['name'];
    $_SESSION['photo'] = (string)$user['profile_photo'];

    echo json_encode([
        'ok' => true,
        'user' => ['id' => (int)$user['id'], 'name' => $user['name'], 'profilePhoto' => $user['profile_photo']],
        'catalog' => fullCatalog(),
    ]);
}

function me(): void
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not signed in']);
        return;
    }

    echo json_encode([
        'user' => [
            'id' => (int)$_SESSION['user_id'],
            'name' => (string)$_SESSION['name'],
            'profilePhoto' => (string)($_SESSION['photo'] ?? ''),
        ],
        'catalog' => fullCatalog(),
    ]);
}

function seedDownloads(PDO $pdo, int $userId): void
{
    $insert = $pdo->prepare('INSERT INTO downloads(user_id, category, video_id, title) VALUES (?, ?, ?, ?)');
    foreach (fullCatalog() as $category => $videos) {
        foreach ($videos as $video) {
            $insert->execute([$userId, $category, $video['videoId'], $video['title']]);
        }
    }
}
