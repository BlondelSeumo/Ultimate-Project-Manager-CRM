

<!---->
<!--<a type="button" class="btn btn-success btn-xs" href="--><?php //echo base_url() ?><!--admin/navigation/add_navigation"><i class="fa fa-pencil-square-o"></i> Add</a>-->
<!--<hr/>-->
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<div class="row">
    <div class="col-sm-12 std_print" data-spy="scroll" data-offset="0">                            
        <div class="panel panel-custom">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <div class="panel-title">
                    <strong>Manage Navigation</strong>
                </div>
            </div>
            <table class="table table-striped" id="Transation_DataTables">
                <thead>
                    <tr class="active" >
                        <th class="col-sm-1">SL</th>
                        <th>Label</th>
                        <th>Icon</th>
                        <th>Parent</th>                                             
                        <th>Sort</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (count($nav)): foreach ($nav as $v_nav) : ?>

                            <tr>
                                <td><?php echo $v_nav->menu_id ?></td>
                                <td><?php echo $v_nav->label ?></td>
                                <td><i class="<?php echo $v_nav->icon ?>"></i></td>
                                <td><?php echo $v_nav->parent ?></td>   
                                <td><?php echo $v_nav->sort ?></td> 
                                <td>
                                    <div class="btn-group" role="group" aria-label="...">
                                        <a type="button" class="btn btn-success btn-xs" href="<?php echo base_url() ?>admin/navigation/add_navigation/<?php echo $v_nav->menu_id ?>"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                        <a type="button" class="btn btn-danger btn-xs" href="<?php echo base_url() ?>admin/navigation/delete_navigation/<?php echo $v_nav->menu_id ?>"><i class="fa fa-times"></i> Delete</a>

                                    </div>
                                </td> 

                            </tr>
                            <?php
                        endforeach;
                        ?>
                    <?php else : ?>
                    <td colspan="3">
                        <strong>There is no Record for display!</strong>
                    </td>
                <?php endif; ?>
                </tbody>
            </table> 
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#Transation_DataTables').dataTable({
            paging: false,
            "bSort": false
        });
    });
</script>
<!-- end -->
