$(document).ready(function(){
    'use strict';

    dpExpensesOnCategories();
});

function dpExpensesOnCategories(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#ExpensesOnCategoriesYear').text(selectedYear);
    }

    var categories = [];
    var amounts = [];

    $.ajax({
        url: admin_url + 'datapulse/expenses_on_categories_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {
            $.each(data, function(index, item) {
                categories.push(item.category_name);
                amounts.push(parseFloat(item.total_amount));
            });

            var ctx = document.getElementById('expensesOnCategories').getContext('2d');
            new Chart(ctx, {
                type: 'horizontalBar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Expenses',
                        data: amounts,
                        backgroundColor: 'rgba(75,161,192,0.2)',
                        borderColor: 'rgb(75,126,192)',
                        borderWidth: 1
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return format_money(tooltipItem.xLabel)
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                callback: function(value) {
                                    return format_money(value)
                                },
                                beginAtZero: true,
                            }
                        }]
                    }
                }
            });
        }
    });
}
