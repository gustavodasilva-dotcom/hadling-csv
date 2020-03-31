<?php

class Usuario {
    // Attributes
    private $idusuario;
    private $deslogin;
    private $dessenha;
    private $dtcadastro;

    // Methods
    
    /**
     * The 'setData()' method put the data that came from the database into de attributes via getters and setters.
     * This method is called into the other methods that deal with the queries.
     */
    public function setData($data) {
        $this->setIdusuario($data['idusuario']);
        $this->setDeslogin($data['deslogin']);
        $this->setDessenha($data['dessenha']);
        $this->setDtcadastro(new DateTime($data['dtcadastro']));
    }

    /**
     * The 'loadById()' - as the name say very clearly - it'll load the some register from the database with the
     * parameter passed (the 'id').
     */
    public function loadById($id) {
        $sql = new Sql();
        
        $results = $sql->select("SELECT * FROM tb_usuario WHERE idusuario = :ID", array(
            ":ID"=>$id
        ));

        if (count($results[0]) > 0) {
            $this->setData($results[0]);
        }
    }

    /**
     * The 'getList()' returns all the registers from the database.
     */
    public static function getList() {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_usuario ORDER BY deslogin");
    }

    /**
     * The 'search()' searches the table using a string passed as a parameter. This method is very powerful when
     * you don't know the whole login, just a piece. We use the MySQL 'LIKE' statement to find some register that
     * looks like the string passed as a parameter.
     */
    public static function search($login) {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_usuario WHERE deslogin LIKE :SEARCH ORDER BY deslogin", array(
            ":SEARCH" => "%" . $login . "%"
        ));
    }

    /**
     * The 'login()' takes the parameters, selects and returns from the table the related data. The data returned
     * is passed to the 'setData()' that, consequently, it's passed to the attributes.
     */
    public function login($login, $password) {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_usuario WHERE deslogin = :LOGIN AND dessenha = :PASSWORD", array(
            ":LOGIN" => $login,
            ":PASSWORD" => $password
        ));

        if (count($results) > 0) {
            $this->setData($results[0]);
        } else {
            throw new Exception("Invalids login and password.");
        }
    }

    /**
     * The 'insert()' method works this way: First things first, we initialize an object of the class 'Sql()',
     * our connection to the database and queries. We make a select statement from the class 'Sql()' passing as
     * parameters the query and the data. The query - as you can see - is a call to a procedure. The procedure
     * will add the data and return the id and an array for the recently registered data. Then we call the 
     * 'setData()' method adding the array to it.
     */
    public function insert() {
        $sql = new Sql();

        $results = $sql->select("CALL sp_usuario_insert(:LOGIN, :PASSWORD)", array(
            ":LOGIN" => $this->getDeslogin(),
            ":PASSWORD" => $this->getDessenha()
        ));

        if (count($results) > 0) {
            $this->setData($results[0]);
        }
    }

    /**
     * The 'update()' method works like the 'insert()' method, but instead of insert data it'll update the register
     * in the database with the parameters passed.
     */
    public function update($login, $password) {
        $this->setDeslogin($login);
        $this->setDessenha($password);
        
        $sql = new Sql();

        $sql->query("UPDATE tb_usuario SET deslogin = :LOGIN, dessenha = :PASSWORD WHERE idusuario = :ID", array(
            ":LOGIN" => $this->getDeslogin(),
            ":PASSWORD" => $this->getDessenha(),
            ":ID" => $this->getIdusuario()
        ));
    }

    /**
     * The 'delete()' method... deletes. We take the 'id' from the getters and, after deleting from the table, we
     * "clean" from the attributes. Just to be clear, to use the 'delete()' method correctly, the class needs
     * already to be initialized as an object.
     */
    public function delete() {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_usuario WHERE idusuario = :ID", array(
            ":ID" => $this->getIdusuario()
        ));

        $this->setIdusuario(0);
        $this->setDeslogin("");
        $this->setDessenha("");
        $this->setDtcadastro(new DateTime());
    }

    public function __toString() {
        return json_encode(array(
            "idusuario" => $this->getIdusuario(),
            "deslogin" => $this->getDeslogin(),
            "dessenha" => $this->getDessenha(),
            "dtcadastro" => $this->getDtcadastro()->format("d/m/Y H:i:s")
        ));
    }

    // Getters, Setters and Construtor
    public function __construct($login = "", $password = "") {
        $this->setDeslogin($login);
        $this->setDessenha($password);
    }

    public function getIdusuario() {
        return $this->idusuario;
    }
    public function setIdusuario($idusuario) {
        $this->idusuario = $idusuario;
    }
    public function getDeslogin() {
        return $this->deslogin;
    }
    public function setDeslogin($deslogin) {
        $this->deslogin = $deslogin;
    }
    public function getDessenha() {
        return $this->dessenha;
    }
    public function setDessenha($dessenha) {
        $this->dessenha = $dessenha;
    }
    public function getDtcadastro() {
        return $this->dtcadastro;
    }
    public function setDtcadastro($dtcadastro) {
        $this->dtcadastro = $dtcadastro;
    }
}

?>