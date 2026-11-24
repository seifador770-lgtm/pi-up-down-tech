# PI-UP-DOWN-TECH — versão corrigida

## Requisitos
- Docker Desktop iniciado
- Docker Compose (incluído nas versões atuais do Docker Desktop)

## Iniciar o projeto
Abra o terminal na pasta que contém `docker-compose.yml` e execute:

```bash
docker compose up -d --build
```

Acesse:
- Loja: http://localhost:8080
- Cadastro de peças: http://localhost:8080/admin.php
- phpMyAdmin: http://localhost:8081

No phpMyAdmin, use servidor `db`, usuário `root` e senha `root`.

## Banco de dados
O banco utilizado pela aplicação é `up-down-tech`. Em uma instalação nova, o Docker cria o banco e importa automaticamente `src/database/schema.sql`.

**Atenção:** scripts em `/docker-entrypoint-initdb.d` só são executados automaticamente quando o volume do MySQL é inicializado pela primeira vez. Se você já tinha um volume de banco criado, importe `src/database/schema.sql` pelo phpMyAdmin, selecionando o banco `up-down-tech`, ou execute os comandos SQL no banco existente. Não remova o volume sem fazer backup, pois isso apaga os dados.

## Observações importantes
- O carrinho é armazenado no navegador (`localStorage`).
- O botão “Finalizar Compra” ainda não registra uma venda nem envia um pedido ao servidor. Para isso, será necessário desenvolver a etapa de pedidos, transações e baixa de estoque no PHP/MySQL.
- As credenciais `root/root` são apenas para desenvolvimento local. Antes de publicar na internet, crie um usuário próprio para a aplicação, use senha forte e proteja a área administrativa com login.
