jQuery('.wpfs-specification-delete').on('click', function() {
    jQuery(this).closest('.wpfs-specification').remove();
});

jQuery('.wpfs-specification-add-button').on('click', function() {
    let template = jQuery('.wpfs-specification-template').clone(true);
    jQuery(template).attr('class', 'wpfs-specification');
    jQuery(template).find('input').first().attr('name', 'wpfs-specification-title[]');
    jQuery(template).find('input').eq(1).attr('name', 'wpfs-specification-description[]');

    jQuery('.wpfs-featured-specifications').children('tbody').append(template);
    return false;
});

jQuery('.wpfs-featured-specifications')
.children('tbody')
.sortable({

});
