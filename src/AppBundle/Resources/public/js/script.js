$( "#search" ).click(function() {
    var order = '';
    if ($(".order-desc").hasClass('order-desc')) {
        order = 'ASC';
    } else if ($(".order-asc").hasClass('order-asc')) {
        order = 'DESC';
    }

    generateAdverts(order, $('#input-search').val());
});

/**
 * Click sur le chevron up
 */
$("i.order-desc").click(function() {
    generateAdverts('DESC', $('#input-search').val());
});

/**
 * Click sur le chevron down
 */
$("i.order-asc").click(function() {
    generateAdverts('ASC', $('#input-search').val());
});

/**
 * Récupère les adverts en fonction du l'ordre du prix et de la recherche
 *
 * @param sort
 * @param terms
 */
function generateAdverts(sort, terms) {
    $.get(Routing.generate(
        'advert', {
            'terms':terms,
            'order':sort
        }
    ), function(html){
        // Récupération du résultat HTML
        var tables_advert = $(html).find('table.table-advert');

        // On colle le résultat
        $('.div-adverts').html(tables_advert);

        $("i.order-desc").click(function() {
            generateAdverts('DESC', $('#input-search').val());
        });

        $("i.order-asc").click(function() {
            generateAdverts('ASC', $('#input-search').val());
        });
    });
}