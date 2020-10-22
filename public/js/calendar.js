// Gère l'affichage du calendrier datePiker
$(document).ready(function () {
    $.fn.datepicker.dates['fr'] = {
        days: [
            "Dimanche",
            "Lundi",
            "Mardi",
            "Mercredi",
            "Jeudi",
            "Vendredi",
            "Samedi"
        ],
        daysShort: [
            "Di",
            "Lu",
            "Ma",
            "Me",
            "Je",
            "Ve",
            "Sa"
        ],
        daysMin: [
            "Di",
            "Lu",
            "Ma",
            "Me",
            "Je",
            "Ve",
            "Sa"
        ],
        months: [
            "Janvier",
            "Février",
            "Mars",
            "Avril",
            "Mai",
            "Juin",
            "Juillet",
            "Août",
            "Septembre",
            "Octobre",
            "Novembre",
            "Decembre"
        ],
        monthsShort: [
            "Janv",
            "Fev",
            "Mar",
            "Avr",
            "Mai",
            "Jui",
            "Jui",
            "Aou",
            "Sep",
            "Oct",
            "Nov",
            "Dec"
        ],
        today: "Aujourd'hui",
        clear: "Effacer",
        format: "dd/mm/yyyy",
        titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
        weekStart: 1
    };

    $('.dateInput').datepicker({
        todayBtn: "linked",
        language: 'fr-FR',
        todayHighlight: true,
        autoclose: true
    });

})