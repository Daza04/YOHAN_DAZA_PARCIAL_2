<?php
header('Content-Type: application/json');

// Configuración de la base de datos desde variables de entorno
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'app_db';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// GET /users - Obtener lista de usuarios
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT id, nombre, email, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'count' => count($users),
            'data' => $users
        ], JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener usuarios']);
    }
}

// POST /users - Agregar nuevo usuario
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del cuerpo de la petición
    $input = json_decode(file_get_contents('php://input'), true);

    // También soportar datos enviados por formulario
    if (!$input) {
        $input = $_POST;
    }

    $nombre = isset($input['nombre']) ? trim($input['nombre']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';

    // Validación
    if (empty($nombre) || empty($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Los campos nombre y email son obligatorios']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'El email no es válido']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (nombre, email) VALUES (?, ?)");
        $stmt->execute([$nombre, $email]);

        $userId = $pdo->lastInsertId();

        // Obtener el usuario recién creado
        $stmt = $pdo->prepare("SELECT id, nombre, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $user
        ], JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(409);
            echo json_encode(['error' => 'El email ya está registrado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear usuario']);
        }
    }
}

// Método no permitido
else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>
