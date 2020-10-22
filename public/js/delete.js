"use strict";

// Gestion des liens supprimer
let linksDelete = document.querySelectorAll('[data-delete]');
// On boucle sur links
linksDelete.forEach(link => {
    link.addEventListener('click', evt => {
        evt.preventDefault();
        let url = link.getAttribute('href');
        let deleteText = link.getAttribute('data-delete-text');
        let container = link.parentNode.parentNode;
        // On envoie un requête AJAX vers le href du lien avec la méthode DELETE
        let token = link.getAttribute('data-token');
        let data = new FormData();
        data.append('_method', 'DELETE')
        data.append('_token', token)
        let init = { method: "POST", body: data };
        $.confirm({
            icon: 'fa fa-spinner fa-spin',
            title: 'Suppression',
            content: `Attention vous allez supprimer ${deleteText}`,
            type: 'red',
            theme: 'dark',
            columnClass: 'small',
            buttons: {
                ok: {
                    text: "Confirmer",
                    btnClass: 'btn-danger',
                    action: () => {
                        fetch(url, init)
                            .then((response) => response.ok ? response.json() : Promise.reject(new Error("Invalid response from server")))
                            .then(result => {
                                let text = result.text;
                                $('.tooltip').tooltip('hide');
                                if (result.success) {
                                    if (link.getAttribute('data-redirect') != "on")
                                        container.remove();
                                    var notification = new NotificationFx({
                                        wrapper: document.body,
                                        message: `<i class="fas fa-check fa-2x"></i><p>${text}</p>`,
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
                                    if (link.getAttribute('data-redirect') == "on") {
                                        setTimeout(() => {
                                            window.location.replace(link.getAttribute('link-redirect'));
                                        }, 2000);
                                    }
                                }
                                else {
                                    link.classList.remove('disabled');
                                    var notification = new NotificationFx({
                                        wrapper: document.body,
                                        message: `<i class="fas fa-exclamation-triangle fa-2x"></i><p>${text}</p>`,
                                        layout: 'bar',
                                        effect: 'slidetop',
                                        type: 'danger',
                                        ttl: 6000,
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

