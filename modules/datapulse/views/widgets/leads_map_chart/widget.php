<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id('dp-leads-map-chart'); ?>"
     data-name="<?php echo _l('datapulse_leads_map_chart'); ?>">
    <?php if (staff_can('view', 'leads')) { ?>
        <div class="row" id="dp-customers-map-chart">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body padding-10">
                        <div class="widget-dragger"></div>

                        <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                            <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                    <!-- Globe Icon -->
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M8 16h.01M12 8v.01M12 16v.01M16 8v.01M16 12v.01M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20V12"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12a8 8 0 11-16 0 8 8 0 0116 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20c3.313 0 6-2.687 6-6s-2.687-6-6-6-6 2.687-6 6 2.687 6 6 6z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2V1M7 3.5a5.5 5.5 0 015.5-5.5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.22 7.22l-.71.71M2 12h1M4.22 16.78l-.71-.71M12 22v-1M19 12h1M19.78 16.78l-.71.71M21 12h-1M19.78 7.22l-.71-.71"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6"/>
                                </svg>

                                <span class="tw-text-neutral-700">
                                <?php echo _l('datapulse_leads_map_chart'); ?>
                            </span>
                            </p>
                        </div>
                        <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">
                        <div id="leads_regions_div" style="max-width: 100%; height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>