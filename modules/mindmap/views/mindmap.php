<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script src="<?php echo base_url();?>modules/mindmap/assets/js/mind-elexir.js"></script>
<div id="wrapper">
    <div class="content">
        <div class="row">
        	<?php
            if(isset($mindmap)){
                echo form_hidden('is_edit','true');
            }
            ?>
            <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'mindmap-form')) ;?>
            <?php echo render_input('staffid','', get_staff_user_id(), 'hidden'); ?>
            <?php $value = (isset($mindmap) ? $mindmap->mindmap_content : ''); ?>
            <textarea style="display: none" id="mindmap_content" name="mindmap_content"><?php echo $value;?></textarea>
            <div class="col-lg-12">
            	<div class="panel_s">
            		<div class="panel-body">
            			<h4 class="no-margin"><?php echo _l('mindmap_create_new'); ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php $value = (isset($mindmap) ? $mindmap->title : ''); ?>
                        <?php echo render_input('title','Title',$value); ?>

                        <?php
                        $selected = (isset($mindmap) ? $mindmap->mindmap_group_id : '');
                        if(is_admin() || get_option('staff_members_create_inline_mindmap_group') == '1'){
                            echo render_select_with_input_group('mindmap_group_id',$mindmap_groups,array('id','name'),'mindmap_group',$selected,'<a href="#" onclick="new_group();return false;"><i class="fa fa-plus"></i></a>');
                        } else {
                            echo render_select('mindmap_group_id',$mindmap_groups,array('id','name'),'mindmap_group',$selected);
                        }
                        ?>

                        <?php $value = (isset($mindmap) ? $mindmap->description : ''); ?>
                        <?php echo render_textarea('description','Description',$value,array('rows'=>4),array()); ?>
            		</div>
            	</div>
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('mindmap'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-12">
                                <div id="map"></div>
                                <style>
                                    #map {
                                        height: 500px;
                                        width: 100%;
                                    }
                                </style>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="btn-bottom-toolbar text-right">
                    <button type="button" class="btn btn-info mindmap-btn"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>
<?php $this->load->view('mindmap/mindmap_group'); ?>
<?php init_tail(); ?>
<script type="text/javascript">
$(function() {
    $(document).off('keypress.shortcuts keydown.shortcuts keyup.shortcuts');

    var mind = new MindElixir({
        el: '#map',
        direction: 2,
        data: ($('textarea#mindmap_content').val() != '')?JSON.parse($('textarea#mindmap_content').val()): MindElixir.new('new topic'),
        draggable: true,
        contextMenu: true,
        toolBar: true,
        nodeMenu: true,
        keypress: true,
    })
    mind.init();

    $("button.mindmap-btn").on('click', function (e) {
        $('textarea#mindmap_content').val(mind.getAllDataString());
        $('#mindmap-form').submit();
    })
    validate_mindmap_form();
    // get a node
    //E('node-id');
});

function validate_mindmap_form(){
    appValidateForm($('#mindmap-form'), {
        title: 'required',
        mindmap_group_id: 'required',
        description : 'required',
    });
}
</script>
<style type="text/css">
	.lt{width: 40px !important;}
    nmenu { border: 1px solid blue !important;}
</style>
</body>
</html>