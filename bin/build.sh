#!/bin/bash

# Script para preparar o plugin para distribuição

echo "🚀 Preparando plugin para distribuição..."

# Criar pasta dist
echo "📁 Criando pasta dist..."
rm -rf dist
mkdir -p dist

# Instalar dependências de produção
echo "📦 Instalando dependências..."
composer install --no-dev --optimize-autoloader

# Copiar todo o conteúdo do src para dist
echo "📋 Copiando arquivos do plugin..."
cp -r src/* dist/

# Copiar vendor para dist
echo "📦 Copiando dependências..."
cp -r vendor dist/vendor

# Criar ZIP do plugin
echo "🗜️ Criando ZIP do plugin..."
cd dist
zip -r ../wp-associates-v2.7.zip . -x "*.DS_Store" "*.git*"

cd ..

echo "✅ Plugin pronto para distribuição:"
echo "   📁 Pasta: dist/"
echo "   📦 ZIP: wp-associates-v2.7.zip"