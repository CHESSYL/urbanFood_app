<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['id_usuario'])){
    header("Location: Login.php");
    exit();
}

if($_SESSION['rol'] != 'repartidor'){
    echo "Acceso denegado. Solo los repartidores pueden acceder a esta página.";
    header("Location: login.php");
    exit();
}
require_once("claseUrbanFood.php");



$obj = new claseConexionFood();
$id_repartidor = $_SESSION['id_repartidor'];



if(isset($_POST['cambiar_estado_repartidor'])){
    $estado = $_POST['estado_repartidor'];

  $obj->modificar(
    "repartidores",
    ["estado" => $estado],
    "id_repartidor",
    $id_repartidor
);
}



if(isset($_POST['cambiar_estado_pedido'])){
    $estado = $_POST['estado_pedido'];
    $id_pedido = $_POST['id_pedido'];

    $obj->modificar(
        "pedidos",
        ["estado" => $estado],
        "id_pedido",
        $id_pedido
    );

  if($estado == 'entregado'){
    
    $obj->modificar(
        "repartidores",
        ["estado" => "Disponible"],
        "id_repartidor",
        $id_repartidor
    );

   
    $obj->modificar(
        "pedidos",
        ["id_repartidor" => null],
        "id_pedido",
        $id_pedido
    );
}
        
    }

$pedido = $obj->consulta("
    SELECT p.*, c.nombre, c.apellido, c.telefono
    FROM pedidos p
    JOIN clientes c ON p.id_cliente = c.id_cliente
    WHERE p.id_repartidor = $id_repartidor
    AND p.estado NOT IN ('entregado', 'cancelado')
    LIMIT 1
")->fetch_assoc();

$detalles = null;

if($pedido){
    $id_pedido = $pedido['id_pedido'];

    $detalles = $obj->consulta("
        SELECT d.*, m.nombre, m.imagen, r.nombre as restaurante, r.direccion as direccion_restaurante
        FROM detalle_pedido d
        JOIN menus m ON d.id_menu = m.id_menu
        JOIN restaurantes r ON m.id_restaurante = r.id_restaurante
        WHERE d.id_pedido = $id_pedido
    ");
}

$repartidor = $obj->consulta("
    SELECT * FROM repartidores 
    WHERE id_repartidor = $id_repartidor
")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Panel Repartidor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <span class="navbar-brand">🚚 Panel del Repartidor</span>
    </div>
    <a href="logout.php" class="btn btn-outline-secondary">
    Cerrar sesión
    </a> 
</nav>
 
<div class="card p-3 mb-4 shadow-sm">
    <h5>Mi estado</h5>

    <form method="POST">
        <select name="estado_repartidor" class="form-select mb-2">
            <option value="Disponible" <?= $repartidor['estado']=='Disponible'?'selected':'' ?>>Disponible</option>
            <option value="Ocupado" <?= $repartidor['estado']=='Ocupado'?'selected':'' ?>>Ocupado</option>
        </select>

        <button name="cambiar_estado_repartidor" class="btn btn-dark w-100">
            Actualizar estado
        </button>
    </form>
</div>


<?php if($pedido){ ?>


<div class="card p-3 shadow-lg mb-4">

    <h4>📦 Pedido #<?= $pedido['id_pedido'] ?></h4>

    <p><strong>👤 Cliente:</strong> <?= $pedido['nombre']." ".$pedido['apellido'] ?></p>
    <p><strong>📞 Teléfono:</strong> <?= $pedido['telefono'] ?></p>
    <p><strong>📍 Dirección:</strong> <?= $pedido['direccion_entrega'] ?></p>
    <p><strong>💳 Pago:</strong> <?= $pedido['metodo_pago'] ?></p>

    <p>
        <strong>📌 Estado:</strong> 
        <span class="badge bg-primary"><?= $pedido['estado'] ?></span>
    </p>

    <h5 class="text-success">💲<?= $pedido['total'] ?></h5>

    
    <form method="POST">
        <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">

<select name="estado_pedido" class="form-select mb-2">
    <option value="pendiente" <?= $pedido['estado']=='pendiente'?'selected':'' ?>>Pendiente</option>
    <option value="en camino" <?= $pedido['estado']=='en camino'?'selected':'' ?>>En camino</option>
    <option value="entregado">Entregado</option>
</select>

        <button name="cambiar_estado_pedido" class="btn btn-success w-100">
            Actualizar pedido
        </button>
    </form>

</div>

    <h5>🍔 Productos</h5>

    <div class="row">
    <?php while($d = $detalles->fetch_assoc()){ ?>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <img src="<?= $d['imagen'] ? $d['imagen'] : 'img/default.jpg' ?>" class="card-img-top">

                <div class="card-body">
                    <h5>Restaurante: <?= $d['restaurante'] ?></h5>
                    <h6><?= $d['nombre'] ?></h6>
                    <p>Dirección: <?= $d['direccion_restaurante'] ?></p>
                    <p>Cant: <?= $d['cantidad'] ?></p>
                    <p>$<?= $d['subtotal'] ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>

<?php } else { ?>

    <div class="alert alert-info">
        No tienes pedidos asignados 🚫
    </div>

<?php } ?>

</div>

</body>
</html>

<style>
body {
    background:#f5f5f5;
}

.card {
    border-radius:15px;
}
</style>