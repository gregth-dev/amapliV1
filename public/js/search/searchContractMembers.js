"use strict";

let searchBar = document.querySelector('.searchBar');
let resultat = [];
let init = { method: "POST" };
fetch(searchBar.getAttribute('data'), init)
    .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
    .then(result => {
        let contractMember;
        let contractMembersList = result.contractMembersList;
        Object.keys(contractMembersList).forEach(key => {
            resultat.push({
                id: contractMembersList[key].id + " ",
                name: contractMembersList[key].name + " ",
                member: contractMembersList[key].member + " ",
                createdAt: contractMembersList[key].createdAt + " ",
                status: contractMembersList[key].status,
                statePayments: contractMembersList[key].statePayments + " ",
                statePaymentsClass: contractMembersList[key].statePaymentsClass,
                balance: contractMembersList[key].balance + " ",
                endDate: contractMembersList[key].endDate + " ",
            });
        });
        $(document).ready(function () {
            $('.searchBar').keyup(function () {
                contractMember = $(this).val();
                let tbody = document.querySelector('tbody');
                tbody.innerHTML = "";
                tbodyConstruct(tbody, resultat);
                if (contractMember != "") {
                    let tbody = document.querySelector('tbody');
                    tbody.innerHTML = "";
                    let row = tbody.insertRow();
                    let cell1 = row.insertCell();
                    let searchResult = filterData(resultat, contractMember);
                    if (!searchResult.length)
                        cell1.textContent = "Aucun contrat adhérent trouvé";
                    else
                        tbodyConstruct(tbody, searchResult);
                }
            })
        })
    })

function tbodyConstruct(tbody, tab) {
    Object.keys(tab).forEach(key => {
        let id = tab[key].id.trim();
        let row = tbody.insertRow();
        let cell1 = row.insertCell();
        cell1.textContent = id;
        let cell2 = row.insertCell();
        cell2.textContent = tab[key].name;
        let cell3 = row.insertCell();
        cell3.textContent = tab[key].member;
        let cell4 = row.insertCell();
        cell4.textContent = tab[key].createdAt;
        let cell5 = row.insertCell();
        if (tab[key].status == "à archiver") {
            let spanArchive = document.createElement('span');
            spanArchive.classList.add("badge", "badge-pill", "badge-warning");
            spanArchive.textContent = "à archiver";
            cell5.append(spanArchive);
        } else
            cell5.textContent = tab[key].statePayments;
        let cell6 = row.insertCell();
        let span = document.createElement('span');
        span.classList.add("badge", "badge-pill", `${tab[key].statePaymentsClass}`);
        let balance = (Math.round(tab[key].balance * 100) / 100).toFixed(2);
        span.textContent = balance + " €";
        cell6.append(span);
        let cell7 = row.insertCell();
        cell7.textContent = tab[key].endDate;
        let cell8 = row.insertCell();
        cell8.classList.add('d-flex', 'justify-content-center');
        if (tab[key].status != "non actif") {
            let showLink = Tools.createLink('info', `/referent/contrats/adherents/${id}/consulter`, null, "Modifier", "far fa-eye");
            let editPaymentLink = Tools.createLink('info', `/referent/contrats/adherents/${id}/nouveau/paiements`, null, "Ajouter un paiement", "fas fa-cash-register");
            let paymentLink = Tools.createLink('info', `/referent/paiement/${id}/contrats/adherents/paiements`, null, "Les paiements", "fas fa-euro-sign");
            cell8.append(showLink);
            cell8.append(editPaymentLink);
            cell8.append(paymentLink);
        }
        if (tab[key].status == "non actif") {
            let showLink = Tools.createLink('info', `/referent/contrats/adherents/${id}/consulter`, null, "Modifier", "far fa-eye");
            let editPaymentLink = Tools.createLink('info', `/referent/contrats/adherents/${id}/nouveau/paiements`, null, "Ajouter un paiement", "fas fa-cash-register");
            let deleteContractLink = Tools.createLink('danger', `/referent/contrats/adherents/${id}`, null, "Supprimer le contrat adhérent", 'far fa-trash-alt');
            cell8.append(showLink);
            cell8.append(editPaymentLink);
            cell8.append(deleteContractLink);
        }
    });
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
}

function filterData(searchTab, search) {
    return searchTab.filter(
        (a) => a.id.toLowerCase().includes(search.toLowerCase()) ||
            a.name.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase()) ||
            a.member.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase()) ||
            a.createdAt.toLowerCase().includes(search.toLowerCase()) ||
            a.statePayments.toLowerCase().includes(search.toLowerCase()) ||
            a.balance.toLowerCase().includes(search.toLowerCase()) ||
            a.endDate.toLowerCase().includes(search.toLowerCase())
    );
};


