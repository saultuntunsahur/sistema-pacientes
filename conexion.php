<?php
$servidor   = "192.168.56.102"; // IP de tu máquina virtual Rocky Linux (o "localhost" si usas XAMPP)
$usuario    = "saul";     // Cambia por tu usuario de MySQL
$contrasena = "zeus2026"; // Cambia por tu contraseña de MySQL
$base_datos = "mi_base_datos";// Cambia por el nombre de tu base de datos

$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8");
?>