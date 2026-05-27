<?php
// Configurações de conexão de resposta da api
    header("Content-Type: application/json; charset=UTF-8");
    // Permitir acesso de qualquer origem (CORS)
    header("Access-Control-Allow-Origin: *");
    // Permitir métodos HTTP específicos
    header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE");
    // Permitir cabeçalhos específicos
    header("Access-Control-Allow-Headers: Content-Type");
    // Verificar o método HTTP da requisição

    include("conexao.php");
    global $conn;
    $method = $_SERVER['REQUEST_METHOD'];

    if (isset($_GET['id'])) {
    $id = $_GET['id'];
    } else {
        $id = null;
    }

    if ($method == 'POST'){
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset(
            $data['nome'],
            $data['sobrenome'],
            $data['email'],
            $data['telefone']
        )){
            $nome = $data['nome'];
            $sobrenome = $data['sobrenome'];
            $email = $data['email'];
            $telefone = $data['telefone'];

            // validação de usuario existente pode ser feita aqui, verificando se o email já existe no banco de dados
            
            $sql = "INSERT INTO banco_noite
            (nome, sobrenome, email, telefone)
            VALUES
            ('$nome', '$sobrenome', '$email', '$telefone')
            RETURNING id";

            $result = pg_query($conn, $sql);
            
            if ($result){
                $row = pg_fetch_assoc($result);
                $idCliente = $row['id'];
                http_response_code(201);
                echo json_encode([
                    'message' => "Cliente criado com sucesso.",
                    'id' => $idCliente
                ], JSON_UNESCAPED_UNICODE);
            }else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao criar cliente.'], JSON_UNESCAPED_UNICODE);
            }
            
        }else {
            http_response_code(400);
            echo json_encode(['error' => 'Todos os campos são obrigatórios.'], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Rota para obter todos os clientes GET /clientes
    if($method =='GET' && $id == null){
    $sql = "SELECT * FROM banco_noite";
    $result = pg_query($conn, $sql);

    $clientes = [];
    while($cliente = pg_fetch_assoc($result)){
        $clientes[] = $cliente;
    }
    http_response_code(200);
    echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
    exit();
}

  // Rota para buscar um cliente por ID GET /clientes/{id}
    
    if($method =='GET' && $id != null){
        $sql = "SELECT * FROM banco_noite WHERE id = $id";
        $result = pg_query($conn, $sql);

        if(pg_num_rows($result) == 0){
            http_response_code(404);
            echo json_encode([
                "error" => "Cliente não encontrado."
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $cliente = pg_fetch_assoc($result);
        http_response_code(200);
        echo json_encode($cliente, JSON_UNESCAPED_UNICODE);
        exit();
    }

    //Rota para deletar um cliente 
    
    if($method == 'DELETE'){
        if ($id == null){
            http_response_code(400);

            echo json_encode([
                "error" => "ID obrigatório."
            ], JSON_UNESCAPED_UNICODE);
            exit();

        }
        $verifica = pg_query($conn, "SELECT * FROM banco_noite WHERE id = $id");

        if (pg_num_rows($verifica) == 0){
            http_response_code(404);
            echo json_encode([
                "error" => "Cliente não encontrado."
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        $sql = "DELETE FROM banco_noite WHERE id = $id";
        $result = pg_query($conn, $sql);

        if($result){
            http_response_code(200);
            echo json_encode([
                "message" => "Cliente deletado com sucesso."
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao deletar cliente."
            ], JSON_UNESCAPED_UNICODE);
        }
        exit();

    }
 ?>