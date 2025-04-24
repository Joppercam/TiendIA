<?php

namespace App\Documentation;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="TiendIA API Documentation",
 *     description="Documentación de la API para la plataforma de e-commerce TiendIA",
 *     @OA\Contact(
 *         email="info@tiendia.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="TiendIA API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi
{
    // Esta clase solo contiene anotaciones de OpenAPI
}