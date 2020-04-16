<!-- Content Header (Page header) -->
<?php echo message_box('success');
$all_customer_group = $this->db->order_by('customer_group_id', 'DESC')->get('tbl_customer_group')->result();
?>
<div class="row">
    <!-- Start Form -->

    <div class="col-sm-9">
        <form role="form" enctype="multipart/form-data" id="form"
              action="<?php echo base_url(); ?>client/settings/update_settings/<?php
              if (!empty($client_info)) {
                  echo $client_info->client_id;
              }
              ?>" method="post" class="form-horizontal  ">
            <section class="panel panel-custom">
                <header class="panel-heading  "><?= lang('settings') ?></header>
                <div class="panel-body">
                    <!--- /************ Company Start ***************/ --->
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#general_compnay"
                                                  data-toggle="tab"><?= lang('general') ?></a>
                            </li>
                            <li><a href="#contact_compnay"
                                   data-toggle="tab"><?= lang('client_contact') . ' ' . lang('details') ?></a>
                            </li>
                            <li><a href="#web_compnay" data-toggle="tab"><?= lang('web') ?></a></li>
                            <li><a href="#hosting_compnay" data-toggle="tab"><?= lang('hosting') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="chart tab-pane active" id="general_compnay">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('company_name') ?>
                                        <span
                                            class="text-danger"> *</span></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" required=""
                                               value="<?php
                                               if (!empty($client_info->name)) {
                                                   echo $client_info->name;
                                               }
                                               ?>" name="name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('company_email') ?>
                                        <span
                                            class="text-danger"> *</span></label>
                                    <div class="col-lg-5">
                                        <input type="email" class="form-control" required=""
                                               value="<?php
                                               if (!empty($client_info->email)) {
                                                   echo $client_info->email;
                                               }
                                               ?>" name="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_vat') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->vat)) {
                                            echo $client_info->vat;
                                        }
                                        ?>" name="vat">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-3 control-label"><?= lang('language') ?></label>
                                    <div class="col-sm-5">
                                        <select name="language" class="form-control select_box"
                                                style="width: 100%">
                                            <?php foreach ($languages as $lang) : ?>
                                                <option
                                                    value="<?= $lang->name ?>"<?php
                                                if (!empty($client_info->language) && $client_info->language == $lang->name) {
                                                    echo 'selected';
                                                } elseif (empty($client_info->language) && $this->config->item('language') == $lang->name) {
                                                    echo 'selected';
                                                } ?>
                                                ><?= ucfirst($lang->name) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('currency') ?></label>
                                    <div class="col-lg-5">
                                        <select name="currency" class="form-control select_box"
                                                style="width: 100%">

                                            <?php if (!empty($currencies)): foreach ($currencies as $currency): ?>
                                                <option
                                                    value="<?= $currency->code ?>"
                                                    <?php
                                                    if (!empty($client_info->currency) && $client_info->currency == $currency->code) {
                                                        echo 'selected';
                                                    } elseif (empty($client_info->currency) && $this->config->item('default_currency') == $currency->code) {
                                                        echo 'selected';
                                                    } ?>
                                                ><?= $currency->name ?></option>
                                                <?php
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('short_note') ?></label>
                                    <div class="col-lg-5">
                                            <textarea class="form-control" name="short_note"><?php
                                                if (!empty($client_info->short_note)) {
                                                    echo $client_info->short_note;
                                                }
                                                ?></textarea>
                                    </div>
                                </div>
                            </div><!-- ************** general *************-->

                            <!-- ************** Contact *************-->
                            <div class="chart tab-pane" id="contact_compnay">
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_phone') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->phone)) {
                                            echo $client_info->phone;
                                        }
                                        ?>" name="phone">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_mobile') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control"
                                               value="<?php
                                               if (!empty($client_info->mobile)) {
                                                   echo $client_info->mobile;
                                               }
                                               ?>" name="mobile">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_fax') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->fax)) {
                                            echo $client_info->fax;
                                        }
                                        ?>" name="fax">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_city') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->city)) {
                                            echo $client_info->city;
                                        }
                                        ?>" name="city">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_country') ?></label>
                                    <div class="col-lg-5">
                                        <select name="country" class="form-control select_box"
                                                style="width: 100%">
                                            <optgroup label="Default Country">
                                                <option
                                                    value="<?= $this->config->item('company_country') ?>"><?= $this->config->item('company_country') ?></option>
                                            </optgroup>
                                            <optgroup label="<?= lang('other_countries') ?>">
                                                <?php if (!empty($countries)): foreach ($countries as $country): ?>
                                                    <option
                                                        value="<?= $country->value ?>" <?= (!empty($client_info->country) && $client_info->country == $country->value ? 'selected' : NULL) ?>><?= $country->value ?>
                                                    </option>
                                                    <?php
                                                endforeach;
                                                endif;
                                                ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_address') ?></label>
                                    <div class="col-lg-5">
                                            <textarea class="form-control" name="address"><?php
                                                if (!empty($client_info->address)) {
                                                    echo $client_info->address;
                                                }
                                                ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label">
                                        <a href="#"
                                           onclick="fetch_lat_long_from_google_cprofile(); return false;"
                                           data-toggle="tooltip"
                                           data-title="<?php echo lang('fetch_from_google') . ' - ' . lang('customer_fetch_lat_lng_usage'); ?>"><i
                                                id="gmaps-search-icon" class="fa fa-google"
                                                aria-hidden="true"></i></a>
                                        <?= lang('latitude') . '( ' . lang('google_map') . ' )' ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->latitude)) {
                                            echo $client_info->latitude;
                                        }
                                        ?>" name="latitude">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('longitude') . '( ' . lang('google_map') . ' )' ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control"
                                               value="<?php
                                               if (!empty($client_info->longitude)) {
                                                   echo $client_info->longitude;
                                               }
                                               ?>" name="longitude">
                                    </div>
                                </div>
                            </div><!-- ************** Contact *************-->
                            <!-- ************** Web *************-->
                            <div class="chart tab-pane" id="web_compnay">
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('company_domain') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->website)) {
                                            echo $client_info->website;
                                        }
                                        ?>" name="website">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('skype_id') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->skype_id)) {
                                            echo $client_info->skype_id;
                                        }
                                        ?>" name="skype_id">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('facebook_profile_link') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->facebook)) {
                                            echo $client_info->facebook;
                                        }
                                        ?>" name="facebook">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('twitter_profile_link') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->twitter)) {
                                            echo $client_info->twitter;
                                        }
                                        ?>" name="twitter">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('linkedin_profile_link') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->linkedin)) {
                                            echo $client_info->linkedin;
                                        }
                                        ?>" name="linkedin">
                                    </div>
                                </div>
                            </div><!-- ************** Web *************-->
                            <!-- ************** Hosting *************-->
                            <div class="chart tab-pane" id="hosting_compnay">
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('hosting_company') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->hosting_company)) {
                                            echo $client_info->hosting_company;
                                        }
                                        ?>" name="hosting_company">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('hostname') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->hostname)) {
                                            echo $client_info->hostname;
                                        }
                                        ?>" name="hostname">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('username') ?> </label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->username)) {
                                            echo $client_info->username;
                                        }
                                        ?>" name="username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('password') ?></label>
                                    <div class="col-lg-5">
                                        <?php
                                        if (!empty($client_info->password)) {
                                            $password = strlen(decrypt($client_info->password));
                                        }
                                        ?>
                                        <input type="password" name="password" value="" placeholder="<?php
                                        if (!empty($password)) {
                                            for ($p = 1; $p <= $password; $p++) {
                                                echo '*';
                                            }
                                        }
                                        ?>" class="form-control">
                                        <strong id="show_password" class="required"></strong>
                                    </div>
                                    <div class="col-lg-3">
                                        <a data-toggle="modal" data-target="#myModal"
                                           href="<?= base_url('admin/client/see_password/c_' . $client_info->client_id) ?>"
                                           id="see_password"><?= lang('see_password') ?></a>
                                        <strong id="hosting_password" class="required"></strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('port') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" value="<?php
                                        if (!empty($client_info->port)) {
                                            echo $client_info->port;
                                        }
                                        ?>" name="port">
                                    </div>
                                </div>
                            </div><!-- ************** Hosting *************-->
                        </div>
                    </div><!-- /.nav-tabs-custom -->
                    <div class="form-group">
                        <label class="col-lg-3"></label>
                        <div class="col-lg-5">
                            <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                        </div>
                    </div>
            </section>
        </form>
    </div>
    <!-- End Form -->
</div>

<script type="text/javascript">
    function fetch_lat_long_from_google_cprofile() {
        var data = {};
        data.address = $('textarea[name="address"]').val();
        data.city = $('input[name="city"]').val();
        data.country = $('select[name="country"] option:selected').text();
        console.log(data);
        $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
        $.post('<?= base_url()?>admin/global_controller/fetch_address_info_gmaps', data).done(function (data) {
            data = JSON.parse(data);
            $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
            if (data.response.status == 'OK') {
                $('input[name="latitude"]').val(data.lat);
                $('input[name="longitude"]').val(data.lng);
            } else {
                if (data.response.status == 'ZERO_RESULTS') {
                    toastr.warning("<?php echo lang('g_search_address_not_found'); ?>");
                } else {
                    toastr.warning(data.response.status);
                }
            }
        });
    }
</script>