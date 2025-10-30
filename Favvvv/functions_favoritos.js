// ...existing code...
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-fav, .btn-fav-toggle');
    if (!btn) return;
    const id = btn.dataset.id;
    if (!id) return;

    const isFav = btn.classList.contains('is-fav');
    const action = isFav ? 'remove' : 'add';

    fetch(BASE_URL_JS + '/favoritos/set', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'productoId=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(action)
    })
    .then(r => r.json())
    .then(json => {
        if (json.status) {
            btn.classList.toggle('is-fav');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-heart');
                icon.classList.toggle('fa-heart-o');
            }
        } else {
            alert(json.msg || 'Error al procesar favorito');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error de red');
    });
});