<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datapulse extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('datapulse_model');
        hooks()->do_action('datapulse_init');
    }

    public function index()
    {
        show_404();
    }

    public function sales_by_saleman_chart()
    {
        echo json_encode($this->datapulse_model->sales_by_saleman_chart(1));
        die;
    }

    public function customers_map_chart()
    {
        $country_data = $this->datapulse_model->customers_map_chart();

        $chart_data = [];
        foreach ($country_data as $country) {
            if ((int)$country->total_customers > 0) {
                $chart_data[] = [$country->short_name, (int)$country->total_customers];
            }
        }

        echo json_encode($chart_data);
        die;
    }

    public function leads_map_chart()
    {
        $country_data = $this->datapulse_model->leads_map_chart();

        $chart_data = [];
        foreach ($country_data as $country) {
            if ((int)$country->total_leads > 0) {
                $chart_data[] = [$country->short_name, (int)$country->total_leads];
            }
        }

        echo json_encode($chart_data);
        die;
    }

    public function staff_assigned_to_leads()
    {
        $year = $_GET['year'] ?? date('Y');
        $data['stats'] = $this->datapulse_model->staff_assigned_to_leads($year);

        echo json_encode($data);
        die;
    }

    public function item_groups_chart()
    {
        $items = $this->datapulse_model->item_groups_chart();
        echo json_encode($items);
        die;
    }

    public function staff_by_departments_chart()
    {
        $items = $this->datapulse_model->staff_by_departments_chart();
        echo json_encode($items);
        die;
    }

    public function staff_assigned_projects_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->staff_assigned_projects_chart($year);
        echo json_encode($items);
        die;
    }

    public function customers_through_year_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->customers_through_year_chart($year);
        echo json_encode($items);
        die;
    }

    public function employee_through_year_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->employee_through_year_chart($year);
        echo json_encode($items);
        die;
    }

    public function expenses_on_categories_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->expenses_on_categories_chart($year);
        echo json_encode($items);
        die;
    }

    public function customers_by_group_chart()
    {
        $items = $this->datapulse_model->customers_by_group_chart();
        echo json_encode($items);
        die;
    }

    public function staff_logged_time_chart()
    {
        $this->load->model('staff_model');

        $selectedfilter = $_GET['selectedfilter'] ?? 'this_month';

        $staff_members = $this->staff_model->get();

        $labels = [];
        $datasets = [];

        $staff_ids = [];
        foreach ($staff_members as $staff) {
            $staff_ids[] = $staff['staffid'];
        }

        $logged_time_data = [];
        foreach ($staff_ids as $id) {
            $logged_time_data[$id] = $this->staff_model->get_logged_time_data($id);
        }

        foreach ($logged_time_data as $id => $data) {
            $logged_hours = seconds_to_time_format($data[$selectedfilter]);

            $staffData = $this->staff_model->get($id);
            $labels[] = $staffData->firstname .' '. $staffData->lastname;

            $datasets[] = $logged_hours;
        }

        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];

        echo json_encode($data);
        die;
    }

    public function added_tickets_by_project_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->added_tickets_by_project_chart($year);
        echo json_encode($items);
        die;
    }

    public function projects_based_on_customers_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->projects_based_on_customers_chart($year);
        echo json_encode($items);
        die;
    }

    public function estimate_assigned_agents_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->estimate_assigned_agents_chart($year);
        echo json_encode($items);
        die;
    }

    public function proposal_assigned_staff_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->proposal_assigned_staff_chart($year);
        echo json_encode($items);
        die;
    }

    public function invoices_stacked_by_customers_chart()
    {
        $year = $_GET['year'] ?? date('Y');
        $items = $this->datapulse_model->invoices_stacked_by_customers_chart($year);
        echo json_encode($items);
        die;
    }

}
