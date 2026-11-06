// Pede confirmação antes de enviar formulários marcados com data-confirm
document.addEventListener('submit', function (ev) {
  var msg = ev.target.getAttribute('data-confirm');
  if (msg && !confirm(msg)) ev.preventDefault();
});
