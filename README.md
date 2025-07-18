# ERP - Montink

Um pequeno sistema de ERP desenvolvido em **CodeIgniter 3** para o teste da Montink. O projeto inclui controle de produtos, estoque, carrinho de compras, cupons de desconto e um webhook para atualização de pedidos.

## ✨ Funcionalidades

* **📦 Produtos:** Cadastro e edição completo de produtos, com suporte a variações e controle de estoque individual por variação.
* **🛒 Carrinho:** Gerenciamento de carrinho de compras em sessão, com cálculo de frete dinâmico baseado no subtotal.
* **🎟️ Cupons:** Sistema de gerenciamento de cupons com regras de valor mínimo e data de validade.
* **📞 Consulta de CEP:** Integração com a API ViaCEP para buscar endereços.
* **훅 Webhook:** Endpoint para receber atualizações de status de pedidos.
* **📧 E-mail:** Envio de e-mail de confirmação ao finalizar um pedido.

## ⚙️ Tecnologias Utilizadas

* **Backend:** PHP 7.4.33
* **Framework:** CodeIgniter 3.1.13
* **Frontend:** Bootstrap 4
* **Banco de Dados:** MySQL

## 🔧 Como Rodar o Projeto

Siga os passos abaixo para configurar e rodar o projeto no seu ambiente local.

#### 1. Clone o repositório

git clone [https://github.com/JoseDanielF/erp-montink](https://github.com/JoseDanielF/erp-montink)

#### 2. Crie o Banco de Dados

Acesse o `phpMyAdmin` e crie um novo banco de dados com o nome de sua preferência.

Depois de criar o banco, execute o script SQL que está no final deste README para criar todas as tabelas e relacionamentos necessários.

#### 3. Configure o CodeIgniter
 
Será necessário ajustar o arquivo database.php com as credenciais do seu banco de dados.

* **`database.php`**: Configure suas credenciais de acesso ao banco de dados.
    ```php
    'hostname' => 'localhost',
    'username' => 'root', // usuário
    'password' => '',     // senha
    'database' => '',     // nome do banco de dados
    ```

#### 4. Credenciais para Envio de E-mail

Para que o sistema possa enviar e-mails de confirmação de compra automaticamente, é fundamental configurar as credenciais de um e-mail SMTP. O projeto está preparado para usar um e-mail do **Gmail**, que oferece um serviço gratuito.

**Atenção:** Você não deve usar sua senha de login comum. Por segurança, é obrigatório gerar e utilizar uma **"Senha de Aplicativo"** na sua Conta do Google.

##### Passo 1: Gerar a Senha de Aplicativo no Gmail

1.  **Ative a Verificação em Duas Etapas** na sua Conta do Google. Este é um pré-requisito obrigatório.
2.  Acesse a página de **Senhas de Aplicativo**: [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
3.  Em "Selecionar o app", escolha **E-mail**.
4.  Em "Selecionar o dispositivo", escolha **Computador Windows** (ou "Outro" e dê um nome como "ERP Montink").
5.  Clique em **Gerar**.
6.  O Google exibirá uma senha de 16 letras sem espaços. **Copie essa senha** para usá-la no arquivo de configuração.

##### Passo 2: Configurar as Credenciais no Projeto

1.  Navegue até a pasta de *controllers* e abra o arquivo `Pedidos.php`:
    * `application/controllers/Pedidos.php`

2.  Dentro do arquivo, procure pela função `enviarEmailConfirmacao()`. No início desta função, você encontrará as variáveis de configuração que precisam ser preenchidas:

    ```php
    $config['smtp_user'] = 'seu_email@gmail.com';
    $config['smtp_pass'] = 'sua_senha_de_aplicativo';
    ```

3.  Altere os valores:
    * **`'smtp_user'`**: Substitua `'seu_email@gmail.com'` pelo seu endereço de e-mail completo do Gmail.
    * **`'smtp_pass'`**: Substitua `'sua_senha_de_aplicativo'` pela senha de 16 letras que você gerou no passo anterior.

4.  Salve o arquivo `Pedidos.php`. Após essa alteração, o sistema estará pronto para enviar os e-mails de confirmação de compra.

#### 5. Rode o Projeto!

Com o Apache e o MySQL rodando no seu XAMPP, acesse a rota http://localhost/erp-montink/ no seu navegador.

## 🚀 Webhook

* **Webhook (para testes com Postman/Insomnia):**
    * **URL:** `http://localhost/erp-montink/webhook/pedidoStatus`
    * **Método:** `POST`
    * **Body (JSON):** `{ "id": 1, "status": "Cancelado" }` ou `{ "id": 1, "status": "Entregue" }`

<br>

---

## 📜 Script SQL para Criação do Banco

<details>
<summary><strong>Clique para expandir e ver o script SQL</strong></summary>

```sql
CREATE DATABASE IF NOT EXISTS erp_montink;
USE erp_montink;

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `preco_base` decimal(10,2) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `produto_variacoes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `produto_id` INT(11) NOT NULL,
    `nome` VARCHAR(100) NOT NULL, 
    `preco_adicional` DECIMAL(10,2) DEFAULT 0.00, 
    PRIMARY KEY (`id`),
    FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `variacao_id_unique` (`variacao_id`),
  FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacoes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL UNIQUE,
  `tipo_desconto` enum('percentual','fixo') NOT NULL,
  `valor_desconto` decimal(10,2) NOT NULL,
  `valor_minimo_pedido` decimal(10,2) DEFAULT 0.00,
  `data_validade` date NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_nome` varchar(255) DEFAULT NULL,
  `cliente_email` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `valor_desconto` decimal(10,2) DEFAULT 0.00,
  `cupom_id` INT(11) DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`cupom_id`) REFERENCES `cupons`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedido_itens` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `pedido_id` INT(11) NOT NULL,
    `produto_nome` VARCHAR(255) NOT NULL,
    `variacao_nome` VARCHAR(100) NOT NULL,
    `quantidade` INT(11) NOT NULL,
    `preco_unitario` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pedido_itens`
ADD COLUMN `produto_variacao_id` INT(11) NULL AFTER `pedido_id`;
