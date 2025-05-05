# Proyecto Prueba Técnica

Este proyecto está construido utilizando una Arquitectura Hexagonal con PHP, Doctrine y Docker.

## Requisitos

Antes de comenzar, asegúrate de tener instalado:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Make](https://www.gnu.org/software/make/) (opcional, pero recomendado)

## Inicio Rápido

La forma más sencilla de configurar y ejecutar el proyecto es utilizando el `Makefile` proporcionado:

```bash
# Configurar todo el entorno (construir contenedores, instalar dependencias, inicializar base de datos)
make setup

# Iniciar la aplicación
make start
```

Después de iniciar, la aplicación estará disponible en:
- Aplicación Web: http://localhost:8080
- Administrador de Base de Datos (Adminer): http://localhost:8081
  - Servidor: mysql
  - Usuario: user
  - Contraseña: password
  - Base de datos: app_db

## Configuración Manual

Si prefieres no utilizar el `Makefile`, puedes ejecutar los comandos manualmente:

```bash
# Construir e iniciar contenedores
docker-compose up -d

# Instalar dependencias PHP
docker-compose exec php composer install

# Inicializar base de datos
docker-compose exec php php bin/console doctrine:database:create --if-not-exists
docker-compose exec php php bin/console doctrine:schema:update --force
```

## Comandos Disponibles

El proyecto incluye muchos comandos útiles en el `Makefile`:

```bash
# Iniciar contenedores
make start

# Detener contenedores
make stop

# Reiniciar contenedores
make restart

# Ejecutar pruebas
make test

# Acceder al contenedor PHP
make bash-php

# Acceder al contenedor MySQL
make bash-mysql

# Ver registros
make logs

# Instalar dependencias
make composer-install

# Corregir estilo de código
make cs-fix
```

## Estructura del Proyecto

El proyecto sigue un Diseño Dirigido por el Dominio y Arquitectura Hexagonal:

- `/src/Domain`: Contiene toda la lógica de negocio, entidades e interfaces
- `/src/Application`: Contiene casos de uso y servicios de aplicación
- `/src/Infrastructure`: Contiene implementaciones de interfaces (repositorios, manejadores de eventos, etc.)
- `/src/UserInterface`: Contiene controladores y endpoints de API

## Entorno Docker

El entorno Docker incluye:

1. **PHP 8.1 con Apache**: Configurado con Doctrine, Composer y extensiones requeridas
2. **MySQL 8.0**: Servidor de base de datos
3. **Adminer**: Herramienta de administración de base de datos

## Configuración

- Las configuraciones del entorno Docker están en `docker-compose.yml`
- Las configuraciones de PHP se pueden encontrar en `docker/php/php.ini`
- Los scripts de inicialización de MySQL están en `docker/mysql/init/`

## Solución de Problemas

Si encuentras algún problema:

1. Revisa los registros: `make logs`
2. Reinicia los contenedores: `make restart`
3. Reconstruye los contenedores: `make build`

## Ejecutando Pruebas

Para ejecutar el conjunto de pruebas:

```bash
make test
```
