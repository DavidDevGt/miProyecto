<?php
require_once('../models/Usuario.php');

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * La función "crear" crea un nuevo usuario con un nombre de usuario y contraseña determinados, y
     * devuelve una respuesta JSON indicando éxito o fracaso.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" representa el nombre de usuario del usuario
     * que se está creando.
     * @param contrasena El parámetro "contrasena" representa la contraseña del usuario.
     * 
     * @return una cadena codificada en JSON. La cadena contiene un estado y un mensaje que indica el
     * resultado de la operación. Si la operación es exitosa, el estado será 'éxito' y el mensaje será
     * 'Usuario creado con éxito'. Si la operación no tiene éxito, el estado será 'error' y el mensaje
     * será 'Datos de usuario inválidos'.
     */
    public function crear($nombreUsuario, $contrasena)
    {
        if (!$this->validarDatos($nombreUsuario, $contrasena)) {
            return json_encode(['status' => 'error', 'message' => 'Datos de usuario inválidos.']);
        }

        $this->usuarioModel->crearNuevoUsuario($nombreUsuario, $contrasena);
        return json_encode(['status' => 'success', 'message' => 'Usuario creado con éxito.']);
    }

    /**
     * La función "obtener" en PHP recupera un usuario por su nombre de usuario y devuelve una
     * respuesta JSON que indica éxito o error.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" es una cadena que representa el nombre de
     * usuario del usuario que queremos obtener.
     * 
     * @return una cadena codificada en JSON. Si se encuentra al usuario, devuelve un estado de éxito
     * junto con el objeto de usuario. Si no se encuentra el usuario, devuelve un estado de error junto
     * con un mensaje que indica que no se encontró el usuario.
     */
    public function obtener($nombreUsuario)
    {
        // Validar el username
        $usuario = $this->usuarioModel->obtenerUsuario($nombreUsuario);
        if ($usuario) {
            return json_encode(['status' => 'success', 'usuario' => $usuario]);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }
    }

    /**
     * La función "actualizar" actualiza la contraseña de un nombre de usuario determinado y devuelve
     * un mensaje de éxito en formato JSON.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" representa el nombre de usuario del usuario
     * cuya contraseña necesita ser actualizada.
     * @param nuevaContrasena El parámetro "nuevaContrasena" representa la nueva contraseña que se
     * actualizará para el usuario.
     * 
     * @return una cadena codificada en JSON que contiene el estado y el mensaje de la operación.
     */
    public function actualizar($nombreUsuario, $nuevaContrasena)
    {
        // validar datos...
        $this->usuarioModel->actualizarUsuario($nombreUsuario, $nuevaContrasena);
        return json_encode(['status' => 'success', 'message' => 'Contraseña actualizada con éxito.']);
    }

    /**
     * La función "eliminar" elimina un usuario de la base de datos y devuelve un mensaje de éxito en
     * formato JSON.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" es una cadena que representa el nombre de
     * usuario del usuario a eliminar.
     * 
     * @return una cadena codificada en JSON que contiene el estado y el mensaje de la operación.
     */
    public function eliminar($nombreUsuario)
    {
        // validar nombreUsuario
        $this->usuarioModel->borrarUsuario($nombreUsuario);
        return json_encode(['status' => 'success', 'message' => 'Usuario eliminado con éxito.']);
    }

    /**
     * La función "login" verifica las credenciales del usuario y, si son válidas, genera un token JWT.
     * 
     * @param nombreUsuario El parámetro "nombreUsuario" representa el nombre de usuario del usuario que intenta iniciar sesión.
     * @param contrasena El parámetro "contrasena" representa la contraseña del usuario que intenta iniciar sesión.
     * 
     * @return una cadena codificada en JSON que contiene el estado de la operación y un token JWT si las credenciales son válidas.
     */
    public function login($nombreUsuario, $contrasena)
    {
        // Validamos los datos del usuario.
        if (!$this->validarDatos($nombreUsuario, $contrasena)) {
            return json_encode(['status' => 'error', 'message' => 'Datos de usuario inválidos.']);
        }

        // Verificamos las credenciales del usuario.
        $usuario = $this->usuarioModel->obtenerUsuarioPorCredenciales($nombreUsuario, $contrasena);
        if ($usuario) {
            // Si las credenciales son válidas, generamos un token JWT.
            $token = JwtHandler::encode(['id' => $usuario->id, 'nombreUsuario' => $usuario->nombreUsuario]);
            return json_encode(['status' => 'success', 'token' => $token]);
        } else {
            // Si las credenciales no son válidas, devolvemos un mensaje de error.
            return json_encode(['status' => 'error', 'message' => 'Nombre de usuario o contraseña incorrectos.']);
        }
    }

    private function validarDatos($nombreUsuario, $contrasena)
    {
        // Verificamos que el nombre de usuario y la contraseña no estén vacíos.
        if (empty($nombreUsuario) || empty($contrasena)) {
            return false;
        }

        // Verificamos que el nombre de usuario y la contraseña cumplan con la longitud mínima y máxima definida.
        if (strlen($nombreUsuario) < 5 || strlen($nombreUsuario) > 20 || strlen($contrasena) < 8) {
            return false;
        }

        // Verificamos que el nombre de usuario y la contraseña solo contengan caracteres permitidos.
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $nombreUsuario) || !preg_match('/^[a-zA-Z0-9!@#$%^&*()_]+$/', $contrasena)) {
            return false;
        }

        // Si todas las validaciones pasan, retornamos verdadero.
        return true;
    }
}