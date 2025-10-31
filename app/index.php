<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicación PHP + MySQL - Docker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .header p {
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-section h2 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .users-section h2 {
            color: #667eea;
            margin-bottom: 20px;
        }
        .user-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }
        .user-card h3 {
            color: #333;
            margin-bottom: 5px;
        }
        .user-card p {
            color: #666;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Aplicación PHP + MySQL</h1>
            <p>Proyecto Docker - Parcial Práctico Avanzado</p>
        </div>

        <div class="content">
            <?php
            // Configuración de la base de datos desde variables de entorno
            $host = getenv('DB_HOST') ?: 'db';
            $dbname = getenv('DB_NAME') ?: 'app_db';
            $username = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: 'root';

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo '<div class="alert alert-error">Error de conexión: ' . htmlspecialchars($e->getMessage()) . '</div>';
                exit;
            }

            // Procesar formulario de agregar usuario
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['email'])) {
                $nombre = trim($_POST['nombre']);
                $email = trim($_POST['email']);

                if (!empty($nombre) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO users (nombre, email) VALUES (?, ?)");
                        $stmt->execute([$nombre, $email]);
                        echo '<div class="alert alert-success">Usuario agregado exitosamente!</div>';
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            echo '<div class="alert alert-error">Error: El email ya está registrado.</div>';
                        } else {
                            echo '<div class="alert alert-error">Error al agregar usuario: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                    }
                } else {
                    echo '<div class="alert alert-error">Por favor, ingrese un nombre y email válidos.</div>';
                }
            }
            ?>

            <!-- Formulario para agregar usuario -->
            <div class="form-section">
                <h2>Agregar Nuevo Usuario</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn">Agregar Usuario</button>
                </form>
            </div>

            <!-- Lista de usuarios -->
            <div class="users-section">
                <h2>Lista de Usuarios</h2>
                <?php
                try {
                    $stmt = $pdo->query("SELECT id, nombre, email, created_at FROM users ORDER BY created_at DESC");
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($users) > 0) {
                        foreach ($users as $user) {
                            echo '<div class="user-card">';
                            echo '<h3>' . htmlspecialchars($user['nombre']) . '</h3>';
                            echo '<p>Email: ' . htmlspecialchars($user['email']) . '</p>';
                            echo '<p style="font-size: 12px; color: #999;">Registrado: ' . htmlspecialchars($user['created_at']) . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="empty-state">No hay usuarios registrados aún.</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-error">Error al obtener usuarios: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
