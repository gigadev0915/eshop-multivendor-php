<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Add Delivery Boy</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Delivery Boy</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <!-- form start -->
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/delivery_boys/add_delivery_boy'); ?>" method="POST" id="add_product_form">
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                                <input type="hidden" name="edit_delivery_boy" value="<?= $fetched_data[0]['id'] ?>">
                            <?php
                            } ?>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" placeholder="Delivery Boy Name" name="name" value="<?= @$fetched_data[0]['username'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">Mobile <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" maxlength="16" oninput="validateNumberInput(this)" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= @$fetched_data[0]['mobile'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= @$fetched_data[0]['email'] ?>">
                                    </div>
                                </div>
                                <?php
                                if (!isset($fetched_data[0]['id'])) {
                                ?>
                                    <div class="form-group row ">
                                        <label for="password" class="col-sm-2 col-form-label">Password <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password" value="<?= @$fetched_data[0]['password'] ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label for="confirm_password" class="col-sm-2 col-form-label">Confirm Password <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="confirm_password" placeholder="Enter Confirm Password" name="confirm_password">
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="address" placeholder="Enter Address" name="address" value="<?= @$fetched_data[0]['address'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <?php
                                    $bonus_type = ['fixed_amount_per_order_item', 'percentage_per_order_item'];
                                    ?>
                                    <label for="bonus_type" class="col-sm-2 control-label">Bonus Types <span class='text-danger text-sm'> * </span></label>
                                    <div class="col-sm-10">
                                        <select name="bonus_type" class="form-control bonus_type">
                                            <option value=" ">Select Types</option>
                                            <?php foreach ($bonus_type as $row) { ?>
                                                <option value="<?= $row ?>" <?= (isset($fetched_data[0]['id']) &&  $fetched_data[0]['bonus_type'] == $row) ? "Selected" : "" ?>><?= ucwords(str_replace('_', ' ', $row)) ?></option>
                                            <?php
                                            } ?>
                                        </select>
                                        <?php ?>
                                    </div>
                                </div>
                                <div class="form-group row fixed_amount_per_order <?= (isset($fetched_data[0]['id'])  && $fetched_data[0]['bonus_type'] == 'fixed_amount_per_order_item') ? '' : 'd-none' ?>">
                                    <label for="bonus" class="col-sm-2 col-form-label">Amount <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="bonus_amount" placeholder="Enter amount to be given to the delivery boy on successful order delivery" name="bonus_amount" value="<?= @$fetched_data[0]['bonus'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row percentage_per_order <?= (isset($fetched_data[0]['id'])  && $fetched_data[0]['bonus_type'] == 'percentage_per_order_item') ? '' : 'd-none' ?>">
                                    <label for="bonus" class="col-sm-2 col-form-label">Bonus(%) <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="bonus_percentage" placeholder="Enter Bonus(%) to be given to the delivery boy on successful order delivery" name="bonus_percentage" value="<?= @$fetched_data[0]['bonus'] ?>">
                                    </div>
                                </div>
                                <?php
                                $pincode_wise_deliverability = (isset($system_settings['pincode_wise_deliverability']) && $system_settings['pincode_wise_deliverability'] == 1) ? $system_settings['pincode_wise_deliverability'] : '0';
                                $city_wise_deliverability = (isset($system_settings['city_wise_deliverability']) && $system_settings['city_wise_deliverability'] == 1) ? $system_settings['city_wise_deliverability'] : '0';
                                ?>

                                <input type="hidden" name="city_wise_deliverability" value="<?= $city_wise_deliverability ?>">
                                <input type="hidden" name="pincode_wise_deliverability" value="<?= $pincode_wise_deliverability ?>">
                                <div class="form-group row">
                                    <?php if ((isset($system_settings['pincode_wise_deliverability']) && $system_settings['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) { ?>
                                        <!-- <div class="form-group "> -->
                                        <label for="serviceable_zipcodes" class="col-form-label col-sm-2">Serviceable Zipcodes <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <?php
                                            $zipcodes = (isset($fetched_data[0]['serviceable_zipcodes']) &&  $fetched_data[0]['serviceable_zipcodes'] != NULL) ? explode(",", $fetched_data[0]['serviceable_zipcodes']) : [];
                                            $zipcodes_name = fetch_details('zipcodes', "", 'zipcode,id', "", "", "", "", "zipcode", $zipcodes);
                                            // echo "<pre>";
                                            // print_r($fetched_data[0]);
                                            // print_r($zipcodes);
                                            // print_r($zipcodes_name);
                                            ?>
                                            <select name="serviceable_zipcodes[]" class="search_zipcode form-control w-100" multiple onload="multiselect()" id="deliverable_zipcodes">
                                                <?php if (isset($zipcodes) && !empty($zipcodes)) {
                                                    foreach ($zipcodes_name as $row) {
                                                ?>
                                                        <option value="<?= $row['id'] ?>" <?= (!empty($zipcodes) && in_array($row['zipcode'], $zipcodes)) ? 'selected' : ''; ?>><?= $row['zipcode'] ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>

                                    <?php  }
                                    if (isset($system_settings['city_wise_deliverability']) && $system_settings['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) { ?>
                                        <!-- <div class="form-group"> -->
                                        <label for="cities" class="col-form-label col-sm-2">Serviceable Cities <span class='text-danger text-sm'>*</span></label>
                                        <?php
                                        $selected_city_ids = (isset($fetched_data[0]['serviceable_cities']) &&  $fetched_data[0]['serviceable_cities'] != NULL) ? explode(",", $fetched_data[0]['serviceable_cities']) : [];
                                        // echo "<pre>";
                                        // print_r($selected_city_ids);
                                        ?>
                                        <div class="col-sm-10">

                                            <select class="form-control city_list " name="serviceable_cities[]" id="deliverable_cities" multiple>
                                                <?php foreach ($cities as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= (in_array($row['id'], $selected_city_ids)) ? 'selected' : ''; ?>><?= $row['name'] ?></option>
                                                <?php }; ?>
                                            </select>
                                        </div>
                                    <?php } ?>

                                </div>
                                <div class="form-group row">
                                    <label for="driving_license" class="col-sm-2 col-form-label">Driving License <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <?php if (isset($fetched_data[0]['driving_license']) && !empty($fetched_data[0]['driving_license'])) { ?>
                                            <span class="text-danger">*Leave blank if there is no change</span>
                                        <?php } else { ?>
                                            <span class="text-danger">*Add Driving License's front and back image(select multiple)</span>
                                        <?php } ?>
                                        <input type="file" class="form-control" name="driving_license[]" id="driving_license" accept="image/*" multiple />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <?php
                                    if (isset($fetched_data[0]['driving_license']) && !empty($fetched_data[0]['driving_license'])) {
                                        $images = explode(",", $fetched_data[0]['driving_license']);
                                        // print_r($images);
                                        foreach ($images as $row) { ?>
                                            <label class="col-sm-2 col-form-label"></label>
                                            <div class="mx-auto col-sm-10 driving-license-image">
                                                <a href="<?= base_url($row); ?>" data-toggle="lightbox" data-gallery="gallery_seller">
                                                    <img src="<?= base_url($row); ?>" class="img-fluid rounded">
                                                </a>
                                            </div>
                                    <?php
                                        }
                                    } ?>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Status <span class='text-danger text-sm'>*</span></label>
                                    <div id="status" class="btn-group">
                                        <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="1" <?= (isset($fetched_data[0]['status']) && $fetched_data[0]['status'] == '1') ? 'Checked' : '' ?>> Approved
                                        </label>
                                        <label class="btn btn-danger" data-toggle-class="btn-danger" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="0" <?= (isset($fetched_data[0]['status']) && $fetched_data[0]['status'] == '0') ? 'Checked' : '' ?>> Not-Approved
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Delivery Boy' : 'Add Delivery Boy' ?></button>
                                </div>
                            </div>

                            <!-- /.card-footer -->
                        </form>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>