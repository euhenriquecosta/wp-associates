#!/bin/bash

# Função para parar containers ao sair
cleanup() {
    echo ""
    echo "> Parando WordPress..."
    docker compose down 2>/dev/null
    echo "> Wordpress parado (dados preservados)"
    exit 0
}

# Capturar Ctrl+C
trap cleanup SIGINT SIGTERM

echo "> Iniciando WordPress..."

# Subir containers em background
docker compose up -d > /dev/null 2>&1

# Aguardar MySQL ficar pronto
until docker exec mysql mysqladmin ping -h"localhost" --silent 2>/dev/null; do
    sleep 2
done

# Aguardar Apache iniciar
sleep 3

# Instalar WP-CLI (sempre necessário pois não persiste no volume)
docker exec wordpress bash -c "
    if ! command -v wp &> /dev/null; then
        curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
        chmod +x wp-cli.phar
        mv wp-cli.phar /usr/local/bin/wp
    fi
" 2>/dev/null

# Verifica se o WordPress já foi instalado
if docker exec wordpress wp core is-installed --allow-root 2>/dev/null; then
    echo "> WordPress iniciado"
    echo ""
    echo "URL:     http://localhost:8080"
    echo "Admin:   http://localhost:8080/wp-admin"
    echo ""
    echo "Usuário: admin"
    echo "Senha:   admin"
    echo ""
else
    echo "> Primeira execução - configurando automaticamente..."
    echo ""
    bin/setup.sh
fi

echo "Pressione Ctrl+C para parar"
echo ""

# Manter script rodando até Ctrl+C
tail -f /dev/null