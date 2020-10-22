"use strict";

let linksPayment = document.querySelectorAll('.checkPayment');
linksPayment.forEach(link => {
    link.addEventListener('click', evt => {
        let id = link.id;
        let status = document.querySelector(`#checkStatus${id}`);
        // On empêche le comportement du lien
        evt.preventDefault();
        let url = link.getAttribute('href');
        $.confirm({
            icon: 'fa fa-spinner fa-spin',
            title: 'Remise d\'un paiement',
            content: `Attention vous allez remettre ce paiement`,
            type: 'orange',
            theme: 'dark',
            columnClass: 'small',
            buttons: {
                ok: {
                    text: "Confirmer",
                    btnClass: 'btn-warning',
                    action: () => {
                        fetch(url)
                            .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
                            .then(result => {
                                if (result.success) {
                                    $('.tooltip').tooltip('hide');
                                    status.textContent = 'remis';
                                    status.classList.remove('badge-danger', 'badge-secondary');
                                    status.classList.remove('badge-danger', 'badge-danger');
                                    status.classList.add('badge', 'badge-pill', 'badge-success');
                                    link.classList.add('disabled');
                                    var notification = new NotificationFx({
                                        wrapper: document.body,
                                        message: '<i class="fas fa-check fa-2x"></i><p>Mise à jour du paiement</p>',
                                        layout: 'bar',
                                        effect: 'slidetop',
                                        type: 'success',
                                        ttl: 3000,
                                        onClose: function () {
                                            return false;
                                        },
                                        onOpen: function () {
                                            return false;
                                        }
                                    });
                                    notification.show();
                                }
                                else {
                                    var notification = new NotificationFx({
                                        wrapper: document.body,
                                        message: '<i class="fas fa-exclamation-triangle fa-2x"></i><p>Erreur lors de la remise du paiement</p>',
                                        layout: 'bar',
                                        effect: 'slidetop',
                                        type: 'danger',
                                        ttl: 3000,
                                        onClose: function () {
                                            return false;
                                        },
                                        onOpen: function () {
                                            return false;
                                        }
                                    });
                                    notification.show();
                                }
                            })
                    }
                },
                Annuler: () => {
                    return;
                }
            }
        })

    })
})