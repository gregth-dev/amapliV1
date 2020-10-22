"use strict";

let searchBar = document.querySelector('.searchBar');

let resultat = [];
let init = { method: "POST" };
fetch(searchBar.getAttribute('data'), init)
    .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
    .then(result => {
        let member;
        let contractListMembers = result.contractListMembers;
        Object.keys(contractListMembers).forEach(key => {
            resultat.push({
                id: contractListMembers[key].id + " ",
                member: contractListMembers[key].member + " "
            });
        });
        $(document).ready(function () {
            $('.searchBar').keyup(function () {
                member = $(this).val();
                let tbody = document.querySelector('tbody');
                tbody.innerHTML = "";
                tbodyConstruct(tbody, resultat);
                if (member != "") {
                    let tbody = document.querySelector('tbody');
                    tbody.innerHTML = "";
                    let row = tbody.insertRow();
                    let cell1 = row.insertCell();
                    let searchResult = filterData(resultat, member);
                    if (!searchResult.length)
                        cell1.textContent = "Aucun adhérent trouvé";
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
        cell2.textContent = tab[key].member;
        let cell3 = row.insertCell();
        cell3.classList.add('d-flex', 'justify-content-center');
        let showLink = Tools.createLink('info', `/referent/paiement/${id}/contrats/adherents/paiements`, null, "Voir les paiements", "far fa-eye");
        cell3.append(showLink);
    });
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
}

function filterData(searchTab, search) {
    return searchTab.filter(
        (a) => a.id.toLowerCase().includes(search.toLowerCase()) ||
            a.member.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase())
    );
};
