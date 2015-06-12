<?php

require '../Slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

$app->get('/', function () {
  echo "SlimProdutos ";
});

$app->get('/clientes','getClientes');
$app->post('/clientes','addCliente');
$app->get('/cliente/:id','getCliente');
$app->put('/cliente/:id','saveCliente');
$app->delete('/cliente/:id','deleteCliente');

$app->run();

function getConn()
{
 try {
    return new PDO("pgsql:host=localhost dbname=controle user=postgres password=msbru00");
 }  catch (PDOException $e) {
   print $e->getMessage();
 }
}

function getClientes()
{
  $stmt = getConn()->query("SELECT * FROM pessoa ORDER BY nome");
  $clientes = $stmt->fetchAll(PDO::FETCH_OBJ);
  //echo "{\"clientes\":".json_encode($clientes)."}";
  echo json_encode($clientes);
}

function addCliente()
{
  $request = \Slim\Slim::getInstance()->request();
  $cliente = json_decode($request->getBody());
  $sql = "INSERT INTO pessoa (nome, sexo, telefone, cpf_cnpj, inscricao_estadual, email, endereco, cidade, uf, cep) values (:nome, :sexo, :telefone, :cpf_cnpj, :inscricao_estadual, :email, :endereco, :cidade, :uf, :cep) ";
  $conn = getConn();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("nome",$cliente->nome);
  $stmt->bindParam("sexo", $cliente->sexo);
  $stmt->bindParam("telefone",$cliente->telefone);
  $stmt->bindParam("email",$cliente->email);
  $stmt->bindParam("cpf_cnpj", $cliente->cpf_cnpj);
  $stmt->bindParam("inscricao_estadual", $cliente->inscricao_estadual);
  $stmt->bindParam("endereco", $cliente->endereco);
  $stmt->bindParam("cidade", $cliente->cidade);
  $stmt->bindParam("uf", $cliente->uf);
  $stmt->bindParam("cep", $cliente->cep);

  try {
    $stmt->execute();
    $cliente->idpessoa = $conn->lastInsertId();
    echo json_encode($cliente);
  } catch (PDOException $e) {
     throw new Exception("Erro incluindo cliente: " + $e.getMessage());
  }

}

function getCliente($id)
{
  $conn = getConn();
  $sql = "SELECT * FROM pessoa WHERE idpessoa=:id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("id",$id);
  $stmt->execute();
  $cliente = $stmt->fetchObject();

  //categoria
  //$sql = "SELECT * FROM categorias WHERE id=:id";
  //$stmt = $conn->prepare($sql);
  //$stmt->bindParam("id",$produto->idCategoria);
  //$stmt->execute();
  //$produto->categoria =  $stmt->fetchObject();

  echo json_encode($cliente);
}

function saveCliente($id)
{
  $request = \Slim\Slim::getInstance()->request();
  $cliente = json_decode($request->getBody());
  $sql = "UPDATE pessoa SET nome = :nome, telefone = :telefone, cpf_cnpj = :cpf_cnpj, inscricao_estadual = :inscricao_estadual,
        email = :email, endereco = :endereco, cidade = :cidade, uf = :uf, cep = :cep, sexo = :sexo WHERE idpessoa=:id";
  $conn = getConn();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("nome",$cliente->nome);
  $stmt->bindParam("telefone",$cliente->telefone);
  $stmt->bindParam("cpf_cnpj", $cliente->cpf_cnpj);
  $stmt->bindParam("inscricao_estadual", $cliente->inscricao_estadual);
  $stmt->bindParam("endereco", $cliente->endereco);
  $stmt->bindParam("cidade", $cliente->cidade);
  $stmt->bindParam("uf", $cliente->uf);
  $stmt->bindParam("cep", $cliente->cep);
  $stmt->bindParam("email",$cliente->email);
  $stmt->bindParam("sexo", $cliente->sexo);
  $stmt->bindParam("id",$cliente->idpessoa);

  try {
    $stmt->execute();
    echo json_encode($cliente);
  } catch (PDOException $e) {
    throw new Exception("Erro atualizando o cliente: " + $e.getMessage());
  }
}

function deleteCliente($id)
{
  $sql = "DELETE FROM pessoa WHERE idpessoa=:id";
  $conn = getConn();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("id",$id);
  try {
  	$stmt->execute();
  } catch (PDOException $e) {
    throw new Exception("Erro ao excluir pessoa: " + $e.getMessage());
  }
  //echo "{'message':'Cliente apagado'}";x
}
