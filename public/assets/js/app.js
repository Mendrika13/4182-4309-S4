document.querySelectorAll('[data-confirm]').forEach(function (el) {
  el.addEventListener('click', function (event) {
    if (! window.confirm(el.getAttribute('data-confirm'))) {
      event.preventDefault();
    }
  });
});
