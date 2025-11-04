#!/bin/bash

# Script para resetar senhas de usuários do e-Cidade
# Uso: ./reset-password.sh <login> <nova_senha>

if [ $# -lt 2 ]; then
    echo "Uso: $0 <login> <nova_senha>"
    echo ""
    echo "Exemplos:"
    echo "  $0 dbseller dbseller"
    echo "  $0 dbpref minhasenha"
    echo ""
    echo "Para resetar todos os usuários principais para 'dbseller':"
    echo "  $0 all dbseller"
    exit 1
fi

LOGIN=$1
SENHA=$2

# Gera o hash SHA1(MD5()) da senha
# O login.php converte a senha para MD5 no JavaScript,
# depois o abrir.php aplica SHA1 sobre o MD5
MD5_HASH=$(echo -n "$SENHA" | md5sum | awk '{print $1}')
SENHA_HASH=$(echo -n "$MD5_HASH" | sha1sum | awk '{print $1}')

if [ "$LOGIN" == "all" ]; then
    echo "Resetando senha de todos os usuários principais para: $SENHA"
    docker exec -i e-cidade-bd-1 psql -U ecidade ecidade -c \
        "UPDATE db_usuarios SET senha = '$SENHA_HASH' WHERE login IN ('dbseller', 'dbpref', 'escritorio', 'funcionario', 'contribuinte', 'fornecedor');"
    echo "✓ Senhas resetadas!"
else
    echo "Resetando senha do usuário '$LOGIN' para: $SENHA"
    docker exec -i e-cidade-bd-1 psql -U ecidade ecidade -c \
        "UPDATE db_usuarios SET senha = '$SENHA_HASH' WHERE login = '$LOGIN';"
    echo "✓ Senha resetada!"
fi

echo ""
echo "Verificando usuário(s):"
if [ "$LOGIN" == "all" ]; then
    docker exec -i e-cidade-bd-1 psql -U ecidade ecidade -c \
        "SELECT id_usuario, nome, login FROM db_usuarios WHERE login IN ('dbseller', 'dbpref', 'escritorio', 'funcionario', 'contribuinte', 'fornecedor') ORDER BY id_usuario;"
else
    docker exec -i e-cidade-bd-1 psql -U ecidade ecidade -c \
        "SELECT id_usuario, nome, login FROM db_usuarios WHERE login = '$LOGIN';"
fi
