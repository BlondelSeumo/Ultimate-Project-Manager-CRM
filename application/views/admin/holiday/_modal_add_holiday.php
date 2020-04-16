
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('new_holiday') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form data-parsley-validate="" novalidate="" action="<?php echo base_url(); ?>admin/holiday/save_holiday/<?php
        if (!empty($holiday_list->holiday_id)) {
            echo $holiday_list->holiday_id;
        }
        ?>" method="post" class="form-horizontal form-groups-bordered">
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('event_name') ?>
                    <span class="required">*</span></label>

                <div class="col-sm-8">
                    <input required type="text" name="event_name" class="form-control" value="<?php
                    if (!empty($holiday_list->event_name)) {
                        echo $holiday_list->event_name;
                    }
                    ?>" id="field-1" placeholder="Enter Your <?= lang('event_name') ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('description') ?><span
                        class="required"> *</span></label>

                <div class="col-sm-8">
                <textarea required style="height: 100px" name="description" class="form-control " id="field-1"
                          placeholder="Enter Your Description"><?php
                    if (!empty($holiday_list->description)) {
                        echo $holiday_list->description;
                    }
                    ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('start_date') ?><span
                        class="required">*</span></label>
                <div class="col-sm-8">
                    <div class="input-group ">
                        <input required type="text" class="form-control start_date" name="start_date" value="<?php
                        if (!empty($holiday_list->start_date)) {
                            echo $holiday_list->start_date;
                        }
                        ?>">

                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('end_date') ?><span
                        class="required">*</span></label>
                <div class="col-sm-8">
                    <div class="input-group ">
                        <input required type="text" class="form-control end_date" name="end_date" value="<?php
                        if (!empty($holiday_list->end_date)) {
                            echo $holiday_list->end_date;
                        }
                        ?>">

                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('location') ?></label>

                <div class="col-sm-8">
                    <input type="text" name="location" class="form-control" value="<?php
                    if (!empty($holiday_list->location)) {
                        echo $holiday_list->location;
                    }
                    ?>" id="field-1" placeholder="Enter Your <?= lang('location') ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="field-1" class="col-sm-3 control-label"></label>

                <div class="color-palet col-sm-8">
                    <span style="background-color:#83c340" class="color-tag clickable active" data-color="#83c340"></span>
                    <span style="background-color:#29c2c2" class="color-tag clickable" data-color="#29c2c2"></span>
                    <span style="background-color:#2d9cdb" class="color-tag clickable" data-color="#2d9cdb"></span>
                    <span style="background-color:#aab7b7" class="color-tag clickable" data-color="#aab7b7"></span>
                    <span style="background-color:#f1c40f" class="color-tag clickable" data-color="#f1c40f"></span>
                    <span style="background-color:#e18a00" class="color-tag clickable" data-color="#e18a00"></span>
                    <span style="background-color:#e74c3c" class="color-tag clickable" data-color="#e74c3c"></span>
                    <span style="background-color:#d43480" class="color-tag clickable" data-color="#d43480"></span>
                    <span style="background-color:#ad159e" class="color-tag clickable" data-color="#ad159e"></span>
                    <span style="background-color:#34495e" class="color-tag clickable" data-color="#34495e"></span>
                    <span style="background-color:#dbadff" class="color-tag clickable" data-color="#dbadff"></span>
                    <span style="background-color:#f05050" class="color-tag clickable" data-color="#f05050"></span>
                    <input id="color" type="hidden" name="color" value="#83c340">
                </div>
            </div>
            <div class="form-group margin">
                <div class="col-sm-offset-3 col-sm-5">
                    <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('save') ?></button>
                </div>
            </div>

        </form>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".color-palet span").click(function () {
                $(".color-palet").find(".active").removeClass("active");
                $(this).addClass("active");
                $("#color").val($(this).attr("data-color"));
            });
        });
    </script>
