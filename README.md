# WP Associates Plugin

Plugin WordPress para registrar associados com nome, localização, imagem e filtros interativos com mapa.

## Estrutura do Plugin

Este plugin foi refatorado seguindo as melhores práticas de desenvolvimento WordPress com Programação Orientada a Objetos (POO).

### Arquivos Principais

- `src/index.php` - Arquivo principal do plugin
- `src/Plugin.php` - Classe principal do plugin
- `src/Autoload.php` - Autoloader para carregamento automático das classes

### Estrutura de Classes

#### Core Classes
- `WP_Associates_Plugin` - Classe principal que gerencia todo o plugin
- `WP_Associates_Autoloader` - Autoloader para carregamento automático

#### Functional Classes
- `WP_Associates_PostType` - Gerencia o custom post type 'associate'
- `WP_Associates_Taxonomy` - Gerencia a taxonomia de categorias
- `WP_Associates_Metabox` - Gerencia os metaboxes do admin
- `WP_Associates_Shortcode` - Gerencia o shortcode [wp-associates]
- `WP_Associates_Assets` - Gerencia scripts e estilos
- `WP_Associates_Municipalities` - Gerencia a lista de municípios da Bahia

#### Context Classes
- `WP_Associates_Admin` - Funcionalidades específicas do admin
- `WP_Associates_Public` - Funcionalidades específicas do frontend

## Funcionalidades

### Custom Post Type
- Post type 'associate' para registrar associados
- Suporte a thumbnail e título
- Interface amigável no admin

### Taxonomia
- Taxonomia 'associate_category' para categorizar associados
- Termos padrão pré-definidos
- Interface de gerenciamento no admin

### Metaboxes
- Metabox para informações do associado (descrição e município)
- Metabox para fotos adicionais
- Upload múltiplo de imagens

### Shortcode
- Shortcode `[wp-associates]` para exibir o mapa interativo
- Filtros por nome, município e categoria
- Mapa com marcadores dos associados
- Modal com informações detalhadas

### Assets
- Carregamento automático de scripts e estilos
- Versionamento baseado em filemtime
- Separação entre assets do admin e frontend

## Uso

### Instalação
1. Faça upload do plugin para o diretório `/wp-content/plugins/`
2. Ative o plugin no admin do WordPress

### Uso do Shortcode
Adicione o shortcode `[wp-associates]` em qualquer página ou post para exibir o mapa interativo dos associados.

### Gerenciamento
- Acesse "Associados" no menu admin para gerenciar associados
- Use "Categorias" para gerenciar as categorias de associados

## Desenvolvimento

### Padrões Seguidos
- Programação Orientada a Objetos (POO)
- Padrão Singleton para classes principais
- Autoloader para carregamento automático
- Hooks do WordPress apropriados
- Sanitização e validação de dados
- Internacionalização (i18n)

### Estrutura de Arquivos
```
src/
├── index.php              # Arquivo principal
├── Plugin.php             # Classe principal
├── Autoload.php           # Autoloader
├── includes/              # Classes funcionais
│   ├── PostType.php
│   ├── Taxonomy.php
│   ├── Metabox.php
│   ├── Shortcode.php
│   ├── Assets.php
│   ├── Admin.php
│   ├── Public.php
│   └── Municipalities.php
├── assets/                # Recursos estáticos
│   └── avatar.png
├── script.js              # JavaScript do admin
└── styles.css             # Estilos do plugin
```

## Version
2.7

## Author
Henrique Costa