<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id('dp-estimate-assigned-agents-chart'); ?>"
     data-name="<?php echo _l('datapulse_estimate_assigned_agents'); ?>">
    <?php if (staff_can('view', 'estimates')) { ?>
        <div class="row" id="dp-estimate-assigned-agents-chart">
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
                                    <!-- Person Icon -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14a4 4 0 100-8 4 4 0 000 8z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 18v-2a4 4 0 00-8 0v2"/>
                                </svg>

                                <span class="tw-text-neutral-700">
                                <?php echo _l('datapulse_estimate_assigned_agents'); ?>
                            </span>
                            </p>
                            <div class="tw-divide-x tw-divide-solid tw-divide-neutral-300 tw-space-x-2 tw-flex tw-items-center">
                                <div class="dropdown pull-right mright10">
                                    <a href="#" class="dropdown-toggle tw-pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span id="dpEstimateAssignedAgentsChartYear" data-active-chart="weekly">
                                            <?php echo date('Y'); ?>
                                        </span>
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= 2016; $year--) { ?>
                                            <li>
                                                <a href="#" data-year="<?php echo $year; ?>" onclick="dpEstimateAssignedAgentsChart(this); return false;">
                                                    <?php echo $year; ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">
                        <canvas height="130" class="dp-estimate-assigned-agents-chart" id="dpEstimateAssignedAgentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>