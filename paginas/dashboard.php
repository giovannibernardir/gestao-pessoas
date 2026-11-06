<?php
$titulo = 'Dashboard';

$ativos    = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE status = 'ativo'")->fetchColumn();
$afastados = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE status = 'afastado'")->fetchColumn();
$novos     = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE data_admissao >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)")->fetchColumn();
$pendentes = $pdo->query("SELECT COUNT(*) FROM ferias WHERE situacao = 'pendente'")->fetchColumn();

$porDepartamento = $pdo->query(
    'SELECT d.nome, COUNT(f.id) AS total
     FROM departamentos d
     LEFT JOIN funcionarios f ON f.departamento_id = d.id AND f.status = "ativo"
     GROUP BY d.id, d.nome ORDER BY total DESC, d.nome'
)->fetchAll();
$maior = max(array_merge([1], array_column($porDepartamento, 'total')));

$recentes = $pdo->query(
    'SELECT f.nome, f.cargo, f.data_admissao, d.nome AS departamento
     FROM funcionarios f JOIN departamentos d ON d.id = f.departamento_id
     ORDER BY f.data_admissao DESC LIMIT 5'
)->fetchAll();

$ferias = $pdo->query(
    "SELECT fe.id, fe.data_inicio, fe.data_fim, f.nome
     FROM ferias fe JOIN funcionarios f ON f.id = fe.funcionario_id
     WHERE fe.situacao = 'pendente' ORDER BY fe.data_inicio LIMIT 5"
)->fetchAll();

require __DIR__ . '/../templates/header.php';
?>
<h1>Visão geral</h1>

<section class="indicadores">
  <div><strong><?= (int)$ativos ?></strong><span>funcionários ativos</span></div>
  <div><strong><?= (int)$afastados ?></strong><span>afastados</span></div>
  <div><strong><?= (int)$novos ?></strong><span>admitidos nos últimos 90 dias</span></div>
  <div class="<?= $pendentes ? 'alerta' : '' ?>"><strong><?= (int)$pendentes ?></strong><span>férias aguardando decisão</span></div>
</section>

<div class="colunas">
  <section>
    <h2>Funcionários ativos por departamento</h2>
    <?php foreach ($porDepartamento as $d): ?>
      <div class="barra">
        <span><?= e($d['nome']) ?></span>
        <div class="trilho"><i style="width: <?= round($d['total'] / $maior * 100) ?>%"></i></div>
        <b><?= (int)$d['total'] ?></b>
      </div>
    <?php endforeach; ?>
  </section>

  <section>
    <h2>Férias para decidir</h2>
    <?php if (!$ferias): ?>
      <p class="vazio">Nenhuma solicitação pendente.</p>
    <?php else: ?>
      <ul class="lista">
        <?php foreach ($ferias as $fe): ?>
          <li><?= e($fe['nome']) ?> <small><?= dataBr($fe['data_inicio']) ?> a <?= dataBr($fe['data_fim']) ?></small></li>
        <?php endforeach; ?>
      </ul>
      <p><a href="<?= url('ferias') ?>">Ver todas as solicitações</a></p>
    <?php endif; ?>
  </section>
</div>

<h2>Últimas admissões</h2>
<table>
  <tr><th>Nome</th><th>Cargo</th><th>Departamento</th><th>Admissão</th></tr>
  <?php foreach ($recentes as $r): ?>
    <tr><td><?= e($r['nome']) ?></td><td><?= e($r['cargo']) ?></td><td><?= e($r['departamento']) ?></td><td><?= dataBr($r['data_admissao']) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../templates/footer.php'; ?>
