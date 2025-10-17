#!/bin/bash

# Script para preparar o plugin para distribuição

echo "🚀 Preparando plugin para distribuição..."

# Extrair versão do arquivo principal do plugin
VERSION=$(grep -o "Version: *[0-9.]*" src/wp-associates.php | grep -o "[0-9.]*")
if [ -z "$VERSION" ]; then
    echo "❌ Erro: Não foi possível extrair a versão do plugin"
    exit 1
fi

echo "📋 Versão detectada: $VERSION"

# Criar pasta temporária para dist
echo "📁 Criando pasta de distribuição..."
rm -rf dist
mkdir -p dist

# Instalar dependências de produção
echo "📦 Movendo vendor para last-vendor..."
mv vendor last-vendor

echo "📦 Instalando dependências..."
composer install --no-dev --optimize-autoloader

# Copiar todo o conteúdo do src para pasta dist
echo "📋 Copiando arquivos do plugin..."
cp -r src/* dist/

# Copiar vendor para pasta temporária
echo "📦 Copiando dependências..."
cp -r vendor dist/vendor

echo "📦 Movendo vendor de volta para vendor..."
rm -rf vendor
mv last-vendor vendor

# Criar pasta build se não existir
mkdir -p build

# Criar ZIP do plugin na pasta build
echo "🗜️ Criando ZIP do plugin..."
cd dist
zip -r ../build/wp-associates-v$VERSION.zip . -x "*.DS_Store" "*.git*"

cd ..

echo "✅ Plugin pronto para distribuição:"
echo "   📦 ZIP: build/wp-associates-v$VERSION.zip"