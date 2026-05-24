<?php
session_start();

// 1. Validar si el usuario está logueado de manera híbrida
if (!isset($_SESSION['id_usuario']) && !isset($_GET['rol'])) {
    header("Location: Login.php");
    exit();
}

// 2. Detectar el rol de manera híbrida
$rol = $_GET['rol'] ?? $_SESSION['rol'] ?? 'cliente'; 

// 3. Asignar el PDF correcto según el rol detectado
if ($rol === 'cliente') {
    $nombre_archivo_pdf = "manualcliente.pdf";
    $titulo_pantalla = "📖 Manual de Usuario - Área de Clientes";
    $pagina_regreso = "Restaurantes.php"; // Página principal del cliente
} else {
    $nombre_archivo_pdf = "manualadmin.pdf";
    $titulo_pantalla = "🛠️ Manual Técnico y Operativo - Administración / Repartidores";
    
    // Determinar regreso exacto si es admin o repartidor
    if ($rol === 'admin') {
        $pagina_regreso = "implementacionUrbanFood.php";
    } else {
        $pagina_regreso = "Panel_repartidor.php";
    }
}

$ruta_completa_pdf = $nombre_archivo_pdf; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual en Línea - UrbanFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .contenedor-manual {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 25px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .visor-pdf {
            width: 100%;
            height: 680px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="contenedor-manual text-center">
        
        <div class="text-start mb-3">
            <a href="<?= $pagina_regreso ?>" class="btn btn-outline-secondary fw-semibold">
                ⬅️ Volver
            </a>
        </div>

        <h2 class="fw-bold mb-2" style="color: #CA7E71;"><?= $titulo_pantalla ?></h2>
        <p class="text-muted mb-4">Consulta el documento interactivo en línea o descárgalo directamente en tu equipo.</p>
        
        <div class="mb-4">
            <a href="<?= $ruta_completa_pdf ?>" download="<?= $nombre_archivo_pdf ?>" class="btn btn-lg text-white" style="background-color: #28a745;">
                📥 Descargar Manual (PDF)
            </a>
        </div>

        <div class="ratio ratio-16x9 visor-pdf">
            <iframe src="<?= $ruta_completa_pdf ?>" width="100%" height="100%" style="border: none;"></iframe>
        </div>
        
        <div class="mt-4">
            <p class="small text-muted">UrbanFood</p>
        </div>
    </div>
</div>

</body>
</html>