# proyecto desa trainer

Este repositorio contiene el desarrollo progresivo del **DESA Trainer**, una aplicación que simula dispositivos de entrenamiento de desfibrilación externa semiautomática (DESA). Este proyecto se desarrolla en **Laravel**, con el objetivo de que el alumnado implemente funcionalidades a medida que aprenden conceptos de desarrollo web.

---

## contenido del repositorio

A medida que se avance en el desarrollo, el repositorio se actualizará con las siguientes secciones:

1. **Estructura inicial del proyecto Laravel**
   - Configuración básica de Laravel.
   - Configuración de base de datos.

2. **Listado de dispositivos DESA**
   - Registro de modelos específicos de DESA Trainer.
   - Visualización de dispositivos registrados.

3. **Escenarios de entrenamiento**
   - Creación de escenarios interactivos.
   - Pausas, instrucciones y transiciones.

4. **Subida de imágenes y archivos de audio**
   - Implementación de almacenamiento en Laravel.

5. **Autenticación de usuarios**
   - Gestión de roles y perfiles de usuario.

6. **Pruebas y validaciones**
   - Testing de funcionalidades implementadas.

---

## requisitos previos

Para ejecutar este proyecto en tu entorno local, asegúrate de tener instalado lo siguiente:

- [PHP 8.1 o superior](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL o MariaDB](https://www.mysql.com/)
- Servidor local (XAMPP, Laragon, etc.) o Docker

---

## instalación

Sigue estos pasos para ejecutar el proyecto en tu máquina local:

1. **Clona el repositorio**
   ```bash
   git clone https://github.com/tuusuario/nombre-repositorio.git
   cd nombre-repositorio
   ```

2. **Instala las dependencias**
   ```bash
   composer install
   ```

3. **Configura el entorno**
   Copia el archivo `.env.example` y renómbralo como `.env`. Luego configura tu base de datos y otros parámetros:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Crea la base de datos**
   Configura la conexión a tu base de datos en el archivo `.env` y ejecuta las migraciones:
   ```bash
   php artisan migrate
   ```

5. **Inicia el servidor**
   ```bash
   php artisan serve
   ```

Accede al proyecto en `http://127.0.0.1:8000`.

---

## estructura de directorios

```plaintext
proyecto-desa-trainer/
├── app/                  # Controladores, modelos y lógica principal
├── database/             # Migraciones y seeders
├── public/               # Recursos públicos (CSS, JS, imágenes)
├── resources/            # Vistas Blade y archivos de recursos
├── routes/               # Rutas de la aplicación
├── .env.example          # Configuración de entorno
├── composer.json         # Dependencias de Laravel
└── README.md             # Este archivo
```

---

## colaboración y participación

Este proyecto se desarrolla de manera progresiva y colaborativa. **Tu participación es clave**:

1. **Sigue las actualizaciones del repositorio**.
2. **Crea ramas nuevas para cada funcionalidad o corrección**.
   ```bash
   git checkout -b feature/nombre-funcionalidad
   ```
3. **Haz tus commits de manera descriptiva**:
   ```bash
   git commit -m "Implementada funcionalidad de listado de dispositivos"
   ```
4. **Envía un Pull Request (PR)** para revisión.

---

## licencia

Este proyecto está licenciado bajo la [MIT License](LICENSE).

---

## contacto

Si tienes dudas o sugerencias, puedes escribirme a **sergioramos@ieselrincon.es** o crear un [issue](https://github.com/tuusuario/nombre-repositorio/issues) en el repositorio.

---

_**IES El Rincón | Proyecto DESA Trainer | Desarrollo de aplicaciones web**_
