# Task Manager Laravel — Docker (NIVEL 4)

## Volúmenes y Redes explicados al detalle

---

# GUÍA RÁPIDA DE INSTALACIÓN

## Requisitos

| Herramienta | Versión mínima | Cómo verificar |
|-------------|---------------|----------------|
| **Docker Desktop** | 24+ | `docker --version` |
| **Docker Compose** | 2.20+ | `docker compose version` |
| **Git** | 2.30+ | `git --version` |
| **Sistema** | Windows 10+/Linux/macOS | — |

> ⚠ **Windows:** Debes tener WSL2 instalado y Docker Desktop configurado con el backend WSL2.
> Para verificar: `wsl --status`

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone https://github.com/jhoanchq/task-manager-laravel.git
cd task-manager-laravel
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
# O usar la versión Docker específica:
cp docker/.env.example .env
```

> Edita `.env` si necesitas cambiar credenciales de BD, Redis o el puerto de Nginx.

### 3. Iniciar los contenedores

```bash
docker compose -f docker/docker-compose.yml up -d
```

Este comando:
- Descarga las imágenes (MySQL 8.4, Redis 7.4, Nginx 1.27, PHP 8.3)
- Construye la imagen de la aplicación Laravel
- Crea las redes `taskman_frontend` y `taskman_backend`
- Crea los volúmenes `taskman_db_data`, `taskman_app_storage`, `taskman_redis_data`, `taskman_nginx_logs`
- Inicia los 4 contenedores en orden: db → redis → app → nginx

**Primera vez:** La descarga+construcción toma **3-8 minutos** (depende del internet).

Para ver el progreso:
```bash
docker compose -f docker/docker-compose.yml logs -f
```

### 4. Ejecutar migraciones y seeders

```bash
# Esperar a que MySQL esté listo (15-30 segundos)
docker compose exec app php artisan migrate --seed
```

Este comando crea las tablas de la base de datos y poblarla con datos de prueba:
- **Usuario demo:** `demo@taskmanager.com` / `password`
- **10 tareas de ejemplo** (5 pendientes, 3 en progreso, 2 completadas)

### 5. Storage link

```bash
docker compose exec app php artisan storage:link
```

### 6. Abrir en el navegador

```
http://localhost
```

Deberías ver la página de bienvenida de Laravel. Ve a `/login` e ingresa con el usuario demo.

## Comandos de uso diario

```bash
# Ver logs en tiempo real
docker compose -f docker/docker-compose.yml logs -f

# Ver logs de un servicio específico
docker compose -f docker/docker-compose.yml logs -f app

# Ejecutar comandos Artisan
docker compose -f docker/docker-compose.yml exec app php artisan tinker
docker compose -f docker/docker-compose.yml exec app php artisan make:model Producto
docker compose -f docker/docker-compose.yml exec app php artisan test

# Acceder a la base de datos
docker compose -f docker/docker-compose.yml exec db mysql -u taskman -p taskmanager

# Detener servicios (los datos se conservan en volúmenes)
docker compose -f docker/docker-compose.yml down

# Detener y ELIMINAR volúmenes (BORRA BD + Redis + storage)
docker compose -f docker/docker-compose.yml down -v

# Reconstruir la imagen de la app (tras cambios en composer.json)
docker compose -f docker/docker-compose.yml build app
docker compose -f docker/docker-compose.yml up -d
```

## Probar la API REST

Una vez funcionando, prueba la API con Postman o cURL:

```bash
# Registrar un usuario
curl -X POST http://localhost/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"12345678","password_confirmation":"12345678"}'

# O usando el usuario demo:
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@taskmanager.com","password":"password"}'

# Usar el token recibido para listar tareas
curl -X GET http://localhost/api/v1/tasks \
  -H "Authorization: Bearer TU_TOKEN_AQUI"

# Ver documentación Swagger interactiva
# Abrir en navegador: http://localhost/docs
```

## Producción

Para desplegar en un servidor de producción:

```bash
docker compose -f docker/docker-compose.yml -f docker/docker-compose.prod.yml up -d
```

Esto usa `Dockerfile.prod` (imagen optimizada, multi-etapa, sin Composer ni Node en runtime).

---

# PARTE I: REDES EN DOCKER

## 1.1 ¿Qué es una red en Docker?

Una **red Docker** es un mecanismo de aislamiento que permite que los contenedores se comuniquen entre sí o con el host. Cada red crea un namespace de red virtual. Los contenedores dentro de la misma red pueden verse por nombre de contenedor (DNS interno), mientras que los de redes diferentes están completamente aislados.

### Tipos de drivers de red

| Driver | Aislamiento | Alcance | DNS interno | Uso típico |
|--------|-------------|---------|-------------|------------|
| `bridge` | Medio | Un solo host | Sí (por nombre de contenedor) | Desarrollo local, apps multi-contenedor |
| `host` | Ninguno | Un solo host | No | Servicios que necesitan rendimiento de red bruto |
| `overlay` | Alto | Multi-host (Swarm/K8s) | Sí | Clústers de producción |
| `none` | Total | Un solo host | No | Contenedores sin red (aislados) |
| `macvlan` | Medio | Un solo host | No | Contenedores con IP propia en LAN física |

## 1.2 Topología de este proyecto

```
┌─────────────────────────────────────────────────────────────────┐
│                        HOST (Windows/Linux)                     │
│                                                                 │
│   localhost:80                                                   │
│       │                                                         │
│       ▼                                                         │
│  ┌───────────────────┐                                          │
│  │ frontend_network  │ ←── bridge                               │
│  │ (expuesta)        │                                          │
│  │                   │                                          │
│  │  ┌─────────┐      │                                          │
│  │  │ nginx   │      │  Puerto 80:80 mapeado al host           │
│  │  │:80      │──────┼──→ host:80                              │
│  │  └────┬────┘      │                                          │
│  │       │           │                                          │
│  │  ┌────▼────┐      │                                          │
│  │  │  app    │      │  app se comunica con nginx vía           │
│  │  │:9000   │      │  frontend_network                        │
│  │  └────┬────┘      │                                          │
│  └───────┼───────────┘                                          │
│          │                                                      │
│  ┌───────┼───────────┐                                          │
│  │backend_network    │ ←── bridge (AISLADA del mundo exterior) │
│  │       │           │                                          │
│  │  ┌────▼────┐  ┌──▼─────┐                                    │
│  │  │  db     │  │ redis  │                                    │
│  │  │:3306   │  │:6379   │  Solo app puede alcanzarlos        │
│  │  └─────────┘  └────────┘                                    │
│  └──────────────────────────────────────────────────────────────┘
└─────────────────────────────────────────────────────────────────┘
```

### ¿Por qué dos redes?

| Aspecto | Una sola red | Dos redes (esta arquitectura) |
|---------|-------------|-------------------------------|
| **Seguridad** | Todos los servicios son accesibles entre sí | db y redis NO son accesibles desde nginx ni desde el host |
| **Acoplamiento** | Alto — cambiar un servicio afecta a todos | Bajo — cada capa puede modificarse independientemente |
| **Escalabilidad** | Limitada | Puedes escalar app horizontalmente sin exponer la BD |
| **Ataque** | Si nginx se compromete, atacante tiene acceso directo a la BD | Si nginx se compromete, solo accede a app (no a la BD directamente) |

### Reglas de DNS Interno

Docker Compose crea un **servidor DNS interno** para cada red. Los contenedores resuelven nombres de otros contenedores en la **misma red**. El nombre DNS es el nombre del servicio en docker-compose.yml:

| Servicio | Se resuelve como | Red | Visible desde |
|----------|-----------------|-----|---------------|
| nginx | `nginx` | frontend | app, host (por puerto mapeado) |
| app | `app` | frontend + backend | nginx, db, redis |
| db | `db` | backend | app |
| redis | `redis` | backend | app |

**Ejemplo:** En el `.env` del proyecto, `DB_HOST=db` funciona porque `app` y `db` están en la misma red `backend_network`.

## 1.3 Configuración de redes en docker-compose.yml

```yaml
networks:
  frontend_network:
    driver: bridge
    name: taskman_frontend
  backend_network:
    driver: bridge
    name: taskman_backend
```

- `driver: bridge` → Red tipo puente (estándar para un solo host)
- `name: taskman_frontend` → Nombre real de la red en Docker (útil con `docker network ls`)

Cada servicio se asigna a una o más redes con `networks:`.

---

# PARTE II: VOLÚMENES EN DOCKER

## 2.1 ¿Qué es un volumen en Docker?

Un **volumen Docker** es un mecanismo para persistir datos generados por contenedores. A diferencia del sistema de archivos del contenedor (efímero), los volúmenes sobreviven a la muerte, reinicio o eliminación del contenedor.

### Tipos de montaje

| Tipo | Almacenamiento | Creado por | Persistencia | Uso |
|------|---------------|------------|-------------|-----|
| **Named Volume** | `/var/lib/docker/volumes/` | Docker | ✅ Alta | Datos de BD, Redis, logs |
| **Bind Mount** | Cualquier ruta del host | Usuario | ✅ Alta | Código fuente, configs |
| **tmpfs mount** | RAM (no en disco) | Docker | ❌ Efímero | Datos temporales, sesiones |
| **Anonymous Volume** | `/var/lib/docker/volumes/` | Docker automático | ✅ Alta | Caché, datos sin nombre |

### Jerarquía de almacenamiento

```
Host filesystem
├── /var/lib/docker/volumes/          ← Named volumes (gestionados por Docker)
│   ├── taskman_db_data/              ← Datos de MySQL
│   ├── taskman_app_storage/          ← Storage de Laravel
│   ├── taskman_redis_data/           ← AOF de Redis
│   └── taskman_nginx_logs/           ← Logs de Nginx
│
├── /home/user/project/               ← Bind mount (código fuente)
│   ├── app/
│   ├── nginx/
│   ├── docker-compose.yml
│   └── Dockerfile
```

## 2.2 Volúmenes de este proyecto

### Volumen `db_data` — Datos de MySQL

```yaml
volumes:
  db_data:
    driver: local
    name: taskman_db_data
```

**Contenido:** Archivos de datos de MySQL (`/var/lib/mysql/`).  
**Propósito:** Garantizar que las tablas, registros y configuraciones de la base de datos sobrevivan a `docker compose down` o `docker compose up --force-recreate`.  
**Ciclo de vida:** Los datos persisten hasta que ejecutes `docker compose down -v` (elimina volúmenes) o `docker volume rm taskman_db_data`.  
**Backup:**
```bash
# Crear backup
docker run --rm -v taskman_db_data:/data -v .:/backup alpine tar czf /backup/db_$(date +%Y%m%d).tar.gz -C /data .

# Restaurar backup
docker run --rm -v taskman_db_data:/data -v ./backups:/backup alpine tar xzf /backup/db_20260713.tar.gz -C /data
```

### Volumen `app_storage` — Storage de Laravel

```yaml
volumes:
  app_storage:
    driver: local
    name: taskman_app_storage
```

**Contenido:** `storage/app/`, `storage/logs/`, `storage/framework/cache/`, `storage/framework/sessions/`, `storage/framework/views/`.  
**Propósito:** Separar los datos generados por la aplicación (logs, archivos subidos, cache compilado de Blade) del código fuente.  
**Problema que resuelve:** Sin este volumen, al reconstruir el contenedor (`docker compose up --build`), se pierden los logs y archivos subidos. Además, `php artisan storage:link` y los permisos deben reconfigurarse cada vez.  
**Importante:** Este volumen convive con el bind mount `./:/var/www`. El orden de montaje importa: el bind mount monta la carpeta raíz, y el volumen monta `storage` por encima (sobrescribe la carpeta dentro del bind mount).

### Volumen `redis_data` — Datos de Redis

```yaml
volumes:
  redis_data:
    driver: local
    name: taskman_redis_data
```

**Contenido:** Archivos AOF (Append Only File) y RDB (snapshots) de Redis en `/data/`.  
**Propósito:** Persistir las colas de jobs, sesiones y caché. Si Redis se reinicia sin este volumen, se pierden todos los jobs pendientes en la cola.  
**Configuración relacionada:** `command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD:-}` — Redis escribe cada operación en el AOF, garantizando durabilidad.

### Volumen `nginx_logs` — Logs de Nginx

```yaml
volumes:
  nginx_logs:
    driver: local
    name: taskman_nginx_logs
```

**Propósito:** Conservar el historial de accesos y errores de Nginx. Sin este volumen, los logs se pierden cada vez que el contenedor se recrea (útil para debuggear problemas intermitentes).

## 2.3 Bind Mount vs Named Volume — Cuándo usar cada uno

| Situación | Usar | Motivo |
|-----------|------|--------|
| Código fuente en desarrollo | Bind mount | Editas en el host, los cambios se reflejan al instante en el contenedor |
| Configuración de Nginx | Bind mount (readonly) | Se versiona en Git, se edita desde el host |
| Scripts SQL de inicialización | Bind mount | Se versionan en Git |
| Base de datos MySQL | Named volume | Los datos NO deben depender del sistema de archivos del host |
| Archivos subidos por usuarios | Named volume | Persisten aunque se reconstruya el contenedor |
| Logs de la aplicación | Named volume | Se acumulan y no deben perderse al reiniciar |
| Caché/Build de frontend | Bind mount (en dev) o copia (en prod) | En dev quieres hot reload; en prod quieres assets compilados en la imagen |

## 2.4 Comandos útiles para volúmenes

```bash
# Listar todos los volúmenes
docker volume ls

# Inspeccionar un volumen (ver ruta en el host)
docker volume inspect taskman_db_data

# Ver cuánto espacio ocupa un volumen
docker system df -v | grep taskman

# Respaldar un volumen (copia de seguridad)
docker run --rm -v taskman_db_data:/data -v $(pwd):/backup alpine tar czf /backup/db_backup.tar.gz -C /data .

# Restaurar un volumen
docker run --rm -v taskman_db_data:/data -v $(pwd):/backup alpine tar xzf /backup/db_backup.tar.gz -C /data

# Eliminar un volumen (CUIDADO: pérdida de datos)
docker volume rm taskman_db_data

# Eliminar TODOS los volúmenes no usados
docker volume prune
```

---

# PARTE III: COMANDOS DE OPERACIÓN

## Iniciar el proyecto

```bash
# 1. Clonar el repositorio
git clone https://github.com/jhoanchq/task-manager-laravel.git
cd task-manager-laravel

# 2. Copiar variables de entorno (Docker)
cp docker/.env.example .env

# 3. Iniciar servicios
docker compose up -d

# 4. Ejecutar migraciones y seeders
docker compose exec app php artisan migrate --seed

# 5. Crear enlace simbólico de storage
docker compose exec app php artisan storage:link

# 6. Abrir en navegador
start http://localhost
```

## Comandos diarios

```bash
# Ver logs de todos los servicios
docker compose logs -f

# Ver logs de un servicio específico
docker compose logs -f app

# Ejecutar comandos Artisan
docker compose exec app php artisan tinker
docker compose exec app php artisan make:model Producto
docker compose exec app php artisan test

# Acceder a MySQL
docker compose exec db mysql -u taskman -p taskmanager

# Detener servicios (sin eliminar volúmenes)
docker compose down

# Detener y eliminar volúmenes (BORRA BD, Redis y storage)
docker compose down -v
```

## Monitoreo de redes

```bash
# Listar redes
docker network ls

# Inspeccionar contenedores conectados a una red
docker network inspect taskman_frontend

# Ver IPs de los contenedores
docker inspect taskman-app --format '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}'
docker inspect taskman-db   --format '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}'
```

---

# PARTE IV: ARQUITECTURA COMPLETA

```
┌─────────────────────────────────────────────────────────────────────┐
│                      ARQUITECTURA DOCKER                            │
│                                                                     │
│  docker-compose.yml                                                 │
│  ├── Services                                                        │
│  │   ├── nginx  (puerto 80:80)                                      │
│  │   ├── app    (PHP-FPM :9000)                                     │
│  │   ├── db     (MySQL  :3306)                                      │
│  │   └── redis  (Redis  :6379)                                      │
│  ├── Volumes                                                         │
│  │   ├── db_data      → /var/lib/docker/volumes/taskman_db_data     │
│  │   ├── app_storage  → /var/lib/docker/volumes/taskman_app_storage │
│  │   ├── redis_data   → /var/lib/docker/volumes/taskman_redis_data  │
│  │   └── nginx_logs   → /var/lib/docker/volumes/taskman_nginx_logs  │
│  └── Networks                                                        │
│      ├── frontend_network (bridge: taskman_frontend)                │
│      └── backend_network  (bridge: taskman_backend)                 │
│                                                                     │
│  Flujo de petición:                                                  │
│  Host:80 → nginx:80 → app:9000 → (db:3306 | redis:6379)           │
└─────────────────────────────────────────────────────────────────────┘
```

---

*Documentación generada para Implementación de Plataformas Web — Semana 12*
*Docente: Jhoan Benito Chite Quispe*
*Basado en: https://github.com/jhoanchq/task-manager-laravel*
