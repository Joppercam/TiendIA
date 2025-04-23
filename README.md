# TiendIA - Plataforma E-commerce

Plataforma completa de comercio electrónico desarrollada con Laravel, que incluye gestión de productos, carrito de compras, procesamiento de pagos, panel de administración y muchas otras funcionalidades avanzadas.

## 📋 Descripción del Proyecto

TiendIA es una solución integral para comercio electrónico desarrollada con Laravel 10.x que implementa un completo sistema de tienda en línea con funcionalidades avanzadas como gestión de productos, catálogo dinámico, carrito de compras, procesamiento de pagos, panel de administración, gestión de usuarios y clientes, y mucho más.

## 🚀 Características Principales

- **Sistema completo de autenticación** con roles y permisos
- **Catálogo de productos** con categorías, marcas, atributos y variantes
- **Gestión de inventario** con control de stock y alertas
- **Carrito de compras** con persistencia y lista de deseos
- **Proceso de checkout** intuitivo y seguro
- **Múltiples opciones de pago** integradas
- **Gestión de pedidos** con seguimiento en tiempo real
- **Panel de administración** completo y personalizable
- **Sistema de valoraciones y reseñas** de productos
- **Marketing y promociones** con cupones y descuentos
- **API RESTful** para integraciones y aplicaciones móviles
- **Optimización SEO** integrada
- **Multilenguaje y multimoneda**
- **Notificaciones** por email, push y SMS

## 🔧 Requisitos Técnicos

- PHP 8.1 o superior
- Composer 2.0+
- Node.js 16+ y NPM
- MySQL 5.7 o superior (o MariaDB 10.2+)
- Servidor web (Apache/Nginx)
- Extensiones PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD

## 💻 Instalación

### Paso 1: Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/tiend-ia.git
cd tiend-ia
```

### Paso 2: Instalar dependencias PHP

```bash
composer install
```

### Paso 3: Instalar dependencias JavaScript

```bash
npm install
```

### Paso 4: Configurar entorno

```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

Editar el archivo `.env` con la configuración de tu base de datos y otras variables de entorno.

### Paso 5: Ejecutar migraciones y seeders

```bash
# Crear tablas en la base de datos
php artisan migrate

# Cargar datos iniciales (opcional)
php artisan db:seed
```

### Paso 6: Compilar assets

```bash
# Para desarrollo
npm run dev

# Para producción
npm run build
```

### Paso 7: Configurar permisos de almacenamiento

```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### Paso 8: Crear enlace simbólico para almacenamiento

```bash
php artisan storage:link
```

### Paso 9: Iniciar servidor de desarrollo

```bash
php artisan serve
```

El proyecto estará disponible en http://localhost:8000

## 🏗️ Estructura del Proyecto

### 1. Autenticación y Usuarios
- **Modelos:** User, Role, Permission, Profile
- **Controladores:** AuthController, UserController, ProfileController
- **Implementación:** Laravel Breeze/Jetstream
- **Funcionalidades:** Registro, login, gestión de perfiles, roles y permisos

### 2. Catálogo de Productos
- **Modelos:** Product, Category, Brand, Attribute, AttributeValue, ProductImage
- **Controladores:** ProductController, CategoryController, BrandController
- **Servicios:** ProductService, SearchService
- **Funcionalidades:** Listado de productos, detalles, búsqueda avanzada, filtros

### 3. Inventario
- **Modelos:** Inventory, InventoryMovement, Supplier
- **Controladores:** InventoryController, SupplierController
- **Servicios:** InventoryService
- **Funcionalidades:** Control de stock, alertas, gestión de proveedores

### 4. Carrito de Compras
- **Modelos:** Cart, CartItem, WishList
- **Controladores:** CartController, WishListController
- **Middleware:** CartMiddleware
- **Servicios:** CartService
- **Funcionalidades:** Gestión de carrito, lista de deseos, persistencia

### 5. Proceso de Checkout
- **Modelos:** Order, OrderItem, Address, PaymentMethod
- **Controladores:** CheckoutController, AddressController
- **Middleware:** CheckoutMiddleware
- **Servicios:** CheckoutService, PaymentService
- **Funcionalidades:** Selección de dirección, métodos de pago, confirmación

### 6. Gestión de Pedidos
- **Modelos:** Order, OrderStatus, Shipment, DeliveryMethod
- **Controladores:** OrderController, ShipmentController
- **Eventos:** OrderPlaced, OrderStatusChanged, OrderCancelled
- **Notifications:** OrderConfirmation, ShipmentUpdate, DeliveryConfirmation
- **Funcionalidades:** Seguimiento de pedidos, gestión de estados, logística

### 7. Valoraciones y Reseñas
- **Modelos:** Review, Rating, Question, Answer
- **Controladores:** ReviewController, QuestionController
- **Políticas:** ReviewPolicy
- **Funcionalidades:** Comentarios, puntuaciones, preguntas y respuestas

### 8. Panel de Administración
- **Controladores:** AdminController, DashboardController, ReportController
- **Middleware:** AdminMiddleware
- **Servicios:** ReportService, StatisticsService
- **Funcionalidades:** Dashboard con métricas, gestión de catálogo, usuarios, informes

### 9. Marketing y Promociones
- **Modelos:** Coupon, Discount, Campaign, Promotion
- **Controladores:** CouponController, DiscountController
- **Servicios:** PromotionService, MarketingService
- **Funcionalidades:** Cupones, descuentos, promociones, productos destacados

### 10. API y Integraciones
- **Controladores:** API/ProductController, API/OrderController, API/UserController
- **Recursos:** ProductResource, UserResource, OrderResource
- **Middleware:** API auth, rate limiting
- **Funcionalidades:** Endpoints REST, webhooks, documentación API

### 11. Integración de Pagos
- **Modelos:** Payment, PaymentGateway, PaymentTransaction, Invoice
- **Servicios:** PaymentGatewayService, PaymentProcessorService
- **Implementaciones:** Pasarelas múltiples (transferencia, PayPal, Stripe, etc.)
- **Funcionalidades:** Procesamiento de pagos, reembolsos, facturas

### 12. SEO y Optimización
- **Modelos:** SeoMetadata, Sitemap, Redirect
- **Middleware:** SeoMiddleware
- **Servicios:** SeoService, PerformanceService
- **Funcionalidades:** Meta tags, URLs amigables, sitemaps, redirecciones

## 🛠️ Tecnologías Implementadas

- **Backend**: Laravel 10.x
- **Frontend**: Blade, TailwindCSS, Alpine.js
- **Base de Datos**: MySQL
- **Autenticación**: Laravel Breeze
- **Gestión de Roles**: Spatie Permission
- **Herramientas Adicionales**: Laravel Debugbar, Intervention Image

## 🔄 Flujo de Trabajo Git

Este proyecto sigue la metodología Gitflow:

- **main**: Código de producción estable
- **develop**: Rama principal de desarrollo
- **feature/x**: Funcionalidades nuevas
- **hotfix/x**: Correcciones urgentes
- **release/x**: Preparación de versiones

## 📝 Comandos Útiles

```bash
# Generar nuevos componentes
php artisan make:model Product -mcrf

# Ejecutar pruebas
php artisan test

# Limpiar caché
php artisan optimize:clear

# Regenerar autoloader
composer dump-autoload

# Actualizar dependencias
composer update && npm update
```

## 🧪 Testing

El proyecto incluye pruebas unitarias y de integración:

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar pruebas específicas
php artisan test --filter=ProductTest
```

## 📚 Documentación

La documentación completa del proyecto está disponible en la carpeta `docs/` y cubre:

- Guía de instalación detallada
- Manual de usuario
- Documentación técnica
- API Reference

## 🤝 Contribución

Consulta [CONTRIBUTING.md](CONTRIBUTING.md) para conocer el proceso de contribución al proyecto.

## 📄 Licencia

Este proyecto está licenciado bajo [MIT License](LICENSE).

## 👥 Autores

- **Tu Nombre** - *Desarrollo inicial* - [tu-usuario](https://github.com/tu-usuario)

## 🙏 Agradecimientos

- Laravel Team por el increíble framework
- Spatie por sus excelentes paquetes
- Todos los contribuidores que han participado en este proyecto