<?php
require "config.php";
$rota = $_GET["rota"] ?? "teste";

function teste(){
    echo "API respondendo com sucesso!";
}

function read($con){
    $stmt = $con->query("SELECT * FROM Clientes");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create($con){
    $nome = $_POST["nome"] ?? "";
    $email = $_POST["email"] ?? "";
    $telefone = $_POST["telefone"] ?? "";
    $endereco = $_POST["endereco"] ?? "";
    
    if (
        $nome === "" ||
        $email === ""
    ) {
        http_response_code(400);
        echo json_encode([
            "erro" => "Preencha nome e email."
        ]);
        exit;
    }
    try {
        $stmt = $con->prepare(
            "INSERT INTO Clientes
            (Nome, Email, Telefone, Endereco)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->execute([
            $nome,
            $email,
            $telefone,
            $endereco
        ]);

        echo json_encode([
            "mensagem" => "Cliente cadastrado com sucesso!"
        ]);

    } catch (PDOException $e) {
        if ($e->getCode() === "23000") {
            http_response_code(409);
            echo json_encode([
                "erro" => "Este email já está cadastrado."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao cadastrar cliente."
            ]);
        }
    }
}

switch ($rota) {
    case "clientes":
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            read($con);
        }
        break;

    case "clientes/create":
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            create($con);
        }
        break;

    default:
        teste();
        break;
}
?>