<?php
session_start();
require_once("claseUrbanFood.php");

$error = "";

if(isset($_POST['login'])){

    $correo = $_POST['correo'];
    $password = $_POST['contrasena'];

    $conexion = new claseConexionFood();

    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultado = $conexion->consulta($sql);

    if($resultado->num_rows > 0){

        $usuario = $resultado->fetch_assoc();

        if(password_verify($password, $usuario['contrasena'])){

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['id_cliente'] = $usuario['id_cliente'];
            $_SESSION['id_repartidor'] = $usuario['id_repartidor']; 
            $_SESSION['rol'] = $usuario['rol'];

            if($usuario['rol'] == 'admin'){
                header("Location: implementacionUrbanFood.php");
            } elseif($usuario['rol'] == 'cliente'){
                header("Location: Restaurantes.php");
            } else {
                header("Location: Panel_repartidor.php"); 
            }
            exit();

        } else {
            $error = "Contraseña incorrecta";
        }

    } else {
        $error = "Usuario no existe";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login - UrbanFood</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #CA7E71, #E8A453);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-login {
    width: 100%;
    max-width: 400px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.btn-custom {
    background-color: #CA7E71;
    border: none;
}

.btn-custom:hover {
    background-color: #b96d61;
}

.form-control {
    border-radius: 10px;
}
</style>
</head>

<body>

<div class="card card-login p-4 bg-white">

    <h3 class="text-center mb-3">🍔 UrbanFood</h3>
    <p class="text-center text-muted mb-4">Inicia sesión</p>

    <?php if($error != ""){ ?>
        <div class="alert alert-danger text-center">
            <?= $error ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" placeholder="ejemplo@gmail.com" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" placeholder="********" required>
        </div>

        <button name="login" class="btn btn-custom w-100 text-white mb-2">
            Iniciar sesión
        </button>

        <a href="RegistroCliente.php" class="btn btn-outline-secondary w-100">
            Crear cuenta
        </a>

    </form>

</div>

</body>
</html>