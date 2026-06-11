# 📋 Task Manager — Laravel 12

Aplicación web **Task Manager** con interfaz gráfica y **API REST** con autenticación, desarrollada en **Laravel 12** con **Laravel Breeze** y **Sanctum**.

```
Implementación de Plataformas Web — Semana 10
IESTP "Jorge Basadre" | Docente: Jhoan Benito Chite Quispe
```

---

## 🚀 Requisitos

- PHP 8.2+
- Composer 2.x
- Node.js 18+ y NPM
- SQLite (incluido en PHP)
- Git

---

## 📦 Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/jhoanchq/task-manager-laravel.git
cd task-manager-laravel

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Instalar y compilar assets frontend
npm install
npm run build

# 5. Ejecutar migraciones y seeders
php artisan migrate
php artisan db:seed

# 6. Iniciar servidor de desarrollo
php artisan serve
```

La aplicación estará disponible en **http://localhost:8000**

---

## 👤 Usuario Demo

| Campo    | Valor                    |
|----------|--------------------------|
| Email    | `demo@taskmanager.com`   |
| Password | `password`               |

El seeder crea **10 tareas de ejemplo** (5 pendientes, 3 en progreso, 2 completadas).

---

## 🖥️ Interfaz Web (Blade + Tailwind)

### Rutas web

| Método | URI              | Nombre          | Descripción            |
|--------|------------------|-----------------|------------------------|
| GET    | `/`              | welcome         | Página de bienvenida   |
| GET    | `/dashboard`     | dashboard       | Redirige a /tasks      |
| GET    | `/register`      | register        | Registro de usuario    |
| GET    | `/login`         | login           | Inicio de sesión       |
| GET    | `/tasks`         | tasks.index     | Listar tareas          |
| GET    | `/tasks/create`  | tasks.create    | Formulario crear       |
| POST   | `/tasks`         | tasks.store     | Guardar tarea          |
| GET    | `/tasks/{id}`    | tasks.show      | Ver tarea              |
| GET    | `/tasks/{id}/edit`| tasks.edit     | Editar tarea           |
| PUT    | `/tasks/{id}`    | tasks.update    | Actualizar tarea       |
| DELETE | `/tasks/{id}`    | tasks.destroy   | Eliminar tarea         |

### Funcionalidades
- ✅ Registro y autenticación de usuarios
- ✅ CRUD completo de tareas
- ✅ Estados: Pendiente, En progreso, Completada
- ✅ Diseño responsive con Tailwind CSS
- ✅ Paginación de resultados
- ✅ Protección por políticas (solo el dueño accede a sus tareas)

---

## 🌐 API REST

### Autenticación

Todas las rutas de tareas requieren autenticación mediante **Bearer Token** (Sanctum).

| Método | Endpoint              | Auth | Descripción              |
|--------|-----------------------|------|--------------------------|
| POST   | `/api/v1/register`    | No   | Registrar nuevo usuario  |
| POST   | `/api/v1/login`       | No   | Iniciar sesión           |
| POST   | `/api/v1/logout`      | Sí   | Cerrar sesión            |
| GET    | `/api/v1/profile`     | Sí   | Obtener perfil           |
| GET    | `/api/v1/tasks`       | Sí   | Listar tareas            |
| POST   | `/api/v1/tasks`       | Sí   | Crear tarea              |
| GET    | `/api/v1/tasks/{id}`  | Sí   | Ver tarea                |
| PUT    | `/api/v1/tasks/{id}`  | Sí   | Actualizar tarea         |
| DELETE | `/api/v1/tasks/{id}`  | Sí   | Eliminar tarea           |

### Ejemplo de uso con cURL

```bash
# 1. Registro
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Juan","email":"juan@mail.com","password":"secret123","password_confirmation":"secret123"}'

# 2. Login (obtener token)
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@taskmanager.com","password":"password"}'

# 3. Listar tareas (con token)
curl -X GET http://localhost:8000/api/v1/tasks \
  -H "Authorization: Bearer 1|abc123..."

# 4. Crear tarea
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{"title":"Nueva tarea","description":"Descripción","status":"pending"}'

# 5. Actualizar tarea
curl -X PUT http://localhost:8000/api/v1/tasks/1 \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{"title":"Actualizada","status":"completed"}'

# 6. Eliminar tarea
curl -X DELETE http://localhost:8000/api/v1/tasks/1 \
  -H "Authorization: Bearer 1|abc123..."
```

---

## 📮 Postman Collection

Desde Scribe se generó automáticamente una colección de Postman:

### Importar la colección

1. Abre **Postman**
2. Ve a **File → Import** (o Ctrl+O)
3. Selecciona el archivo **`postman_collection.json`** (en la raíz del proyecto)
4. Haz clic en **Import**

### Configurar variables de entorno en Postman

1. Crea un nuevo **Environment** en Postman
2. Agrega las siguientes variables:

| Variable      | Valor inicial                      |
|---------------|------------------------------------|
| `base_url`    | `http://localhost:8000`            |
| `token`       | *(dejar vacío, se llena al login)* |

### Probar los endpoints

1. **Register** — Crea un nuevo usuario (no requiere token)
2. **Login** — Autentica y devuelve un token
3. Copia el token y asígnalo a la variable `token` en el entorno
4. **Get Profile** — Verifica que el token funciona
5. **List Tasks** — Obtiene tareas del usuario autenticado
6. **Create Task** — Crea una nueva tarea
7. **Get Task** — Obtiene detalle de una tarea
8. **Update Task** — Actualiza título, descripción o estado
9. **Delete Task** — Elimina una tarea

> 💡 **Tip:** En la pestaña **Authorization** de cada request protegido, selecciona `Bearer Token` e ingresa `{{token}}`.

---

## 📖 Documentación Swagger / OpenAPI

La documentación interactiva se genera automáticamente con **Scribe**.

### Ver la documentación

Inicia el servidor y visita:

```
http://localhost:8000/docs
```

### Interfaz interactiva

La documentación incluye:

- **Descripción** de cada endpoint
- **Parámetros** requeridos y opcionales
- **Códigos de respuesta** esperados
- **Ejemplos** de request y response en JSON
- **Try it out** — ¡Prueba los endpoints directamente desde el navegador!

### Archivos generados

| Archivo | Ubicación |
|---------|-----------|
| Documentación HTML | `http://localhost:8000/docs` |
| OpenAPI YAML | `storage/app/private/scribe/openapi.yaml` |
| Postman JSON | `postman_collection.json` (raíz) |

### Regenerar documentación

```bash
php artisan scribe:generate
```

---

## 📁 Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/          # Controladores API
│   │   │   ├── AuthController.php
│   │   │   └── TaskController.php
│   │   ├── Auth/            # Controladores de autenticación web
│   │   └── TaskController.php  # Controlador web
│   ├── Requests/
│   │   ├── StoreTaskRequest.php
│   │   └── UpdateTaskRequest.php
│   └── Resources/
│       └── TaskResource.php     # Transformer API
├── Models/
│   ├── Task.php
│   └── User.php
├── Policies/
│   └── TaskPolicy.php
database/
├── factories/
│   └── TaskFactory.php
├── migrations/
│   └── ..._create_tasks_table.php
└── seeders/
    └── DatabaseSeeder.php
resources/views/
├── tasks/                    # Vistas CRUD
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── edit.blade.php
└── layouts/
    ├── app.blade.php
    └── navigation.blade.php
routes/
├── api.php                   # Rutas API (v1)
├── web.php                   # Rutas web
└── auth.php                  # Rutas de autenticación
tests/
└── Feature/
    └── TaskApiTest.php       # Pruebas API
```

---

## 🧪 Tests

```bash
php artisan test --filter=TaskApiTest
```

**9 tests incluidos:**

| Test | Descripción |
|------|-------------|
| `user_can_register` | Registro de nuevo usuario |
| `user_can_login` | Inicio de sesión exitoso |
| `login_fails_with_invalid_credentials` | Login con credenciales inválidas |
| `unauthenticated_user_cannot_access_tasks` | Acceso sin token |
| `user_can_create_task` | Creación de tarea |
| `user_can_list_own_tasks` | Listado de tareas propias |
| `user_cannot_see_others_tasks` | No ver tareas de otros usuarios |
| `user_can_update_own_task` | Actualización de tarea propia |
| `user_can_delete_own_task` | Eliminación de tarea propia |

---

## 🛠️ Tecnologías

| Tecnología | Versión | Uso |
|------------|---------|-----|
| Laravel    | 12.x    | Framework PHP |
| PHP        | 8.2+    | Lenguaje backend |
| SQLite     | 3.x     | Base de datos |
| Laravel Breeze | 2.x | Autenticación web (Blade) |
| Laravel Sanctum | 4.x | Autenticación API |
| Tailwind CSS | 4.x | Estilos frontend |
| Alpine.js  | 3.x     | Interactividad frontend |
| Scribe     | 5.x     | Documentación OpenAPI |

---

## 📄 Licencia

Proyecto educativo — MIT License

Desarrollado para el curso **Implementación de Plataformas Web** — Semana 10
IESTP "Jorge Basadre" — Mollendo, 2026
