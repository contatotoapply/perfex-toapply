<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script src="<?php echo base_url();?>modules/mindmap/assets/js/mind-elexir.js"></script>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php $value = (isset($mindmap) ? $mindmap->mindmap_content : ''); ?>
            <textarea style="display: none" id="mindmap_content" name="mindmap_content"><?php echo $value;?></textarea>
            <div class="col-lg-12">
            	<div class="panel_s">
            		<div class="panel-body">
            			<h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php $value = (isset($mindmap) ? $mindmap->title : ''); ?>
                        <?php echo render_input('title','Title',$value,'',['disabled'=>'disabled']); ?>

                        <?php
                        $mmgroup = ($mindmap_group)?$mindmap_group->name:'';
                        echo render_input('mindmap_group_id','mindmap_group',$mmgroup,'',['disabled'=>'disabled']);
                        ?>

                        <?php $value = (isset($mindmap) ? $mindmap->description : ''); ?>
                        <?php echo render_textarea('description','Description',$value,array('rows'=>4, 'disabled'=>'disabled'),array()); ?>
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
                    <a href="<?php echo admin_url('mindmap'); ?>" class="btn btn-info mindmap-btn"><?php echo _l('Go Back'); ?></a>
                </div>
            </div>
        </div>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
$(function() {
    var mind = new MindElixir({
        el: '#map',
        direction: 2,
        data: ($('textarea#mindmap_content').val() != '')?JSON.parse($('textarea#mindmap_content').val()): MindElixir.new('new topic'),
        draggable: true,
        contextMenu: false,
        toolBar: true,
        nodeMenu: false,
        keypress: false,
    })
    mind.init();
});
</script>
<style type="text/css">
    .lt{width: 40px !important;}
    nmenu { border: 1px solid blue !important;}
</style>
</body>
</html>