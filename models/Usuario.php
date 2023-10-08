<?php
require_once('../config/db.php');

class Usuario
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conexion->connect_error) {
            die("Error al conectar a la base de datos: " . $this->conexion->connect_error);
        }
    }


    /**
     * La función "crearNuevoUsuario" crea un nuevo usuario en una base de datos con una contraseña
     * hash.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" es el nombre de usuario del nuevo usuario que
     * deseas crear. Es un valor de cadena que representa el identificador único del usuario.
     * @param contraseña El parámetro "contraseña" es la contraseña que el usuario quiere establecer
     * para su nueva cuenta.
     */
    public function crearNuevoUsuario($nombreUsuario, $contrasena)
    {
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

        $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombreUsuario, contraseña) VALUES (?, ?)");

        $stmt->bind_param("ss", $nombreUsuario, $contrasenaHash);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * La función "actualizarUsuario" actualiza la contraseña de un usuario en la base de datos.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" es el nombre de usuario del usuario cuya
     * contraseña necesita ser actualizada.
     * @param nuevaContrasena El parámetro "nuevaContrasena" representa la nueva contraseña que se
     * actualizará para el usuario.
     */
    public function actualizarUsuario($nombreUsuario, $nuevaContrasena)
    {
        $nuevaContrasenaHash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
        $stmt = $this->conexion->prepare("UPDATE usuarios SET contraseña = ? WHERE nombreUsuario = ?");
        $stmt->bind_param("ss", $nuevaContrasenaHash, $nombreUsuario);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * La función "borrarUsuario" en PHP actualiza el campo "activo" de un usuario en la tabla "usuarios"
     * a 0 según el nombre de usuario proporcionado.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" es el nombre de usuario del usuario que desea
     * eliminar de la base de datos.
     */
    public function borrarUsuario($nombreUsuario)
    {
        $stmt = $this->conexion->prepare("UPDATE usuarios SET activo = 0 WHERE nombreUsuario = ?");
        $stmt->bind_param("s", $nombreUsuario);
        $stmt->execute();
        $stmt->close();
    }
}