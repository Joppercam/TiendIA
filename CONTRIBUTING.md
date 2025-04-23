# Guía de Contribución - Proyecto TiendIA

¡Gracias por tu interés en contribuir al proyecto TiendIA! Esta guía te ayudará a entender el proceso para contribuir de manera efectiva al desarrollo de nuestra plataforma de e-commerce.

## Código de Conducta

Al participar en este proyecto, aceptas respetar nuestro Código de Conducta. Esperamos que todos los contribuidores mantengan un ambiente amigable, inclusivo y respetuoso.

## Cómo Contribuir

Hay muchas formas de contribuir al proyecto TiendIA:

- Reportar bugs
- Sugerir nuevas funcionalidades
- Mejorar la documentación
- Corregir errores de código
- Implementar nuevas características

## Proceso de Desarrollo

### 1. Configuración del Entorno de Desarrollo

1. **Fork del repositorio**
   
   Haz un fork del repositorio a tu cuenta personal de GitHub.

2. **Clona tu fork localmente**
   
   ```bash
   git clone https://github.com/TU-USUARIO/tiend-ia.git
   cd tiend-ia
   ```

3. **Configura el repositorio upstream**
   
   ```bash
   git remote add upstream https://github.com/USUARIO-ORIGINAL/tiend-ia.git
   ```

4. **Instala las dependencias**
   
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configura la base de datos**
   
   Actualiza el archivo `.env` con tus credenciales de base de datos.

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

### 2. Flujo de Trabajo con Git

Utilizamos una variación de GitFlow para gestionar el desarrollo del proyecto. Nuestras ramas principales son:

- **main**: Código de producción estable
- **develop**: Rama principal de desarrollo
- **feature/x**: Nuevas funcionalidades
- **bugfix/x**: Correcciones de errores
- **hotfix/x**: Correcciones urgentes para producción
- **release/x**: Preparación de versiones

#### Proceso para desarrollar una nueva funcionalidad

1. **Asegúrate de tener la última versión de develop**
   
   ```bash
   git checkout develop
   git pull upstream develop
   ```

2. **Crea una rama para tu funcionalidad**
   
   ```bash
   git checkout -b feature/nombre-descriptivo
   ```

   Utiliza un nombre descriptivo que refleje la funcionalidad que estás implementando, por ejemplo:
   - `feature/product-search`
   - `feature/payment-gateway-integration`
   - `feature/admin-dashboard`

3. **Desarrolla tu código**
   
   Implementa la funcionalidad o corrección, asegurándote de seguir las convenciones de código.

4. **Realiza commits frecuentes**
   
   ```bash
   git add .
   git commit -m "Mensaje descriptivo del cambio"
   ```

5. **Actualiza tu rama con los últimos cambios de develop**
   
   ```bash
   git pull upstream develop
   ```

   Resuelve cualquier conflicto que pueda surgir.

6. **Envía tu rama al repositorio**
   
   ```bash
   git push origin feature/nombre-descriptivo
   ```

7. **Crea un Pull Request**
   
   Ve a GitHub y crea un nuevo Pull Request desde tu rama hacia la rama `develop` del repositorio original.

### 3. Convenciones de Código

#### Estilo de Código PHP

Seguimos las convenciones de estilo de código PSR-12. Puedes verificar tu código con:

```bash
composer check-style
```

Y corregir automáticamente algunos problemas con:

```bash
composer fix-style
```

#### Convenciones de Nomenclatura

- **Clases**: PascalCase (ej. `ProductController`)
- **Métodos y funciones**: camelCase (ej. `getProductById()`)
- **Variables**: camelCase (ej. `$totalPrice`)
- **Constantes**: UPPER_CASE con guiones bajos (ej. `APP_VERSION`)
- **Archivos de vista**: kebab-case (ej. `product-detail.blade.php`)
- **Tablas de base de datos**: snake_case, plural (ej. `product_categories`)
- **Modelos**: PascalCase, singular (ej. `Product`)

#### Documentación del Código

Todos los métodos y clases deben estar documentados siguiendo el estándar PHPDoc:

```php
/**
 * Obtiene un producto por su ID
 *
 * @param int $id ID del producto
 * @return Product|null Retorna el modelo Product o null si no existe
 * @throws ProductNotFoundException Si el producto no puede ser encontrado
 */
public function getProductById(int $id): ?Product
{
    // Implementación
}
```

### 4. Pruebas

Cada nueva funcionalidad o corrección debe incluir pruebas. Utilizamos PHPUnit para pruebas unitarias y de integración.

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar pruebas específicas
php artisan test --filter=ProductTest
```

#### Tipos de Pruebas

1. **Pruebas Unitarias**: Para probar componentes individuales
2. **Pruebas de Integración**: Para probar cómo interactúan varios componentes
3. **Pruebas de Características**: Para probar funcionalidades completas
4. **Pruebas de Navegador**: Utilizamos Laravel Dusk para pruebas de interfaz de usuario

### 5. Proceso de Pull Request

#### Creación del PR

1. Asegúrate de que tu código pasa todas las pruebas
2. Actualiza la documentación si es necesario
3. Crea un Pull Request desde tu rama hacia `develop` del repositorio original
4. Completa la plantilla de PR con:
   - Descripción de los cambios
   - Problema que resuelve
   - Cómo probar los cambios
   - Screenshots (si aplica)

#### Revisión de Código

Todos los PR serán revisados por al menos un miembro del equipo principal. Durante la revisión:

1. Se verificará que el código siga las convenciones establecidas
2. Se ejecutarán las pruebas automáticas
3. Se revisará la funcionalidad implementada
4. Se discutirán posibles mejoras o alternativas

#### Fusión del PR

Una vez aprobado, un mantenedor del proyecto fusionará tu PR. Por lo general seguimos esta estrategia:

- Squash and merge para PR pequeños (1-3 commits)
- Rebase and merge para PR medianos que mantienen la historia limpia
- Create a merge commit para PR grandes o complejos

### 6. Versionado

Seguimos el esquema de versionado semántico (SemVer):

- **MAJOR**: Cambios incompatibles con versiones anteriores
- **MINOR**: Nuevas funcionalidades compatibles con versiones anteriores
- **PATCH**: Correcciones de errores compatibles con versiones anteriores

Las versiones se etiquetan como: vX.Y.Z (ej. v1.2.3)

## Reportar Bugs

Si encuentras un bug, por favor crea un issue en GitHub siguiendo estos pasos:

1. Verifica que el bug no haya sido reportado anteriormente
2. Utiliza la plantilla de bug report
3. Incluye pasos detallados para reproducir el problema
4. Adjunta capturas de pantalla o vídeos si es posible
5. Menciona tu entorno (sistema operativo, navegador, versión de PHP, etc.)

## Solicitar Nuevas Funcionalidades

Para solicitar nuevas funcionalidades:

1. Verifica que la funcionalidad no haya sido solicitada anteriormente
2. Utiliza la plantilla de feature request
3. Describe claramente la funcionalidad y su propósito
4. Explica cómo beneficiaría al proyecto
5. Proporciona ejemplos de casos de uso

## Proceso de Lanzamiento

1. Se crea una rama `release/vX.Y.Z` desde `develop`
2. Se realizan ajustes finales, correcciones y actualización de documentación
3. Se fusiona en `main` y se etiqueta con la versión
4. Se fusiona de vuelta en `develop`

## Recursos Adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [Convenciones PSR-12](https://www.php-fig.org/psr/psr-12/)
- [Git Flow Cheatsheet](https://danielkummer.github.io/git-flow-cheatsheet/)

## Agradecimientos

¡Gracias por dedicar tu tiempo a contribuir al proyecto TiendIA! Tu ayuda es muy valiosa para mejorar esta plataforma de e-commerce.

---

*Última actualización: Abril 2025*