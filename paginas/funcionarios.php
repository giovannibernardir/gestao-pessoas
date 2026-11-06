<?php
$titulo = 'Funcionários';
$erro   = null;
$status = ['ativo' => 'Ativo', 'afastado' => 'Afastado', 'desligado' => 'Desligado'];
$deptos = $pdo->query('SELECT id, nome FROM departamentos ORDER BY nome')->fetchAll();

// Excluir
if ($acao === 'excluir' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('DELETE FROM ferias WHERE funcionario_id = :id')->execute([':id' => $id]);
    $pdo->prepare('DELETE FROM funcionarios WHERE id = :id')->execute([':id' => $id]);
    header('Location: ' . url('funcionarios'));
    exit;
}

// Cadastrar / editar
if ($acao === 'novo' || $acao === 'editar') {
    $f = ['nome' => '', 'email' => '', 'cargo' => '', 'data_admissao' => date('Y-m-d'), 'status' => 'ativo', 'departamento_id' => ''];

    if ($acao === 'editar') {
        $st = $pdo->prepare('SELECT * FROM funcionarios WHERE id = :id');
        $st->execute([':id' => $id]);
        $f = $st->fetch();
        if (!$f) { http_response_code(404); require __DIR__ . '/404.php'; return; }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $f = array_merge($f, array_map('trim', array_intersect_key($_POST, $f)));
        if ($f['nome'] === '' || $f['cargo'] === '' || !$f['departamento_id'] || !$f['data_admissao']) {
            $erro = 'Preencha nome, cargo, departamento e data de admissão.';
        } elseif (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) {
            $erro = 'Informe um e-mail válido.';
        } elseif (!isset($status[$f['status']])) {
            $erro = 'Status inválido.';
        } else {
            $dados = [
                ':nome' => $f['nome'], ':email' => $f['email'], ':cargo' => $f['cargo'],
                ':adm' => $f['data_admissao'], ':st' => $f['status'], ':dep' => (int)$f['departamento_id'],
            ];
            try {
                if ($acao === 'novo') {
                    $sql = 'INSERT INTO funcionarios (nome, email, cargo, data_admissao, status, departamento_id)
                            VALUES (:nome, :email, :cargo, :adm, :st, :dep)';
                } else {
                    $sql = 'UPDATE funcionarios SET nome = :nome, email = :email, cargo = :cargo,
                            data_admissao = :adm, status = :st, departamento_id = :dep WHERE id = :id';
                    $dados[':id'] = $id;
                }
                $pdo->prepare($sql)->execute($dados);
                header('Location: ' . url('funcionarios'));
                exit;
            } catch (PDOException $ex) {
                $erro = $ex->getCode() === '23000' ? 'Já existe um funcionário com esse e-mail.' : 'Não foi possível salvar.';
            }
        }
    }

    $titulo = $acao === 'novo' ? 'Novo funcionário' : 'Editar funcionário';
    require __DIR__ . '/../templates/header.php';
    ?>
    <h1><?= e($titulo) ?></h1>
    <?php if ($erro): ?><p class="erro"><?= e($erro) ?></p><?php endif; ?>
    <form method="post" class="formulario">
      <label>Nome <input name="nome" value="<?= e($f['nome']) ?>" required></label>
      <label>E-mail <input type="email" name="email" value="<?= e($f['email']) ?>" required></label>
      <label>Cargo <input name="cargo" value="<?= e($f['cargo']) ?>" required></label>
      <label>Departamento
        <select name="departamento_id" required>
          <option value="">Selecione</option>
          <?php foreach ($deptos as $d): ?>
            <option value="<?= (int)$d['id'] ?>" <?= (int)$f['departamento_id'] === (int)$d['id'] ? 'selected' : '' ?>><?= e($d['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Data de admissão <input type="date" name="data_admissao" value="<?= e($f['data_admissao']) ?>" required></label>
      <label>Status
        <select name="status">
          <?php foreach ($status as $chave => $rotulo): ?>
            <option value="<?= $chave ?>" <?= $f['status'] === $chave ? 'selected' : '' ?>><?= $rotulo ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <div class="acoes">
        <button class="botao">Salvar funcionário</button>
        <a href="<?= url('funcionarios') ?>">Cancelar</a>
      </div>
    </form>
    <?php
    require __DIR__ . '/../templates/footer.php';
    return;
}

// Listagem com busca
$q  = trim($_GET['q'] ?? '');
$st = $pdo->prepare(
    'SELECT f.*, d.nome AS departamento
     FROM funcionarios f JOIN departamentos d ON d.id = f.departamento_id
     WHERE f.nome LIKE :q1 OR f.cargo LIKE :q2 OR d.nome LIKE :q3
     ORDER BY f.nome'
);
$st->execute([':q1' => "%$q%", ':q2' => "%$q%", ':q3' => "%$q%"]);
$lista = $st->fetchAll();

require __DIR__ . '/../templates/header.php';
?>
<h1>Funcionários</h1>
<div class="barra-topo">
  <form method="get" class="busca">
    <input name="q" value="<?= e($q) ?>" placeholder="Buscar por nome, cargo ou departamento">
    <button class="botao">Buscar</button>
  </form>
  <a class="botao" href="<?= url('funcionarios/novo') ?>">Novo funcionário</a>
</div>

<?php if (!$lista): ?>
  <p class="vazio">Nenhum funcionário encontrado. <a href="<?= url('funcionarios/novo') ?>">Cadastre o primeiro</a>.</p>
<?php else: ?>
<table>
  <tr><th>Nome</th><th>Cargo</th><th>Departamento</th><th>Admissão</th><th>Status</th><th></th></tr>
  <?php foreach ($lista as $f): ?>
    <tr>
      <td><?= e($f['nome']) ?><br><small><?= e($f['email']) ?></small></td>
      <td><?= e($f['cargo']) ?></td>
      <td><?= e($f['departamento']) ?></td>
      <td><?= dataBr($f['data_admissao']) ?></td>
      <td><span class="selo <?= e($f['status']) ?>"><?= e($status[$f['status']]) ?></span></td>
      <td class="acoes">
        <a href="<?= url('funcionarios/editar/' . $f['id']) ?>">Editar</a>
        <form method="post" action="<?= url('funcionarios/excluir/' . $f['id']) ?>" data-confirm="Excluir <?= e($f['nome']) ?> e as férias dele?">
          <button class="link perigo">Excluir</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
<?php require __DIR__ . '/../templates/footer.php'; ?>
