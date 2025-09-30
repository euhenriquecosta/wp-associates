#!/bin/bash

# Script para criar ZIP do plugin WordPress
# Ignora arquivos de desenvolvimento

PLUGIN_NAME="associados-interativo"
BUILD_DIR="build"
OUTPUT_FILE="${PLUGIN_NAME}.zip"

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
cp index.php ${BUILD_DIR}/${PLUGIN_NAME}/
cp styles.css ${BUILD_DIR}/${PLUGIN_NAME}/
cp script.js ${BUILD_DIR}/${PLUGIN_NAME}/

# Se tiver imagem placeholder, copiar também
if [ -f "placeholder.png" ]; then
    cp placeholder.png ${BUILD_DIR}/${PLUGIN_NAME}/
fi

echo "🗜️  Criando arquivo ZIP..."

# Criar ZIP
cd ${BUILD_DIR}
zip -r ../${OUTPUT_FILE} ${PLUGIN_NAME}
cd ..

# Limpar diretório build
rm -rf ${BUILD_DIR}

echo "✅ Plugin criado com sucesso: ${OUTPUT_FILE}"
echo "📦 Pronto para instalar no WordPress!"
