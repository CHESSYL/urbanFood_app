<?php
session_start();
require_once("claseUrbanFood.php");
$obj = new claseConexionFood();

$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['mensaje']);

if(isset($_POST['agregar'])){
    $id = $_POST['id_menu'];

    if(isset($_SESSION['carrito'][$id])){
        $_SESSION['carrito'][$id]['cantidad'] += $_POST['cantidad'];
    } else {
        $_SESSION['carrito'][$id] = [
            "nombre" => $_POST['nombre'],
            "precio" => $_POST['precio'],
            "cantidad" => $_POST['cantidad']
        ];
    }

    $_SESSION['mensaje'] = "Producto agregado al carrito";

    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}

if(!isset($_SESSION['carrito'])){
    $_SESSION['carrito'] = [];
}


if(isset($_GET['eliminar'])){
    unset($_SESSION['carrito'][$_GET['eliminar']]);
}


if(isset($_GET['vaciar'])){
    $_SESSION['carrito'] = [];
}



$id = $_GET['id'];

$menus = $obj->obtenerConJoin(
    "menus",
    [],
    "*"
);

$categoria = $_GET['categoria'] ?? '';

$id = intval($_GET['id']);

if($categoria != ''){
    $sql = "SELECT * FROM menus 
            WHERE id_restaurante = $id 
            AND categoria = '$categoria'";
} else {
    $sql = "SELECT * FROM menus 
            WHERE id_restaurante = $id";
}

$menus = $obj->consulta($sql);


if(isset($_POST['finalizar'])){

    $id_cliente = $_SESSION['id_cliente'];
    $total = 0;

    foreach($_SESSION['carrito'] as $item){
        $total += $item['precio'] * $item['cantidad'];
    }

    $repartidor = $obj->consulta("
        SELECT id_repartidor 
        FROM repartidores 
        WHERE estado = 'Disponible' LIMIT 1
    ")->fetch_assoc();

    if(!$repartidor){
        $_SESSION['mensaje'] = "No hay repartidores disponibles";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $datosPedido = [
        "id_cliente" => $id_cliente,
        "total" => $total,
        "estado" => "pendiente",
        "metodo_pago" => $_POST['metodo_pago'],
        "direccion_entrega" => $_POST['direccion_entrega'],
        "id_repartidor" => $repartidor['id_repartidor']
    ];

    $obj->consulta("
        UPDATE repartidores 
        SET estado = 'Ocupado' 
        WHERE id_repartidor = ".$repartidor['id_repartidor']
    );

    $obj->insertar("pedidos", $datosPedido);

    $id_pedido = $obj->consulta("SELECT LAST_INSERT_ID() as id")
                     ->fetch_assoc()['id'];

    foreach($_SESSION['carrito'] as $id_menu => $item){

        $datosDetalle = [
            "id_pedido" => $id_pedido,
            "id_menu" => $id_menu,
            "cantidad" => $item['cantidad'],
            "precio_unitario" => $item['precio'],
            "subtotal" => $item['precio'] * $item['cantidad']
        ];

        $obj->insertar("detalle_pedido", $datosDetalle);
    }

    $_SESSION['carrito'] = [];

    header("Location: Ver_pedido.php?id=".$id_pedido);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Menú</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>


<nav class="navbar bg-white shadow-sm px-4">
    <a href="restaurantes.php" class="btn btn-outline-secondary">← Volver</a>
    <span class="fw-bold fs-5">Menú del Restaurante</span>

    <button class="btn btn-outline-dark" data-bs-toggle="offcanvas" data-bs-target="#carritoCanvas">
        🛒
    </button>
</nav>

<div class="container mt-4">

    
    <?php if($mensaje != ''){ ?>
        <div class="alert alert-success text-center shadow-sm">
            <?= $mensaje ?>
        </div>
    <?php } ?>

   
    <div class="row">

    <?php while($m = $menus->fetch_assoc()){ ?>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">

             
                <img src="<?= $m['imagen'] ? $m['imagen'] : 'img/default.jpg' ?>" 
                     class="card-img-top" 
                     style="height:200px; object-fit:cover;">

                <div class="card-body d-flex flex-column">

                    <h5 class="fw-bold"><?= $m['nombre'] ?></h5>

                    <p class="text-muted small"><?= $m['descripcion'] ?></p>

                    <h6 class="text-success fw-bold">$<?= $m['precio'] ?></h6>

                 
                    <form method="POST" class="mt-auto">
                        <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                        <input type="hidden" name="nombre" value="<?= $m['nombre'] ?>">
                        <input type="hidden" name="precio" value="<?= $m['precio'] ?>">

                        <div class="d-flex gap-2">
                            <input type="number" name="cantidad" value="1" min="1" class="form-control">
                            <button name="agregar" class="btn btn-success">
                                +
                            </button>
                        </div>
                    </form>

                </div>

            </div>
        </div>

    <?php } ?>

    </div>
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="carritoCanvas">
    <div class="offcanvas-header">
        <h5>🛒 Carrito</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">

<?php
$total = 0;

if(!empty($_SESSION['carrito'])){
    foreach($_SESSION['carrito'] as $id => $item){
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
?>
        <div class="card mb-2 p-2">
            <strong><?= $item['nombre'] ?></strong><br>
            $<?= $item['precio'] ?> x <?= $item['cantidad'] ?><br>
            Subtotal: $<?= $subtotal ?>

            <a href="?eliminar=<?= $id ?>" class="btn btn-sm btn-danger mt-2">X</a>
        </div>
<?php
    }
} else {
    echo "<p>Carrito vacío</p>";
}

if ($_SESSION['carrito'] !== []) {
?>
    <a href="?vaciar=1" class="btn btn-warning w-100">Vaciar</a>
    <hr>

    <h5>Total: $<?= $total ?></h5>

    <form method="POST">
        <div class="card p-3 shadow-sm border-0 mt-3">

            <h6 class="mb-3">🧾 Finalizar pedido</h6>

            <label class="form-label fw-semibold">Método de pago</label>
            <select name="metodo_pago" class="form-select mb-3" required>
                <option disabled selected value="">Seleccione una opción</option>
                <option value="Efectivo">💵 Efectivo</option>
                <option value="Tarjeta">💳 Tarjeta</option>
            </select>

            <label class="form-label fw-semibold">Dirección de entrega</label>
            <input type="text" 
                   name="direccion_entrega" 
                   class="form-control mb-3"
                   placeholder="Ej: Colonia Escalón, calle #123"
                   required>

            <button name="finalizar" class="btn btn-success w-100">
                Finalizar compra
            </button>

        </div>
    </form>
<?php
}
?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<style>
body {
    background-color: #f5f5f5;
}

.card {
    border-radius: 15px;
    transition: 0.2s;
}

.card:hover {
    transform: scale(1.03);
}
</style>


