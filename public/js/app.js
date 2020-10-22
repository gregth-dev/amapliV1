"use strict";

//pour les tooltips des liens
$(function () {
    $("[data-toggle='tooltip']").tooltip();
});

//enleve le message flash au bout de 3sec
/* let flash = document.querySelector('.flash');
if (flash) {
    setTimeout(() => {
        flash.parentNode.remove();
    }, 3000)
} */
//Modifie la taille des icones en fonction de la taille de l'écran
let i = document.querySelectorAll('i');
if (screen.width <= 360) {
    i.forEach(e => {
        e.classList.replace('fa-2x', 'fa-1x');
    })

}

// On récupère le numéro des futurs champs qui vont être créés
let index = $('#contract_deliveries div.form-group').length;
$('#add-delivery').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#contract_deliveries').data('prototype').replace(/__name__/g, index);
    index++;
    // On injecte ce code au sein de la div
    $('#contract_deliveries').append(tmpl);
    $('.dateInput').datepicker({
        startDate: new Date(),
        todayBtn: "linked",
        language: 'fr-FR',
        todayHighlight: true,
        autoclose: true
    });
    // On gère le bouton supprimer
    handleDeleteButtons();
})

// On récupère le numéro des futurs champs qui vont être créés
let index2 = $('#contract_member2_orders div.form-group').length;
$('#add-order').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#contract_member2_orders').data('prototype').replace(/__name__/g, index2);
    index2++;
    // On injecte ce code au sein de la div
    $('#contract_member2_orders').append(tmpl);
    handleDeleteButtons();
    // on gère l'affichage de select2
    $('#contract_member2_orders').find('select').each(function () {
        $(this).select2();
    });
})

let index3 = $('#contract_member3_orders div.form-group').length;
$('#add-order2').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#contract_member3_orders').data('prototype').replace(/__name__/g, index3);
    index3++;
    // On injecte ce code au sein de la div
    $('#contract_member3_orders').append(tmpl);
    handleDeleteButtons();
    // on gère l'affichage de select2
    $('#contract_member3_orders').find('select').each(function () {
        $(this).select2();
    });
})

let index4 = $('#contract_member4_payments div.form-group').length;
$('#add-payment').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#contract_member4_payments').data('prototype').replace(/__name__/g, index4);
    index4++;
    // On injecte ce code au sein de la div
    $('#contract_member4_payments').append(tmpl);
    $('.dateInput').datepicker({
        startDate: new Date(),
        todayBtn: "linked",
        language: 'fr-FR',
        todayHighlight: true,
        autoclose: true
    });
    handleDeleteButtons();
})

let index5 = $('#contract_edit_member_orders div.form-group').length;
$('#add-order-edit').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#contract_edit_member_orders').data('prototype').replace(/__name__/g, index5);
    index5++;
    // On injecte ce code au sein de la div
    $('#contract_edit_member_orders').append(tmpl);
    handleDeleteButtons();
    // on gère l'affichage de select2
    $('#contract_edit_member_orders').find('select').each(function () {
        $(this).select2();
    });
})

let index6 = $('#contract_edit_member_payments div.form-group').length;
$('#add-payment-edit').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#contract_edit_member_payments').data('prototype').replace(/__name__/g, index6);
    index6++;
    // On injecte ce code au sein de la div
    $('#contract_edit_member_payments').append(tmpl);
    handleDeleteButtons();
    // on gère l'affichage de select2
    $('#contract_edit_member_payments').find('select').each(function () {
        $(this).select2();
    });
})

let index7 = $('#subscription_payment div.form-group').length;
$('#add-paymentCotisation').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#subscription_payment').data('prototype').replace(/__name__/g, index7);
    index7++;
    // On injecte ce code au sein de la div
    $('#subscription_payment').append(tmpl);
    $('.dateInput').datepicker({
        startDate: new Date(),
        todayBtn: "linked",
        language: 'fr-FR',
        todayHighlight: true,
        autoclose: true
    });
    handleDeleteButtons();
})

let index8 = $('#donation_payment div.form-group').length;
$('#add-DonationPayment').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#donation_payment').data('prototype').replace(/__name__/g, index8);
    index8++;
    // On injecte ce code au sein de la div
    $('#donation_payment').append(tmpl);
    $('.dateInput').datepicker({
        startDate: new Date(),
        todayBtn: "linked",
        language: 'fr-FR',
        todayHighlight: true,
        autoclose: true
    });
    handleDeleteButtons();
})


let index9 = $('#subscription_payment div.form-group').length;
$('#add-subscriptionPayment').click(() => { // On récupère le prototype des entrées
    const tmpl = $('#subscription_payment').data('prototype').replace(/__name__/g, index9);
    index9++;
    // On injecte ce code au sein de la div
    $('#subscription_payment').append(tmpl);
    $('.dateInput').datepicker({
        startDate: new Date(),
        todayBtn: "linked",
        language: 'fr-FR',
        todayHighlight: true,
        autoclose: true
    });
    handleDeleteButtons();
})

handleDeleteButtons();

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        // On supprime l'ensemble de l'élément
        document.querySelector(`${target}`).parentNode.remove();
    })
}

$(document).ready(function () {
    $(`.js-select`).select2();
    $(`.js-select-multiple`).select2();
});
