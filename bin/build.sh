#!/bin/bash

# Script para criar ZIP do plugin WordPress
# Ignora arquivos de desenvolvimento

# Obter o diretório do script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

PLUGIN_NAME="wp-associates"
BUILD_DIR="${PROJECT_DIR}/.build"
OUTPUT_FILE="${PROJECT_DIR}/${PLUGIN_NAME}.zip"

echo "🚀 Iniciando build do plugin ${PLUGIN_NAME}..."

# Criar diretório build se não existir
mkdir -p ${BUILD_DIR}

# Limpar build anterior
rm -rf ${BUILD_DIR}/${PLUGIN_NAME}
rm -f ${OUTPUT_FILE}

# Criar estrutura do plugin
mkdir -p ${BUILD_DIR}/${PLUGIN_NAME}

echo "📦 Copiando arquivos do plugin..."

# Copiar todo o conteúdo da pasta src
cp -r ${PROJECT_DIR}/src/* ${BUILD_DIR}/${PLUGIN_NAME}/

echo "🗜️  Criando arquivo ZIP..."

# Criar ZIP
cd ${BUILD_DIR}
zip -r ${OUTPUT_FILE} ${PLUGIN_NAME}
cd ${PROJECT_DIR}

# Limpar diretório build
rm -rf ${BUILD_DIR}

echo "✅ Plugin criado com sucesso: ${OUTPUT_FILE}"
echo "📦 Pronto para instalar no WordPress!"