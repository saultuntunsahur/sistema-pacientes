<?php
include("conexion.php");

$mensaje = "";
$editando = false;
$id_edit = "";
$nombre_edit = "";
$apellido_edit = "";
$edad_edit = "";

// Guardar nuevo registro
if (isset($_POST['guardar'])) {
    $nombre   = $conexion->real_escape_string($_POST['nombre']);
    $apellido = $conexion->real_escape_string($_POST['apellido']);
    $edad     = intval($_POST['edad']);

    $sql = "INSERT INTO pacientes (nombre, apellido, edad) VALUES ('$nombre', '$apellido', $edad)";
    if ($conexion->query($sql)) {
        $mensaje = "¡Paciente registrado exitosamente!";
    } else {
        $mensaje = "Error al registrar: " . $conexion->error;
    }
}

// Eliminar registro
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM pacientes WHERE id = $id";
    if ($conexion->query($sql)) {
        $mensaje = "¡Paciente eliminado exitosamente!";
    }
}

// Cargar datos para editar
if (isset($_GET['editar'])) {
    $editando = true;
    $id_edit = intval($_GET['editar']);
    $resultado_edit = $conexion->query("SELECT * FROM pacientes WHERE id = $id_edit");
    if ($resultado_edit && $resultado_edit->num_rows > 0) {
        $row_edit = $resultado_edit->fetch_assoc();
        $nombre_edit = $row_edit['nombre'];
        $apellido_edit = $row_edit['apellido'];
        $edad_edit = $row_edit['edad'];
    }
}

// Actualizar registro
if (isset($_POST['actualizar'])) {
    $id       = intval($_POST['id']);
    $nombre   = $conexion->real_escape_string($_POST['nombre']);
    $apellido = $conexion->real_escape_string($_POST['apellido']);
    $edad     = intval($_POST['edad']);

    $sql = "UPDATE pacientes SET nombre='$nombre', apellido='$apellido', edad=$edad WHERE id=$id";
    if ($conexion->query($sql)) {
        $mensaje = "¡Paciente actualizado exitosamente!";
        $editando = false;
    } else {
        $mensaje = "Error al actualizar: " . $conexion->error;
    }
}

// Consultar / Buscar pacientes
$busqueda = "";
if (isset($_GET['buscar']) && trim($_GET['buscar']) != "") {
    $busqueda = $conexion->real_escape_string(trim($_GET['buscar']));
    $sql = "SELECT * FROM pacientes WHERE nombre LIKE '%$busqueda%' OR apellido LIKE '%$busqueda%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM pacientes ORDER BY id DESC";
}
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Aplicativo Web - Práctica</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { margin-top: 0; color: #333; }
        input[type=text], input[type=number] { padding: 8px; margin: 5px; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; }
        .btn-green { background-color: #28a745; }
        .btn-blue { background-color: #007bff; }
        .btn-orange { background-color: #fd7e14; }
        .btn-red { background-color: #dc3545; }
        .btn-gray { background-color: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

<div class="card">
    <h2><?php echo isset($editando) && $editando ? "Modificar Registro" : "Registrar Paciente"; ?></h2>
    
    <?php if (isset($mensaje) && $mensaje != ""): ?>
        <p style="color: green; font-weight: bold;"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <!-- Formulario Guardar / Modificar -->
    <form method="POST" action="index.php">
        <input type="hidden" name="id" value="<?php echo isset($id_edit) ? $id_edit : ''; ?>">
        
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre_edit); ?>" required>
        
        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($apellido_edit); ?>" required>
        
        <label>Edad:</label>
        <input type="number" name="edad" value="<?php echo htmlspecialchars($edad_edit); ?>" required>
        
        <?php if (isset($editando) && $editando): ?>
            <button type="submit" name="actualizar" class="btn btn-orange">Guardar Cambios</button>
            <a href="index.php" class="btn btn-gray">Cancelar</a>
        <?php else: ?>
            <button type="submit" name="guardar" class="btn btn-green">Guardar / Registrar</button>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h2>Consultar y Lista de Pacientes</h2>
    
    <!-- Formulario de Consulta -->
    <form method="GET" action="index.php">
        <input type="text" name="buscar" placeholder="Buscar por nombre o apellido..." value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit" class="btn btn-blue">Consultar</button>
        <a href="index.php" class="btn btn-gray">Limpiar Consulta</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Edad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($resultado) && $resultado && $resultado->num_rows > 0): ?>
                <?php while($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                    <td><?php echo $row['edad']; ?></td>
                    <td>
                        <a href="index.php?editar=<?php echo $row['id']; ?>" class="btn btn-orange">Editar</a>
                        <a href="index.php?eliminar=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('¿Está seguro de eliminar este registro?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No se encontraron registros.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>