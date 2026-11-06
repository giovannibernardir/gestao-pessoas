<?php
$titulo = 'Férias';
$erro   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($acao === 'aprovar' || $acao === 'recusar') && $id) {
        $nova = $acao === 'aprovar' ? 'aprovada' : 'recusada';
        $pdo->prepare('UPDATE ferias SET situacao = :s WHERE id = :id')->execute([':s' => $nova, ':id' => $id]);
        header('Location: ' . url('ferias'));
        exit;
    }
    if ($acao === 'novo') {
        $fid = (int)($_POST['funcionario_id'] ?? 0);
        $ini = $_POST['data_inicio'] ?? '';
        $fim = $_POST['data_fim'] ?? '';
        if (!$fid || !$ini || !$fim || $fim < $ini) {
            $erro = 'Escolha o funcionário e um período válido (o fim não pode ser antes do início).';
        } else {
            $pdo->prepare('INSERT INTO ferias (funcionario_id, data_inicio, data_fim) VALUES (:f, :i, :t)')
                ->execute([':f' => $fid, ':i' => $ini, ':t' => $fim]);
            header('Location: ' . url('ferias'));
            exit;
        }
    }
}

$ativos = $pdo->query("SELECT id, nome FROM funcionarios WHERE status = 'ativo' ORDER BY nome")->fetchAll();
$lista  = $pdo->query(
    "SELECT fe.*, f.nome FROM ferias fe JOIN funcionarios f ON f.id = fe.funcionario_id
     ORDER BY FIELD(fe.situacao, 'pendente', 'aprovada', 'recusada'), fe.data_inicio"
)->fetchAll();

require __DIR__ . '/../templates/header.php';
?>
<h1>Férias</h1>
<?php if ($erro): ?><p class="erro"><?= e($erro) ?></p><?php endif; ?>

<form method="post" action="<?= url('ferias/novo') ?>" class="busca">
  <select name="funcionario_id" required>
    <option value="">Funcionário</option>
    <?php foreach ($ativos as $a): ?><option value="<?= (int)$a['id'] ?>"><?= e($a['nome']) ?></option><?php endforeach; ?>
  </select>
  <input type="date" name="data_inicio" required>
  <input type="date" name="data_fim" required>
  <button class="botao">Registrar solicitação</button>
</form>

<?php if (!$lista): ?>
  <p class="vazio">Nenhuma solicitação de férias registrada.</p>
<?php else: ?>
<table>
  <tr><th>Funcionário</th><th>Início</th><th>Fim</th><th>Situação</th><th></th></tr>
  <?php foreach ($lista as $fe): ?>
    <tr>
      <td><?= e($fe['nome']) ?></td>
      <td><?= dataBr($fe['data_inicio']) ?></td>
      <td><?= dataBr($fe['data_fim']) ?></td>
      <td><span class="selo <?= e($fe['situacao']) ?>"><?= e(ucfirst($fe['situacao'])) ?></span></td>
      <td class="acoes">
        <?php if ($fe['situacao'] === 'pendente'): ?>
          <form method="post" action="<?= url('ferias/aprovar/' . $fe['id']) ?>"><button class="link">Aprovar</button></form>
          <form method="post" action="<?= url('ferias/recusar/' . $fe['id']) ?>" data-confirm="Recusar esta solicitação?"><button class="link perigo">Recusar</button></form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
<?php require __DIR__ . '/../templates/footer.php'; ?>
