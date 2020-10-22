"use strict";

let searchBar = document.querySelector('.searchBar');

let resultat = [];
let init = { method: "POST" };
fetch(searchBar.getAttribute('data'), init)
    .then((response) => response.ok ? response.json() : Promise.reject(new Error("Réponse invalide du server. Actualisez la page.")))
    .then(result => {
        let doc;
        let listDocuments = result.listDocuments;
        Object.keys(listDocuments).forEach(key => {
            resultat.push({
                id: listDocuments[key].id + " ",
                document: listDocuments[key].document + " ",
                createdAt: listDocuments[key].createdAt + " ",
                updatedAt: listDocuments[key].updatedAt + " ",
                type: listDocuments[key].type + " ",
                icon: listDocuments[key].icon + " ",
            });
        });
        $(document).ready(function () {
            $('.searchBar').keyup(function () {
                doc = $(this).val();
                let tbody = document.querySelector('tbody');
                tbody.innerHTML = "";
                tbodyConstruct(tbody, resultat);
                if (doc != "") {
                    let tbody = document.querySelector('tbody');
                    tbody.innerHTML = "";
                    let row = tbody.insertRow();
                    let cell1 = row.insertCell();
                    let searchResult = filterData(resultat, doc);
                    if (!searchResult.length)
                        cell1.textContent = "Aucun document trouvé";
                    else
                        tbodyConstruct(tbody, searchResult);
                }
            })
        })
    })

function tbodyConstruct(tbody, tab) {
    Object.keys(tab).forEach(key => {
        let row = tbody.insertRow();
        let cell1 = row.insertCell();
        cell1.textContent = tab[key].id;
        let cell2 = row.insertCell();
        cell2.textContent = tab[key].document;
        let cell3 = row.insertCell();
        cell3.textContent = tab[key].createdAt;
        let cell4 = row.insertCell();
        cell4.textContent = tab[key].updatedAt;
        let cell5 = row.insertCell();
        cell5.textContent = tab[key].type;
        let cell6 = row.insertCell();
        cell6.classList.add('d-flex', 'justify-content-center');
        let showLink = Tools.createLink(null, `/referent/document/${tab[key].id.trim()}/consulter`, null, "Voir le document", `far fa-file${tab[key].icon}`);
        cell6.append(showLink);
    });
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
}

function filterData(searchTab, search) {
    return searchTab.filter(
        (a) => a.id.toLowerCase().includes(search.toLowerCase()) ||
            a.document.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().includes(search.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase()) ||
            a.createdAt.toLowerCase().includes(search.toLowerCase()) ||
            a.updatedAt.toLowerCase().includes(search.toLowerCase()) ||
            a.type.toLowerCase().includes(search.toLowerCase())
    );
};
