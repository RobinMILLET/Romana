function statut(element, id) {
    var reservations = document.querySelectorAll('#reservations .statut'+id)
    for (var reservation of reservations) {
        if (element.checked) reservation.classList.remove('statut_hidden')
        else reservation.classList.add('statut_hidden')
    }
}

function mask(show) {
    if (show) document.body.classList.add('show-private')
    else document.body.classList.remove('show-private')
}

window.statut = statut
window.mask = mask