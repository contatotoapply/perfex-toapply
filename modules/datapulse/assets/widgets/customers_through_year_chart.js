$(document).ready(function(){
    'use strict';

    dpCustomersThroughYear();
});

function dpCustomersThroughYear(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#customersThroughYearChartYear').text(selectedYear);
    }

    var months = [];
    var customerCounts = Array.from({length: 12}, () => 0);

    $.ajax({
        url: admin_url + 'datapulse/customers_through_year_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {

            $.each(data, function(index, item) {
                customerCounts[item.month - 1] = item.total_customers;
            });

            var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            months = monthNames;

            var ctx = document.getElementById('customersThroughYearChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'New Customers',
                        data: customerCounts,
                        backgroundColor: 'rgba(75,192,77,0.2)',
                        borderColor: 'rgb(75,192,77)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
}
