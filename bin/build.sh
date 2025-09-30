#!/bin/bash

# Script para criar ZIP do plugin WordPress
# Ignora arquivos de desenvolvimento

# Obter o diretório do script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

PLUGIN_NAME="associados-interativo"
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

# Copiar apenas os arquivos necessários
cp ${PROJECT_DIR}/index.php ${BUILD_DIR}/${PLUGIN_NAME}/
cp ${PROJECT_DIR}/styles.css ${BUILD_DIR}/${PLUGIN_NAME}/
cp ${PROJECT_DIR}/script.js ${BUILD_DIR}/${PLUGIN_NAME}/

# Se tiver imagem placeholder, copiar também
if [ -f "${PROJECT_DIR}/placeholder.png" ]; then
    cp ${PROJECT_DIR}/placeholder.png ${BUILD_DIR}/${PLUGIN_NAME}/
fi

echo "🗜️  Criando arquivo ZIP..."

# Criar ZIP
cd ${BUILD_DIR}
zip -r ${OUTPUT_FILE} ${PLUGIN_NAME}
cd ${PROJECT_DIR}

# Limpar diretório build
rm -rf ${BUILD_DIR}

echo "✅ Plugin criado com sucesso: ${OUTPUT_FILE}"
echo "📦 Pronto para instalar no WordPress!"