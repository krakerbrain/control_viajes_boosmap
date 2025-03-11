<?php

class HaColaborado
{
    private $con;
    private $idusuario;

    public function __construct($conexion, $idusuario)
    {
        $this->con = $conexion;
        $this->idusuario = $idusuario;
    }

    /**
     * Verifica si el usuario ha colaborado y devuelve el resultado en un array
     * con un unico elemento con la clave "verificado" con valor booleano
     *
     * @return array
     */
    public function haColaborado()
    {
        $sql = $this->con->prepare("SELECT verificado FROM colaboraciones WHERE idusuario = :idusuario");
        $sql->bindParam(':idusuario', $this->idusuario);
        $sql->execute();
        $datos = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
}
