"use strict";

let searchBar = document.querySelector('.searchBar');

let resultat = [];
let init = { method: "POST" };
fetch(searchBar.getAttribute('data'), init)
    .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
    .then(result => {
        let member;
        let membersList = result.membersList;
        let userRole = result.userRole;
        Object.keys(membersList).forEach(key => {
            resultat.push({
                id: membersList[key].id + " ",
                member: membersList[key].member + " ",
                role: membersList[key].role + " ",
                createdAt: membersList[key].createdAt + " ",
            });
        });
        $(document).ready(function () {
            $('.searchBar').keyup(function () {
                let tbody = document.querySelector('tbody');
                tbody.innerHTML = "";
                member = $(this).val();
                tbodyConstruct(tbody, resultat, userRole);
                if (member != "") {
                    let tbody = document.querySelector('tbody');
                    tbody.innerHTML = "";
                    let row = tbody.insertRow();
                    let cell1 = row.insertCell();
                    let searchResult = filterData(resultat, member);
                    if (!searchResult.length)
                        cell1.textContent = "Aucun adhérent trouvé";
                    else {
                        tbodyConstruct(tbody, searchResult, userRole);
                    }
                }
            })
        })
    })

function tbodyConstruct(tbody, tab, userRole) {
    Object.keys(tab).forEach(key => {
        let id = tab[key].id.trim();
        let row = tbody.insertRow();
        let cell1 = row.insertCell();
        cell1.textContent = id;
        let cell2 = row.insertCell();
        cell2.textContent = tab[key].member;
        let cell3 = row.insertCell();
        cell3.textContent = tab[key].role;
        let cell4 = row.insertCell();
        cell4.textContent = tab[key].createdAt;
        let cell5 = row.insertCell();
        cell5.classList.add('d-flex', 'justify-content-center');
        let showLink = Tools.createLink('info', `/referent/adherents/consulter/${id}`, null, "Consulter", "far fa-eye fa-2x");
        let editLink = Tools.createLink('info', `/referent/adherents/${id}/editer`, null, "Modifier", "far fa-edit fa-2x");
        cell5.append(showLink);
        cell5.append(editLink);
        if (userRole == 'Administrateur') {
            let editRoleLink = Tools.createLink('primary', `/referent/adherents/${id}/editer/role`, null, "Modifier le rôle", "fas fa-user-tag");
            cell5.append(editRoleLink);
        }
    });
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
}

function filterData(searchTab, search) {
    return searchTab.filter(
        (a) => a.id.toLowerCase().includes(search.toLowerCase()) ||
            a.member.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase()) ||
            a.role.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase()) ||
            a.createdAt.toLowerCase().includes(search.toLowerCase())
    );
};


