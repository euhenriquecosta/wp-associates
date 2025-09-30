#!/bin/bash

# Função para parar containers ao sair
cleanup() {
    echo ""
    echo "🛑 Parando WordPress..."
    docker-compose down -v 2>/dev/null
    echo "✅ Wordpress parado!"
    exit 0
}

# Capturar Ctrl+C
trap cleanup SIGINT SIGTERM

echo "🚀 Iniciando WordPress..."

# Subir containers em background
docker-compose up -d 2>&1 | grep -v "Pulling\|Downloaded\|Waiting"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📝 URL:     http://localhost:8080"
echo "📝 Admin:   http://localhost:8080/wp-admin"
echo ""
echo "⚠️  IMPORTANTE: Na primeira execução, configure:"
echo "👤 Usuário: admin"
echo "🔑 Senha:   admin"
echo "📧 Email:   admin@email.com"
echo ""
echo "💡 Depois, ative manualmente o plugin 'WP Associates'"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "⌨️  Pressione Ctrl+C para parar o WordPress"
echo ""

# Manter script rodando até Ctrl+C
tail -f /dev/null