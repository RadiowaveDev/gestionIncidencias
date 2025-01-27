<?php
session_start();
require '../MODELO/conexion.php';

//clase ControladorIncidente, constructor, funciones Create, Update, Remove incidente
class ControladorIncidente
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function editarIncidente($id, $categoria, $prioridad, $estado, $descripcion, $c_usuario)
    {
        $query = "UPDATE u_incidentes SET categoria = ?, prioridad = ?, estado = ?, descripcion = ?, c_usuario = ? WHERE id_incidente = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssssi", $categoria, $prioridad, $estado, $descripcion, $c_usuario, $id);
        $stmt->execute();
    }

    public function eliminarIncidente($id)
    {
        $query = "DELETE FROM u_incidentes WHERE id_incidente = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function agregarResponsable($id,$id_admin)
    {
        $query = "UPDATE u_incidentes SET id_admin=? WHERE id_incidente =?";
        $stmt = $this->conexion->prepare($query);
        $stmt -> bind_param("si",$id_admin,$id);
        $stmt->execute();
    }

    public function agregarIncidente($categoria, $prioridad, $estado, $descripcion, $c_usuario, $imagen)
    {
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $contenido = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
        } else {
            $contenido = null;
        }

        $query = "INSERT INTO u_incidentes (categoria, prioridad, estado, descripcion, c_usuario, imagen) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ssssss", $categoria, $prioridad, $estado, $descripcion, $c_usuario, $contenido);
        $stmt->execute();
    }

    public function filtrarIncidentes($categoria, $prioridad, $estado)
    {
        $query = "SELECT * FROM u_incidentes WHERE 1 = 1";

        if (!empty($categoria)) {
            $query .= " AND categoria = '$categoria'";
        }

        if (!empty($prioridad)) {
            $query .= " AND prioridad = '$prioridad'";
        }

        if (!empty($estado)) {
            $query .= " AND estado = '$estado'";
        }

        $resultado = mysqli_query($this->conexion, $query);
        return $resultado;
    }
}

//EDITAR UN INCIDENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_GET['action'] === 'editar') {
        $id = $_POST['id_incidente'];
        $categoria = $_POST['categoria'];
        $prioridad = $_POST['prioridad'];
        $estado = $_POST['estado'];
        $descripcion = $_POST['descripcion'];
        $c_usuario = $_POST['c_usuario'];

        $controlador = new ControladorIncidente($conexion);
        $controlador->editarIncidente($id, $categoria, $prioridad, $estado, $descripcion, $c_usuario);

        header("Location: ../VISTA/incidencias_admin.php");
        exit();
    }
}

//ELIMINAR UN INCIDENTE
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] === 'eliminar' && isset($_GET['id'])) {
        $id = $_GET['id'];

        $controlador = new ControladorIncidente($conexion);
        $filasEliminadas = $controlador->eliminarIncidente($id);

        if ($filasEliminadas > 0) {
            echo '<p>Incidente eliminado correctamente.</p>';
            header("Location: ../VISTA/incidencias_admin.php");
        } else {
            echo '<p>No se encontró el incidente o no se pudo eliminar.</p>';
        }
    }
}

//AGREGAR UN INCIDENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_GET['action'] === 'agregar') {
        $categoria = $_POST['categoria'];
        $prioridad = $_POST['prioridad'];
        $estado = $_POST['estado'];
        $descripcion = $_POST['descripcion'];
        $c_usuario = $_POST['c_usuario'];
        $imagen = $_FILES['imagen'];

        $controlador = new ControladorIncidente($conexion);
        $controlador->agregarIncidente($categoria, $prioridad, $estado, $descripcion, $c_usuario, $imagen);

        header("Location: ../VISTA/incidencias_admin.php");
        exit();
    }
}



//ASIGNAR INCIDENTES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_GET['action'] === 'asignar') {
        $id = $_POST['id_incidente'];
        $responsable = $_POST['responsable'];
        
        $controlador = new ControladorIncidente($conexion);
        $controlador->agregarResponsable($id, $responsable);

        header("Location: ../VISTA/asignar_incidencias.php");
        exit();
    }
}    
mysqli_close($conexion);
?>