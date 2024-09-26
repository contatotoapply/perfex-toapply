<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datapulse_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function sales_by_saleman_weekly_chart($currency)
    {
        $all_payments = [];
        $has_permission_payments_view = has_permission('payments', '', 'view');
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE)');
        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Current week
        $all_payments[] = $this->db->get()->result_array();
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ');

        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Last Week
        $all_payments[] = $this->db->get()->result_array();

        $chart = [
            'labels' => get_weekdays(),
            'datasets' => [
                [
                    'label' => _l('this_week_payments'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor' => '#84c529',
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
                [
                    'label' => _l('last_week_payments'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor' => '#c53da9',
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
            ],
        ];


        for ($i = 0; $i < count($all_payments); $i++) {
            foreach ($all_payments[$i] as $payment) {
                $payment_day = date('l', strtotime($payment['date']));
                $x = 0;
                foreach (get_weekdays_original() as $day) {
                    if ($payment_day == $day) {
                        $chart['datasets'][$i]['data'][$x] += $payment['amount'];
                    }
                    $x++;
                }
            }
        }

        return $chart;
    }

    public function customers_map_chart()
    {
        $this->db->select('c.short_name, COUNT(cl.country) as total_customers');
        $this->db->from(db_prefix() . 'countries c');
        $this->db->join(db_prefix() . 'clients cl', 'cl.country = c.country_id', 'left');
        $this->db->group_by('c.short_name');

        $query = $this->db->get();

        return $query->result();
    }

    public function leads_map_chart()
    {
        $this->db->select('c.short_name, COUNT(cl.country) as total_leads');
        $this->db->from(db_prefix() . 'countries c');
        $this->db->join(db_prefix() . 'leads cl', 'cl.country = c.country_id', 'left');
        $this->db->group_by('c.short_name');

        $query = $this->db->get();

        return $query->result();
    }

    public function staff_assigned_to_leads($year)
    {
        $query = $this->db->select('MONTH(' . db_prefix() . 'leads.dateadded) as month, COUNT(*) as leads_count, ' . db_prefix() . 'leads.assigned, CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) AS staff_name, SUM(COUNT(*)) OVER (PARTITION BY ' . db_prefix() . 'leads.assigned) AS total_leads')
            ->from(db_prefix() . 'leads')
            ->join(db_prefix() . 'staff', db_prefix() . 'leads.assigned = ' . db_prefix() . 'staff.staffid')
            ->where(db_prefix() . 'leads.assigned !=', 0)
            ->where('YEAR(' . db_prefix() . 'leads.dateadded)', $year)
            ->group_by('MONTH(' . db_prefix() . 'leads.dateadded), ' . db_prefix() . 'leads.assigned')
            ->get();

        return $query->result();
    }

    public function item_groups_chart()
    {
        $query = $this->db->select(db_prefix() . 'items.*, IFNULL(' . db_prefix() . 'items_groups.name, "No Category") AS group_name')
            ->from(db_prefix() . 'items')
            ->join(db_prefix() . 'items_groups', db_prefix() . 'items.group_id = ' . db_prefix() . 'items_groups.id', 'left')
            ->get();
        return $query->result();
    }

    public function staff_by_departments_chart()
    {
        $query = $this->db->select(db_prefix() . 'departments.name AS department_name, COUNT(' . db_prefix() . 'staff_departments.staffid) AS total_active_staff')
            ->from(db_prefix() . 'departments')
            ->join(db_prefix() . 'staff_departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'staff_departments.departmentid')
            ->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'staff_departments.staffid')
            ->where(db_prefix() . 'staff.active', 1)
            ->group_by(db_prefix() . 'departments.name')
            ->get();
        return $query->result();
    }

    public function staff_assigned_projects_chart($year)
    {
        $query = $this->db->select(db_prefix() . 'projects.name AS project_name, COUNT(' . db_prefix() . 'project_members.staff_id) AS total_staff_members')
            ->from(db_prefix() . 'projects')
            ->join(db_prefix() . 'project_members', db_prefix() . 'projects.id = ' . db_prefix() . 'project_members.project_id')
            ->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id')
            ->where('YEAR(' . db_prefix() . 'projects.project_created)', $year)
            ->group_by(db_prefix() . 'projects.name')
            ->get();
        return $query->result();
    }

    public function customers_through_year_chart($year)
    {
        $query = $this->db->select('MONTH(datecreated) AS month, COUNT(*) AS total_customers')
            ->from(db_prefix() . 'clients')
            ->where('YEAR(datecreated)', $year)
            ->group_by('MONTH(datecreated)')
            ->get();
        return $query->result();
    }

    public function employee_through_year_chart($year)
    {
        $query = $this->db->select('MONTH(datecreated) AS month, COUNT(*) AS total_staff')
            ->from(db_prefix() . 'staff')
            ->where('YEAR(datecreated)', $year)
            ->group_by('MONTH(datecreated)')
            ->get();
        return $query->result();
    }

    public function expenses_on_categories_chart($year)
    {
        $query = $this->db->select(db_prefix() . 'expenses_categories.name AS category_name, SUM(amount) AS total_amount')
            ->from(db_prefix() . 'expenses')
            ->join(db_prefix() . 'expenses_categories', db_prefix() . 'expenses.category = ' . db_prefix() . 'expenses_categories.id')
            ->where('YEAR(date)', $year)
            ->group_by(db_prefix() . 'expenses.category')
            ->get();

        return $query->result();
    }

    public function customers_by_group_chart()
    {
        $query = $this->db->select(db_prefix() . 'customers_groups.name AS group_name, COUNT(' . db_prefix() . 'customer_groups.customer_id) AS customer_count')
            ->from(db_prefix() . 'customer_groups')
            ->join(db_prefix() . 'customers_groups', db_prefix() . 'customer_groups.groupid = ' . db_prefix() . 'customers_groups.id')
            ->group_by(db_prefix() . 'customers_groups.name')
            ->get();

        return $query->result();
    }

    public function added_tickets_by_project_chart($year)
    {
        $this->db->select('p.name AS project_name, MONTH(t.date) AS month, COUNT(t.ticketid) AS ticket_count', false);
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'tickets t', 'p.id = t.project_id', 'left');
        $this->db->where('YEAR(t.date)', $year);
        $this->db->group_by('p.id, MONTH(t.date)');
        $query = $this->db->get();

        $result = $query->result();

        $projects = [];
        foreach ($result as $row) {
            $projectName = $row->project_name;
            if (!isset($projects[$projectName])) {
                $projects[$projectName] = array_fill(1, 12, 0);
            }
            $projects[$projectName][$row->month] = $row->ticket_count;
        }

        $finalResult = [];
        foreach ($projects as $projectName => $monthlyCounts) {
            foreach ($monthlyCounts as $month => $count) {
                $finalResult[] = (object) [
                    'project_name' => $projectName,
                    'month' => $month,
                    'ticket_count' => $count
                ];
            }
        }

        return $finalResult;
    }

    public function projects_based_on_customers_chart($year)
    {
        $query = $this->db->select('c.company AS client_name, COUNT(p.id) AS project_count')
            ->from(db_prefix() . 'clients c')
            ->join(db_prefix() . 'projects p', 'c.userid = p.clientid', 'left')
            ->where('YEAR(p.start_date)', $year)
            ->group_by('c.userid')
            ->get();

        return $query->result();
    }

    public function estimate_assigned_agents_chart($year)
    {
        $query = $this->db->select('CONCAT(s.firstname, " ", s.lastname) AS agent_name, MONTH(e.date) AS month, COUNT(e.id) AS estimate_count')
            ->from(db_prefix() . 'estimates e')
            ->join(db_prefix() . 'staff s', 'e.sale_agent = s.staffid', 'left')
            ->where('YEAR(e.date)', $year)
            ->group_by('e.sale_agent, MONTH(e.date)')
            ->get();

        return $query->result();
    }

    public function proposal_assigned_staff_chart($year)
    {
        $query = $this->db->select('CONCAT(s.firstname, " ", s.lastname) AS staff_name, MONTH(p.date) AS month, COUNT(p.id) AS proposal_count')
            ->from(db_prefix() . 'proposals p')
            ->join(db_prefix() . 'staff s', 'p.assigned = s.staffid', 'left')
            ->where('YEAR(p.date)', $year)
            ->group_by('p.assigned, MONTH(p.date)')
            ->get();

        return $query->result();
    }

    public function invoices_stacked_by_customers_chart($year)
    {
        $query = $this->db->select('c.company AS customer_name, MONTH(i.date) AS month, SUM(i.total) AS total_sum')
            ->from(db_prefix() . 'invoices i')
            ->join(db_prefix() . 'clients c', 'i.clientid = c.userid', 'left')
            ->where('YEAR(i.date)', $year)
            ->group_by('i.clientid, MONTH(i.date)')
            ->get();

        return $query->result();
    }

}
