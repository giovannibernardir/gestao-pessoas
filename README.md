# Gestão de Pessoas (dashboard em PHP)

1. Instale o XAMPP (Apache + PHP + MySQL) e inicie Apache e MySQL.
2. Copie a pasta `gestao-pessoas` para `C:\xampp\htdocs\`.
3. Em `C:\xampp\apache\conf\httpd.conf`, confirme que `mod_rewrite` está ativo
   (linha `LoadModule rewrite_module ...` sem `#`) e que `AllowOverride All`
   está definido para a pasta `htdocs`. Reinicie o Apache.
4. No phpMyAdmin (http://localhost/phpmyadmin), aba Importar, envie o arquivo `database.sql`.
5. Se sua senha do MySQL não for vazia, ajuste `config/conexao.php`.
6. Acesse http://localhost/gestao-pessoas/

URLs: /dashboard, /funcionarios, /funcionarios/novo, /funcionarios/editar/ID,
/departamentos, /ferias
