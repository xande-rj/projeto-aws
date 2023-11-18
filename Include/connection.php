<?php
// Criando conexao com o Banco
class Conn
{
    private $servername;
    private $username;
    private $password;
    private $database;
    private $conn; // Propriedade para armazenar a conexão

    public function __construct($servername, $username, $password, $database = null)
    {

        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        // Inicializar a conexão no construtor se o banco de dados foi fornecido
        if (!empty($database)) {
            $this->Create_Dbs();
        }
    }

    public function Create_Dbs()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

        $this->conn->query("
            CREATE DATABASE IF NOT EXISTS Gerencia;
        ");

        $this->conn->query("
            CREATE DATABASE IF NOT EXISTS Telecall;
        ");

        //conectando no banco
        if ($this->conn->connect_error) {
            /* header('location: http://localhost:8080/projeto/nao-encontrado.php'); */
            header('location: '. URL .'nao-encontrado.php');
            exit;
        }
    }
    public function Create_Table_Usuario()
    {
        $this->database = 'Telecall';

        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);
        $this->conn->query("
            CREATE TABLE IF NOT EXISTS Usuarios(
            nome varchar(100) not null, 
            data_nascimento date not null , 
            sexo varchar(100) not null, 
            nome_materno varchar(100), 
            cpf varchar(11) primary key not null , 
            telefone_celular varchar(17) not null, 
            telefone_fixo varchar(17) not null, 
            endereco varchar(255) not null, 
            complemento varchar(255) not null, 
            login varchar(6) not null , 
            senha varchar(100) not null
            );
        ");
        if ($this->conn->connect_error) {
            /* header('location: http://localhost:8080/projeto/nao-encontrado.php'); */
            header('location: '. URL .'nao-encontrado.php');
            exit;
        }
    }

    public function Create_Table_Gerencia()
    {

        $this->database = 'Gerencia';

        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);


        $result = $this->conn->query("
        CREATE TABLE IF NOT EXISTS Usuario(
            nome varchar(100),
            usuario varchar(100),
            senha varchar(100),
            cpf int primary key not null
            );
        ");

        $resultdb = $this->conn->query("
        SELECT * FROM Usuario;
        ");

        $resultcheck = mysqli_num_rows($resultdb);

        if ($resultcheck == 0) {
            // Criacao de admin
            $result = $this->conn->query("
            Insert INTO Usuario values ('Alexandre S.', 'admin',md5('admin'), '12345678')
            ");
        }
    }
    public function getQueryAdmin()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);
        return $result = $this->conn->query("SELECT * FROM Usuarios");
    }

    public function deleteUser($CpfUsuario)
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);
        return $result = $this->conn->query("DELETE FROM Usuarios WHERE cpf = '$CpfUsuario'");
    }

    public function getServerName()
    {
        return $this->servername;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public function setDatabase($database)
    {
        $this->database = $database;
    }
    public function getDatabase()
    {
        return $this->database;
    }

    // Adicionar métodos adicionais conforme necessário para manipular a conexão
}

//Classe Gerencia 
class mysqldb
{

    private $connection; // Propriedade para armazenar a instância de Conn

    public function __construct()
    {
        // Crie uma instância de Conn ao instanciar a classe Gerencia
        $this->connection = new Conn(HOST, USER, PASS);
    }


    public function SearchLogin_Gerencia($NomeUsuarioGerente, $SenhaUsuarioGerente)
    {
        $this->connection->setDatabase('Gerencia');

        $conn = new mysqli($this->connection->getServerName(), $this->connection->getUserName(), $this->connection->getPassword(), $this->connection->getDatabase());

        $result = $conn->query("
        SELECT nome,cpf FROM Usuario WHERE '$NomeUsuarioGerente' = usuario AND md5('$SenhaUsuarioGerente') = senha;
        ");
        $resultcheck = mysqli_num_rows($result);
        if ($resultcheck == 1) {
            while ($row = mysqli_fetch_assoc($result)) {
                session_start();
                $_SESSION["Usuario"] = $row['nome'];
                header('location: '. URL);
                exit;
            }
        } else {
            // exit;
            /* header('location: http://localhost:8080/projeto'); */
            header('location: '. URL .'erro-login.php');
            exit;
        }
    }
}


//Classe Usuario 
include(__DIR__ . '/../conf.php');
class mysqldbUsuario
{
    private $connection; // Propriedade para armazenar a instância de Conn

    public function __construct()
    {
        // Crie uma instância de Conn ao instanciar a classe Gerencia
        $this->connection = new Conn(HOST, USER, PASS, "Telecall");
    }



    public function Login_Usuario($NomeUsuario, $SenhaUsuario)
    {
        $conn = new mysqli($this->connection->getServerName(), $this->connection->getUserName(), $this->connection->getPassword(), $this->connection->getDatabase());

        $result = $conn->query("
        SELECT nome,cpf FROM Usuarios WHERE '$NomeUsuario' = login AND md5('$SenhaUsuario') = senha;
        ");
        $resultcheck = mysqli_num_rows($result);
        if ($resultcheck) {
            if ($resultcheck == 1) {
                while ($row = mysqli_fetch_assoc($result)) {
                    session_start();
                    $_SESSION["Usuario"] = $row['nome'];
                    header('location: '. URL);
                    exit;
                }
            }
        } else {
            /* header('location: http://localhost:8080/projeto/erro-login.php '); */
            header('location: '. URL .'erro-login.php');
            exit;
        }
    }

    public function Register_Usuario($nome, $dataNascimento, $sexo, $nomeMaterno, $cpf, $telefoneCelular, $telefoneFixo, $endereco, $complemento, $login, $senha)
    {

        $conn = new mysqli($this->connection->getServerName(), $this->connection->getUserName(), $this->connection->getPassword(), $this->connection->getDatabase());

        $result = "INSERT INTO Usuarios 
        (nome, data_nascimento, sexo, nome_materno, cpf, telefone_celular, telefone_fixo, endereco, complemento, login, senha) 
        VALUES 
        ('$nome', '$dataNascimento', '$sexo', '$nomeMaterno', '$cpf', '$telefoneCelular', '$telefoneFixo', '$endereco', '$complemento' ,'$login', '$senha')";

        if ($conn->query($result) === TRUE) {
            header('location: '. URL .'log.php');
            exit;
            //Xampp
            // header('location: /projeto-telecall/log.php');
            // exit;
        } else {

            /* header('location: http://localhost:8080/projeto/erro-login.php '); */
            header('location: '. URL .'erro-login.php');
            exit;
        }
    }
}
