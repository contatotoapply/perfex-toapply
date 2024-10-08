<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$isGridView = 0;
if ($this->session->has_userdata('mindmap_grid_view') && $this->session->userdata('mindmap_grid_view') == 'true') {
    $isGridView = 1;
}
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="_filters _hidden_inputs hidden">
                        <?php
                        echo form_hidden('my_mindmap');
                        foreach($staffs as $staff){
                            echo form_hidden('staffid_'.$staff['staffid']);
                        }
                        foreach($groups as $group){
                            echo form_hidden('mindmap_group_id_'.$group['id']);
                        }

                        ?>
                    </div>

                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if(has_permission('mindmap','','create')){ ?>
                                <a href="<?php echo admin_url('mindmap/mindmap_create'); ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('mindmap_create_new'); ?></a>
                            <?php } ?>

                            <a href="<?php echo admin_url('mindmap/switch_grid/'.$switch_grid); ?>" class="btn btn-default hidden-xs">
                                <?php if($switch_grid == 1){ echo _l('mindmap_switch_to_list_view');}else{echo _l('mindmap_switch_to_grid_view');}; ?>
                            </a>
                            <div class="visible-xs">
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix mtop20"></div>
                        <div class="row" id="mindmap-table">
                            <?php if($isGridView ==0){ ?>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="bold"><?php echo _l('filter_by'); ?></p>
                                    </div>
                                    <?php if(has_permission('mindmap','','view')){ ?>
                                        <div class="col-md-3 mindmap-filter-column">
                                            <?php echo render_select('view_assigned',$staffs,array('staffid',array('firstname','lastname')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('mindmap_staff')),array(),'no-mbot'); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="col-md-3 mindmap-filter-column">
                                        <?php echo render_select('view_group',$groups,array('id',array('name')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('mindmap_group')),array(),'no-mbot'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="hr-panel-heading" />
                            <?php } ?>
                            <div class="col-md-12">
                        <?php if($this->session->has_userdata('mindmap_grid_view') && $this->session->userdata('mindmap_grid_view') == 'true') { ?>
                            <div class="grid-tab" id="grid-tab">
                                <div class="row">
                                    <div id="mindmap-grid-view" class="container-fluid">

                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <?php render_datatable(array(
                                _l('mindmap_title'),
                                _l('mindmap_desc'),
                                _l('mindmap_staff'),
                                _l('mindmap_group'),
                                _l('mindmap_created_at')
                            ),'mindmap', array('customizable-table'),
                              array(
                                  'id'=>'table-mindmap',
                                  'data-last-order-identifier'=>'mindmap',
                                  'data-default-order'=>get_table_last_order('mindmap'),
                              )); ?>
                        <?php } ?>
                        </div>
                        </div>

                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Mindmap Modal-->
<div class="modal fade mindmap-modal" id="mindmap-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content data">

        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url();?>modules/mindmap/assets/js/mind-elexir.js"></script>
<script>
    var _lnth = 12;
$(function(){
    var TblServerParams = {
        "assigned": "[name='view_assigned']",
        "group": "[name='view_group']",
    };

    if(<?php echo $isGridView ?> == 0) {
        var tAPI = initDataTable('.table-mindmap', admin_url+'mindmap/table', [2, 3], [2, 3], TblServerParams);

        $.each(TblServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                $('table.table-mindmap').DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });

    }else{
        loadGridView();

        $(document).off().on('click','a.paginate',function(e){
            e.preventDefault();
            console.log("$(this)", $(this).data('ci-pagination-page'))
            var pageno = $(this).data('ci-pagination-page');
            var formData = {
                search: $("input#search").val(),
                start: (pageno-1),
                length: _lnth,
                draw: 1
            }
            gridViewDataCall(formData, function (resposne) {
                $('div#grid-tab').html(resposne)
            })
        });
    }

     // initDataTable('.table-mindmap', admin_url + 'mindmap/table', [2, 3], [2, 3]);
     //    $('.table-goals').DataTable().on('draw', function () {
     //        var rows = $('.table-goals').find('tr');
     //        $.each(rows, function () {
     //            var td = $(this).find('td').eq(6);
     //            var percent = $(td).find('input[name="percent"]').val();
     //            $(td).find('.goal-progress').circleProgress({
     //                value: percent,
     //                size: 45,
     //                animation: false,
     //                fill: {
     //                    gradient: ["#28b8da", "#059DC1"]
     //                }
     //            })
     //        })
     //    });

});
</script>
</body>
</html>
