<?php
/**
 * @OA\Info(
 *     title="TiendIA API",
 *     version="1.0.0",
 *     description="API para la plataforma de e-commerce TiendIA",
 *     @OA\Contact(
 *         email="soporte@tiendia.com",
 *         name="Soporte TiendIA"
 *     )
 * )
 *
 * @OA\Server(
 *     description="Servidor local de desarrollo",
 *     url="http://localhost:8000/api/v1"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     title="Producto",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Smartphone XYZ"),
 *     @OA\Property(property="slug", type="string", example="smartphone-xyz"),
 *     @OA\Property(property="description", type="string", example="Descripción detallada del producto"),
 *     @OA\Property(property="price", type="number", format="float", example=599.99),
 *     @OA\Property(property="sku", type="string", example="PROD001"),
 *     @OA\Property(property="quantity", type="integer", example=50),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */