<?php
session_start();
require_once("claseUrbanFood.php");

$obj = new claseConexionFood();

$id_cliente = $_SESSION['id_cliente'];

$pedidos = $obj->consulta("
    SELECT p.*, r.nombre as repartidor
    FROM pedidos p
    LEFT JOIN repartidores r ON p.id_repartidor = r.id_repartidor
    WHERE p.id_cliente = $id_cliente
    ORDER BY p.fecha DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Mis Pedidos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h2 class="mb-4">📦 Historial de Pedidos</h2>
<a href="Restaurantes.php" class="btn btn-outline-secondary mb-4">← Volver a Restaurantes</a>

<?php if($pedidos->num_rows > 0){ ?>

<div class="row">

<?php while($p = $pedidos->fetch_assoc()){ ?>

    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100">

            <div class="card-body">

                <h5>Pedido #<?= $p['id_pedido'] ?></h5>

                <p class="mb-1">
                    📅 <?= date("d/m/Y", strtotime($p['fecha'])) ?>
                </p>

                <p class="mb-1">
                    💲 <?= $p['total'] ?>
                </p>

                <p>
                    📌 
                    <span class="badge bg-<?=
                        $p['estado']=='entregado' ? 'success' :
                        ($p['estado']=='pendiente' ? 'warning' :
                        ($p['estado']=='en camino' ? 'primary' : 'danger'))
                    ?>">
                        <?= $p['estado'] ?>
                    </span>
                </p>

                <a href="ver_pedido.php?id=<?= $p['id_pedido'] ?>" 
                   class="btn btn-outline-dark w-100">
                   Ver detalle
                </a>

            </div>

        </div>
    </div>

<?php } ?>

</div>

<?php } else { ?>

<div class="alert alert-info">
    No tienes pedidos aún 🛒
</div>

<?php } ?>

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
    transform: scale(1.03);
}
</style>