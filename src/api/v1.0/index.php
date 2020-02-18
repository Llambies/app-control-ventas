<?php
require_once '../includes/conexion.php';
if (!$conexion) {
    die("Error: " . mysqli_connect_error());
}

define('API_VERSION', 'v1.0');
/***************************
1.‐ Parsear la URL...
 ***************************/
// 1.1.‐ Obtenemos la parte del path que va después de la versión de la API
$uri = explode(API_VERSION.'/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))[1];
// 1.2.‐ Lo covertimos en un array ...
$uri_array = explode('/',$uri);
/***************************
2.‐ Obtener el recurso solicitado...
 ***************************/
$recurso = array_shift($uri_array);
/***************************
3.‐ Obtener el tipo de operación solicitada...
 ***************************/
$operacion = strtolower($_SERVER['REQUEST_METHOD']);
/***************************
4.- Preparar la salida
 ***************************/
$salida = array();
$vista = 'json';
$http_code = 404;
// modelo
include "modelos/$operacion-$recurso.php";
// vista
include "vistas/$vista.php";


$sql = 'SELECT vendedores.nombre as nombreVendedor, vendedores.apellidos as
apellidosVendedor,clientes.nombre as nombreCliente, ventas.* FROM `ventas`,
vendedores, clientes WHERE ventas.vendedor = vendedores.id AND
ventas.cliente = clientes.id';
$res = mysqli_query($conexion, $sql);
$resultado = array();
while($fila = mysqli_fetch_assoc($res)){
$vendedor = array("id" => $fila["vendedor"], "nombre" =>
$fila["nombreVendedor"], "apellidos" => $fila["apellidosVendedor"]);
$cliente = array("id" => $fila["cliente"], "nombre" =>
$fila["nombreCliente"]);
$fila["vendedor"] = $vendedor;
$fila["cliente"] = $cliente;
unset($fila["nombreVendedor"]);
unset($fila["apellidosVendedor"]);
unset($fila["nombreCliente"]);
array_push($resultado, $fila);
}
//header('Access‐Control‐Allow‐Origin: *');
//header('Access‐Control‐Allow‐Methods: PUT, GET, POST, DELETE');
//header('Content‐Type: application/json; charset=utf‐8');
echo json_encode($resultado);
?>
