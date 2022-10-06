
import './styles/app.css';
import { Tooltip } from 'bootstrap';
import './bootstrap';

$(document).ready(function() {
    const tooltipTriggerList = $('.ingredient');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));    

    $('.ingredient').click(function() {
        const self = $(this);
        const ingredientId = self.attr('value');
        const pizzaId = self.parents('tr').attr('value');
        const action = self.hasClass('btn-success') ? 'DELETE' : 'POST';

        $.ajax({
            url : '/pizzas/' + pizzaId + '/ingredients/' + ingredientId,
            type: action
        })
        .done(function(data) {
            if(action === 'DELETE') {
                self.removeClass('btn-success');
                self.addClass('btn-secondary');
            }
            else {
                self.addClass('btn-success');
                self.removeClass('btn-secondary');
            }

            // Update pizza price with new price
            self.parents('tr').find('.pizzaPrice').html(data.price);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log('error', errorThrown);
        });
    });
});
