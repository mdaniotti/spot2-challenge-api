# 🚀 Spot2 URL Shortener API

API RESTful para el acortador de URLs construida con Laravel 12 y MongoDB. Genera URLs cortas de máximo 8 caracteres fáciles de leer.

## 📋 Características

- ✅ **URLs cortas únicas** (máximo 8 caracteres)
- ✅ **Validación de URLs** robusta
- ✅ **Estadísticas de clics** en tiempo real
- ✅ **URLs con expiración** opcional
- ✅ **Documentación Swagger** automática
- ✅ **Sanitización MongoDB** integrada
- ✅ **API RESTful** completa
- ✅ **Manejo de errores** estructurado

## 🛠️ Tecnologías

- **Laravel 12** - Framework PHP
- **MongoDB** - Base de datos NoSQL
- **Swagger/OpenAPI** - Documentación automática
- **PHP 8.2+** - Lenguaje de programación
- **Composer** - Gestor de dependencias

## 🚀 Instalación Rápida

### Prerrequisitos

- PHP 8.2 o superior
- Composer
- MongoDB (local o Atlas)
- Git

### 1. Instalar Dependencias

```bash
composer install
```

### 2. Configurar Variables de Entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurar Base de Datos

Edita el archivo `.env`:

```env
APP_NAME="Spot2 URL Shortener"
APP_ENV=local
APP_KEY=base64:tu_clave_generada
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mongodb
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=spot2_db
```

### 4. Ejecutar la Aplicación

```bash
# Opción 1: Solo API
php artisan serve

```

### 5. Acceder a la Documentación

- **API**: http://localhost:8000
- **Swagger**: http://localhost:8000/api/documentation

## 📚 Endpoints de la API

### URLs

| Método   | Endpoint         | Descripción             | Parámetros                              |
| -------- | ---------------- | ----------------------- | --------------------------------------- |
| `GET`    | `/api/urls`      | Listar todas las URLs   | `?page=1&limit=10`                      |
| `POST`   | `/api/urls`      | Crear nueva URL corta   | `original_url`, `expires_at` (opcional) |
| `GET`    | `/api/urls/{id}` | Obtener URL específica  | `id`                                    |
| `DELETE` | `/api/urls/{id}` | Eliminar URL            | `id`                                    |
| `GET`    | `/{shortCode}`   | Redireccionar URL corta | `shortCode`                             |

## 🗄️ Estructura de Base de Datos

### Colección: `short_urls`

```json
{
  "_id": "ObjectId",
  "original_url": "string",
  "short_code": "string",
  "clicks": "integer",
  "expires_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Índices

- `short_code` (único)
- `created_at` (descendente)
- `expires_at` (TTL para limpieza automática)

## 🔧 Comandos Útiles

### Desarrollo

```bash
# Instalar dependencias
composer install

# Generar clave de aplicación
php artisan key:generate

# Limpiar cache
php artisan config:clear
php artisan cache:clear

```

### Base de Datos

```bash
# Ejecutar seeders
php artisan db:seed

# Ejecutar seeders específicos
php artisan db:seed --class=ShortUrlSeeder
```

### Testing

```bash
# Ejecutar todos los tests
composer run test

# Ejecutar tests específicos
php artisan test --filter=ShortUrlTest

```

### Producción

```bash
# Optimizar para producción
composer install --optimize-autoloader --no-dev

# Cache de configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🧪 Testing

### Ejecutar Tests

```bash
# Todos los tests
composer run test

# Tests específicos
php artisan test tests/Feature/ShortUrlTest.php
```

## 📊 Monitoreo y Logs

### Logs de Aplicación

```bash
# Ver logs en tiempo real
php artisan pail

# Logs específicos
tail -f storage/logs/laravel.log
```

### Métricas Importantes

- **URLs creadas por día**
- **Clicks totales**
- **URLs más populares**
- **Tiempo de respuesta promedio**

## 🔒 Seguridad

### Middleware Implementado

- **SanitizeMongoInput**: Previene inyección NoSQL
- **CORS**: Configurado para frontend
- **Rate Limiting**: Límite de requests por minuto
- **Validation**: Validación robusta de URLs

## 🚀 Despliegue

### Variables de Entorno de Producción

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

MONGODB_URI=mongodb+srv://usuario:password@cluster.mongodb.net/spot2_db
MONGODB_DATABASE=spot2_db
```

### Optimizaciones

```bash
# Cache de configuración
php artisan config:cache

# Cache de rutas
php artisan route:cache

# Optimizar autoloader
composer install --optimize-autoloader --no-dev
```

## 🐛 Troubleshooting

### Problemas Comunes

#### 1. Error de Conexión MongoDB

```bash
# Verificar conexión
php artisan tinker
>>> DB::connection()->getPing()
```

#### 2. URLs no se redirigen

```bash
# Verificar rutas
php artisan route:list

# Verificar middleware
php artisan route:list --name=redirect
```

#### 3. Swagger no carga

```bash
# Generar documentación
php artisan l5-swagger:generate

# Limpiar cache
php artisan config:clear
```

#### 4. Tests fallan

```bash
# Verificar configuración de testing
php artisan test --env=testing

# Verificar base de datos de testing
php artisan migrate --env=testing
```

### Despliegue Automático en EC2

La aplicación está configurada para desplegarse automáticamente en AWS EC@ con cada push a la rama principal.

#### Configuración del Pipeline

**1. Archivo de Configuración**

El archivo `.github/workflows/deploy.yml` en la raíz del proyecto define el pipeline de CI/CD:

**2. Proceso de Deploy Automático**

Cada vez que hagas push a la rama configurada:

```bash
git add .
git commit -m "Nueva funcionalidad"
git push origin main
```

**3. URLs de Acceso**

- **URL de Producción**: `https://api.grupokoiviajes.com.ar/api`

### GitHub Actions (Ejemplo)

```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.2"
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: composer run test
```

## 📝 Contribución

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📄 Licencia

MIT License - Ver archivo `LICENSE` para detalles.

## 🆘 Soporte

- **Documentación API**: http://localhost:8000/api/documentation
- **Issues**: GitHub Issues
- **Logs**: `storage/logs/laravel.log`
