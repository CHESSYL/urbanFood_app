<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['id_usuario'])){
    header("Location: Login.php");
    exit();
}

if($_SESSION['rol'] != 'admin'){
    echo "Acceso denegado. Solo los administradores pueden acceder a esta página.";
    header("Location: login.php");
    exit();
}

require_once("claseUrbanFood.php");


$tabla = $_POST['tabla'] ?? $_GET['tabla'] ?? '';
$vista = $_GET['vista'] ?? 'form';
$objetoConexion = new claseConexionFood();

$tablas = [
"usuarios" => [
    "tabla" => "usuarios",
    "id" => "id_usuario",
    "campos" => [
        "Correo",
        "rol",
        "id_cliente",
        "id_repartidor",
    ]
],

"clientes" => [
    "tabla" => "clientes",
    "id" => "id_cliente",
    "campos" => [
        "nombre",
        "apellido",
        "telefono"
    ]
],

"restaurantes" => [
    "tabla" => "restaurantes",
    "id" => "id_restaurante",
    "campos" => [
        "imagen",
        "nombre",
        "direccion",
        "telefono",
        "correo"
    ]
],

"repartidores" => [
    "tabla" => "repartidores",
    "id" => "id_repartidor",
    "campos" => [
        "nombre",
        "telefono",
        "vehiculo"
    ]
],

"menus" => [
    "tabla" => "menus",
    "id" => "id_menu",
    "campos" => [
        "imagen",
        "nombre",
        "descripcion",
        "precio",
        "categoria",
        "id_restaurante"
    ]
    ],
    "pedidos" => [
        "tabla" => "pedidos",
        "id" => "id_pedido",
        "campos" => [
            "id_cliente",
            "fecha",
            "total",
            "estado",
            "id_repartidor",
        ]
    ],

    "detalle_pedido" => [
        "tabla" => "detalle_pedido",
        "id" => "id_detalle",
        "campos" => [
            "id_pedido",
            "id_menu",
            "cantidad",
            "precio_unitario",
            "subtotal"
        ]
    ]
];

if(!isset($tablas[$tabla])){
    $tabla = '';
}

if(isset($_POST["guardar"])){

    $tablaActual = $tablas[$tabla];
    $datos = [];

    foreach($tablaActual["campos"] as $campo){
        $valor = $_POST[$campo] ?? null;

        $datos[$campo] = $valor;
    }

    if($tabla == "repartidores"){

    
    $id_repartidor = $objetoConexion->insertar("repartidores", $datos);

  
 $correo = $_POST['correo'] ?? null;
$contrasena = $_POST['contrasena'] ?? null;

$contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

$datosUsuario = [
    "Correo" => $correo,
    "rol" => "repartidor",
    "id_cliente" => null,
    "id_repartidor" => $id_repartidor,
    "contrasena" => $contrasenaHash
];

$objetoConexion->insertar("usuarios", $datosUsuario);

}elseif($tabla == "menus"){

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

        $carpeta = "img/menus/";

        if(!is_dir($carpeta)){
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = time() . "_" . $_FILES['imagen']['name'];
        $ruta = $carpeta . $nombreArchivo;

        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

        $datos["imagen"] = $ruta;
    } else {
       
        unset($datos["imagen"]);
    }
    $objetoConexion->insertar("menus", $datos);
}

elseif($tabla == "restaurantes"){

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

        $carpeta = "img/restaurantes/";

        if(!is_dir($carpeta)){
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = time() . "_" . $_FILES['imagen']['name'];
        $ruta = $carpeta . $nombreArchivo;

        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

        $datos["imagen"] = $ruta;

    } else {
        unset($datos["imagen"]);
    }

   
    $objetoConexion->insertar("restaurantes", $datos);
}

else{
    $objetoConexion->insertar($tablaActual["tabla"], $datos);
}

header("Location: ".$_SERVER["PHP_SELF"]."?tabla=$tabla&vista=tabla");
exit();
}


if(isset($_POST["modificar"])){

$tablaActual = $tablas[$tabla];
$datos = [];

foreach($tablaActual["campos"] as $campo){

    $valor = $_POST[$campo] ?? null;
    $datos[$campo] = $valor;
}

if($tabla == "menus"){

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

        $carpeta = "img/menus/";

        if(!is_dir($carpeta)){
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = time() . "_" . $_FILES['imagen']['name'];
        $ruta = $carpeta . $nombreArchivo;

        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

        $datos["imagen"] = $ruta;

    } else {
        unset($datos["imagen"]); 
    }
}

if($tabla == "restaurantes"){

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

        $carpeta = "img/restaurantes/";

        if(!is_dir($carpeta)){
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = time() . "_" . $_FILES['imagen']['name'];
        $ruta = $carpeta . $nombreArchivo;

        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

        $datos["imagen"] = $ruta;

    } else {
        unset($datos["imagen"]); 
    }
}

$objetoConexion->modificar(
    $tablaActual["tabla"],
    $datos,
    $tablaActual["id"],
    $_POST["id"]
);

header("Location: ".$_SERVER["PHP_SELF"]."?tabla=$tabla");
exit();
}
if(isset($_GET["eliminar"])){

$tablaActual = $tablas[$tabla];

$objetoConexion->eliminar(
    $tablaActual["tabla"],
    $tablaActual["id"],
    $_GET["eliminar"]
);

}

$registroEditar = null;

if(isset($_GET["editar"])){

$tablaActual = $tablas[$tabla];

$resultado = $objetoConexion->seleccionarUno( $tablaActual["tabla"], $tablaActual["id"], $_GET["editar"] );

$registroEditar = $resultado->fetch_assoc();

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Restaurantes</title>
</head>
<body>
    <div class="container mt-4">
    <section>  
    <h2 class="text-center mb-4">Sistema UrbanFood</h2>

    <article>
        <form id="menu-sidebar" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            
            <label class="fs-4 fw-bold">Consultar Tablas:</label>
            
<div class="btn-group mb-4">
    <a href="?tabla=usuarios" class="btn btn-primary">Usuarios</a>
    <a href="?tabla=clientes" class="btn btn-success">Clientes</a>
    <a href="?tabla=repartidores" class="btn btn-warning">Repartidores</a>
    <a href="?tabla=restaurantes" class="btn btn-success">Restaurantes</a>
    <a href="?tabla=menus" class="btn btn-info">Menús</a>
    <a href="?tabla=pedidos" class="btn btn-dark">Pedidos</a>
    <a href="backup.php" class="btn btn-danger">Realizar respaldo de la BD</a>
    <a href="logout.php" class="btn btn-warning">Cerrar Sesion</a>
    <a href="Manual.php?rol=admin" class="btn btn-success">
        ❓ Ayuda Técnico
    </a>
</li>
</div>

        </form>
    </article>
</section>


<main>


    <?php 
  

    
    if ($tabla != '') {

        switch ($tabla) {

case 'usuarios':

    echo '<h3>Lista de usuarios</h3>';

    $recibeResultados = $objetoConexion->obtenerConJoin("usuarios");
    include("UrbanFoodUsuarios.php");

break;

case 'clientes':

    echo '<h3>Lista de clientes</h3>';

    $recibeResultados = $objetoConexion->obtenerConJoin("clientes");
    include("UrbanFoodClientes.php");

break;


case 'restaurantes':

if($vista == 'tabla'){

    echo '<h3>Lista de restaurantes</h3>';
    echo '<a class="btn btn-primary mb-3" href="?tabla=restaurantes&vista=form">Volver al formulario</a>';

    $recibeResultados = $objetoConexion->obtenerConJoin("restaurantes");
    include("UrbanFoodRestaurantes.php");


} else {
?>

<form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
<input type="hidden" name="tabla" value="restaurantes">
<input type="hidden" name="id" value="<?= $registroEditar['id_restaurante'] ?? '' ?>">

<label>Nombre del restaurante</label>
<input class="form-control mb-3" type="text" name="nombre" required
value="<?= $registroEditar['nombre'] ?? '' ?>">

<label>Dirección</label>
<input class="form-control mb-3" type="text" name="direccion" required
value="<?= $registroEditar['direccion'] ?? '' ?>">

<label>Número telefónico</label>
<input class="form-control mb-3" type="tel" name="telefono" required
value="<?= $registroEditar['telefono'] ?? '' ?>">

<label>Correo electrónico</label>
<input class="form-control mb-3" type="email" name="correo" required
value="<?= $registroEditar['correo'] ?? '' ?>">

<label>Imagen del restaurante</label>
<input class="form-control mb-3" type="file" name="imagen" accept="image/*">

<?php if(!empty($registroEditar['imagen'])){ ?>
    <img src="<?= $registroEditar['imagen'] ?>" width="100" class="mb-2">
<?php } ?>


<?php if ($registroEditar) { ?>
    <button class="btn btn-success" type="submit" name="modificar">Modificar</button>
<?php } else { ?>
    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
<?php } ?>

<?php if ($registroEditar) { ?>
    <a class="btn btn-secondary" href="?tabla=restaurantes&vista=tabla">Cancelar</a>
<?php } else { ?>
    <a class="btn btn-secondary" href="?tabla=restaurantes&vista=tabla">Ver restaurantes</a>
<?php } ?>


</form>

<?php
}

break;

case 'repartidores':

if($vista == 'tabla'){

    echo '<h3>Lista de repartidores</h3>';
    echo '<a class="btn btn-primary mb-3" href="?tabla=repartidores&vista=form">Volver al formulario</a>';

    $recibeResultados = $objetoConexion->obtenerConJoin("repartidores");
    include("UrbanFoodRepartidores.php");

} else {
?>

<form method="POST"  class="card p-4 shadow-sm">
<input type="hidden" name="tabla" value="repartidores">
<input type="hidden" name="id" value="<?= $registroEditar['id_repartidor'] ?? '' ?>">

<label>Nombre del repartidor</label>
<input class="form-control mb-3" type="text" name="nombre" required
value="<?= $registroEditar['nombre'] ?? '' ?>">

<label>Número telefónico</label>
<input class="form-control mb-3" type="tel" name="telefono" required
value="<?= $registroEditar['telefono'] ?? '' ?>">

<label>Vehículo</label>
<input class="form-control mb-3" type="text" name="vehiculo" required
value="<?= $registroEditar['vehiculo'] ?? '' ?>">

<?php if (!$registroEditar) { ?>

<label>Correo electrónico</label>
<input class="form-control mb-3" type="email" name="correo" required>

<label>Contraseña</label>
<input class="form-control mb-3" type="password" name="contrasena" required>

<?php } ?>

<?php if ($registroEditar) { ?>
    <button class="btn btn-success" type="submit" name="modificar">Modificar</button>
<?php } else { ?>
    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
<?php } ?>

<?php if ($registroEditar) { ?>
    <a class="btn btn-secondary" href="?tabla=repartidores&vista=tabla">Cancelar</a>
<?php } else { ?>
    <a class="btn btn-secondary" href="?tabla=repartidores&vista=tabla">Ver repartidores</a>
<?php } ?>


</form>

<?php
}

break;

case 'menus':

    if($vista == 'tabla'){
        echo '<h3>Lista de menús</h3>';
        echo '<a class="btn btn-primary mb-3" href="?tabla=menus&vista=form">Volver al formulario</a>';

        $recibeResultadosMenus = $objetoConexion->obtenerConJoin(
            "menus",
            [    
                [
                    "tabla" => "restaurantes",
                    "condicion" => "menus.id_restaurante = restaurantes.id_restaurante",
                ],
            ],
            "menus.*,
            restaurantes.nombre AS restaurante"

            );


        include("UrbanFoodMenus.php");
    } else {    
    ?>
<form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
<input type="hidden" name="tabla" value="menus">
<input type="hidden" name="id" value="<?= $registroEditar['id_menu'] ?? '' ?>">

<label>Nombre del menú</label>
<input class="form-control mb-3" type="text" name="nombre" required
value="<?= $registroEditar['nombre'] ?? '' ?>">

<label>Descripción</label>
<input class="form-control mb-3" type="text" name="descripcion" required
value="<?= $registroEditar['descripcion'] ?? '' ?>">

<label>Imagen del menú</label>
<input class="form-control mb-3" type="file" name="imagen" accept="image/*">

<?php if(!empty($registroEditar['imagen'])){ ?>
    <img src="<?= $registroEditar['imagen'] ?>" width="100" class="mb-2">
<?php } ?>

<label>Precio</label>
<input class="form-control mb-3" type="number" step="0.01" name="precio" required
value="<?= $registroEditar['precio'] ?? '' ?>">

<label>Categoría</label>
<input class="form-control mb-3" type="text" name="categoria" required
value="<?= $registroEditar['categoria'] ?? '' ?>">

<label>ID del restaurante</label>
<input class="form-control mb-3" type="number" name="id_restaurante" required
value="<?= $registroEditar['id_restaurante'] ?? '' ?>">

<?php if ($registroEditar) { ?>
    <button class="btn btn-success" type="submit" name="modificar">Modificar</button>
<?php } else { ?>
    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
<?php } ?>

<?php if ($registroEditar) { ?>
    <a class="btn btn-secondary" href="?tabla=menus&vista=tabla">Cancelar</a>
<?php } else { ?>
    <a class="btn btn-secondary" href="?tabla=menus&vista=tabla">Ver menús</a>
<?php } ?>
</form>
<?php
}

break;

case 'pedidos':

    echo '<h3>Lista de pedidos</h3>';

    $recibeResultadosPedidos = $objetoConexion->obtenerConJoin(
        "pedidos",
        [
            [
                "tabla" => "repartidores",
                "condicion" => "pedidos.id_repartidor = repartidores.id_repartidor"
            ],
            [
                "tabla" => "clientes",
                "condicion" => "pedidos.id_cliente = clientes.id_cliente"
            ]
        ],
        "pedidos.*,
        clientes.nombre AS cliente,
        repartidores.nombre AS repartidor"
    );

    include("UrbanFoodPedidos.php");

break;

    }
}
?>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>