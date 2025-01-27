<?php
session_start();
require '../MODELO/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria = $_POST["categoria"];
    $prioridad = $_POST["prioridad"];
    $descripcion = $_POST["descripcion"];
    $c_usuario = $_POST["correo"];  

 
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $imagen = $_FILES["imagen"]["name"];
        $imagen_temp = $_FILES["imagen"]["tmp_name"];
    } else {
        $imagen = null;
    }


    $query = "INSERT INTO u_incidentes (categoria, prioridad, descripcion, imagen, c_usuario) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);

    if ($stmt === false) {
        die('Error en la preparación del statement: ' . $conexion->error);
    }

    $stmt->bind_param("sssss", $categoria, $prioridad, $descripcion, $imagen, $c_usuario);

    if ($stmt->execute()) {
        $_SESSION['registro_confirmado'] = "Se confirmó el registro.";
        header("Location: ../VISTA/incidencias_alumnos.php");
        echo '<script>alert("El incidente se ha agregado correctamente.");</script>';
    } else {
        echo "Error al agregar el incidente.";
    }

    $stmt->close();
    $conexion->close();
}
?>
