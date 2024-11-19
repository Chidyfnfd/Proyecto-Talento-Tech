<?php
class GestorUsuario
{
    public function busqueda(Usuario $usuario)
    {
        $credencialesCorrectas = false;

        $conexion = new Conexion();
        $enlace_conexion = $conexion->abrir();
        $usuarioN = $usuario->obtener_usuarioN();
        $contraseña = $usuario->obtener_contraseña();

        // Preparar la consulta
        $sql = $enlace_conexion->prepare("SELECT * FROM usuarios WHERE usuarioN = ?");
        $sql->bind_param("s", $usuarioN);
        $sql->execute();
        $resultado = $sql->get_result()->fetch_assoc();

        if ($resultado) {
            // Comparar la contraseña directamente (no recomendado en producción)
            if ($resultado["contraseña"] == $contraseña) {
                $credencialesCorrectas = $resultado; // Retorna los datos completos
            } else {
                $_SESSION["mensaje"] = "Las credenciales son incorrectas";
            }
        } else {
            $_SESSION["mensaje"] = "El usuario no existe";
        }

        $conexion->cerrar();
        return $credencialesCorrectas; // Retorna los datos completos o false
    }
    public function agregarCliente(Usuario $usuario)
    {
        $conexion = new Conexion();
        $enlaceConexion = $conexion->abrir();
        $nombre = $usuario->obtener_nombre();
        $contraseña = $usuario->obtener_contraseña();
        $usuarioN = $usuario->obtener_usuarioN();
        $tipoUsuario = $usuario->obtener_tipoUsuario();

        // Prepara la declaración
        $sql = $enlaceConexion->prepare("INSERT INTO usuarios(nombre, contraseña, usuarioN, tipoUsuario) VALUES (?, ?, ?, ?)");

        // Verifica si la preparación fue exitosa
        if ($sql === false) {
            die("Error al preparar la consulta: " . mysqli_error($enlaceConexion));
        }

        // Enlaza los parámetros
        $sql->bind_param("sssi", $nombre, $contraseña, $usuarioN, $tipoUsuario);

        // Ejecuta la declaración
        if (!$sql->execute()) {
            die("Error al ejecutar la consulta: " . $sql->error);
        }

        // Obtiene el número de filas afectadas
        $filasAfectadas = $sql->affected_rows;

        // Cierra la declaración y la conexión
        $sql->close();
        $conexion->cerrar();

        return $filasAfectadas;
    }

}
