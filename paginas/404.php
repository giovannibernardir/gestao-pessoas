<?php
$titulo = 'Página não encontrada';
require __DIR__ . '/../templates/header.php';
?>
<h1>Página não encontrada</h1>
<p>O endereço que você acessou não existe. <a href="<?= url('dashboard') ?>">Voltar ao dashboard</a></p>
<?php require __DIR__ . '/../templates/footer.php'; ?>
