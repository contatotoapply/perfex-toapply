$(document).ready(function(){
    'use strict';

    dpInvoicesStackedByCustomersChart();
});

function dpInvoicesStackedByCustomersChart(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#dpInvoicesStackedByCustomersChartYear').text(selectedYear);
    }

    $.ajax({
        url: admin_url + 'datapulse/invoices_stacked_by_customers_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {
            var customerNames = [];
            var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var dataset = [];

            var customerData = {};

            $.each(data, function(index, item) {
                if (!customerData.hasOwnProperty(item.customer_name)) {
                    customerData[item.customer_name] = Array.from({ length: 12 }, () => 0);
                }

                customerData[item.customer_name][item.month - 1] = parseFloat(item.total_sum);
            });

            for (var customerName in customerData) {
                if (customerData.hasOwnProperty(customerName)) {
                    customerNames.push(customerName);
                    var backgroundColor = dPrandomColor();
                    dataset.push({
                        label: customerName,
                        backgroundColor: backgroundColor,
                        borderColor: 'rgb(208,208,208)',
                        borderWidth: 1,
                        data: customerData[customerName]
                    });
                }
            }

            var ctx = document.getElementById('dpInvoicesStackedByCustomersChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: dataset
                },
                options: {
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                                var label = data.labels[tooltipItem.index];
                                var value = format_money(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);

                                return datasetLabel + ': ' + label + ' - ' + value;
                            }
                        }
                    },
                    scales: {
                        xAxes: [{ stacked: true }],
                        yAxes: [{ stacked: true }]
                    }
                }
            });
        }
    });
}

function dPrandomColor() {
    var r = Math.floor(Math.random() * 256);
    var g = Math.floor(Math.random() * 256);
    var b = Math.floor(Math.random() * 256);
    return 'rgba(' + r + ',' + g + ',' + b + ', 0.2)';
}
