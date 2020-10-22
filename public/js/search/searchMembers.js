"use strict";

let searchBar = document.querySelector('.searchBar');

let resultat = [];

let init = { method: "POST" };
fetch(searchBar.getAttribute('data'), init)
    .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
    .then(result => {
        let member;
        let membersList = result.membersList;
        Object.keys(membersList).forEach(key => {
            resultat.push({ member: membersList[key].member + " " });
        });
        $(document).ready(function () {
            $('.searchBar').keyup(function () {
                let tbody = document.querySelector('tbody');
                tbody.innerHTML = "";
                tbodyConstruct(tbody, resultat);
                member = $(this).val();
                if (member != "") {
                    tbody.innerHTML = "";
                    let searchResult = filterData(resultat, member);
                    let row = tbody.insertRow();
                    let cell1 = row.insertCell();
                    if (!searchResult.length)
                        cell1.textContent = "Aucun adhérent trouvé";
                    tbodyConstruct(tbody, searchResult);
                }
            })
        })
    })

function tbodyConstruct(tbody, tab) {
    Object.keys(tab).forEach(key => {
        let row = tbody.insertRow();
        let cell1 = row.insertCell();
        cell1.textContent = tab[key].member;
    });
}

function filterData(searchTab, search) {
    return searchTab.filter(
        (a) => a.member.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase())
    );
};

//string.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
