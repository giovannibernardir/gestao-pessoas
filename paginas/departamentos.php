<?php
$titulo = 'Departamentos';
$erro   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($acao === 'novo') {
        $nome = trim($_POST['nome'] ?? '');
        if ($nome === '') {
            $erro = 'Informe o nome do departamento.';
        } else {
            $pdo->prepare('INSERT INTO departamentos (nome) VALUES (:nome)')->execute([':nome' => $nome]);
            header('Location: ' . url('departamentos'));
            exit;
        }
    } elseif ($acao === 'excluir' && $id) {
        $st = $pdo->prepare('SELECT COUNT(*) FROM funcionarios WHERE departamento_id = :id');
        $st->execute([':id' => $id]);
        if ($st->fetchColumn() > 0) {
            $erro = 'Esse departamento ainda tem funcionários. Mova ou exclua os funcionários antes.';
        } else {
            $pdo->prepare('DELETE FROM departamentos WHERE id = :id')->execute([':id' => $id]);
            header('Location: ' . url('departamentos'));
            exit;
        }
    }
}

$lista = $pdo->query(
    'SELECT d.id, d.nome, COUNT(f.id) AS total
     FROM departamentos d LEFT JOIN funcionarios f ON f.departamento_id = d.id
     GROUP BY d.id, d.nome ORDER BY d.nome'
)->fetchAll();

require __DIR__ . '/../templates/header.php';
?>
<h1>Departamentos</h1>
<?php if ($erro): ?><p class="erro"><?= e($erro) ?></p><?php endif; ?>

<form method="post" action="<?= url('departamentos/novo') ?>" class="busca">
  <input name="nome" placeholder="Nome do novo departamento" required>
  <button class="botao">Adicionar departamento</button>
</form>

<table>
  <tr><th>Departamento</th><th>Funcionários</th><th></th></tr>
  <?php foreach ($lista as $d): ?>
    <tr>
      <td><?= e($d['nome']) ?></td>
      <td><?= (int)$d['total'] ?></td>
      <td class="acoes">
        <form method="post" action="<?= url('departamentos/excluir/' . $d['id']) ?>" data-confirm="Excluir o departamento <?= e($d['nome']) ?>?">
          <button class="link perigo">Excluir</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../templates/footer.php'; ?>
