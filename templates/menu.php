<?php
$itens = ['dashboard' => 'Dashboard', 'funcionarios' => 'Funcionários', 'departamentos' => 'Departamentos', 'ferias' => 'Férias'];
?>
<nav class="menu">
  <a class="marca" href="<?= url('dashboard') ?>">Gestão de Pessoas</a>
  <?php foreach ($itens as $rota => $nome): ?>
    <a href="<?= url($rota) ?>" class="<?= $pagina === $rota ? 'ativo' : '' ?>"><?= $nome ?></a>
  <?php endforeach; ?>
</nav>
