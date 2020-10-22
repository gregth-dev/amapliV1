"use strict";

// Gestion du lien supprimer
let linksArchive = document.querySelectorAll('.archive');

// On boucle sur links
linksArchive.forEach(link => {
    link.addEventListener('click', evt => {
        let id = link.id;
        let status = document.querySelector(`#archiveStatus${id}`);
        // On empêche le comportement du lien
        evt.preventDefault();
        let url = link.getAttribute('href');
        let init = { method: "POST" };
        $.confirm({
            icon: 'fa fa-spinner fa-spin',
            title: 'Archivage d\'un contrat',
            content: `Attention vous allez archiver ce contrat`,
            type: 'orange',
            theme: 'dark',
            columnClass: 'small',
            buttons: {
                ok: {
                    text: "Confirmer",
                    btnClass: 'btn-warning',
                    action: () => {
                        fetch(url, init)
                            .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
                            .then(result => {
                                if (result.success) {
                                    status.textContent = 'archivé';
                                    link.classList.add('disabled');
                                    var notification = new NotificationFx({
                                        wrapper: document.body,
                                        message: `<p>${result.text}</p>`,
                                        layout: 'growl',
                                        effect: 'genie',
                                        type: 'warning',
                                        ttl: 2000,
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
                                        message: `<p>${result.text}</p>`,
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