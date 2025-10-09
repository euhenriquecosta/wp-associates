# Plugin WP Associates - WordPress

Plugin para registrar associados com mapa interativo usando Leaflet.

## 🚀 Desenvolvimento Local (Docker)

### Iniciar o ambiente:
```bash
composer dev
```

### Primeira configuração (apenas 1x):
1. Acesse: http://localhost:8080
2. Configure o WordPress:
   - **Título:** WP Associates
   - **Usuário:** admin
   - **Senha:** admin
   - **Email:** admin@email.com
3. Vá em Plugins e ative **"WP Associates"**

### Acessos:
- **URL:** http://localhost:8080
- **Admin:** http://localhost:8080/wp-admin
- **Usuário:** admin
- **Senha:** admin

### Outros comandos:
```bash
composer start   # Iniciar containers
composer stop    # Parar containers  
composer restart # Reiniciar containers
composer logs    # Ver logs do WordPress
composer build   # Gerar ZIP do plugin
```

### Edição em tempo real:
O plugin está montado como volume, então você pode editar os arquivos diretamente:
- `index.php` - Plugin principal
- `styles.css` - Estilos
- `script.js` - JavaScript do autocomplete

As mudanças são refletidas imediatamente no WordPress (apenas recarregue a página).

### Parar o ambiente:
```bash
docker compose down
```

### Limpar dados (cuidado!):
```bash
docker compose down -v
```

## 📦 Gerar ZIP para Produção

Para criar um arquivo ZIP do plugin (pronto para instalar em qualquer WordPress):

```bash
composer build
```

Isso vai gerar o arquivo `associados-interativo.zip` contendo apenas os arquivos necessários:
- ✅ `index.php`
- ✅ `styles.css`
- ✅ `script.js`
- ✅ `placeholder.png` (se existir)

**Arquivos ignorados no ZIP:**
- ❌ `.git/`
- ❌ `vendor/`
- ❌ `docker compose.yml`
- ❌ `composer.json`
- ❌ `.vscode/`
- ❌ `.gitignore`
- ❌ `README.md`
- ❌ Scripts de build

## 📝 Como usar o plugin

1. Instale e ative o plugin
2. Vá em **Associados** no menu lateral
3. Adicione novos associados com:
   - Nome
   - Função
   - Localização (com autocomplete)
   - Estado
   - Imagem destacada
   - Categorias
4. Crie uma página e adicione o shortcode: `[wp-associates]`
5. Visualize a página para ver o mapa interativo!
