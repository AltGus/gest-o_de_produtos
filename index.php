<?php
session_start();

class Produto {
    private $nome;
    private $preco;
    private $quantidade;

    public function __construct($nome, $preco, $quantidade) {
        $this->nome = $nome;
        $this->preco = $preco;
        $this->quantidade = $quantidade;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function aplicarDesconto($percentual) {
        $this->preco -= $this->preco * ($percentual / 100);
    }

    public function atualizarQuantidade($novaQuantidade) {
        $this->quantidade = $novaQuantidade;
    }

    public function exibirInformacoes() {
        return "Produto: $this->nome, Preço: R$ $this->preco, Quantidade: $this->quantidade";
    }
}

class Estoque {
    private $produtos = [];

    public function adicionarProduto(Produto $produto) {
        $this->produtos[] = $produto;
    }

    public function removerProduto($nomeProduto) {
        foreach ($this->produtos as $index => $produto) {
            if ($produto->getNome() === $nomeProduto) {
                unset($this->produtos[$index]);
                $this->produtos = array_values($this->produtos);
                return true;
            }
        }
        return false;
    }

    public function listarProdutos() {
        if (empty($this->produtos)) {
            echo "Nenhum produto no estoque.<br>";
            return;
        }
        
        foreach ($this->produtos as $produto) {
            echo $produto->exibirInformacoes() . " ";
            echo "<form method='POST' style='display:inline;'>
                    <input type='hidden' name='remover' value='" . $produto->getNome() . "'>
                    <input type='submit' value='Remover'>
                  </form><br>";
        }
    }

    public function calcularValorTotal() {
        $total = 0;
        foreach ($this->produtos as $produto) {
            $total += $produto->getPreco() * $produto->getQuantidade();
        }
        return $total;
    }
}

if (!isset($_SESSION['estoque'])) {
    $_SESSION['estoque'] = new Estoque();
}
$estoque = $_SESSION['estoque'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nome']) && isset($_POST['preco']) && isset($_POST['quantidade'])) {
        $produto = new Produto($_POST['nome'], $_POST['preco'], $_POST['quantidade']);
        $estoque->adicionarProduto($produto);
    }
    if (isset($_POST['remover'])) {
        $estoque->removerProduto($_POST['remover']);
    }
    $_SESSION['estoque'] = $estoque;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Estoque</title>
</head>
<body>
    <h1>Gerenciamento de Estoque</h1>
    <form method="POST">
        <label for="nome">Nome do Produto:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="preco">Preço:</label>
        <input type="number" id="preco" name="preco" step="0.01" required><br><br>

        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" required><br><br>

        <input type="submit" value="Adicionar Produto">
    </form>
    
    <h2>Produtos no Estoque:</h2>
    <?php $estoque->listarProdutos(); ?>
    
    <h3>Valor Total do Estoque: R$ <?php echo $estoque->calcularValorTotal(); ?></h3>
</body>
</html>
