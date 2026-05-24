<?php
require_once("claseUrbanFood.php");

$mensaje = "";

if(isset($_POST['registrar'])){

    $objetoConexion = new claseConexionFood();

    $datosCliente = [
        "nombre" => $_POST['nombre'],
        "apellido" => $_POST['apellido'],
        "telefono" => $_POST['telefono'],
    ];

    $id_cliente = $objetoConexion->insertar("clientes", $datosCliente);

    $hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    $datosUsuario = [
        "correo" => $_POST['correo'],
        "contrasena" => $hash,
        "id_cliente" => $id_cliente
    ];

    $objetoConexion->insertar("usuarios", $datosUsuario);

    $mensaje = "Registro exitoso 🎉";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Registro</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg, #E8A453, #FEE5AD);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.card{
    border-radius:20px;
    padding:30px;
}

.titulo{
    font-weight:bold;
    color:#CA7E71;
}
</style>

</head>

<body>

<div class="card shadow-lg" style="width: 450px;">

    <h3 class="text-center titulo mb-4">📝 Crear cuenta</h3>

    <?php if($mensaje){ ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php } ?>

    <form method="POST">

        <input class="form-control mb-2" type="text" name="nombre" placeholder="Nombre" required>

        <input class="form-control mb-2" type="text" name="apellido" placeholder="Apellido" required>

        <input class="form-control mb-2" type="text" name="telefono" placeholder="Teléfono" required>

        <input class="form-control mb-2" type="email" name="correo" placeholder="Correo" required>

        <input class="form-control mb-3" type="password" name="contrasena" placeholder="Contraseña" required>

        <button class="btn btn-success w-100 mb-2" name="registrar">
            Registrarse
        </button>

        <a class="btn btn-outline-secondary w-100" href="login.php">
            Volver al login
        </a>

    </form>

</div>

</body>
</html>