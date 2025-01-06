<?php
include '../php-action/conexao.php';

session_start();
if (!isset($_SESSION["logado"]) || $_SESSION["logado"] !== true) {
    header("Location: ../index.php");
    exit;
}

// Obtenha o nome do usuário da sessão
$username = $_SESSION["nome_usuario"];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fechar Comanda</title>
    <link rel="shortcut icon" href="../favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/comanda.css">
</head>

<body>
    <header>
        <img src="../img/logo.png" id="logo" onclick="javascript:location.href='direcionamento.php'">
    </header>

    <main>
        <div class="grid-container">
            <?php
            // Conectar ao banco de dados
            include "../php-action/conexao.php";

            // Consultar o banco de dados para obter as informações da comanda
            $sql = "SELECT mesa_pedido, nome_pedido, valor_pedido, quantidade_pedido, total FROM pedidos";
            $result = $conn->query($sql);

            // Inicializar a variável para armazenar o número da mesa atual
            $mesa_atual = null;
            $total_mesa = 0; // Inicializar o total da mesa

            // Verificar se há resultados
            if ($result->num_rows > 0) {
                // Loop através dos resultados
                while ($row = $result->fetch_assoc()) {
                    // Verificar se a mesa é diferente da mesa anterior
                    if ($row['mesa_pedido'] != $mesa_atual) {
                        // Se não for a primeira mesa, fechar a div do card anterior
                        if ($mesa_atual !== null) {
                            echo '</div>'; // Fechar a div da info-comanda
                            echo '<div class="total">';
                            echo '<span>Total: R$' . number_format($total_mesa, 2, ',', '.') . '</span>';
                            echo '</div>';
                            echo '<div class="btn-area">';
                            echo '<button class="btn-encerrar">Encerrar</button>';
                            echo '</div>';
                            echo '</div>'; // Fechar a div do card anterior
                            echo '</div>';
                        }

                        // Abrir a div do novo card
                        echo '<div class="card">';
                        echo '<h1>Mesa: ' . $row['mesa_pedido'] . '</h1>';
                        echo '<div class="info-comanda">';
                        echo '<div class="pedidos">';
                        $total_mesa = 0; // Resetar o total da mesa para a nova mesa
                    }

                    // Explodir a string de produtos em um array
                    $produtos = explode(",", $row['nome_pedido']);
                    $quantidades = explode(",", $row['quantidade_pedido']);

                    // Verificar se os arrays de produtos e quantidades têm o mesmo comprimento
                    if (count($produtos) !== count($quantidades)) {
                        // Tratar erro de dados inconsistentes
                        echo "Erro: Dados inconsistentes na comanda.";
                        continue; // Pular para a próxima iteração do loop
                    }

                    // Loop através dos produtos
                    for ($i = 0; $i < count($produtos); $i++) {
                        echo '<ul>';
                        echo '<li>' . $produtos[$i] . ' (Quantidade: ' . $quantidades[$i] . ')</li>';
                        echo '<li class="valor-pedido">R$' . number_format($row['valor_pedido'] * $quantidades[$i], 2, ',', '.') . '</li>'; // Valor total do produto
                        echo '</ul>';
                    }

                    // Adicionar o valor total do pedido ao total da mesa
                    $total_mesa += $row['valor_pedido'];

                    // Atualizar a variável da mesa atual
                    $mesa_atual = $row['mesa_pedido'];
                }

                // Fechar a última div do card, se houver algum resultado
                if ($mesa_atual !== null) {
                    echo '</div>'; // Fechar a última div da info-comanda
                    echo '<div class="total">';
                    echo '<span>Total: R$' . number_format($total_mesa, 2, ',', '.') . '</span>';
                    echo '</div>';
                    echo '<div class="btn-area">';
                    echo '<button class="btn-encerrar">Encerrar</button>';
                    echo '</div>';
                    echo '</div>'; // Fechar a div do último card
                }
            } else {
                echo "Nenhum resultado encontrado.";
            }

            // Fechar a conexão
            $conn->close();
            ?>
        </div>
    </main>


</body>

</html>