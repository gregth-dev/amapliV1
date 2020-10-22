"use strict";

let searchBar = document.querySelector('.searchBar');

let resultat = [];
let init = { method: "POST" };
fetch(searchBar.getAttribute('data'), init)
    .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
    .then(result => {
        let payment;
        let paymentsList = result.paymentsList;
        Object.keys(paymentsList).forEach(key => {
            resultat[paymentsList[key].id] = {
                id: paymentsList[key].id + " ",
                member: paymentsList[key].member + " ",
                checkNumber: paymentsList[key].checkNumber + " ",
                amount: paymentsList[key].amount + " ",
                depositDate: paymentsList[key].depositDate + " ",
                compareDate: paymentsList[key].compareDate,
                status: paymentsList[key].status + " ",
            };
        });
        $(document).ready(function () {
            $('.searchBar').keyup(function () {
                payment = $(this).val();
                let tbody = document.querySelector('tbody');
                tbody.innerHTML = "";
                tbodyConstruct(tbody, resultat);
                if (payment != "") {
                    let tbody = document.querySelector('tbody');
                    tbody.innerHTML = "";
                    let row = tbody.insertRow();
                    let cell1 = row.insertCell();
                    let searchResult = filterData(resultat, payment);
                    if (!searchResult.length)
                        cell1.textContent = "Aucun paiement trouvé";
                    else
                        tbodyConstruct(tbody, searchResult);
                }
            })
        })
    })

function tbodyConstruct(tbody, tab) {
    Object.keys(tab).forEach(key => {
        let status = tab[key].status;
        let id = tab[key].id.trim();
        let row = tbody.insertRow();
        let cell1 = row.insertCell();
        cell1.textContent = id;
        let cell2 = row.insertCell();
        cell2.textContent = tab[key].member;
        let cell3 = row.insertCell();
        cell3.textContent = tab[key].checkNumber;
        let cell4 = row.insertCell();
        cell4.textContent = tab[key].amount;
        let cell5 = row.insertCell();
        cell5.textContent = tab[key].depositDate;
        let cell6 = row.insertCell();
        let statusSpan = document.createElement('span');
        if (status.trim() === 'remis')
            statusSpan.classList.add('badge', 'badge-pill', 'badge-success');
        else if (status.trim() === 'non remis')
            statusSpan.classList.add('badge', 'badge-pill', 'badge-danger');
        else
            statusSpan.classList.add('badge', 'badge-pill', 'badge-secondary');
        statusSpan.id = `checkStatus${id}`;
        statusSpan.textContent = status;
        cell6.append(statusSpan);
        let cell7 = row.insertCell();
        cell7.classList.add('d-flex', 'justify-content-center');
        let checkPayment = Tools.createLink('warning', `/tresorier/adhesions/paiements/remettre/${id}`, null, "Remettre le paiement", "fas fa-cash-register");
        checkPayment.classList.add('checkPayment');
        if (status.trim() === 'remis')
            checkPayment.classList.add('disabled');
        checkPayment.id = `${id}`;
        cell7.append(checkPayment);
    });
    let linksPayment = document.querySelectorAll('.checkPayment');
    linksPayment.forEach(link => {
        link.addEventListener('click', evt => {
            let id = link.id;
            let status = document.querySelector(`#checkStatus${id}`);
            // On empêche le comportement du lien
            evt.preventDefault();
            let url = link.getAttribute('href');
            let init = { method: "POST" };
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
                            fetch(url, init)
                                .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
                                .then(result => {
                                    if (result.success) {
                                        //on actualise le tableau de données sur lequel on fait la recherche
                                        resultat[id].status = "remis "
                                        status.textContent = 'remis';
                                        status.classList.remove('badge-danger', 'badge-secondary');
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
}

function filterData(searchTab, search) {
    return searchTab.filter(
        (a) => a.id.toLowerCase().includes(search.toLowerCase()) ||
            a.member.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase()) ||
            a.checkNumber.toLowerCase().includes(search.toLowerCase()) ||
            a.amount.toLowerCase().includes(search.toLowerCase()) ||
            a.status.toLowerCase().includes(search.toLowerCase()) ||
            a.depositDate.toLowerCase().includes(search.toLowerCase())
    );
};
