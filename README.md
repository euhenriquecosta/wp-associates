# Plugin Associados Interativo - WordPress Docker

## Como executar

1. **Iniciar o ambiente:**
```bash
docker-compose up -d
```

2. **Acessar o WordPress:**
- URL: http://localhost:8080
- Admin: http://localhost:8080/wp-admin
- Usuário: admin
- Senha: admin123

3. **Ativar o plugin:**
- Vá em Plugins no admin do WordPress
- Ative o plugin "Associados Interativo"

## Edição em tempo real

O plugin está montado como volume, então você pode editar os arquivos diretamente:
- `index.php` - Plugin principal
- `associados.css` - Estilos
- `autocomplete.js` - JavaScript do autocomplete

As mudanças são refletidas imediatamente no WordPress.

## Parar o ambiente

```bash
docker-compose down
```

## Limpar dados (cuidado!)

```bash
docker-compose down -v
```
