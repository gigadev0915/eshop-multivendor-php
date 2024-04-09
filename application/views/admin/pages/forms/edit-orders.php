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
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
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
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Manage Digital Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body ">
                                <form class="form-horizontal form-submit-event" action="<?= base_url('admin/orders/send_digital_product'); ?>" method="POST" enctype="multipart/form-data">
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
                                                    <label for="product_name">Message</label>
                                                    <textarea class="textarea" id="mail_msg" placeholder="Message for Email" name="message"></textarea>
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
                                        <button type="submit" class="btn btn-success mt-3" id="submit_btn" value="Save"><?= labels('send_mail', 'Send Mail') ?></button>
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
                                            <form class="form-horizontal " id="order_tracking_form" action="<?= base_url('admin/orders/update-order-tracking/'); ?>" method="POST" enctype="multipart/form-data">
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
                                                        <input type="hidden" name="fromadmin" value="1" id="fromadmin" />
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
                                                        <div class="col-md-6">
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

                <!-- model for update bank transfer recipt  -->
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="payment_transaction_modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="user_name"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <!-- form start -->
                                            <form class="form-horizontal " id="edit_transaction_form" action="<?= base_url('admin/orders/edit_transactions/'); ?>" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="id" id="id">

                                                <div class="card-body pad">
                                                    <div class="form-group ">
                                                        <label for="transaction"> Update Transaction </label>
                                                        <select class="form-control" name="status" id="t_status">
                                                            <option value="awaiting"> Awaiting </option>
                                                            <option value="Success"> Success </option>
                                                            <option value="Failed"> Failed </option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="txn_id">Txn_id</label>
                                                        <input type="text" class="form-control" name="txn_id" id="txn_id" placeholder="txn_id" />
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="message">Message</label>
                                                        <input type="text" class="form-control" name="message" id="message" placeholder="Message" />
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="reset" class="btn btn-warning">Reset</button>
                                                        <button type="submit" class="btn btn-success" id="submit_btn">Update Transaction</button>
                                                    </div>
                                                </div>

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

                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-body">
                            <table class="table">
                                <?php
                                $mobile_data = fetch_details('addresses', ['id' => $order_detls[0]['address_id']], 'mobile');
                                ?>
                                <tr>
                                    <input type="hidden" name="hidden" id="order_id" value="<?php echo $order_detls[0]['id']; ?>">
                                    <th class="w-10px">ID</th>
                                    <td><?php echo $order_detls[0]['id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Name</th>
                                    <td><?php echo "Account Holder Person : " . $order_detls[0]['uname'] . " | Order Recipient Person :  " . $order_detls[0]['user_name']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Email</th>
                                    <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email']; ?></td>
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
                                        <th class="w-10px">Order note</th>
                                        <td><?php echo  $order_detls[0]['notes']; ?></td>
                                    </tr>
                                <?php } ?>

                                <?php $sellers = array_values(array_unique(array_column($order_detls, "seller_id"))); ?>
                                <tr>
                                    <td colspan="2">

                                        <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] == 'digital_product') { ?>
                                            <p>
                                                <lable class="badge badge-success" style="font-size:13px;">Select status and radio button of seller which you want to update</lable>
                                            </p>
                                            <div class="row">
                                                <div class="col-md-4 ">
                                                    <select name="status" class="form-control status">
                                                        <option value=''>Select Status</option>
                                                        <option value="delivered">Delivered</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <a href="javascript:void(0);" title="Bulk Update" class="btn btn-primary col-sm-12 col-md-12 update_status_admin_bulk mr-1">
                                                        Update
                                                    </a>
                                                </div>
                                            </div>
                                            <p>
                                                <lable class="badge badge-warning mt-4" style="font-size:13px;">Note : Select square box of item only when you want to update it as cancelled or returned.</lable>
                                            </p>

                                        <?php } else {

                                        ?>

                                            <p>
                                                <lable class="badge badge-success " style="font-size:13px;">Select status, delivery boy and radio button of seller which you want to update</lable>
                                            </p>
                                            <?php
                                            // print_R($items[0]['active_status']);
                                            // die;
                                            ?>
                                            <div class="row delivery_boy ">
                                                <div class="col-md-3">
                                                    <select name="status" class="form-control status" <?php echo $items[0]['active_status'] == "awaiting" ? 'disabled' : '' ?>>
                                                        <option value=''>Select Status</option>
                                                        <option value="processed">Processed</option>
                                                        <option value="shipped">Shipped</option>
                                                        <option value="delivered">Delivered</option>
                                                        <option value="cancelled">Cancel</option>
                                                        <option value="returned">Returned</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <select id='deliver_by' name='deliver_by' class='form-control' <?php echo $items[0]['active_status'] == "awaiting" ? 'disabled' : '' ?> required>
                                                        <option value=''>Select Delivery Boy</option>
                                                        <?php foreach ($delivery_res as $row) { ?>
                                                            <option value="<?= $row['user_id'] ?>"><?= $row['username'] ?></option>
                                                        <?php  } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xs mr-1 action-btn ml-1 " title="Order Tracking" data-order_id=' <? //= $order_detls[0]['id']; 
                                                                                                                                                                                                        ?>' data-order_item_id=' <? //= $item['id'] 
                                                                                                                                                                                                                                                            ?> ' data-seller_id=' <? //= $item['seller_id'] 
                                                                                                                                                                                                                                                                                                    ?> ' data-courier_agency=' <? //= $item['courier_agency'] 
                                                                                                                                                                                                                                                                                                                                                        ?> ' data-tracking_id=' <? //= $item['tracking_id'] 
                                                                                                                                                                                                                                                                                                                                                                                                                ?> ' data-url=' <? //= $item['url'] 
                                                                                                                                                                                                                                                                                                                                                                                                                                                            ?> ' data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a> -->
                                                    <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xl " title="Order Tracking" data-order_id=' <?= $order_detls[0]['id']; ?>' data-target="#transaction_modal" data-toggle="modal" style="height:35px;width:38px;"><i class="fa fa-map-marker-alt"></i></a>
                                                    <a href="javascript:void(0);" title="Bulk Update" data-id=' <?= $sellers[$i] ?> ' class="btn btn-primary ml-3 col-md-4 update_status_admin_bulk">
                                                        Update
                                                    </a>
                                                    <?php if ($shipping_method['shiprocket_shipping_method'] == 1) { ?>
                                                        <button type="button" disabled class="btn btn-primary ml-3 col-md-6 create_shiprocket_order float-right" data-target="#order_parcel_modal" data-toggle="modal"> Create Shiprocket Order</button>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                            <p>
                                                <lable class="badge badge-warning mt-4 " style="font-size:13px;">Note : Select square box of item only when you want to update it as cancelled or returned.</lable>
                                            </p>
                                            <?php if ($shipping_method['shiprocket_shipping_method'] == 1) { ?>
                                                <p>
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#ShiprocketOrderFlow">How to manage shiprocket order </button>
                                                </p>
                                            <?php } ?>

                                        <?php } ?>
                                    </td>
                                </tr>
                                <div class="row">
                                    <form id="update_form" class="<?= $sellers[$i] ?>">
                                        <?php
                                        for ($i = 0; $i < count($sellers); $i++) {
                                            $seller_data = fetch_details('users', ['id' => $sellers[$i]], 'username,fcm_id,email,mobile');
                                            $seller_otp = fetch_details('order_items', ['order_id' => $order_detls[0]['order_id'], 'seller_id' => $sellers[$i]], 'otp')[0]['otp'];
                                            $order_caharges_data = fetch_details('order_charges', ['order_id' => $order_detls[0]['order_id'], 'seller_id' => $sellers[$i]]);
                                            $this->load->model('Order_model');
                                            $seller_order = $this->Order_model->get_order_details(['o.id' => $order_detls[0]['order_id'], 'oi.seller_id' => $sellers[$i]]);
                                            $pickup_location = array_values(array_unique(array_column($seller_order, "pickup_location")));

                                        ?>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="card card-info mb-3 mt-2 ">
                                                        <div class="card-body">
                                                            <div class="col-md-6 m-2 text-left">
                                                                <input type="radio" name="seller_id" value="<?= $sellers[$i] ?>" style="height:15px;width:15px;">
                                                                <strong>
                                                                    <p class="mb-0">Seller :
                                                                </strong>
                                                                <?= ucfirst($seller_data[0]['username']) ?></p>
                                                                <?php if ($items[0]['product_type'] != 'digital_product') { ?>
                                                                    <strong>
                                                                        <p>OTP :
                                                                    </strong>
                                                                    <span class="badge badge-danger"><?= isset($order_caharges_data[0]['otp']) ? $order_caharges_data[0]['otp'] : $seller_otp ?></span></p>
                                                                <?php } ?>
                                                            </div>
                                                            <?php for ($j = 0; $j < count($pickup_location); $j++) {
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
                                                                    $active_status = fetch_details('order_items', ['id' => $id, 'seller_id' => $sellers[$i]], 'active_status')[0]['active_status'];

                                                                    if ($shiprocket_order['data']['status'] == 'PICKUP SCHEDULED' &&  $active_status != 'shipped') {
                                                                        $this->Order_model->update_order(['active_status' => 'shipped'], ['id' => $id, 'seller_id' => $sellers[$i]], false, 'order_items');
                                                                        $this->Order_model->update_order(['status' => 'shipped'], ['id' => $id, 'seller_id' => $sellers[$i]], true, 'order_items');
                                                                        $type = ['type' => "customer_order_shipped"];
                                                                        $order_status = 'shipped';
                                                                    }
                                                                    if (($shiprocket_order['data']['status'] == 'CANCELED' || $shiprocket_order['data']['status'] == 'CANCELLATION REQUESTED') &&  $active_status != 'cancelled') {
                                                                        $this->Order_model->update_order(['active_status' => 'cancelled'], ['id' => $id, 'seller_id' => $sellers[$i]], false, 'order_items');
                                                                        $this->Order_model->update_order(['status' => 'cancelled'], ['id' => $id, 'seller_id' => $sellers[$i]], true, 'order_items');
                                                                        $type = ['type' => "customer_order_cancelled"];
                                                                        $order_status = 'cancelled';
                                                                    }
                                                                    if (strtolower($shiprocket_order['data']['status']) == 'delivered' &&  $active_status != 'delivered') {
                                                                        $this->Order_model->update_order(['active_status' => 'delivered'], ['id' => $id, 'seller_id' => $sellers[$i]], false, 'order_items');
                                                                        $this->Order_model->update_order(['status' => 'delivered'], ['id' => $id, 'seller_id' => $sellers[$i]], true, 'order_items');
                                                                        $type = ['type' => "customer_order_delivered"];
                                                                        $order_status = 'delivered';
                                                                    }
                                                                    if ($shiprocket_order['data']['status'] == 'READY TO SHIP' &&  $active_status != 'processed') {
                                                                        $this->Order_model->update_order(['active_status' => 'processed'], ['id' => $id, 'seller_id' => $sellers[$i]], false, 'order_items');
                                                                        $this->Order_model->update_order(['status' => 'processed'], ['id' => $id, 'seller_id' => $sellers[$i]], true, 'order_items');
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
                                                                        $seller_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $seller_data[0]['username'] . ' Order status updated to' . $order_status . ' for order ID #' . $order_detls[0]['id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                                                                        $fcmMsg = array(
                                                                            'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                                                                            'body' =>  $customer_msg,
                                                                            'type' => "order",
                                                                        );
                                                                        $seller_fcmMsg = array(
                                                                            'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                                                                            'body' =>  $seller_msg,
                                                                            'type' => "order",
                                                                        );
                                                                        $user_res = fetch_details('users', ['id' => $order_detls[0]['user_id']], 'fcm_id');
                                                                        $fcm_ids = $seller_fcm_ids =  array();

                                                                        //send notification to customer
                                                                        if (!empty($user_res[0]['fcm_id'])) {
                                                                            $fcm_ids[0][] = $user_res[0]['fcm_id'];
                                                                            send_notification($fcmMsg, $fcm_ids);
                                                                        }
                                                                        (notify_event(
                                                                            $type['type'],
                                                                            ["customer" => [$order_detls[0]['email']], "seller" => [$seller_data[0]['email']]],
                                                                            ["customer" => [$order_detls[0]['mobile']],  "seller" => [$seller_data[0]['mobile']]],
                                                                            ["orders.id" => $order_detls[0]['id']]
                                                                        ));

                                                                        //send notification to seller
                                                                        if (!empty($seller_data[0]['fcm_id'])) {
                                                                            $seller_fcm_ids[0][] = $seller_data[0]['fcm_id'];
                                                                            send_notification($seller_fcmMsg, $seller_fcm_ids);
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                                <?php if ($shipping_method['shiprocket_shipping_method'] == 1 && isset($pickup_location[$j]) && !empty($pickup_location[$j]) && $pickup_location[$j] != 'NULL') { ?>
                                                                    <div class="row">
                                                                        <div class="col-sm-0 ml-4 m-2 text-left mt-3 ">
                                                                            <?php if ($item['product_type'] != 'digital_product' && empty($order_tracking_data[0]['shipment_id'])) { ?>
                                                                                <input type="radio" name="pickup_location" class="check_create_order" data-id="<?= $sellers[$i] ?>" id="<?php print_r($pickup_location[$j]); ?>" />
                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php if (isset($pickup_location[$j]) && !empty($pickup_location[$j]) && $pickup_location[$j] != '') { ?>
                                                                            <div class="col-md-6 m-2 text-left mt-3">
                                                                                <strong>

                                                                                    <p class="mb-0">Pickup Location :
                                                                                </strong>
                                                                                <?= ucfirst($pickup_location[$j]) ?></p>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                    <div class="row m-2 ml-6">
                                                                        <div class="col-sm-0 ml-4 m-2"></div>
                                                                        <?php if (isset($order_tracking_data[0]['shipment_id']) && !empty($order_tracking_data[0]['shipment_id']) && empty($order_tracking_data[0]['is_canceled']) && $order_tracking_data[0]['is_canceled'] != 1 && $shiprocket_order['data']['status'] != 'CANCELED') { ?>
                                                                            <div class="col-md-1">
                                                                                <span class="badge bg-success ml-1">Order created</span>
                                                                            </div>
                                                                        <?php } ?>
                                                                        <?php if (isset($items[0]['product_type']) && ($item['product_type'] != 'digital_product')) {  ?>
                                                                            <?php if (empty($order_tracking_data[0]['shipment_id'])) { ?>
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
                                                                                    <a href="" title="Generate AWB" class="btn btn-primary btn-xs mr-1 generate_awb" data-fromadmin="1" id=<?php print_r($order_tracking_data[0]['shipment_id']); ?>>AWB</a>
                                                                                <?php } else { ?>
                                                                                    <?php if (empty($order_tracking_data[0]['pickup_scheduled_date']) && ($shiprocket_order['data']['status_code'] != 4 || $shiprocket_order['data']['status'] != 'PICKUP SCHEDULED') && $shiprocket_order['data']['status'] != 'CANCELED' && $shiprocket_order['data']['status'] != 'CANCELLATION REQUESTED') { ?>
                                                                                        <a href="" title="Send Pickup Request" class="btn btn-primary btn-xs mr-1 send_pickup_request" data-fromadmin="1" name=<?php print_r($order_tracking_data[0]['shipment_id']); ?>><i class="fas fa-shipping-fast "></i></a>
                                                                                    <?php }
                                                                                    if (isset($order_tracking_data[0]['is_canceled']) && $order_tracking_data[0]['is_canceled'] == 0) { ?>
                                                                                        <a href="" title="Cancel Order" class="btn btn-primary btn-xs mr-1 cancel_shiprocket_order" data-fromadmin="1" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="fas fa-redo-alt"></i></a>
                                                                                    <?php } ?>

                                                                                    <?php if (isset($order_tracking_data[0]['label_url']) && !empty($order_tracking_data[0]['label_url'])) { ?>
                                                                                        <a href="<?php print_r($order_tracking_data[0]['label_url']); ?>" title="Download Label" data-fromadmin="1" class="btn btn-primary btn-xs mr-1 download_label"><i class="fas fa-download"></i> Label</a>
                                                                                    <?php } else { ?>
                                                                                        <a href="" title="Generate Label" class="btn btn-primary btn-xs mr-1 generate_label" data-fromadmin="1" name=<?php print_r($order_tracking_data[0]['shipment_id']); ?>><i class="fas fa-tags"></i></a>
                                                                                    <?php } ?>

                                                                                    <?php if (isset($order_tracking_data[0]['invoice_url']) && !empty($order_tracking_data[0]['invoice_url'])) { ?>
                                                                                        <a href="<?php print_r($order_tracking_data[0]['invoice_url']); ?>" title="Download Invoice" data-fromadmin="1" class="btn btn-primary  btn-xs mr-1 download_invoice"><i class="fas fa-download"></i> Invoice</a>
                                                                                    <?php } else { ?>
                                                                                        <a href="" title="Generate Invoice" class="btn btn-primary btn-xs mr-1 generate_invoice" data-fromadmin="1" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="far fa-money-bill-alt"></i></a>
                                                                                    <?php }
                                                                                    if (isset($order_tracking_data[0]['awb_code']) && !empty($order_tracking_data[0]['awb_code'])) { ?>
                                                                                        <a href="https://shiprocket.co/tracking/<?php echo $order_tracking_data[0]['awb_code']; ?>" target=" _blank" title="Track Order" class="btn btn-primary action-btn btn-xs mr-1 track_order" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="fas fa-map-marker-alt"></i></a>
                                                                                    <?php } ?>
                                                                                <?php } ?>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>

                                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                                <input type="hidden" name="order_id" value="<?= $order_detls[0]['order_id'] ?>">

                                                                <?php $total = 0;
                                                                $tax_amount = 0; ?>
                                                                <div class="container-fluid">

                                                                    <?php
                                                                    echo '<div class="container-fluid row">';


                                                                    foreach ($items as $item) {
                                                                        $selected = "";
                                                                        $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                                                        $total += $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                                                        $tax_amount += $item['tax_amount'];
                                                                        $total += $subtotal = $tax_amount;
                                                                    ?>
                                                                        <?php if ($sellers[$i] == $item['seller_id']) {
                                                                            if ($pickup_location[$j] == $item['pickup_location']) {
                                                                                $order_tracking_data = get_shipment_id($item['id'], $order_detls[0]['id']); ?>
                                                                                <div class=" card col-md-3 col-sm-12 p-3 mb-2 bg-white rounded m-1 grow">
                                                                                    <div class="row m-2">
                                                                                    </div>
                                                                                    <div class="mb-2">
                                                                                        <input type="checkbox" id="<?= $sellers[$i] ?>" name="order_item_id" value=' <?= $item['id'] ?> ' disabled>
                                                                                    </div>
                                                                                    <div class="order-product-image">
                                                                                        <a href='<?= base_url() . $item['product_image'] ?>' data-toggle='lightbox' data-gallery='order-images'> <img src='<?= base_url() . $item['product_image'] ?>' class='h-75'></a>
                                                                                    </div>
                                                                                    <div><span class="text-bold">Product Type : </span><small><?= ucwords(str_replace('_', ' ', $item['product_type'])); ?> </small></div>
                                                                                    <div><span class="text-bold">Variant ID : </span><?= $item['product_variant_id'] ?> </div>
                                                                                    <?php if (isset($item['product_variants']) && !empty($item['product_variants'])) { ?>
                                                                                        <div><span class="text-bold">Variants : </span><?= str_replace(',', ' | ', $item['product_variants'][0]['variant_values']) ?> </div>
                                                                                    <?php } ?>
                                                                                    <div><span class="text-bold">Name : </span><small><?= $item['pname'] ?> </small></div>
                                                                                    <div><span class="text-bold">Quantity : </span><?= $item['quantity'] ?> </div>
                                                                                    <!-- <div><span class="text-bold">Price : </span><?= $item['price'] + $item['tax_amount'] ?></div> -->
                                                                                    <div><span class="text-bold">Price : </span><?= $item['price'] ?></div>
                                                                                    <div><span class="text-bold">Discounted Price : </span> <?= $item['discounted_price'] ?> </div>
                                                                                    <div><span class="text-bold">Subtotal : </span><?= $item['price'] * $item['quantity'] ?> </div>
                                                                                    <?php if (isset($item['product_type']) && ($item['product_type'] != 'digital_product')) { ?>
                                                                                        <div><span class="text-bold">Pickup Location : </span><?= $item['pickup_location'] ?> </div>
                                                                                        <?php if (isset($order_tracking_data[0]['shipment_id']) && !empty($order_tracking_data[0]['shipment_id'])) { ?>
                                                                                            <div><span class="text-bold">Shipment Id : </span><?= $order_tracking_data[0]['shipment_id'] ?></div>
                                                                                    <?php  }
                                                                                    } ?>
                                                                                    <?php
                                                                                    $badges = ["awaiting" => "secondary", "received" => "primary", "processed" => "info", "shipped" => "warning", "delivered" => "success", "returned" => "danger", "cancelled" => "danger", "return_request_approved" => "success", "return_request_decline" => "danger", "return_request_pending" => "warning"]
                                                                                    ?>
                                                                                    <?php if (isset($item['updated_by'])) { ?>
                                                                                        <div><span class="text-bold">Updated By : </span><?= $item['updated_by'] ?> </div>
                                                                                    <?php } ?>
                                                                                    <?php if (isset($item['deliver_by'])) { ?>
                                                                                        <div><span class="text-bold">Deliver By : </span><?= $item['deliver_by'] ?> </div>
                                                                                    <?php } ?>
                                                                                    <div><span class="text-bold">Active Status : </span> <span class="badge ml-1 badge-<?= $badges[$item['active_status']] ?>"> <small><?= str_replace('_', ' ', $item['active_status']) ?></small></span></div>
                                                                                    <div><span class="text-bold">View Product : </span> <a href=" <?= BASE_URL('admin/product/view-product?edit_id=' . $item['product_id'] . '') ?> " title="View Product" class="btn action-btn ml-1 btn-primary btn-xs">
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
                                                                                                <!-- <a href="javascript:void(0)" class="edit_btn btn action-btn btn-warning btn-xs" title="Edit" data-id="<?= $item['id'] ?>" data-url="admin/orders/">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </a> -->
                                                                                                <a href="javascript:void(0)" class="btn action-btn btn-warning btn-xs " data-target="#sendMailModal" data-toggle="modal" title="Edit" data-id="<?= $item['id'] ?>" data-url="admin/orders/">
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

                                                                                    <!-- <div class="row mb-1 mt-1 order_item_status">
                                                    <div class="col-md-7 text-center"><select class="form-control-sm w-100">
                                                            <option value="processed" <?= (strtolower($item['active_status']) == 'processed') ? 'selected' : '' ?>>Processed</option>
                                                            <option value="shipped" <?= (strtolower($item['active_status']) == 'shipped') ? 'selected' : '' ?>>Shipped</option>
                                                            <option value="delivered" <?= (strtolower($item['active_status']) == 'delivered') ? 'selected' : '' ?>>Delivered</option>
                                                            <option value="returned" <?= (strtolower($item['active_status']) == 'returned') ? 'selected' : '' ?>>Return</option>
                                                            <option value="cancelled" <?= (strtolower($item['active_status']) == 'cancelled') ? 'selected' : '' ?>>Cancel</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5 d-flex align-items-center">
                                                        <a href="javascript:void(0);" title="Update status" data-id=' <?= $item['id'] ?> ' class="btn btn-primary btn-xs action-btn ml-1 update_status_admin mr-1">
                                                            <i class="far fa-arrow-alt-circle-up"></i>
                                                        </a>
                                                        <a href=" <?= BASE_URL('admin/product/view-product?edit_id=' . $item['product_id'] . '') ?> " title="View Product" class="btn action-btn ml-1 btn-primary btn-xs">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </div> -->
                                                                                    <!-- <div class="row mb-1 mt-1 delivery_boy">
                                                    <div class="col-md-7 text-center">
                                                        <select name='single_deliver_by' class='form-control-sm w-100' required>
                                                            <option value=''>Select Delivery Boy</option>
                                                            <?php
                                                                                $delivery_boy_id = fetch_details('order_items', ['id' => $item['id']], 'delivery_boy_id');
                                                                                foreach ($delivery_res as $row) {
                                                                                    $selected = (isset($delivery_boy_id) && !empty($delivery_boy_id) && $delivery_boy_id[0]['delivery_boy_id'] == $row['user_id']) ? 'selected' : '';
                                                            ?>
                                                                <option value="<?= $row['user_id'] ?>" <?= $selected ?>><?= $row['username'] ?></option>
                                                            <?php  } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5 d-flex align-items-center">

                                                        <a href="javascript:void(0);" title="Update Delivery Boy" data-id=' <?= $item['id'] ?> ' class="btn btn-primary btn-xs action-btn ml-1 update_delivery_boy_admin mr-1">
                                                            <i class="far fa-arrow-alt-circle-up"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xs mr-1 action-btn ml-1 " title="Order Tracking" data-order_id=' <?= $order_detls[0]['id']; ?>' data-order_item_id=' <?= $item['id'] ?> ' data-seller_id=' <?= $item['seller_id'] ?> ' data-courier_agency=' <?= $item['courier_agency'] ?> ' data-tracking_id=' <?= $item['tracking_id'] ?> ' data-url=' <?= $item['url'] ?> ' data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>
                                                        <?php
                                                                                $transaction_data = fetch_details('transactions', ['order_item_id' => $item['id']], 'txn_id,amount');
                                                                                if (($order_detls[0]['payment_method'] == 'RazorPay' || $order_detls[0]['payment_method'] == 'razorpay' || $order_detls[0]['payment_method'] == 'Razorpay') && $item['active_status'] == 'cancelled' || $item['active_status'] == 'returned') { ?>
                                                            <a href="javascript:void(0)" class="edit_order_refund btn shipped-box btn-xs mr-1 " title="Refund" data-order_id=' <?= $order_detls[0]['id']; ?>' data-order_item_id=' <?= $item['id'] ?>' data-txn_id=' <?= $transaction_data[0]['txn_id'] ?>' data-txn_amount=' <?= $transaction_data[0]['amount'] ?>' data-target="#refund_modal" data-toggle="modal"><i class="fa fa-undo"></i></a>
                                                        <?php }
                                                        ?>
                                                    </div>
                                                </div> -->


                                                                                </div>
                                                                    <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    echo '</div>';
                                                                    ?>
                                                                    <div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                        } ?>
                                    </form>
                                </div>
                                <tr>
                                    <th class="w-10px">Total(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'>
                                        <?php
                                        echo $order_detls[0]['order_total'];
                                        $total = $order_detls[0]['order_total'];
                                        ?>
                                    </td>
                                </tr>

                                <tr class="d-none">
                                    <th class="w-10px">Tax(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo $tax_amount;
                                                    ?></td>
                                </tr>
                                <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] != 'digital_product') { ?>
                                    <tr>
                                        <th class="w-10px">Delivery Charge(<?= $settings['currency'] ?>)</th>
                                        <td id='delivery_charge'>
                                            <?php echo $order_detls[0]['delivery_charge'];
                                            $total = $total + $order_detls[0]['delivery_charge']; ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                    <th class="w-10px">Wallet Balance(<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $order_detls[0]['wallet_balance'];
                                        $total = $total - $order_detls[0]['wallet_balance']; ?></td>
                                </tr>

                                <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $order_detls[0]['order_total'] + $order_detls[0]['delivery_charge'] ?>">
                                <input type="hidden" name="final_amount" id="final_amount" value="<?php echo $order_detls[0]['final_total']; ?>">

                                <tr>
                                    <th class="w-10px">Promo Code Discount (<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $order_detls[0]['promo_discount'];
                                        $total = floatval($total -
                                            $order_detls[0]['promo_discount']); ?></td>
                                </tr>
                                <?php
                                if (isset($order_detls[0]['discount']) && $order_detls[0]['discount'] > 0) {
                                    $discount = $order_detls[0]['total_payable']  *  ($order_detls[0]['discount'] / 100);
                                    $total = round($order_detls[0]['total_payable'] - $discount, 2);
                                }
                                ?>
                                <tr>
                                    <th class="w-10px">Payable Total(<?= $settings['currency'] ?>)</th>
                                    <td><input type="text" class="form-control" id="final_total" name="final_total" value="<?= $total; ?>" disabled></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Payment Method</th>
                                    <td><?php echo $order_detls[0]['payment_method']; ?>
                                        <?php if (isset($transaction_search_res)) { 
                                            ?>

                                            <a href="javascript:void(0)" class="edit_transaction action-btn btn btn-success btn-xs mr-1 mb-1" title="Update bank transfer recipt status " data-id="<?= $order_detls[0]['id'] ?>" data-txn_id="<?= $transaction_search_res[0]['txn_id'] ?>" data-status="<?= $transaction_search_res[0]['status'] ?>" data-message="<?= $transaction_search_res[0]['message'] ?>" data-target="#payment_transaction_modal" data-toggle="modal"><i class="fa fa-pen"></i></a>

                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                if (!empty($bank_transfer)) { ?>
                                    <tr>
                                        <th class="w-10px">Bank Transfers</th>
                                        <td>
                                            <div class="col-md-6">
                                                <?php $status = ["history", "ban", "check"]; ?>
                                                <a class="btn btn-primary btn-xs mr-1 mb-1 " title="Current Status" href="javascript:void(0)" data-id="<?= $order_detls[0]['id']; ?>"><i class="fa fa-<?= $status[$bank_transfer[0]['status']] ?>"></i></a>
                                                <?php $i = 1;
                                                foreach ($bank_transfer as $row1) { ?>
                                                    <small>[<a href="<?= base_url() . $row1['attachments'] ?>" target="_blank">Attachment <?= $i ?> </a>] </small>
                                                    <?php if ($row1['status'] == 0) { ?>
                                                        <label class="badge badge-warning"><?= !empty($this->lang->line('pending')) ? $this->lang->line('pending') : 'Pending' ?></label>
                                                    <?php } else if ($row1['status'] == 1) { ?>
                                                        <label class="badge badge-danger"><?= !empty($this->lang->line('rejected')) ? $this->lang->line('rejected') : 'Rejected' ?></label>
                                                    <?php } else if ($row1['status'] == 2) { ?>
                                                        <label class="badge badge-primary"><?= !empty($this->lang->line('accepted')) ? $this->lang->line('accepted') : 'Accepted' ?></label>
                                                    <?php } else { ?>
                                                        <label class="badge badge-danger"><?= !empty($this->lang->line('invalid_value')) ? $this->lang->line('invalid_value') : 'Invalid Value' ?></label>
                                                    <?php } ?>
                                                    <a class="delete-receipt btn btn-danger btn-xs mr-1 mb-1" title="Delete" href="javascript:void(0)" data-id="<?= $row1['id']; ?>"><i class="fa fa-trash"></i></a>
                                                <?php $i++;
                                                } ?>
                                                <select name="update_receipt_status" id="update_receipt_status" class="form-control status" data-id="<?= $order_detls[0]['id']; ?>" data-user_id="<?= $order_detls[0]['user_id']; ?>">
                                                    <option value=''>Select Status</option>
                                                    <option value="1" <?= (isset($bank_transfer[0]['status']) && $bank_transfer[0]['status'] == 1) ? "selected" : ""; ?>>Rejected</option>
                                                    <option value="2" <?= (isset($bank_transfer[0]['status']) && $bank_transfer[0]['status'] == 2) ? "selected" : ""; ?>>Accepted</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] != 'digital_product') {
                                    $address_number = fetch_details('addresses', 'id =' . $order_detls[0]['address_id'], 'mobile');
                                ?>
                                    <tr>
                                        <th class="w-10px">Address</th>
                                        <td><?php echo $order_detls[0]['address'] .  ' ,Mobile- ' . $address_number[0]['mobile']; ?></td>
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
                            <?php
                            // echo "<pre>";
                            // print_R($order_detls) 
                            ?>
                            <? //= ($order_detls[0]['mobile'] != '' && isset($order_detls[0]['mobile'])) ? $order_detls[0]['mobile'] : ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($mobile_data[0]['mobile']) - 3) . substr($mobile_data[0]['mobile'], -3) : $mobile_data[0]['mobile'])   
                            ?>


                            <a href="https://api.whatsapp.com/send?phone=<?= ($order_detls[0]['country_code']) ?><?= ($order_detls[0]['mobile'] != '' && isset($order_detls[0]['mobile'])) ?
                                                                                                                        $order_detls[0]['mobile'] : ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($mobile_data[0]['mobile']) - 3) . substr($mobile_data[0]['mobile'], -3) : $mobile_data[0]['mobile'])   ?>&amp;text=Hello <?= $order_detls[0]['uname'] ?>, Your order with ID : <?= $order_detls[0]['order_id'] ?> and is <?= $order_detls[0]['oi_active_status'] ?>. Please take a note of it. If you have further queries feel free to contact us. Thank you." target="_blank" title="Send Whatsapp Notification For Order" class="btn btn-success"><i class="fa fa-whatsapp"></i> Send Whatsapp Notification</a>
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
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="refund_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="user_name">Payment Refund</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-info">
                            <!-- form start -->
                            <form class="form-horizontal " id="refund_form" action="<?= base_url('admin/orders/refund_payment'); ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                    <input type="hidden" name="item_id" id="item_id">
                                </div>
                                <div class="card-body pad">
                                    <div class="form-group ">
                                        <label for="transaction_id">Transaction Id</label>
                                        <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="Transaction Id" disabled />
                                    </div>
                                    <div class="form-group ">
                                        <label for="txn_amount">Amount</label>
                                        <input type="text" class="form-control" name="txn_amount" id="txn_amount" placeholder="Amount" disabled />
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-secondary" id="submit_btn">Refund</button>
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