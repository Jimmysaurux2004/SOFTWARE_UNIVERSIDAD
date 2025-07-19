# Sistema Web de Tutorías Universitarias

**Primera Iteración - Metodología XP**

## 📋 Descripción del Proyecto

Sistema web profesional desarrollado en PHP nativo para gestionar tutorías universitarias. Implementa un enfoque minimalista y moderno con arquitectura MVC simple, diseñado siguiendo la metodología Extreme Programming (XP) para su primera iteración.

## 🎯 Historias de Usuario Implementadas

### HU01 - Solicitar Tutoría en Línea
- ✅ Formulario intuitivo y responsive para solicitar tutorías
- ✅ Validaciones en frontend (JavaScript) y backend (PHP)
- ✅ Almacenamiento seguro en base de datos
- ✅ Confirmación visual al enviar solicitud
- ✅ Pruebas de funcionalidad y usabilidad

### HU02 - Agendar Sesión con Docente
- ✅ Módulo de visualización de disponibilidad de docentes
- ✅ Lógica para prevenir choques de horario
- ✅ Interfaz intuitiva de selección de fecha y hora
- ✅ Confirmación automática tras agendamiento
- ✅ Pruebas de integración y manejo de errores

### HU03 - Ver Historial Académico y Tutorías
- ✅ Vista organizada por semestre y estado
- ✅ Consultas optimizadas a base de datos académica
- ✅ Identificación visual de cursos repetidos
- ✅ Acceso de solo lectura para estudiantes
- ✅ Pruebas de visualización y rendimiento

## 🏗️ Arquitectura del Sistema

### Estructura del Proyecto
```
/
├── app/
│   ├── controllers/         # Controladores MVC
│   ├── models/             # Modelos de datos
│   └── views/              # Vistas con HTML incrustado
├── config/
│   └── db.php              # Configuración de base de datos
├── public/
│   ├── css/                # Estilos CSS
│   ├── js/                 # JavaScript puro
│   └── img/                # Imágenes
├── routes/
│   └── web.php             # Definición de rutas
├── sql/
│   └── schema.sql          # Esquema de base de datos
├── views/                  # Páginas principales
├── docker-compose.yml      # Configuración Docker
├── Dockerfile              # Imagen personalizada
└── README.md              # Documentación
```

### Tecnologías Utilizadas
- **Backend**: PHP 8.2 nativo con PDO
- **Frontend**: HTML5, CSS3 moderno, JavaScript vanilla
- **Base de Datos**: MySQL 8.0
- **Contenedores**: Docker + Docker Compose
- **Servidor Web**: Apache 2.4
- **Gestión BD**: Adminer

## 🚀 Instalación y Configuración

### Prerrequisitos
- Docker y Docker Compose instalados
- Puerto 8095, 3309 y 8096 disponibles

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <url-repositorio>
   cd sistema-tutorias
   ```

2. **Levantar el entorno con Docker**
   ```bash
   docker-compose up -d
   ```

3. **Verificar que los contenedores estén ejecutándose**
   ```bash
   docker-compose ps
   ```

### Acceso al Sistema

| Servicio | URL | Puerto |
|----------|-----|--------|
| **Sistema Web** | http://localhost:8095 | 8095 |
| **Adminer (Gestor BD)** | http://localhost:8096 | 8096 |
| **MySQL** | localhost:3309 | 3309 |

### Credenciales de Base de Datos (Adminer)
- **Servidor**: mysql
- **Usuario**: root
- **Contraseña**: root
- **Base de datos**: tutorias_db

### Usuarios de Prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Estudiante** | juan.perez@universidad.edu | password |
| **Estudiante** | maria.garcia@universidad.edu | password |
| **Docente** | prof.rodriguez@universidad.edu | password |
| **Admin** | admin@universidad.edu | password |

## 🎨 Características de Diseño

### Paleta de Colores
- **Primario**: #3B82F6 (Azul)
- **Secundario**: #6B7280 (Gris)
- **Éxito**: #10B981 (Verde)
- **Advertencia**: #F59E0B (Amarillo)
- **Error**: #EF4444 (Rojo)

### Componentes UI
- Dashboard minimalista con navegación lateral
- Formularios responsivos con validaciones en tiempo real
- Tablas estilizadas con estados visuales
- Sistema de alertas contextual
- Animaciones suaves y microinteracciones

## 🔧 Funcionalidades Técnicas

### Seguridad
- Autenticación obligatoria con sesiones PHP
- Hashing seguro de contraseñas con `password_hash()`
- Validación y sanitización de entradas
- Prepared statements para prevenir SQL injection
- Protección de rutas sin autenticación

### Base de Datos
- **5 tablas principales** con relaciones FK
- **Datos de ejemplo** precargados
- **Índices optimizados** para consultas frecuentes
- **Comentarios descriptivos** en todas las tablas

### Responsive Design
- Mobile-first approach
- Breakpoints: 480px, 768px, 1024px
- Grid CSS y Flexbox para layouts
- Optimización táctil para dispositivos móviles

## 🧪 Pruebas Realizadas

### Validaciones Frontend
- ✅ Formularios con JavaScript puro
- ✅ Validación en tiempo real
- ✅ Mensajes de error contextuales
- ✅ Prevención de envíos inválidos

### Validaciones Backend
- ✅ Sanitización de datos de entrada
- ✅ Validación de tipos y rangos
- ✅ Manejo de errores de base de datos
- ✅ Logging de errores para debugging

### Casos de Prueba
- ✅ Login correcto/incorrecto
- ✅ Solicitud de tutoría exitosa
- ✅ Agendamiento sin choques de horario
- ✅ Visualización de historial completo
- ✅ Responsividad en múltiples dispositivos

## 📊 Comandos Útiles

### Docker
```bash
# Levantar servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down

# Reconstruir contenedores
docker-compose build --no-cache
```

### Base de Datos
```bash
# Acceder a MySQL
docker exec -it tutorias_mysql mysql -u root -p

# Respaldar base de datos
docker exec tutorias_mysql mysqldump -u root -proot tutorias_db > backup.sql
```

## 🔄 Próximas Iteraciones

### Funcionalidades Planificadas
- Notificaciones por email
- Panel de administración completo
- Reportes y estadísticas avanzadas
- API REST para integración móvil
- Sistema de calificaciones de tutorías

### Mejoras Técnicas
- Cache con Redis
- Tests automatizados (PHPUnit)
- CI/CD con GitHub Actions
- Monitoreo con logs estructurados

## 📝 Gestión de Cambios

Ver [CHANGELOG.md](CHANGELOG.md) para historial detallado de cambios y riesgos identificados.

## 👥 Contribución

Este proyecto sigue la metodología XP. Para contribuir:

1. Crear branch desde `main`
2. Implementar funcionalidad completa
3. Escribir pruebas correspondientes
4. Crear Pull Request con descripción detallada

## 📄 Licencia

Proyecto académico - Universidad. Todos los derechos reservados.

---

**Desarrollado con ❤️ para la educación universitaria**