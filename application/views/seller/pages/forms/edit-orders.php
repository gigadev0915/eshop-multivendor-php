<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Order</h4>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('seller/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <!-- modal for send digital product -->
                <div id="sendMailModal" class="modal fade editSendMail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg ">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Manage Digital Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body ">
                                <form class="form-horizontal form-submit-event" id="digital_product_management" action="<?= base_url('seller/orders/send_digital_product'); ?>" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <input type="hidden" name="order_id" value="<?= $order_detls[0]['order_id'] ?>">
                                        <input type="hidden" name="order_item_id" value="<?= $this->input->get('edit_id') ?>">
                                        <input type="hidden" name="username" value="<?= $order_detls[0]['uname']  ?>">
                                        <div class="row form-group">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="product_name">Customer Email-ID </label>
                                                    <input type="text" class="form-control" id="email" name="email" value="<?= $order_detls[0]['user_email'] ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="product_name">Subject </label>
                                                    <input type="text" class="form-control" id="subject" placeholder="Enter Subject for email" name="subject" value="">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="product_name">Message </label>
                                                    <textarea type="text" class="form-control textarea" rows="6" id="message" placeholder="Message for Email" name="message"><?= isset($product_details[0]['short_description']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $product_details[0]['short_description'])) : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2" id="digital_media_container">
                                                <label for="image" class="ml-2">File <span class='text-danger text-sm'>*</span></label>
                                                <div class='col-md-6'><a class="uploadFile img btn btn-primary text-white btn-sm" data-input='pro_input_file' data-isremovable='1' data-media_type='archive,document' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>
                                                <div class="container-fluid row image-upload-section">
                                                    <div class="col-md-6 col-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success mt-3" value="Save"><?= labels('send_mail', 'Send Mail') ?></button>
                                    </div>
                                </form>
                            </div>
                            <!-- <div class="d-flex justify-content-center">
                                <div class="form-group" id="error_box">
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- modal for order tracking -->
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="transaction_modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="user_name">Order Tracking</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <!-- form start -->
                                            <form class="form-horizontal " id="order_tracking_form" action="<?= base_url('seller/orders/update-order-tracking/'); ?>" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="order_id" id="order_id">
                                                <input type="hidden" name="order_item_id" id="order_item_id">
                                                <input type="hidden" name="seller_id" id="seller_id">
                                                <div class="card-body pad">
                                                    <div class="form-group ">
                                                        <label for="courier_agency">Courier Agency</label>
                                                        <input type="text" class="form-control" name="courier_agency" id="courier_agency" placeholder="Courier Agency" />
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="tracking_id">Tracking Id</label>
                                                        <input type="text" class="form-control" name="tracking_id" id="tracking_id" placeholder="Tracking Id" />
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="url">URL</label>
                                                        <input type="text" class="form-control" name="url" id="url" placeholder="URL" />
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="reset" class="btn btn-warning">Reset</button>
                                                        <button type="submit" class="btn btn-success" id="submit_btn">Save</button>
                                                    </div>
                                                </div>
                                                <!-- <div class="d-flex justify-content-center">
                                                    <div class="form-group" id="error_box">
                                                    </div>
                                                </div> -->
                                                <!-- /.card-body -->
                                            </form>
                                        </div>
                                        <!--/.card-->
                                    </div>
                                    <!--/.col-md-12-->
                                </div>
                                <!-- /.row -->

                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- modal for create shiprocket order -->
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="order_parcel_modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Create Shipprocket Order Parcel</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <!-- form start -->
                                            <form class="form-horizontal " id="shiprocket_order_parcel_form" action="" method="POST">

                                                <?php
                                                $total_items = count($items);
                                                ?>
                                                <div class="card-body pad">
                                                    <div class="form-group">
                                                        <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                                        <input type="hidden" id="order_id" name="order_id" value="<?php print_r($order_detls[0]['id']); ?>" />
                                                        <input type="hidden" name="user_id" id="user_id" value="<?php echo $order_detls[0]['user_id']; ?>" />
                                                        <input type="hidden" name="total_order_items" id="total_order_items" value="<?php echo $total_items; ?>" />
                                                        <input type="hidden" name="shiprocket_seller_id" value="" />
                                                        <input type="hidden" name="fromseller" value="1" id="fromseller" />
                                                        <textarea id="order_items" name="order_items[]" hidden><?= json_encode($items, JSON_FORCE_OBJECT); ?></textarea>
                                                    </div>
                                                    <div class="mt-1 p-2 bg-danger text-white rounded">
                                                        <p><b>Note:</b> Make your pickup location associated with the order is verified from <a href="https://app.shiprocket.in/company-pickup-location?redirect_url=" target="_blank" style="text-decoration: underline;color: white;"> Shiprocket Dashboard </a> and then in <a href="<?php base_url('admin/Pickup_location/manage-pickup-locations'); ?>" target="_blank" style="text-decoration: underline;color: white;"> admin panel </a>. If it is not verified you will not be able to generate AWB later on.</p>
                                                    </div>
                                                    <div class="form-group row mt-4">
                                                        <div class="col-4">
                                                            <label for="txn_amount">Pickup location</label>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" name="pickup_location" id="pickup_location" placeholder="Pickup Location" value="" readonly />
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mt-3">
                                                        <div class="col-6">
                                                            <label for="txn_amount">Total Weight of Box</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mt-4">
                                                        <div class="col-3">
                                                            <label for="parcel_weight" class="control-label col-md-12">Weight <small>(kg)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_weight" placeholder="Parcel Weight" id="parcel_weight" value="" step=".01">
                                                        </div>
                                                        <div class="col-3">
                                                            <label for="parcel_height" class="control-label col-md-12">Height <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_height" placeholder="Parcel Height" id="parcel_height" value="" min="1">
                                                        </div>
                                                        <div class="col-3">
                                                            <label for="parcel_breadth" class="control-label col-md-12">Breadth <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_breadth" placeholder="Parcel Breadth" id="parcel_breadth" value="" min="1">
                                                        </div>
                                                        <div class="col-3">
                                                            <label for="parcel_length" class="control-label col-md-12">Length <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_length" placeholder="Parcel Length" id="parcel_length" value="" min="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success create_shiprocket_parcel">Create Order</button>
                                                </div>

                                                <!-- <div class="d-flex justify-content-center">
                                                    <div class="form-group" id="error_box">
                                                    </div>
                                                </div> -->
                                                <!-- /.card-body -->

                                            </form>
                                        </div>
                                        <!--/.card-->
                                    </div>
                                    <!--/.col-md-12-->
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-body">
                            <table class="table">
                                <?php
                                $mobile_data = fetch_details('addresses', ['id' => $order_detls[0]['address_id']], 'mobile');
                                ?>
                                <?php $this->load->model('Order_model'); ?>
                                <tr>
                                    <input type="hidden" name="hidden" id="order_id" value="<?php echo $order_detls[0]['id']; ?>">

                                    <th class="w-10px">ID</th>
                                    <td><?php echo $order_detls[0]['id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Name</th>
                                    <td><?php echo $order_detls[0]['uname']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Email</th>
                                    <td>
                                        <?php if (isset($order_detls[0]['email']) && !empty($order_detls[0]['email']) && $order_detls[0]['email'] != "" && $order_detls[0]['email'] != " ") {
                                            echo ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) || ($this->ion_auth->is_seller() && get_seller_permission($seller_id, 'customer_privacy') == false)) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email'];
                                        } ?>
                                    </td>
                                </tr>
                                <?php if ($order_detls[0]['mobile'] != '' && isset($order_detls[0]['mobile'])) {
                                ?>
                                    <tr>
                                        <th class="w-10px">Contact</th>
                                        <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3) : $order_detls[0]['mobile']; ?>
                                        </td>
                                    </tr>

                                <?php  } else {
                                ?>
                                    <tr>
                                        <th class="w-10px">Contact</th>
                                        <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($mobile_data[0]['mobile']) - 3) . substr($mobile_data[0]['mobile'], -3) : $mobile_data[0]['mobile']; ?>
                                        </td>
                                    </tr>
                                <?php
                                } ?>

                                <?php if (!empty($order_detls[0]['notes'])) { ?>
                                    <tr>
                                        <th class="w-15px">Order note</th>
                                        <td><?php echo  $order_detls[0]['notes']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th class="w-10px">Items</th>

                                    <td>
                                        <form id="update_form">
                                            <input type="hidden" name="order_id" value="<?= $order_detls[0]['order_id'] ?>">
                                            <!-- <input type="hidden" name="seller_id" value="<?= $items[0]['seller_id'] ?>"> -->
                                            <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] == 'digital_product') { ?>
                                                <div class="row">
                                                    <div class="col-md-12 mb-2">
                                                        <lable class="badge badge-success">Select status which you want to update</lable>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select name="status" class="form-control status">
                                                            <option value=''>Select Status</option>
                                                            <option value="delivered">Delivered</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <a href="javascript:void(0);" title="Bulk Update" data-seller_id="<?= $items[0]['seller_id'] ?>" class="btn btn-primary col-sm-12 col-md-12 update_status_admin_bulk mr-1">
                                                            Update
                                                        </a>
                                                    </div>
                                                </div>
                                                <p>
                                                    <lable class="badge badge-warning mt-2" style="font-size:13px;">Note : Select square box of item only when you want to update it as cancelled or returned.</lable>
                                                </p>
                                            <?php } else { ?>
                                                <div class="row">
                                                    <div class="col-md-12 mb-2">
                                                        <lable class="badge badge-success">Select status <?= get_seller_permission($seller_id, 'assign_delivery_boy') ? 'and delivery boy' : '' ?> which you want to update</lable>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <select name="status" class="form-control status">
                                                            <option value=''>Select Status</option>
                                                            <option value="received">Received</option>
                                                            <option value="processed">Processed</option>
                                                            <?php if (get_seller_permission($seller_id, 'assign_delivery_boy')) {
                                                            ?>
                                                                <option value="shipped">Shipped</option>
                                                            <?php } ?>
                                                            <?php if (get_seller_permission($seller_id, 'view_order_otp') == true) { ?>
                                                                <option value="delivered">Delivered</option>
                                                            <?php } ?>
                                                            <option value="cancelled">Cancel</option>
                                                            <option value="returned">Returned</option>
                                                        </select>
                                                    </div>
                                                    <?php if (get_seller_permission($seller_id, 'assign_delivery_boy')) {
                                                    ?>
                                                        <div class="col-md-3">
                                                            <select id='deliver_by' name='deliver_by' class='form-control'>
                                                                <option value=''>Select Delivery Boy</option>
                                                                <?php foreach ($delivery_res as $row) { ?>
                                                                    <option value="<?= $row['user_id'] ?>"><?= $row['username'] ?></option>
                                                                <?php  } ?>
                                                            </select>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-md-6">
                                                        <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xl col-md-1" title="Order Tracking" data-order_id=' <?= $order_detls[0]['id']; ?>' data-seller_id="<?= $items[0]['seller_id'] ?>" data-target="#transaction_modal" data-toggle="modal" style="height:35px;width:38px;"><i class="fa fa-map-marker-alt"></i></a>
                                                        <a href="javascript:void(0);" title="Bulk Update" data-seller_id="<?= $items[0]['seller_id'] ?>" class="btn btn-primary col-md-3 ml-1 update_status_admin_bulk">
                                                            Update
                                                        </a>
                                                        <?php if ($shipping_method['shiprocket_shipping_method'] == 1) { ?>
                                                            <button type="button" disabled class="btn btn-primary ml-1 col-md-6 create_shiprocket_order" data-target="#order_parcel_modal" data-toggle="modal"> Create Shiprocket Order</button>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <p>
                                                    <lable class="badge badge-warning mt-4" style="font-size:13px;">Note : Select square box of item only when you want to update it as cancelled or returned.</lable>
                                                </p>
                                                <?php if ($shipping_method['shiprocket_shipping_method'] == 1) { ?>
                                                    <p>
                                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#ShiprocketOrderFlow">How to manage shiprocket order </button>
                                                    </p>
                                                <?php } ?>

                                                <?php
                                                if (get_seller_permission($seller_id, 'view_order_otp') == true) {
                                                    if ($items[0]['item_otp'] != 0) { ?>
                                                        <p><span class="text-bold">Item OTP : </span><span class="badge badge-warning"><?= $items[0]['item_otp']; ?></span></p>
                                                    <?php } elseif ($items[0]['seller_otp'] != 0) { ?>
                                                        <p><span class="text-bold">Item OTP : </span><span class="badge badge-warning"><?= $items[0]['seller_otp']; ?></span></p>
                                                <?php }
                                                } ?>
                                            <?php } ?>
                                            <?php

                                            $seller_order = $this->Order_model->get_order_details(['o.id' => $order_detls[0]['order_id'], 'oi.seller_id' => $this->session->userdata('user_id')]);
                                            $pickup_location = array_values(array_unique(array_column($seller_order, "pickup_location")));

                                            for ($j = 0; $j < count($pickup_location); $j++) {

                                                $ids = "";
                                                foreach ($seller_order as $row) {

                                                    if ($row['pickup_location'] == $pickup_location[$j]) {
                                                        $ids .= $row['order_item_id'] . ',';
                                                    }
                                                }
                                                $order_item_ids = explode(',', trim($ids, ','));
                                                $order_tracking_data = get_shipment_id($order_item_ids[0], $order_detls[0]['order_id']);
                                                $shiprocket_order = get_shiprocket_order($order_tracking_data[0]['shiprocket_order_id']);
                                                foreach ($order_item_ids as $id) {
                                                    $active_status = fetch_details('order_items', ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], 'active_status')[0]['active_status'];

                                                    if ($shiprocket_order['data']['status'] == 'PICKUP SCHEDULED' &&  $active_status != 'shipped') {
                                                        $this->Order_model->update_order(['active_status' => 'shipped'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                                        $this->Order_model->update_order(['status' => 'shipped'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                                        $type = ['type' => "customer_order_shipped"];
                                                        $order_status = 'shipped';
                                                    }
                                                    if ($shiprocket_order['data']['status'] == 'CANCELED' &&  $active_status != 'cancelled') {
                                                        $this->Order_model->update_order(['active_status' => 'cancelled'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                                        $this->Order_model->update_order(['status' => 'cancelled'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                                        $type = ['type' => "customer_order_cancelled"];
                                                        $order_status = 'cancelled';
                                                    }
                                                    if (strtolower($shiprocket_order['data']['status']) == 'delivered' &&  $active_status != 'delivered') {
                                                        $this->Order_model->update_order(['active_status' => 'delivered'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                                        $this->Order_model->update_order(['status' => 'delivered'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                                        $type = ['type' => "customer_order_delivered"];
                                                        $order_status = 'delivered';
                                                    }
                                                    if ($shiprocket_order['data']['status'] == 'READY TO SHIP' &&  $active_status != 'processed') {
                                                        $this->Order_model->update_order(['active_status' => 'processed'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                                        $this->Order_model->update_order(['status' => 'processed'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                                        $type = ['type' => "customer_order_processed"];
                                                        $order_status = 'processed';
                                                    }

                                                    //send notification while shiprocket order status changed
                                                    if (isset($type) && !empty($type)) {
                                                        $settings = get_settings('system_settings', true);
                                                        $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                                                        $custom_notification = fetch_details('custom_notifications', $type, '');
                                                        $hashtag_cutomer_name = '< cutomer_name >';
                                                        $hashtag_order_id = '< order_item_id >';
                                                        $hashtag_application_name = '< application_name >';
                                                        $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                                                        $hashtag = html_entity_decode($string);
                                                        $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($order_detls[0]['uname'], $order_detls[0]['id'], $app_name), $hashtag);
                                                        $message = output_escaping(trim($data, '"'));
                                                        $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $order_detls[0]['uname'] . ' Order status updated to' . $order_status . ' for order ID #' . $order_detls[0]['id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                                                        $fcmMsg = array(
                                                            'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                                                            'body' =>  $customer_msg,
                                                            'type' => "order",
                                                        );

                                                        $user_res = fetch_details('users', ['id' => $order_detls[0]['user_id']], 'fcm_id,mobile,email');
                                                        $fcm_ids  =  array();

                                                        //send notification to customer
                                                        if (!empty($user_res[0]['fcm_id'])) {
                                                            $fcm_ids[0][] = $user_res[0]['fcm_id'];
                                                            send_notification($fcmMsg, $fcm_ids);
                                                        }
                                                        (notify_event(
                                                            $type['type'],
                                                            ["customer" => [$user_res[0]['email']]],
                                                            ["customer" => [$user_res[0]['mobile']]],
                                                            ["orders.id" => $order_detls[0]['id']]
                                                        ));
                                                    }
                                                }

                                            ?>
                                                <?php if ($shipping_method['shiprocket_shipping_method'] == 1 && isset($pickup_location[$j]) && !empty($pickup_location[$j]) && $pickup_location[$j] != 'NULL') { ?>
                                                    <div class="row">
                                                        <div class="col-sm-0 ml-4 m-2 text-left mt-3">
                                                            <?php if ($item['product_type'] != 'digital_product' && empty($order_tracking_data[0]['shipment_id'])) { ?>
                                                                <input type="radio" name="pickup_location" class="check_create_order" data-id="<?= $this->session->userdata('user_id') ?>" id="<?php print_r($pickup_location[$j]); ?>" />
                                                            <?php } ?>
                                                        </div>
                                                        <div class="col-md-6 m-2 text-left mt-3">
                                                            <strong>

                                                                <p class="mb-0">Pickup Location :
                                                            </strong>
                                                            <?= ucfirst($pickup_location[$j]) ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="row m-2 ml-6">
                                                        <div class="col-sm-0 ml-4 m-2"></div>
                                                        <?php if (isset($order_tracking_data[0]['shipment_id']) && !empty($order_tracking_data[0]['shipment_id']) && empty($order_tracking_data[0]['is_canceled']) && $order_tracking_data[0]['is_canceled'] != 1 && $shiprocket_order['data']['status'] != 'CANCELED') { ?>
                                                            <div class="col-md-1">
                                                                <span class="badge bg-success ml-1">Order created</span>
                                                            </div>
                                                        <?php } ?>
                                                        <?php if (isset($items[0]['product_type']) && ($item['product_type'] != 'digital_product')) {  ?>
                                                            <?php if (!isset($order_tracking_data[0]['shipment_id']) && empty($order_tracking_data[0]['shipment_id'])) { ?>
                                                                <div class="col-md-1">
                                                                    <span class="badge bg-primary ml-1">Order not created</span>
                                                                </div>
                                                        <?php }
                                                        } ?>

                                                        <?php if ((isset($order_tracking_data[0]['is_canceled']) && $order_tracking_data[0]['is_canceled'] != 0) || $shiprocket_order['data']['status'] == 'CANCELED') { ?>
                                                            <div class="col-md-1">
                                                                <span class="badge bg-danger ml-1">Order cancelled</span>
                                                            </div>
                                                        <?php  } ?>
                                                        <div class="col-md-5">
                                                            <?php if (isset($order_tracking_data[0])) { ?>
                                                                <?php if (isset($order_tracking_data[0]['shipment_id']) && (empty($order_tracking_data[0]['awb_code']) || $order_tracking_data[0]['awb_code'] == 'NULL') && $shiprocket_order['data']['status'] != 'CANCELED') { ?>
                                                                    <a href="" title="Generate AWB" class="btn btn-primary btn-xs mr-1 generate_awb" data-fromseller="1" id=<?php print_r($order_tracking_data[0]['shipment_id']); ?>>AWB</a>
                                                                <?php } else { ?>
                                                                    <?php if (empty($order_tracking_data[0]['pickup_scheduled_date']) && ($shiprocket_order['data']['status_code'] != 4 || $shiprocket_order['data']['status'] != 'PICKUP SCHEDULED') && $shiprocket_order['data']['status'] != 'CANCELED' && $shiprocket_order['data']['status'] != 'CANCELLATION REQUESTED') { ?>
                                                                        <a href="" title="Send Pickup Request" class="btn btn-primary btn-xs mr-1 send_pickup_request" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shipment_id']); ?>><i class="fas fa-shipping-fast "></i></a>
                                                                    <?php }
                                                                    if (isset($order_tracking_data[0]['is_canceled']) && $order_tracking_data[0]['is_canceled'] == 0) { ?>
                                                                        <a href="" title="Cancel Order" class="btn btn-primary btn-xs mr-1 cancel_shiprocket_order" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="fas fa-redo-alt"></i></a>
                                                                    <?php } ?>

                                                                    <?php if (isset($order_tracking_data[0]['label_url']) && !empty($order_tracking_data[0]['label_url'])) { ?>
                                                                        <a href="<?php print_r($order_tracking_data[0]['label_url']); ?>" title="Download Label" data-fromseller="1" class="btn btn-primary btn-xs mr-1 download_label"><i class="fas fa-download"></i> Label</a>
                                                                    <?php } else { ?>
                                                                        <a href="" title="Generate Label" class="btn btn-primary btn-xs mr-1 generate_label" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shipment_id']); ?>><i class="fas fa-tags"></i></a>
                                                                    <?php } ?>

                                                                    <?php if (isset($order_tracking_data[0]['invoice_url']) && !empty($order_tracking_data[0]['invoice_url'])) { ?>
                                                                        <a href="<?php print_r($order_tracking_data[0]['invoice_url']); ?>" data-fromseller="1" title="Download Invoice" class="btn btn-primary  btn-xs mr-1 download_invoice"><i class="fas fa-download"></i> Invoice</a>
                                                                    <?php } else { ?>
                                                                        <a href="" title="Generate Invoice" class="btn btn-primary btn-xs mr-1 generate_invoice" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="far fa-money-bill-alt"></i></a>
                                                                    <?php }
                                                                    if (isset($order_tracking_data[0]['awb_code']) && !empty($order_tracking_data[0]['awb_code'])) { ?>
                                                                        <a href="https://shiprocket.co/tracking/<?php echo $order_tracking_data[0]['awb_code']; ?>" target=" _blank" title="Track Order" class="btn btn-primary action-btn btn-xs mr-1 track_order" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="fas fa-map-marker-alt"></i></a>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php

                                                $total = 0;
                                                $tax_amount = 0;
                                                echo '<div class="container-fluid row">';
                                                foreach ($items as $item) {

                                                    $selected = "";
                                                    $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                                    $total += $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                                    $tax_amount += $item['tax_amount'];
                                                    $total += $subtotal = $tax_amount;
                                                    // $total += $subtotal;
                                                    if ($pickup_location[$j] == $item['pickup_location']) {
                                                ?>
                                                        <div class="  card col-md-3 col-sm-12 p-3 mb-2 bg-white rounded m-1 grow">
                                                            <div class="mb-2">
                                                                <input type="checkbox" id="<?= $sellers[$i] ?>" name="order_item_id" value=' <?= $item['id'] ?> '>
                                                            </div>
                                                            <div class="order-product-image">
                                                                <a href='<?= base_url() . $item['product_image'] ?>' data-toggle='lightbox' data-gallery='order-images'> <img src='<?= base_url() . $item['product_image'] ?>' class='h-75'></a>
                                                            </div>

                                                            <!-- <?php if (isset($item['product_type']) && $item['product_type'] != 'digital_product') {
                                                                        if (get_seller_permission($seller_id, 'view_order_otp') == true) {
                                                                            if ($item['item_otp'] != 0) { ?>
                                                                <div><span class="text-bold">Item OTP : </span><span class="badge badge-warning"><?= $item['item_otp']; ?></span></div>
                                                            <?php } elseif ($item['seller_otp'] != 0) { ?>
                                                                <div><span class="text-bold">Item OTP : </span><span class="badge badge-warning"><?= $item['seller_otp']; ?></span></div>
                                                    <?php }
                                                                        }
                                                                    } ?> -->
                                                            <div><span class="text-bold">Product Type : </span><small><?= ucwords(str_replace('_', ' ', $item['product_type'])); ?> </small></div>
                                                            <div><span class="text-bold">Variant ID : </span><?= $item['product_variant_id'] ?> </div>
                                                            <?php if (isset($item['product_variants']) && !empty($item['product_variants'])) { ?>
                                                                <div><span class="text-bold">Variants : </span><?= str_replace(',', ' | ', $item['product_variants'][0]['variant_values']) ?> </div>
                                                            <?php } ?>
                                                            <div><span class="text-bold">Name : </span><small><?= $item['pname'] ?> </small></div>
                                                            <div><span class="text-bold">Quantity : </span><?= $item['quantity'] ?> </div>
                                                            <!-- <div><span class="text-bold">Price : </span><?//= $item['price'] + $item['tax_amount'] ?></div> -->
                                                            <div><span class="text-bold">Price : </span><?= $item['price'] ?></div>
                                                            <div><span class="text-bold">Discounted Price : </span> <?= $item['discounted_price'] ?> </div>
                                                            <div><span class="text-bold">Subtotal : </span><?= $item['price'] * $item['quantity'] ?> </div>
                                                            <?php
                                                            $badges = ["awaiting" => "secondary", "received" => "primary", "processed" => "info", "shipped" => "warning", "delivered" => "success", "returned" => "danger", "cancelled" => "danger", "return_request_approved" => "danger", "return_request_decline" => "danger", "return_request_pending" => "danger"]
                                                            ?>
                                                            <?php if (isset($item['updated_by'])) { ?>
                                                                <div><span class="text-bold">Updated By : </span><?= $item['updated_by'] ?> </div>
                                                            <?php } ?>
                                                            <?php if (isset($item['deliver_by'])) { ?>
                                                                <div><span class="text-bold">Deliver By : </span><?= $item['deliver_by'] ?> </div>
                                                            <?php } ?>
                                                            <div><span class="text-bold">Active Status : </span> <span class="badge badge-<?= $badges[$item['active_status']] ?>"> <small><?= str_replace('_', ' ', $item['active_status']) ?></small></span></div>
                                                            <div><span class="text-bold">View Product : </span> <a href=" <?= BASE_URL('seller/product/view-product?edit_id=' . $item['product_id'] . '') ?> " title="View Product" class="btn btn-primary btn-xs">
                                                                    <i class="fa fa-eye"></i>
                                                                </a></div>

                                                            <?php if ($item['product_type'] == "digital_product" && $item['download_allowed'] == 0 && $item['is_sent'] == 0) { ?>
                                                                <div class="row mb-1 mt-1 order_item_mail_status">

                                                                    <div class="col-md-7 text-center">
                                                                        <select class="form-control-sm w-100">
                                                                            <option value="1">Mail Sent</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-1 mr-1 d-flex align-items-center">
                                                                        <a href="javascript:void(0);" title="Update status" data-id=' <?= $item['id'] ?> ' class="btn btn-primary btn-xs action-btn ml-1 update_mail_status_admin mr-1">
                                                                            <i class="far fa-arrow-alt-circle-up"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-md-1 d-flex ml-1 align-items-center">
                                                                        <a href="javascript:void(0)" class=" btn action-btn btn-warning btn-xs" data-target="#sendMailModal" data-toggle="modal" title="Edit" data-id="<?= $item['id'] ?>" data-url="seller/orders/">
                                                                            <i class="fas fa-paper-plane"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-md-1 d-flex ml-1 align-items-center">
                                                                        <a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=<?= $item['user_email'] ?>" class="btn action-btn btn-danger btn-xs" target="_blank">
                                                                            <i class="fab fa-google"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>

                                                        </div>
                                            <?php
                                                    }
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                            <div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Total(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php 
                                            echo $order_detls[0]['order_total'];
                                            $total = $order_detls[0]['order_total'];
                                        ?></td>
                                </tr>

                                <tr class="d-none">
                                    <th class="w-10px">Tax(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo $tax_amount; ?></td>
                                </tr>
                                <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] != 'digital_product') { ?>
                                    <tr>
                                        <th class="w-10px">Delivery Charge(<?= $settings['currency'] ?>)</th>
                                        <td id='delivery_charge'>
                                            <?php echo $items[0]['seller_delivery_charge'];
                                            $total = $total + $order_detls[0]['delivery_charge']; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <!-- <tr>
                                    <th class="w-10px">Wallet Balance(<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $order_detls[0]['wallet_balance'];
                                        // $total = $total - $order_detls[0]['wallet_balance']; 
                                        ?></td>
                                </tr> -->
                                <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $order_detls[0]['order_total'] + $order_detls[0]['delivery_charge'] ?>">
                                <input type="hidden" name="final_amount" id="final_amount" value="<?php echo $order_detls[0]['final_total']; ?>">
                                <tr>
                                    <th class="w-10px">Promo Code Discount (<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $items[0]['seller_promo_discount'];
                                        $total = floatval($total -
                                            $order_detls[0]['promo_discount']); ?></td>
                                </tr>
                                <?php
                                if (isset($order_detls[0]['discount']) && $order_detls[0]['discount'] > 0) {
                                    $discount = $order_detls[0]['total_payable']  *  ($order_detls[0]['discount'] / 100);
                                    $total = round($order_detls[0]['total_payable'] - $discount, 2);
                                }
                                ?>
                                <!-- <tr>
                                    <th class="w-10px">Payable Total(<?= $settings['currency'] ?>)</th>
                                    <td><input type="text" class="form-control" id="final_total" name="final_total" value="<?= $total; ?>" disabled></td>
                                </tr> -->
                                <tr>
                                    <th class="w-10px">Payment Method</th>
                                    <td><?php echo $order_detls[0]['payment_method']; ?></td>
                                </tr>
                                <?php
                                if (!empty($bank_transfer)) { ?>
                                    <tr>
                                        <th class="w-10px">Bank Transfers</th>
                                        <td>
                                            <div class="col-md-6">
                                                <?php $i = 1;
                                                foreach ($bank_transfer as $row1) { ?>
                                                    <small>[<a href="<?= base_url() . $row1['attachments'] ?>" target="_blank">Attachment <?= $i ?> </a>] </small>
                                                    <a class="delete-receipt btn btn-danger btn-xs mr-1 mb-1" title="Delete" href="javascript:void(0)" data-id="<?= $row1['id']; ?>"><i class="fa fa-trash"></i></a>
                                                <?php $i++;
                                                } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] != 'digital_product') { ?>
                                    <tr>
                                        <th class="w-10px">Address</th>
                                        <td><?php echo $order_detls[0]['address']; ?></td>
                                    </tr>
                                    <tr>
                                        <th class="w-10px">Delivery Date & Time</th>
                                        <td><?php echo (!empty($order_detls[0]['delivery_date']) && $order_detls[0]['delivery_date'] != NUll) ? date('d-M-Y', strtotime($order_detls[0]['delivery_date'])) . " - " . $order_detls[0]['delivery_time'] : "Anytime"; ?></td>
                                    </tr>

                                <?php } ?>
                                <tr>
                                    <th class="w-10px">Order Date</th>
                                    <td><?php echo date('d-M-Y', strtotime($order_detls[0]['date_added'])); ?></td>
                                </tr>
                            </table>
                        </div>
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
<div class="modal fade" id="ShiprocketOrderFlow" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">How to manage shiprocket order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ">
                <h6><b>Steps:</b></h6>
                <ol>
                    <li> Select Pickup Location for which you want to create parcel and click on <b>Create Shiprocket Order</b> button.</li>
                    <img src="<?= BASE_URL("assets/admin/images/create_order.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> After create order generate AWB code(its unique number use for identify order) like this.</li>
                    <img src="<?= BASE_URL("assets/admin/images/generate_awb.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> After generate AWB Send pickup request for scheduled you shipping.</li>
                    <img src="<?= BASE_URL("assets/admin/images/send_pickup_request.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> Generate and download Label.</li>
                    <img src="<?= BASE_URL("assets/admin/images/generate_label.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <img src="<?= BASE_URL("assets/admin/images/download_label.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> Generate and download Invoice.</li>
                    <img src="<?= BASE_URL("assets/admin/images/generate_invoice.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <img src="<?= BASE_URL("assets/admin/images/download_invoice.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> Cancel shiprocket order.</li>
                    <img src="<?= BASE_URL("assets/admin/images/cancel_order.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> shiprocket order traking.</li>
                    <img src="<?= BASE_URL("assets/admin/images/order_tracking.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                </ol>
            </div>
        </div>
    </div>
</div>