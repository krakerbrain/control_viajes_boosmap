<?php

class HaColaborado
{
    private $con;
    private $idusuario;

    /**
     * Constructor de la clase.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param int $idusuario ID del usuario a verificar.
     */
    public function __construct($conexion, $idusuario)
    {
        $this->con = $conexion;
        $this->idusuario = $idusuario;
    }

    /**
     * Verifica si el usuario ha colaborado.
     *
     * @return bool True si el usuario ha colaborado, false en caso contrario.
     * @throws PDOException Si ocurre un error al ejecutar la consulta.
     */
    public function haColaborado()
    {
        try {
            // Consulta para verificar si el usuario ha colaborado
            $sql = $this->con->prepare("SELECT COUNT(*) as colaborado FROM colaboraciones WHERE idusuario = :idusuario AND verificado = 1");
            $sql->bindParam(':idusuario', $this->idusuario, PDO::PARAM_INT);
            $sql->execute();

            // Obtener el resultado
            $resultado = $sql->fetch(PDO::FETCH_ASSOC);

            // Devolver true si el usuario ha colaborado, false en caso contrario
            return $resultado['colaborado'] > 0;
        } catch (PDOException $e) {
            // Lanzar la excepción para manejarla en un nivel superior
            throw new PDOException("Error al verificar la colaboración: " . $e->getMessage());
        }
    }
}
