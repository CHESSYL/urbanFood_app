<?php
require_once("claseUrbanFood.php");
$obj = new claseConexionFood();

$id = $_GET['id'];

if(isset($_POST['cancelar_pedido'])){

    $id_pedido = $_POST['id_pedido'];

    $obj->modificar(
        "pedidos",
        ["estado" => "cancelado"],
        "id_pedido",
        $id_pedido
    );

$obj->consulta("
    UPDATE repartidores 
    SET estado = 'Disponible'
    WHERE id_repartidor = (
        SELECT id_repartidor 
        FROM pedidos 
        WHERE id_pedido = $id_pedido
    )
");

$obj->modificar(
    "pedidos",
    ["id_repartidor" => null],
    "id_pedido",
    $id_pedido
);

    header("Location: Ver_pedido.php?id=".$id_pedido);
    exit();
}

$pedido = $obj->consulta("
    SELECT p.*, concat(c.nombre, ' ', c.apellido) as cliente, r.nombre as repartidor, r.telefono as telefono_repartidor
    FROM pedidos p
    JOIN clientes c ON p.id_cliente = c.id_cliente
    LEFT JOIN repartidores r ON p.id_repartidor = r.id_repartidor
    WHERE p.id_pedido = $id
")->fetch_assoc();

$detalles = $obj->consulta("
    SELECT d.*, m.nombre as menu, m.imagen , r.nombre as restaurante
    FROM detalle_pedido d
    JOIN menus m ON d.id_menu = m.id_menu
    JOIN restaurantes r ON m.id_restaurante = r.id_restaurante
    WHERE d.id_pedido = $id
    
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Detalle del Pedido</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

  
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body">

            <h3 class="mb-4">📦 Pedido #<?= $pedido['id_pedido'] ?></h3>

            <div class="row">

    <div class="col-md-6">
        <p><strong>👤 Cliente:</strong> <?= $pedido['cliente'] ?></p>

        <p><strong>🚚 Repartidor:</strong> 
            <?= $pedido['repartidor'] ?? 'Sin asignar' ?>
        </p>

           <p><strong>🚚 Telefono del repartidor:</strong> 
            <?= $pedido['telefono_repartidor'] ?? 'Sin asignar' ?>
        </p>

        <p><strong>📍 Dirección:</strong> 
            <?= $pedido['direccion_entrega'] ?? 'No especificada' ?>
        </p>
    </div>

    <div class="col-md-6 text-md-end">

        <p><strong>📅 Fecha:</strong><br>
            <?= date("d/m/Y h:i A", strtotime($pedido['fecha'])) ?>
        </p>

        <p><strong>💳 Método de pago:</strong><br>
            <?= $pedido['metodo_pago'] ?? 'No definido' ?>
        </p>

        <p>
            <strong>📌 Estado:</strong><br>
           <span class="badge bg-<?=
        $pedido['estado']=='entregado' ? 'success' :
        ($pedido['estado']=='pendiente' ? 'warning' :
        ($pedido['estado']=='en camino' ? 'primary' :
        ($pedido['estado']=='cancelado' ? 'danger' : 'secondary')))
        ?>">
                <?= $pedido['estado'] ?>
            </span>
        </p>

        <h4 class="text-success">💲<?= $pedido['total'] ?></h4>
        <?php if($pedido['estado'] != 'entregado' && $pedido['estado'] != 'cancelado'){ ?>

<form method="POST" class="mt-3">
    <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">

    <button type="submit" name="cancelar_pedido" 
        class="btn btn-danger w-100"
        onclick="return confirm('¿Seguro que deseas cancelar el pedido?')">
        ❌ Cancelar pedido
    </button>
</form>

<?php } ?>
    </div>

</div>

        </div>
    </div>

  
    <h4 class="mb-3">🍔 Productos</h4>

    <div class="row">

    <?php while($d = $detalles->fetch_assoc()){ ?>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">

                <img src="<?= $d['imagen'] ? $d['imagen'] : 'img/default.jpg' ?>" 
                     class="card-img-top">

                <div class="card-body">

                    <h4 class="mb-1">Restaurante: <?= $d['restaurante'] ?></h4>
                    <h5 class="fw-bold"><?= $d['menu'] ?></h5>
                    <p class="mb-1">Cantidad: <?= $d['cantidad'] ?></p>
                    <p class="mb-1">Precio: $<?= $d['precio_unitario'] ?></p>
                   

                    <hr>

                    <h6 class="text-end text-success">
                        Subtotal: $<?= $d['subtotal'] ?>
                    </h6>

                </div>
            </div>
        </div>

    <?php } ?>

    </div>

 
    <a href="Restaurantes.php" class="btn btn-dark mt-3">
        ← Volver
    </a>

</div>

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
    transform: scale(1.02);
}

.badge {
    font-size: 14px;
    padding: 8px 12px;
}
</style>