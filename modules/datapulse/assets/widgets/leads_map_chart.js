$(document).ready(function () {
    'use strict';

    google.charts.load('current', {
        'packages':['geochart'],
    });
    google.charts.setOnLoadCallback(drawLeadRegionsMap);
});

function drawLeadRegionsMap() {
    'use strict';

    $.ajax({
        url: admin_url + 'datapulse/leads_map_chart/',
        dataType: "json",
        success: function(data) {

            var chart_data = [];

            chart_data.push(['Country', 'Total Leads']);
            chart_data = chart_data.concat(data);

            var data = google.visualization.arrayToDataTable(chart_data);
            var options = {};
            var chart = new google.visualization.GeoChart(document.getElementById('leads_regions_div'));

            chart.draw(data, options);
        }
    });
}
