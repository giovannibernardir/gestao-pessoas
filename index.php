<?php
// Roteador: transforma /funcionarios/editar/3 em página + ação + id
require_once __DIR__ . '/config/conexao.php';

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

function url(string $caminho = ''): string { global $base; return $base . '/' . ltrim($caminho, '/'); }
function e($valor): string { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function dataBr(?string $d): string { return $d ? date('d/m/Y', strtotime($d)) : ''; }

$partes = explode('/', trim($_GET['url'] ?? '', '/'));
$pagina = $partes[0] ?: 'dashboard';
$acao   = $partes[1] ?? null;
$id     = isset($partes[2]) ? (int)$partes[2] : null;

$permitidas = ['dashboard', 'funcionarios', 'departamentos', 'ferias'];

if (in_array($pagina, $permitidas, true)) {
    require __DIR__ . "/paginas/{$pagina}.php";
} else {
    http_response_code(404);
    require __DIR__ . '/paginas/404.php';
}
