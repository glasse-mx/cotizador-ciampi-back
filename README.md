
# Cotizador BackEnd -Laravel

Este proyecto contiene el codigo fuente para el sistema cotizador que se usa en las tres empresas, es importante revisar esta documentacion antes de hacer algun cambio, y recuerda que si has hecho alguna contrubucion, documentarla.

## Dependencias

Este proyecto esta escrito en laravel 10, y usa JWT para poder validar las peticiones via API REST.

## Estructura
Este sistema solo presta servicios via API rest a traves de sus controladores, revisar el archivo de routes para identificar las funciones y controladores involucrados.

## Capacidades

El sistema tiene las operaciones CRUD basicas para poder

<ul>
    <li>Crear, editar, ver y buscar Clientes</li>
    <li>Crear, editar, actualizar, y cancelar Ordenes (notas de venta o cotizaciones)</li>
</ul>

### Instalacion

<ul>
    <li>Antes de subir a un entorno de produccion, se recomienda probar extensamente, clonar este repositorio</li>
    <li>Usar el comando -composer install- para actualizar el arbol de dependencias</li>
    <li>Usar el comando -php artisan migrate- para crear y migrar la base de datos.</li>
    <li>Usar el comando -php artisan serve- para inicializar el servidor local</li>
    <li>Usar postman o un cliente de API's para testear las operaciones y rutas del archivo route.php</li>
</ul>







