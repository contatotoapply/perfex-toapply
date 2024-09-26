<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id('dp-projects-based-on-customers-chart'); ?>"
     data-name="<?php echo _l('datapulse_projects_based_on_customers'); ?>">
    <?php if (staff_can('view', 'projects')) { ?>
        <div class="row" id="dp-item-groups-chart">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body padding-10">
                        <div class="widget-dragger"></div>

                        <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                            <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                    <!-- Document Icon -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10h-2M13 14h-2M7 10H5M7 14H5M17 10h-2M17 14h-2"/>
                                    <!-- Building Icon -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7.5v9.25A2.75 2.75 0 004.75 19H7v-2h2v2h6v-2h2v2h2.25A2.75 2.75 0 0022 16.75V7.5L12 2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v17"/>
                                </svg>


                                <span class="tw-text-neutral-700">
                                <?php echo _l('datapulse_projects_based_on_customers'); ?>
                            </span>
                            </p>
                            <div class="tw-divide-x tw-divide-solid tw-divide-neutral-300 tw-space-x-2 tw-flex tw-items-center">
                                <div class="dropdown pull-right mright10">
                                    <a href="#" class="dropdown-toggle tw-pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span id="projectsBasedOnCustomersChartYear" data-active-chart="weekly">
                                            <?php echo date('Y'); ?>
                                        </span>
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= 2016; $year--) { ?>
                                            <li>
                                                <a href="#" data-year="<?php echo $year; ?>" onclick="dpProjectsBasedOnCustomersChart(this); return false;">
                                                    <?php echo $year; ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">
                        <canvas height="130" class="dp-projects-based-on-customers-chart" id="projectsBasedOnCustomersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>