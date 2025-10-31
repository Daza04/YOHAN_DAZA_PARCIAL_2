# Parcial Práctico Avanzado - Proyecto Docker (PHP + MySQL)

Aplicación web contenerizada con PHP y MySQL usando Docker y Docker Compose.

## Descripción del Proyecto

Este proyecto es una aplicación web básica en PHP que permite gestionar usuarios mediante una interfaz web y una API REST. La aplicación se conecta a una base de datos MySQL y está completamente contenerizada usando Docker.

### Funcionalidades

- **Interfaz Web**: Visualización y creación de usuarios mediante formulario HTML
- **API REST**:
  - `GET /users.php` - Obtener lista de usuarios en formato JSON
  - `POST /users.php` - Crear nuevos usuarios
- **Base de datos MySQL** con persistencia de datos
- **Datos de prueba** precargados automáticamente

## Estructura del Proyecto

```
project-root/
├── app/
│   ├── index.php         # Interfaz web principal
│   ├── users.php         # API REST para gestión de usuarios
│   └── Dockerfile        # Imagen personalizada de PHP
├── db/
│   └── init.sql          # Script de inicialización de BD
├── docker-compose.yml    # Configuración de servicios Docker
├── .env                  # Variables de entorno (no subir a Git)
├── .env.example          # Ejemplo de variables de entorno
└── README.md             # Este archivo
```

## Requisitos Previos

- Docker Desktop instalado
- Docker Compose instalado
- Cuenta en Docker Hub (para push de imágenes)
- Git instalado

## Configuración Inicial

### 1. Clonar el repositorio

```bash
git clone https://github.com/Daza04/YOHAN_DAZA_PARCIAL_2.git
cd YOHAN_DAZA_PARCIAL_2
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
```

Editar el archivo `.env` con tus credenciales (opcional, ya tiene valores por defecto):

```env
DB_HOST=db
DB_NAME=app_db
DB_USER=appuser
DB_PASSWORD=secure_password_123
```

## Construcción y Despliegue

### Opción 1: Usar imagen de Docker Hub (Recomendado)

La imagen ya está disponible en Docker Hub. Solo ejecutar:

```bash
docker-compose up -d
```

### Opción 2: Construir imagen localmente

Si deseas construir la imagen desde cero:

```bash
# Construir la imagen
cd app
docker build -t daza04/yohan_daza_parcial2:1.0 .

# (Opcional) Subir a Docker Hub
docker login
docker push daza04/yohan_daza_parcial2:1.0

# Volver al directorio raíz
cd ..

# Levantar los servicios
docker-compose up -d
```

## Uso de la Aplicación

### Acceder a la interfaz web

Abrir en el navegador:
```
http://localhost:8080
```

### Usar la API REST

**Obtener lista de usuarios (GET):**
```bash
curl http://localhost:8080/users.php
```

**Crear un nuevo usuario (POST):**
```bash
curl -X POST http://localhost:8080/users.php \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Pedro Martínez","email":"pedro.martinez@example.com"}'
```

### Acceder a la base de datos

```bash
docker exec -it mysql-db mysql -u appuser -p
# Contraseña: secure_password_123 (o la que hayas configurado en .env)
```

Comandos útiles dentro de MySQL:
```sql
USE app_db;
SHOW TABLES;
SELECT * FROM users;
```

## Comandos Docker Útiles

### Ver logs de los contenedores
```bash
docker-compose logs -f
docker-compose logs app
docker-compose logs db
```

### Detener los servicios
```bash
docker-compose down
```

### Detener y eliminar volúmenes (perderás los datos)
```bash
docker-compose down -v
```

### Reiniciar los servicios
```bash
docker-compose restart
```

### Ver estado de los contenedores
```bash
docker-compose ps
```

### Reconstruir la imagen
```bash
docker-compose build --no-cache
docker-compose up -d
```

## Detalles Técnicos

### Servicios Docker

#### Servicio `app` (PHP + Apache)
- **Imagen**: `daza04/yohan_daza_parcial2:1.0`
- **Puerto**: 8080 → 80
- **Base**: php:8.2-apache
- **Extensiones**: pdo, pdo_mysql, mysqli

#### Servicio `db` (MySQL)
- **Imagen**: mysql:8
- **Puerto**: 3306
- **Volumen**: `mysql-data` para persistencia
- **Inicialización**: Script `init.sql` se ejecuta automáticamente

### Red
- Red bridge compartida: `app-network`
- Comunicación interna entre servicios por nombre de servicio

### Persistencia
- Volumen Docker `mysql-data` para datos de MySQL
- Los datos persisten incluso si se eliminan los contenedores

## Base de Datos

### Tabla `users`

| Campo      | Tipo          | Descripción                |
|------------|---------------|----------------------------|
| id         | INT           | Primary Key, Auto-increment|
| nombre     | VARCHAR(100)  | Nombre del usuario         |
| email      | VARCHAR(100)  | Email único                |
| created_at | TIMESTAMP     | Fecha de creación          |

### Datos de prueba incluidos

El script `init.sql` incluye 3 usuarios de prueba:
1. Juan Pérez (juan.perez@example.com)
2. María García (maria.garcia@example.com)
3. Carlos López (carlos.lopez@example.com)

## Resolución de Problemas

### La aplicación no se conecta a MySQL
1. Verificar que el contenedor de MySQL esté en estado healthy:
   ```bash
   docker-compose ps
   ```
2. Ver logs de MySQL:
   ```bash
   docker-compose logs db
   ```
3. Esperar unos segundos a que MySQL termine de iniciar

### Error al construir la imagen
- Asegúrate de estar en el directorio `app/` al ejecutar `docker build`
- Verificar que Docker Desktop esté corriendo

### Puerto 8080 ya está en uso
- Cambiar el puerto en `docker-compose.yml`:
  ```yaml
  ports:
    - "9090:80"  # Usar puerto 9090 en lugar de 8080
  ```

### Email duplicado al insertar usuario
- La columna email es UNIQUE, no puedes insertar el mismo email dos veces
- Usar un email diferente

## Autor

**Yohan Daza**
- Docker Hub: [daza04/yohan_daza_parcial2](https://hub.docker.com/r/daza04/yohan_daza_parcial2)
- GitHub: [Daza04/YOHAN_DAZA_PARCIAL_2](https://github.com/Daza04/YOHAN_DAZA_PARCIAL_2)

## Licencia

Proyecto académico - Parcial Práctico Avanzado
