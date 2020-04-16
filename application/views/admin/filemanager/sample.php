
<form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
      action="<?php echo base_url(); ?>admin/account/save_account/<?php
      if (!empty($account_info)) {
          echo $account_info->account_id;
      }
      ?>" method="post" class="form-horizontal  ">
    <div class="form-group">
        <label class="col-lg-3 control-label"><?= lang('account_name') ?> <span
                    class="text-danger">*</span></label>
        <div class="col-lg-5">
            <input type="text" class="form-control" value="<?php
            if (!empty($account_info)) {
                echo $account_info->account_name;
            }
            ?>" name="account_name" required="">
        </div>

    </div>
    <!-- End discount Fields -->
    <div class="form-group terms">
        <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
        <div class="col-lg-5">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($account_info)) {
                                echo $account_info->description;
                            }
                            ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"><?= lang('initial_balance') ?> <span
                    class="text-danger">*</span></label>
        <div class="col-lg-5">
            <input type="text" data-parsley-type="number" class="form-control" value="<?php
            if (!empty($account_info)) {
                echo $account_info->balance;
            }
            ?>" name="balance" required="">
        </div>

    </div>
    <div class="btn-bottom-toolbar text-right">
        <button type="submit"
                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
    </div>
</form>
<script type="text/javascript">
    $(function () {
        var params = {
            // Request parameters
            "national_id": "{19924919461000284}",
            "person_dob": "{1992-10-15}",
            "person_fullname": "{Muhammad Abdul Kahhar}",
        };
        console.log(params);
        $.ajax({
            url: "https://kyc24nme.azure-api.net/testkyc/check-person?" + $.param(params),
            beforeSend: function (xhrObj) {
                // Request headers
                xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key", "{d26643ff2f894ab99acbc16592064cec}");
            },
            type: "POST",
            // Request body
            data: "{body}",
        })
            .done(function (data) {
                alert("success");
            })
            .fail(function () {
                alert("error");
            });
    });
</script>