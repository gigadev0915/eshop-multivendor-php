<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['razorpay', 'stripe', 'paystack', 'flutterwave', 'midtrans']);
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Order_model','Transaction_model']);

        if (!has_permissions('read', 'orders')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        } else {
            $this->session->set_flashdata('authorize_flag', "");
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-orders';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Order Management | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Order Management  | ' . $settings['app_name'];
            $this->data['about_us'] = get_settings('about_us');
            $this->data['curreny'] = get_settings('currency');
            $orders_count['awaiting'] = orders_count("awaiting");
            $orders_count['received'] = orders_count("received");
            $orders_count['processed'] = orders_count("processed");
            $orders_count['shipped'] = orders_count("shipped");
            $orders_count['delivered'] = orders_count("delivered");
            $orders_count['cancelled'] = orders_count("cancelled");
            $orders_count['returned'] = orders_count("returned");
            $this->data['status_counts'] = $orders_count;

            if (isset($_GET['edit_id'])) {
                $order_item_data = fetch_details('order_items', ['id' => $_GET['edit_id']], 'order_id,product_name,user_id');
                $order_data = fetch_details('orders', ['id' => $order_item_data[0]['order_id']], 'email');
                $user_data = fetch_details('users', ['id' => $order_item_data[0]['user_id']], 'username');
                $this->data['fetched'] = $order_data;
                $this->data['order_item_data'] = $order_item_data;
                $this->data['user_data'] = $user_data[0];
                // $this->data['attribute'] = $attribute_value[0]['value'];
            }
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function view_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Order_model->get_orders_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function view_order_items()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Order_model->get_order_items_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function get_digital_order_mails()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Order_model->get_digital_order_mail_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function send_digital_product()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');
            $this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
            $this->form_validation->set_rules('pro_input_file', 'Attachment file', 'trim|required|xss_clean');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = strip_tags(validation_errors());
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }

            $mail =  $this->Order_model->send_digital_product($_POST);
            // print_R($mail);

            if ($mail['error'] == true) {
                $this->response['error'] = true;
                $this->response['message'] = "Cannot send mail. You can try to send mail manually.";
                $this->response['data'] = $mail['message'];
                echo json_encode($this->response);
                return false;
            } else {
                $this->response['error'] = false;
                $this->response['message'] = 'Mail sent successfully.';
                $this->response['data'] = array();
                echo json_encode($this->response);
                update_details(['active_status' => 'delivered'], ['id' => $_POST['order_item_id']], 'order_items');
                update_details(['is_sent' => 1], ['id' => $_POST['order_item_id']], 'order_items');
                $data = array(
                    'order_id' => $_POST['order_id'],
                    'order_item_id' => $_POST['order_item_id'],
                    'subject' => $_POST['subject'],
                    'message' => $_POST['message'],
                    'file_url' => $_POST['pro_input_file'],
                );
                insert_details($data, 'digital_orders_mails');
                return false;
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function delete_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('delete', 'orders'), PERMISSION_ERROR_MSG, 'orders')) {
                return false;
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            $delete = array(
                "order_items" => 0,
                "orders" => 0,
                "order_bank_transfer" => 0
            );
            $orders = $this->db->where(' oi.order_id=' . $_GET['id'])->join('orders o', 'o.id=oi.order_id', 'right')->get('order_items oi')->result_array();
            if (!empty($orders)) {
                // delete orders
                if (delete_details(['order_id' => $_GET['id']], 'order_items')) {
                    $delete['order_items'] = 1;
                }
                if (delete_details(['id' => $_GET['id']], 'orders')) {
                    $delete['orders'] = 1;
                }
                if (delete_details(['order_id' => $_GET['id']], 'order_bank_transfer')) {
                    $delete['order_bank_transfer'] = 1;
                }
            }
            $deleted = FALSE;
            if (!in_array(0, $delete)) {
                $deleted = TRUE;
            }
            if ($deleted == TRUE) {
                $response['error'] = false;
                $response['message'] = 'Deleted Successfully';
                $response['permission'] = !has_permissions('delete', 'orders');
            } else {
                $response['error'] = true;
                $response['message'] = 'Something went wrong';
            }
            echo json_encode($response);
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function delete_order_items()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('delete', 'orders'), PERMISSION_ERROR_MSG, 'orders')) {
                return false;
            }
            $delete = array(
                "order_items" => 0,
                "orders" => 0,
                "order_bank_transfer" => 0
            );
            /* check order items */
            $order_items = fetch_details('order_items', ['id' => $_GET['id']], 'id,order_id');
            if (delete_details(['id' => $_GET['id']], 'order_items')) {
                $delete['order_items'] = 1;
            }
            $res_order_id = array_values(array_unique(array_column($order_items, "order_id")));
            for ($i = 0; $i < count($res_order_id); $i++) {
                $orders = $this->db->where(' oi.order_id=' . $res_order_id[$i])->join('orders o', 'o.id=oi.order_id', 'right')->get('order_items oi')->result_array();
                if (empty($orders)) {
                    // delete orders
                    if (delete_details(['id' => $res_order_id[$i]], 'orders')) {
                        $delete['orders'] = 1;
                    }
                    if (delete_details(['order_id' => $res_order_id[$i]], 'order_bank_transfer')) {
                        $delete['order_bank_transfer'] = 1;
                    }
                }
            }

            if ($delete['order_items'] == TRUE) {
                $response['error'] = false;
                $response['message'] = 'Deleted Successfully';
                $response['permission'] = !has_permissions('delete', 'orders');
            } else {
                $response['error'] = true;
                $response['message'] = 'Something went wrong';
            }
            echo json_encode($response);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    /* Update complete order status */
    public function update_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'orders'), PERMISSION_ERROR_MSG, 'orders')) {
                return false;
            }
            $msg = '';
            $order_method = fetch_details('orders', ['id' => $_POST['orderid']], 'payment_method');
            if ($order_method[0]['payment_method'] == 'bank_transfer') {
                $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $_POST['orderid']]);
                $transaction_status = fetch_details('transactions', ['order_id' => $_POST['orderid']], 'status');
                if (empty($bank_receipt) || strtolower($transaction_status[0]['status']) != 'success') {
                    $this->response['error'] = true;
                    $this->response['message'] = "Order Status can not update, Bank verification is remain from transactions.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            if (isset($_POST['deliver_by']) && !empty($_POST['deliver_by']) && isset($_POST['orderid']) && !empty($_POST['orderid'])) {
                $where = "id = " . $_POST['orderid'] . "";
                $current_delivery_boy = fetch_details('orders', $where, 'delivery_boy_id');
                $settings = get_settings('system_settings', true);
                $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                $user_res = fetch_details('users', ['id' => $_POST['deliver_by']], 'fcm_id,username,mobile,email');
                $fcm_ids = array();
                //custom message
                if (isset($user_res[0]) && !empty($user_res[0])) {
                    if ((isset($current_delivery_boy[0]['delivery_boy_id']) && $current_delivery_boy[0]['delivery_boy_id'] == $_POST['deliver_by']) || (isset($_POST['status']) && $_POST['status'] == 'cancelled')) {
                        if ($_POST['status'] == 'received') {
                            $type = ['type' => "customer_order_received"];
                        } elseif ($_POST['status'] == 'processed') {
                            $type = ['type' => "customer_order_processed"];
                        } elseif ($_POST['status'] == 'shipped') {
                            $type = ['type' => "customer_order_shipped"];
                        } elseif ($_POST['status'] == 'delivered') {
                            $type = ['type' => "customer_order_delivered"];
                        } elseif ($_POST['status'] == 'cancelled') {
                            $type = ['type' => "customer_order_cancelled"];
                        } elseif ($_POST['status'] == 'returned') {
                            $type = ['type' => "customer_order_returned"];
                        }
                        $custom_notification = fetch_details('custom_notifications', $type, '');
                        $hashtag_cutomer_name = '< cutomer_name >';
                        $hashtag_order_id = '< order_item_id >';
                        $hashtag_application_name = '< application_name >';
                        $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                        $hashtag = html_entity_decode($string);
                        $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $_POST['orderid'], $app_name), $hashtag);
                        $message = output_escaping(trim($data, '"'));
                        $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . ' Order status updated to' . $_POST['val'] . ' for order ID #' . $_POST['orderid'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                        $fcmMsg = array(
                            'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                            'body' =>  $customer_msg,
                            'type' => "order",
                        );
                        notify_event(
                            $type['type'],
                            ["delivery_boy" => [$user_res[0]['email']]],
                            ["delivery_boy" => [$user_res[0]['mobile']]],
                            ["orders.id" => $_POST['order_id']]
                        );
                    } else {
                        $custom_notification =  fetch_details('custom_notifications', ['type' => "delivery_boy_order_deliver"], '');
                        $hashtag_cutomer_name = '< cutomer_name >';
                        $hashtag_order_id = '< order_id >';
                        $hashtag_application_name = '< application_name >';
                        $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                        $hashtag = html_entity_decode($string);
                        $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $_POST['orderid'], $app_name), $hashtag);
                        $message = output_escaping(trim($data, '"'));
                        $customer_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $user_res[0]['username'] . ' you have new order to be deliver order ID #' . $_POST['orderid'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                        $fcmMsg = array(
                            'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : " You have new order to deliver",
                            'body' => $customer_msg,
                            'type' => "order",
                        );
                        $msg = 'Delivery Boy Updated. ';
                        notify_event(
                            "delivery_boy_order_deliver",
                            ["delivery_boy" => [$user_res[0]['email']]],
                            ["delivery_boy" => [$user_res[0]['mobile']]],
                            ["orders.id" => $_POST['order_id']]
                        );
                    }
                }
                if (!empty($user_res[0]['fcm_id'])) {
                    $fcm_ids[0][] = $user_res[0]['fcm_id'];
                    send_notification($fcmMsg, $fcm_ids);
                }
                $where = [
                    'id' => $_POST['orderid']
                ];


                if ($this->Order_model->update_order(['delivery_boy_id' => $_POST['deliver_by']], $where)) {
                    $delivery_error = false;
                }
            }

            $res = validate_order_status($_POST['orderid'], $_POST['val'], 'orders');
            if ($res['error']) {
                $this->response['error'] = true;
                $this->response['message'] = $msg . $res['message'];
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            $priority_status = [
                'received' => 0,
                'processed' => 1,
                'shipped' => 2,
                'delivered' => 3,
                'cancelled' => 4,
                'returned' => 5,
            ];

            $update_status = 1;
            $error = TRUE;
            $message = '';

            $where_id = "id = " . $_POST['orderid'] . " and (active_status != 'cancelled' and active_status != 'returned' ) ";
            $where_order_id = "order_id = " . $_POST['orderid'] . " and (active_status != 'cancelled' and active_status != 'returned' ) ";

            $order_items_details = fetch_details('order_items', $where_order_id, 'active_status');
            $counter = count($order_items_details);
            $cancel_counter = 0;
            foreach ($order_items_details as $row) {
                if ($row['active_status'] == 'cancelled') {
                    ++$cancel_counter;
                }
            }
            if ($cancel_counter == $counter) {
                $update_status = 0;
            }

            if (isset($_POST['orderid']) && isset($_POST['field']) && isset($_POST['val'])) {
                if ($_POST['field'] == 'status' && $update_status == 1) {

                    $current_orders_status = fetch_details('orders', $where_id, 'user_id,active_status');
                    $user_id = $current_orders_status[0]['user_id'];
                    $current_orders_status = $current_orders_status[0]['active_status'];

                    if ($priority_status[$_POST['val']] > $priority_status[$current_orders_status]) {
                        $set = [
                            $_POST['field'] => $_POST['val'] // status => 'proceesed'
                        ];

                        // Update Active Status of Order Table
                        if ($this->Order_model->update_order($set, $where_id, $_POST['json'])) {
                            if ($this->Order_model->update_order(['active_status' => $_POST['val']], $where_id)) {
                                if ($this->Order_model->update_order($set, $where_order_id, $_POST['json'], 'order_items')) {
                                    if ($this->Order_model->update_order(['active_status' => $_POST['val']], $where_order_id, false, 'order_items')) {
                                        $error = false;
                                    }
                                }
                            }
                        }

                        if ($error == false) {
                            //custom message
                            $settings = get_settings('system_settings', true);
                            $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                            if ($_POST['status'] == 'received') {
                                $type = ['type' => "customer_order_received"];
                            } elseif ($_POST['status'] == 'processed') {
                                $type = ['type' => "customer_order_processed"];
                            } elseif ($_POST['status'] == 'shipped') {
                                $type = ['type' => "customer_order_shipped"];
                            } elseif ($_POST['status'] == 'delivered') {
                                $type = ['type' => "customer_order_delivered"];
                            } elseif ($_POST['status'] == 'cancelled') {
                                $type = ['type' => "customer_order_cancelled"];
                            } elseif ($_POST['status'] == 'returned') {
                                $type = ['type' => "customer_order_returned"];
                            }
                            $custom_notification = fetch_details('custom_notifications', $type, '');
                            $hashtag_cutomer_name = '< cutomer_name >';
                            $hashtag_order_id = '< order_item_id >';
                            $hashtag_application_name = '< application_name >';
                            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $_POST['orderid'], $app_name), $hashtag);
                            $message = output_escaping(trim($data, '"'));
                            $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . ' Order status updated to' . $_POST['val'] . ' for order ID #' . $_POST['orderid'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                            $user_res = fetch_details('users', ['id' => $user_id], 'username,fcm_id,mobile,email');
                            $fcm_ids = array();
                            if (!empty($user_res[0]['fcm_id'])) {
                                $fcmMsg = array(
                                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : " Order status updated",
                                    'body' => $customer_msg,
                                    'type' => "order"
                                );

                                $fcm_ids[0][] = $user_res[0]['fcm_id'];
                                send_notification($fcmMsg, $fcm_ids);
                            }
                            notify_event(
                                $type['type'],
                                ["customer" => [$user_res[0]['email']]],
                                ["customer" => [$user_res[0]['mobile']]],
                                ["orders.id" => $_POST['order_id']]
                            );
                            /* Process refer and earn bonus */
                            process_refund($_POST['orderid'], $_POST['val'], 'orders');
                            if (trim($_POST['val'] == 'cancelled')) {
                                $data = fetch_details('order_items', ['order_id' => $_POST['orderid']], 'product_variant_id,quantity');
                                $product_variant_ids = [];
                                $qtns = [];
                                foreach ($data as $d) {
                                    array_push($product_variant_ids, $d['product_variant_id']);
                                    array_push($qtns, $d['quantity']);
                                }

                                update_stock($product_variant_ids, $qtns, 'plus');
                            }
                            $response = process_referral_bonus($user_id, $_POST['orderid'], $_POST['val']);
                            $message = 'Status Updated Successfully';
                            update_details(['updated_by' => $_SESSION['user_id']], ['order_id' =>  $_POST['orderid']], 'order_items');
                        }
                    }
                }
                if ($error == true) {
                    $message = $msg . ' Status Updation Failed';
                }
            }
            $response['error'] = $error;
            $response['message'] = $message;
            $response['total_amount'] = (!empty($data) ? $data : '');
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            print_r(json_encode($response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function edit_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (!has_permissions('read', 'orders')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            $bank_transfer = array();
            $this->data['main_page'] = FORMS . 'edit-orders';
            $settings = get_settings('system_settings', true);

            $this->data['title'] = 'View Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'View Order | ' . $settings['app_name'];
            $res = $this->Order_model->get_order_details(['o.id' => $_GET['edit_id']]);


            if (is_exist(['id' => $res[0]['address_id']], 'addresses')) {
                $area_id = fetch_details('addresses', ['id' => $res[0]['address_id']], 'area_id');

                if (!empty($area_id) && $area_id[0]['area_id'] != 0) {
                    $zipcode_id = fetch_details('areas', ['id' => $area_id[0]['area_id']], 'zipcode_id');
                    if (!empty($zipcode_id)) {
                        $this->data['delivery_res'] = $this->db->where(['ug.group_id' => '3', 'u.active' => 1])->where('find_in_set(' . $zipcode_id[0]['zipcode_id'] . ', u.serviceable_zipcodes)!=', 0)->join('users_groups ug', 'ug.user_id = u.id')->get('users u')->result_array();
                    } else {
                        $this->data['delivery_res'] = $this->db->where(['ug.group_id' => '3', 'u.active' => 1])->join('users_groups ug', 'ug.user_id = u.id')->get('users u')->result_array();
                    }
                } else {
                    $this->data['delivery_res'] = $this->db->where(['ug.group_id' => '3', 'u.active' => 1])->join('users_groups ug', 'ug.user_id = u.id')->get('users u')->result_array();
                }
            } else {
                $this->data['delivery_res'] = $this->db->where(['ug.group_id' => '3', 'u.active' => 1])->join('users_groups ug', 'ug.user_id = u.id')->get('users u')->result_array();
            }
            if ($res[0]['payment_method'] == "bank_transfer") {
                $bank_transfer = fetch_details('order_bank_transfer', ['order_id' => $res[0]['order_id']]);
                $transaction_search_res = fetch_details('transactions', ['order_id' => $res[0]['order_id']]);
            }
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
                // check for notification param
                if (isset($_GET['noti_id']) && !empty($_GET['noti_id']) && is_numeric($_GET['noti_id'])) {
                    update_details(['read_by' => '1'], ['id' => $_GET['noti_id']], 'system_notification');
                }
                $items = [];
                foreach ($res as $row) {
                    // echo "<pre>";
                    // print_R($row);
                    $multipleWhere = ['seller_id' => $row['seller_id'], 'order_id' => $row['id']];
                    $order_charge_data = $this->db->where($multipleWhere)->get('order_charges')->result_array();

                    $updated_username = fetch_details('users', 'id =' . $row['updated_by'], 'username');
                    $address_number = fetch_details('addresses', 'id =' . $row['address_id'], 'mobile');
                    // print_R($address_number);
                    $deliver_by = fetch_details('users', 'id =' . $row['delivery_boy_id'], 'username');
                    $temp['id'] = $row['order_item_id'];
                    $temp['item_otp'] = $row['item_otp'];
                    $temp['tracking_id'] = $row['tracking_id'];
                    $temp['courier_agency'] = $row['courier_agency'];
                    $temp['url'] = $row['url'];
                    $temp['product_id'] = $row['product_id'];
                    $temp['product_variant_id'] = $row['product_variant_id'];
                    $temp['product_type'] = $row['type'];
                    $temp['pname'] = $row['pname'];
                    $temp['quantity'] = $row['quantity'];
                    $temp['is_cancelable'] = $row['is_cancelable'];
                    $temp['is_returnable'] = $row['is_returnable'];
                    $temp['tax_amount'] = $row['tax_amount'];
                    $temp['discounted_price'] = $row['discounted_price'];
                    $temp['price'] = $row['price'];
                    $temp['row_price'] = $row['row_price'];
                    $temp['updated_by'] = $updated_username[0]['username'];
                    $temp['deliver_by'] = $deliver_by[0]['username'];
                    $temp['active_status'] = $row['oi_active_status'];
                    $temp['product_image'] = $row['product_image'];
                    $temp['product_variants'] = get_variants_values_by_id($row['product_variant_id']);
                    $temp['product_type'] = $row['type'];
                    $temp['product_id'] = $row['product_id'];
                    $temp['pickup_location'] = $row['pickup_location'];
                    $temp['seller_otp'] = $order_charge_data[0]['otp'];
                    $temp['is_sent'] = $row['is_sent'];
                    $temp['seller_id'] = $row['seller_id'];
                    $temp['download_allowed'] = $row['download_allowed'];
                    $temp['user_email'] = $row['user_email'];
                    $temp['product_slug'] = $row['product_slug'];
                    $temp['sku'] = isset($row['product_sku']) && !empty($row['product_sku']) ? $row['product_sku'] : $row['sku'];
                    $temp['address_number'] = $address_number[0]['mobile'];
                    $temp['county_code'] = $row[0]['county_code'];
                    array_push($items, $temp);
                }
                // echo "<pre>";
                // print_r($res[0]['country_code']);
                $this->data['order_detls'] = $res;
                $this->data['bank_transfer'] = $bank_transfer;
                $this->data['transaction_search_res'] = $transaction_search_res;
                $this->data['items'] = $items;
                $this->data['settings'] = get_settings('system_settings', true);
                $this->data['shipping_method'] = get_settings('shipping_method', true);
                $this->load->view('admin/template', $this->data);
            } else {
                redirect('admin/orders/', 'refresh');
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    /* Update individual order item status */
    public function update_order_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'orders'), PERMISSION_ERROR_MSG, 'orders')) {
                return false;
            }
            if (empty($_POST['status']) && empty($_POST['deliver_by'])) {
                $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean', array('required' => "Please select status or delivery boy for updation."));
            }
            if ($_POST['status'] == 'cancelled' || $_POST['status'] == 'returned') {
                $this->form_validation->set_rules('order_item_id[]', 'Order Item ID', 'trim|required|xss_clean', array('required' => "Please select atleast one item of seller for order cancelation or return."));
            }
            if (empty($_POST['seller_id']) || !isset($_POST['seller_id'])) {
                $this->form_validation->set_rules('seller_id', 'Seller ID', 'trim|required|xss_clean', array('required' => "Please select atleast one seller to update order item's."));
            }
            if (isset($_POST['deliver_by']) && !empty($_POST['deliver_by']) && $_POST['deliver_by'] != '') {
                $this->form_validation->set_rules('deliver_by', 'Delvery Boy Id', 'trim|numeric|xss_clean');
            }
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] != '') {
                $this->form_validation->set_rules('status', 'Status', 'trim|xss_clean|in_list[received,processed,shipped,delivered,cancelled,returned]');
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = strip_tags(validation_errors());
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }

            //for order item
            $order_itam_id = [];
            $order_itam_ids = [];
            if ($_POST['status'] == 'cancelled' || $_POST['status'] == 'returned') {
                $order_itam_ids = $_POST['order_item_id'];
            } else {
                $order_itam_id = fetch_details('order_items', ['order_id' => $_POST['order_id'], 'seller_id' => $_POST['seller_id'], 'active_status !=' => 'cancelled'], 'id');
                foreach ($order_itam_id as $ids) {
                    array_push($order_itam_ids, $ids['id']);
                }
            }
            if (empty($order_itam_ids)) {
                $this->response['error'] = true;
                $this->response['message'] = 'You can not assign delivery boy of cancelled order.';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }
            $s = [];
            foreach ($order_itam_ids as $ids) {
                $order_detail = fetch_details('order_items', ['id' => $ids], 'is_sent,hash_link,product_variant_id');
                $product_data = fetch_details('product_variants', ['id' => $order_detail[0]['product_variant_id']], 'product_id');
                $product_detail = fetch_details('products', ['id' => $product_data[0]['product_id']], 'type');
                if (empty($order_detail[0]['hash_link']) || $order_detail[0]['hash_link'] == '' || $order_detail[0]['hash_link'] == null) {
                    array_push($s, $order_detail[0]['is_sent']);
                }
            }

            $order_data = fetch_details('order_items', ['id' => $order_itam_ids[0]], 'product_variant_id')[0]['product_variant_id'];
            $product_id = fetch_details('product_variants', ['id' => $order_data], 'product_id')[0]['product_id'];
            $product_type = fetch_details('products', ['id' => $product_id], 'type')[0]['type'];

            if ($product_type == 'digital_product' && in_array(0, $s)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Some of the selected item have not been sent,Please select an item that has been sent.';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }


            $order_items = fetch_details('order_items', "", '*', "", "", "", "", "id", $order_itam_ids);

            if (empty($order_items)) {
                $this->response['error'] = true;
                $this->response['message'] = 'No Order Item Found';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }

            if (count($order_itam_ids) != count($order_items)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Some item was not found on status update';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
            }

            $order_id = $order_items[0]['order_id'];

            $order_method = fetch_details('orders', ['id' => $order_id], 'payment_method');
            $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $order_id]);
            $transaction_status = fetch_details('transactions', ['order_id' => $order_id], 'status');

            /* validate bank transfer method status */
            if (isset($order_method[0]['payment_method']) && $order_method[0]['payment_method'] == 'bank_transfer') {
                if ($_POST['status'] != 'cancelled' && (empty($bank_receipt) || strtolower($transaction_status[0]['status']) != 'success' || $bank_receipt[0]['status'] == "0" || $bank_receipt[0]['status'] == "1")) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Order item status can't update, Bank verification is remain from transactions for this order.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            $current_status = fetch_details('order_items', ['seller_id' => $_POST['seller_id'], 'order_id' => $_POST['order_id']], 'active_status,delivery_boy_id');
            $awaitingPresent = false;

            foreach ($current_status as $item) {
                if ($item['active_status'] === 'awaiting') {
                    $awaitingPresent = true;
                    break;
                }
            }

            // delivery boy update here
            $message = '';
            $delivery_boy_updated = 0;
            $delivery_boy_id = (isset($_POST['deliver_by']) && !empty(trim($_POST['deliver_by']))) ? $this->input->post('deliver_by', true) : 0;

            // assign delivery boy when status is processed
            // print_r($_POST['status']);
            // print_r($delivery_boy_id);
            // die;
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == 'processed') {
                if (!isset($delivery_boy_id) || empty($delivery_boy_id) || $delivery_boy_id == 0) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Please select delivery boy to mark this order as processed.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            
            // validate delivery boy when status is shipped
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == 'shipped') {
                if (!isset($current_status[0]['delivery_boy_id']) || empty($current_status[0]['delivery_boy_id']) || $current_status[0]['delivery_boy_id'] == 0) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Please select delivery boy to mark this order as shipped.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }


            if (!empty($delivery_boy_id)) {
                if ($awaitingPresent) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Delivery Boy can't assign to awaiting orders ! please confirm the order first.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                } else {

                    $delivery_boy = fetch_details('users', ['id' => trim($delivery_boy_id)], 'id');
                    if (empty($delivery_boy)) {
                        $this->response['error'] = true;
                        $this->response['message'] = "Invalid Delivery Boy";
                        $this->response['data'] = array();
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        print_r(json_encode($this->response));
                        return false;
                    } else {
                        $current_delivery_boys = fetch_details('order_items', "",  '*', "", "", "", "", "id", $order_itam_ids);

                        $settings = get_settings('system_settings', true);
                        $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                        if (isset($current_delivery_boys[0]['delivery_boy_id']) && !empty($current_delivery_boys[0]['delivery_boy_id'])) {
                            $user_res = fetch_details('users', "",  'fcm_id,username,email,mobile', "", "", "", "", "id", array_column($current_delivery_boys, "delivery_boy_id"));
                        } else {
                            $user_res = fetch_details('users', ['id' => $delivery_boy_id], 'fcm_id,username,email,mobile');
                        }

                        $fcm_ids = array();
                        if (isset($user_res[0]) && !empty($user_res[0])) {
                            //custom message
                            $current_delivery_boy = array_column($current_delivery_boys, "delivery_boy_id");

                            if ($_POST['status'] == 'received') {
                                $type = ['type' => "customer_order_received"];
                            } elseif ($_POST['status'] == 'processed') {
                                $type = ['type' => "customer_order_processed"];
                            } elseif ($_POST['status'] == 'shipped') {
                                $type = ['type' => "customer_order_shipped"];
                            } elseif ($_POST['status'] == 'delivered') {
                                $type = ['type' => "customer_order_delivered"];
                            } elseif ($_POST['status'] == 'cancelled') {
                                $type = ['type' => "customer_order_cancelled"];
                            } elseif ($_POST['status'] == 'returned') {
                                $type = ['type' => "customer_order_returned"];
                            }
                            $custom_notification = fetch_details('custom_notifications', $type, '');
                            if (!empty($current_delivery_boy[0]) && count($current_delivery_boy) > 1) {
                                for ($i = 0; $i < count($current_delivery_boys); $i++) {
                                    $hashtag_cutomer_name = '< cutomer_name >';
                                    $hashtag_order_id = '< order_item_id >';
                                    $hashtag_application_name = '< application_name >';
                                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                                    $hashtag = html_entity_decode($string);
                                    $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[$i]['username'], $order_items[0]['order_id'], $app_name), $hashtag);
                                    $message = output_escaping(trim($data, '"'));
                                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[$i]['username'] . ' ' . 'Order status updated to' . $_POST['status'] . ' for order ID #' . $order_items[0]['order_id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                                    $fcmMsg = array(
                                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                                        'body' => $customer_msg,
                                        'type' => "order",
                                        'order_id' => $order_items[0]['order_id'],
                                    );
                                    if (!empty($user_res[$i]['fcm_id'])) {
                                        $fcm_ids[0][] = $user_res[$i]['fcm_id'];
                                    }
                                    notify_event(
                                        $type['type'],
                                        ["delivery_boy" => [$user_res[0]['email']]],
                                        ["delivery_boy" => [$user_res[0]['mobile']]],
                                        ["orders.id" => $order_items[0]['order_id']]
                                    );
                                }
                                $message = 'Delivery Boy Updated.';
                                $delivery_boy_updated = 1;
                            } else {
                                if (isset($current_delivery_boys[0]['delivery_boy_id']) && $current_delivery_boys[0]['delivery_boy_id'] == $_POST['deliver_by']) {
                                    if ($_POST['status'] == 'received') {
                                        $type = ['type' => "customer_order_received"];
                                    } elseif ($_POST['status'] == 'processed') {
                                        $type = ['type' => "customer_order_processed"];
                                    } elseif ($_POST['status'] == 'shipped') {
                                        $type = ['type' => "customer_order_shipped"];
                                    } elseif ($_POST['status'] == 'delivered') {
                                        $type = ['type' => "customer_order_delivered"];
                                    } elseif ($_POST['status'] == 'cancelled') {
                                        $type = ['type' => "customer_order_cancelled"];
                                    } elseif ($_POST['status'] == 'returned') {
                                        $type = ['type' => "customer_order_returned"];
                                    }
                                    $custom_notification = fetch_details('custom_notifications', $type, '');
                                    $hashtag_cutomer_name = '< cutomer_name >';
                                    $hashtag_order_id = '< order_item_id >';
                                    $hashtag_application_name = '< application_name >';
                                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                                    $hashtag = html_entity_decode($string);
                                    $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $order_items[0]['order_id'], $app_name), $hashtag);
                                    $message = output_escaping(trim($data, '"'));
                                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . ' ' . 'Order status updated to' . $_POST['status'] . ' for order ID #' . $order_items[0]['order_id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                                    $fcmMsg = array(
                                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                                        'body' => $customer_msg,
                                        'type' => 'order',
                                        'order_id' => $order_items[0]['order_id'],
                                    );
                                    $message = 'Delivery Boy Updated';
                                    $delivery_boy_updated = 1;
                                    notify_event(
                                        $type['type'],
                                        ["delivery_boy" => [$user_res[0]['email']]],
                                        ["delivery_boy" => [$user_res[0]['mobile']]],
                                        ["orders.id" => $_POST['order_id']]
                                    );
                                } else {
                                    $custom_notification =  fetch_details('custom_notifications',  ['type' => "delivery_boy_order_deliver"], '');
                                    $hashtag_cutomer_name = '< cutomer_name >';
                                    $hashtag_order_id = '< order_id >';
                                    $hashtag_application_name = '< application_name >';
                                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                                    $hashtag = html_entity_decode($string);
                                    $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $order_items[0]['order_id'], $app_name), $hashtag);
                                    $message = output_escaping(trim($data, '"'));
                                    $customer_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $user_res[0]['username'] . ' ' . ' you have new order to be deliver order ID #' . $order_items[0]['order_id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                                    $fcmMsg = array(
                                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : " You have new order to deliver",
                                        'body' => $customer_msg,
                                        'type' => "order",
                                        'order_id' => $order_items[0]['order_id']
                                    );
                                    $message = 'Delivery Boy Updated.';
                                    $delivery_boy_updated = 1;
                                }
                                notify_event(
                                    'delivery_boy_order_deliver',
                                    ["delivery_boy" => [$user_res[0]['email']]],
                                    ["delivery_boy" => [$user_res[0]['mobile']]],
                                    ["orders.id" => $order_items[0]['order_id']]
                                );
                                if (!empty($user_res[0]['fcm_id'])) {
                                    $fcm_ids[0][] = $user_res[0]['fcm_id'];
                                }
                            }
                        }
                        if (!empty($fcm_ids)) {
                            send_notification($fcmMsg, $fcm_ids);
                        }

                        if ($this->Order_model->update_order(['delivery_boy_id' => $delivery_boy_id], $order_itam_ids, false, 'order_items')) {
                            $delivery_error = false;
                        }
                    }
                }
            }

            $item_ids = implode(",", $order_itam_ids);

            $res = validate_order_status($item_ids, $_POST['status']);

            if ($res['error']) {
                $this->response['error'] = $delivery_boy_updated == 1 ? false : true;
                $this->response['message'] = (isset($_POST['status']) && !empty($_POST['status']) && $delivery_error == FALSE) ? $message . $res['message'] :  $message;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            if (!empty($order_items)) {
                for ($j = 0; $j < count($order_items); $j++) {
                    $order_item_id = $order_items[$j]['id'];
                    /* velidate bank transfer method status */
                    $order_method = fetch_details('orders', ['id' => $order_items[$j]['order_id']], 'payment_method');
                    if ($order_method[0]['payment_method'] == 'bank_transfer') {
                        $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $order_items[$j]['order_id']]);
                        $transaction_status = fetch_details('transactions', ['order_id' => $order_items[$j]['order_id']], 'status');
                        if ($_POST['status'] != 'cancelled' && (empty($bank_receipt) || strtolower($transaction_status[0]['status']) != 'success' || $bank_receipt[0]['status'] == "0" || $bank_receipt[0]['status'] == "1")) {
                            $this->response['error'] = true;
                            $this->response['message'] = "Order item status can not update, Bank verification is remain from transactions for this order.";
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['data'] = array();
                            print_r(json_encode($this->response));
                            return false;
                        }
                    }

                    // processing order items
                    $order_item_res = $this->db->select(' * , (Select count(id) from order_items where order_id = oi.order_id ) as order_counter ,(Select count(active_status) from order_items where active_status ="cancelled" and order_id = oi.order_id ) as order_cancel_counter , (Select count(active_status) from order_items where active_status ="returned" and order_id = oi.order_id ) as order_return_counter,(Select count(active_status) from order_items where active_status ="delivered" and order_id = oi.order_id ) as order_delivered_counter , (Select count(active_status) from order_items where active_status ="processed" and order_id = oi.order_id ) as order_processed_counter , (Select count(active_status) from order_items where active_status ="shipped" and order_id = oi.order_id ) as order_shipped_counter , (Select status from orders where id = oi.order_id ) as order_status ')
                        ->where(['id' => $order_item_id])
                        ->get('order_items oi')->result_array();

                    if ($this->Order_model->update_order(['status' => $_POST['status']], ['id' => $order_item_res[0]['id']], true, 'order_items')) {
                        $this->Order_model->update_order(['active_status' => $_POST['status']], ['id' => $order_item_res[0]['id']], false, 'order_items');
                        process_refund($order_item_res[0]['id'], $_POST['status'], 'order_items');
                        if (trim($_POST['status']) == 'cancelled' || trim($_POST['status']) == 'returned') {
                            $data = fetch_details('order_items', ['id' => $order_item_id], 'product_variant_id,quantity');
                            update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
                        }
                        if (($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_cancel_counter']) + 1 && $_POST['status'] == 'cancelled') ||  ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_return_counter']) + 1 && $_POST['status'] == 'returned') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_delivered_counter']) + 1 && $_POST['status'] == 'delivered') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_processed_counter']) + 1 && $_POST['status'] == 'processed') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_shipped_counter']) + 1 && $_POST['status'] == 'shipped')) {
                            /* process the refer and earn */
                            $user = fetch_details('orders', ['id' => $order_item_res[0]['order_id']], 'user_id');
                            $user_id = $user[0]['user_id'];
                            $response = process_referral_bonus($user_id, $order_item_res[0]['order_id'], $_POST['status']);
                        }
                    }
                    // Update login id in order_item table
                    update_details(['updated_by' => $_SESSION['user_id']], ['order_id' => $order_item_res[0]['order_id'], 'seller_id' => $order_item_res[0]['seller_id']], 'order_items');
                }
                // update_details(['updated_by' => $admin_id], ['id' => $order_item_res[0]['id']], 'order_items');
                $settings = get_settings('system_settings', true);
                $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                $user_res = fetch_details('users', ['id' => $user_id], 'username,fcm_id,email,mobile');
                //custom message
                if ($_POST['status'] == 'received') {
                    $type = ['type' => "customer_order_received"];
                } elseif ($_POST['status'] == 'processed') {
                    $type = ['type' => "customer_order_processed"];
                } elseif ($_POST['status'] == 'shipped') {
                    $type = ['type' => "customer_order_shipped"];
                } elseif ($_POST['status'] == 'delivered') {
                    $type = ['type' => "customer_order_delivered"];
                } elseif ($_POST['status'] == 'cancelled') {
                    $type = ['type' => "customer_order_cancelled"];
                } elseif ($_POST['status'] == 'returned') {
                    $type = ['type' => "customer_order_returned"];
                }
                $custom_notification = fetch_details('custom_notifications', $type, '');
                $hashtag_cutomer_name = '< cutomer_name >';
                $hashtag_order_id = '< order_item_id >';
                $hashtag_application_name = '< application_name >';
                $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                $hashtag = html_entity_decode($string);
                $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $order_item_res[0]['id'], $app_name), $hashtag);
                $message = output_escaping(trim($data, '"'));
                $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . ' Order status updated to' . $_POST['val'] . ' for order ID #' .  $order_item_res[0]['id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                $fcm_ids = array();
                if (!empty($user_res[0]['fcm_id'])) {
                    $fcmMsg = array(
                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : " Order status updated",
                        'body' => $customer_msg,
                        'type' => "order",
                        'order_id' => $order_item_res[0]['order_id'],
                    );

                    $fcm_ids[0][] = $user_res[0]['fcm_id'];
                    send_notification($fcmMsg, $fcm_ids);
                }
                notify_event(
                    $type['type'],
                    ["customer" => [$user_res[0]['email']]],
                    ["customer" => [$user_res[0]['mobile']]],
                    ["orders.id" => $order_item_res[0]['order_id']]
                );

                $seller_res = fetch_details('users', ['id' => $order_item_res[0]['seller_id']], 'username,fcm_id,mobile,email');
                $fcm_ids = array();
                if (!empty($seller_res[0]['fcm_id'])) {
                    $hashtag_cutomer_name = '< cutomer_name >';
                    $hashtag_order_id = '< order_item_id >';
                    $hashtag_application_name = '< application_name >';
                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);
                    $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($seller_res[0]['username'], $order_item_res[0]['id'], $app_name), $hashtag);
                    $message = output_escaping(trim($data, '"'));
                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $seller_res[0]['username'] . ' Order status updated to' . $_POST['status'] . ' for your order ID #' . $order_item_res[0]['id'] . ' please take note of it! Regards ' . $app_name . '';
                    $fcmMsg = array(
                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : " Order status updated",
                        'body' => $customer_msg,
                        'type' => "order",
                        'order_id' => $order_item_res[0]['order_id'],
                    );

                    $fcm_ids[0][] = $seller_res[0]['fcm_id'];
                    send_notification($fcmMsg, $fcm_ids);
                }
                notify_event(
                    $type['type'],
                    ["seller" => [$seller_res[0]['email']]],
                    ["seller" => [$seller_res[0]['mobile']]],
                    ["orders.id" => $order_item_res[0]['id']]
                );

                $this->response['error'] = false;
                $this->response['message'] = 'Status Updated Successfully';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access not allowed!';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }
    }

    function update_order_mail_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $order_item_id = $_POST['order_item_id'];
            $status = $_POST['status'];

            if (update_details(['is_sent' => $status], ['id' => $order_item_id], 'order_items')) {
                $this->response['error'] = false;
                $this->response['message'] = 'Status Updated Successfully';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Status not Updated Successfully';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access not allowed!';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }
    }

    // delete_receipt
    function delete_receipt()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (empty($_GET['id'])) {
                $response['error'] = true;
                $response['message'] = 'Something went wrong';
            }
            if (delete_details(['id' => $_GET['id']], "order_bank_transfer")) {
                $response['error'] = false;
                $response['message'] = 'Deleted Successfully';
            } else {
                $response['error'] = true;
                $response['message'] = 'Something went wrong';
            }
            echo json_encode($response);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    function update_receipt_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $this->form_validation->set_rules('order_id', 'Order Id', 'trim|required|xss_clean');
            $this->form_validation->set_rules('user_id', 'User Id', 'trim|required|xss_clean');
            $this->form_validation->set_rules('status', 'status', 'trim|required|xss_clean');

            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $order_id = $this->input->post('order_id', true);
                $user_id = $this->input->post('user_id', true);
                $status = $this->input->post('status', true);

                if (update_details(['status' => $status], ['order_id' => $order_id], 'order_bank_transfer')) {
                    if ($status == 1) {
                        $status = "Rejected";
                    } else if ($status == 2) {
                        $status = "Accepted";
                        update_details(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');
                        $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                        update_details(['status' => $status], ['order_id' => $order_id], 'order_items', false);
                    } else {
                        $status = "Pending";
                    }
                    //custom message
                    $custom_notification =  fetch_details('custom_notifications', ['type' => "bank_transfer_receipt_status"], '');
                    $hashtag_status = '< status >';
                    $hashtag_order_id = '< order_id >';
                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);
                    $data = str_replace(array($hashtag_status, $hashtag_order_id), array($status, $order_id), $hashtag);
                    $message = output_escaping(trim($data, '"'));
                    $customer_title = (!empty($custom_notification)) ? $custom_notification[0]['title'] : 'Bank Transfer Receipt Status';
                    $customer_msg = (!empty($custom_notification)) ? $message : 'Bank Transfer Receipt' . $status . ' for order ID: ' . $order_id;
                    $user = fetch_details("users", ['id' => $user_id], 'email,fcm_id');
                    // send_mail($user[0]['email'], $customer_title, $customer_msg);                    
                    notify_event(
                        'bank_transfer_recipt_status',
                        ["customer" => [$user[0]['email']]],
                        ["customer" => [$user[0]['mobile']]],
                        ["orders.id" => $order_id]
                    );
                    $fcm_ids[0][] = $user[0]['fcm_id'];
                    if (!empty($fcm_ids)) {
                        $fcmMsg = array(
                            'title' => $customer_title,
                            'body' =>   $customer_msg,
                            'type' => "order",
                        );
                        send_notification($fcmMsg, $fcm_ids);
                    }
                    $this->response['error'] = false;
                    $this->response['message'] = 'Updated Successfully';
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Something went wrong';
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                }
            }

            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    function order_tracking()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'order-tracking';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Order Tracking | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Order Tracking | ' . $settings['app_name'];
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_order_tracking()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Order_model->get_order_tracking_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_order_tracking()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->form_validation->set_rules('courier_agency', 'courier_agency', 'trim|required|xss_clean');
            $this->form_validation->set_rules('tracking_id', 'tracking_id', 'trim|required|xss_clean');
            $this->form_validation->set_rules('url', 'url', 'trim|required|xss_clean');
            $this->form_validation->set_rules('order_id', 'order_id', 'trim|required|xss_clean');
            if (empty($_POST['seller_id']) || !isset($_POST['seller_id'])) {
                $this->form_validation->set_rules('seller_id', 'Seller ID', 'trim|required|xss_clean', array('required' => "Please select atleast one seller to update order item's."));
            }
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {

                $order_id = $this->input->post('order_id', true);
                $order_item_id = $this->input->post('order_item_id', true);
                $seller_id = $this->input->post('seller_id', true);
                $courier_agency = $this->input->post('courier_agency', true);
                $tracking_id = $this->input->post('tracking_id', true);
                $url = $this->input->post('url', true);

                $order_item_ids = fetch_details('order_items', ['order_id' => $order_id, 'seller_id' => $seller_id], 'id');

                foreach ($order_item_ids as $ids) {

                    $data = array(
                        'order_id' => $order_id,
                        'order_item_id' => $ids['id'],
                        'courier_agency' => $courier_agency,
                        'tracking_id' => $tracking_id,
                        'url' => $url,
                    );

                    if (is_exist(['order_item_id' => $ids['id'], 'order_id' => $order_id], 'order_tracking', null)) {
                        if (update_details($data, ['order_id' => $order_id, 'order_item_id' => $ids['id']], 'order_tracking') == TRUE) {
                            $this->response['error'] = false;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = "Tracking details Update Successfuly.";
                        } else {
                            $this->response['error'] = true;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = "Not Updated. Try again later.";
                        }
                    } else {
                        if (insert_details($data, 'order_tracking')) {
                            $this->response['error'] = false;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = "Tracking details Insert Successfuly.";
                        } else {
                            $this->response['error'] = true;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = "Not Inserted. Try again later.";
                        }
                    }
                }

                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    function digital_product_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-digital-product-order';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Order Management | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Order Management  | ' . $settings['app_name'];
            $this->data['about_us'] = get_settings('about_us');
            $this->data['curreny'] = get_settings('currency');
            if (isset($_GET['edit_id'])) {
                $order_data = fetch_details('orders', ['id' => $_GET['edit_id']], 'email');
                $order_item_data = fetch_details('order_items', ['order_id' => $_GET['edit_id']], 'id,product_name');
                $this->data['fetched'] = $order_data;
                $this->data['order_item_data'] = $order_item_data;
                // $this->data['attribute'] = $attribute_value[0]['value'];
            }

            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function refund_payment()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->form_validation->set_rules('txn_id', 'Transaction Id', 'trim|required|xss_clean');
            $this->form_validation->set_rules('txn_amount', 'Transaction Amount', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                if (!empty($_POST) || (isset($_POST['txn_id']) && $_POST['txn_id']) != '' && (isset($_POST['amount']) && $_POST['amount']) != '') {
                    $item_id = trim($_POST['item_id']);
                    $txn_id = $_POST['txn_id'];
                    $amount = $_POST['txn_amount'];
                    $payment = ($this->razorpay->refund_payment($txn_id, $amount));
                    if (([$payment['http_code']] != 'null') && empty($payment['http_code']) && $payment['http_code'] != '400') {
                        update_details(['is_refund' => 1], ['order_item_id' => $item_id], 'transactions');
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Payment Refund Successfully";
                    } else {
                        $message = json_decode($payment['body'], true);

                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = $message['error']['description'];
                    }
                    print_r(json_encode($this->response));
                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function create_shiprocket_order()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $this->form_validation->set_rules('pickup_location', ' Pickup Location ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('parcel_weight', ' Parcel Weight ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('parcel_height', ' Parcel Height ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('parcel_breadth', ' Parcel Breadth ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('parcel_length', ' Parcel Length ', 'trim|required|xss_clean');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $_POST['order_items'] = json_decode($_POST['order_items'][0], 1);
                $this->load->library(['Shiprocket']);
                $order_items =  $_POST['order_items'];
                $items = [];
                $subtotal = 0;
                $order_id = 0;

                $pickup_location_pincode = fetch_details('pickup_locations', ['pickup_location' => $_POST['pickup_location']], 'pin_code');
                $user_data = fetch_details('users', ['id' => $_POST['user_id']], 'username,email');
                $order_data = fetch_details('orders', ['id' => $_POST['order_id']], 'date_added,address_id,mobile,payment_method,delivery_charge');
                $address_data = fetch_details('addresses', ['id' => $order_data[0]['address_id']], 'address,city_id,pincode,state,country');
                $city_data = fetch_details('cities', ['id' => $address_data[0]['city_id']], 'name');

                $availibility_data = [
                    'pickup_postcode' => $pickup_location_pincode[0]['pin_code'],
                    'delivery_postcode' => $address_data[0]['pincode'],
                    'cod' => (strtoupper($order_data[0]['payment_method']) == 'COD') ? '1' : '0',
                    'weight' => $_POST['parcel_weight'],
                ];

                $check_deliveribility = $this->shiprocket->check_serviceability($availibility_data);
                $get_currier_id = shiprocket_recomended_data($check_deliveribility);

                foreach ($order_items as $row) {
                    if ($row['pickup_location'] == $_POST['pickup_location'] && $row['seller_id'] == $_POST['shiprocket_seller_id']) {
                        $order_item_id[] = $row['id'];
                        $order_id .= '-' . $row['id'];
                        $order_item_data = fetch_details('order_items', ['id' => $row['id']], 'sub_total');
                        $subtotal += $order_item_data[0]['sub_total'];
                        if (isset($row['product_variants']) && !empty($row['product_variants'])) {
                            $sku = $row['product_variants'][0]['sku'];
                        } else {
                            $sku = $row['sku'];
                        }
                        $row['product_slug'] = strlen($row['product_slug']) > 8 ? substr($row['product_slug'], 0, 8) : $row['product_slug'];
                        $temp['name'] = $row['pname'];
                        $temp['sku'] = isset($sku) && !empty($sku) ? $sku : $row['product_slug'];
                        $temp['units'] = $row['quantity'];
                        $temp['selling_price'] = $row['price'];
                        $temp['discount'] = $row['discounted_price'];
                        $temp['tax'] = $row['tax_amount'];
                        array_push($items, $temp);
                    }
                }

                $order_item_ids = implode(",", $order_item_id);
                $random_id = '-' . rand(10, 10000);
                $delivery_charge = (strtoupper($order_data[0]['payment_method']) == 'COD') ? $order_data[0]['delivery_charge'] : 0;
                $create_order = [
                    'order_id' => $_POST['order_id'] . $order_id . $random_id,
                    'order_date' => $order_data[0]['date_added'],
                    'pickup_location' => $_POST['pickup_location'],
                    'billing_customer_name' =>  $user_data[0]['username'],
                    'billing_last_name' => "",
                    'billing_address' => $address_data[0]['address'],
                    'billing_city' => $city_data[0]['name'],
                    'billing_pincode' => $address_data[0]['pincode'],
                    'billing_state' => $address_data[0]['state'],
                    'billing_country' => $address_data[0]['country'],
                    'billing_email' => $user_data[0]['email'],
                    'billing_phone' => $order_data[0]['mobile'],
                    'shipping_is_billing' => true,
                    'order_items' => $items,
                    'payment_method' => (strtoupper($order_data[0]['payment_method']) == 'COD') ? 'COD' : 'Prepaid',
                    'sub_total' => $subtotal + $delivery_charge,
                    'length' => $_POST['parcel_length'],
                    'breadth' => $_POST['parcel_breadth'],
                    'height' => $_POST['parcel_height'],
                    'weight' => $_POST['parcel_weight'],
                ];

                $response = $this->shiprocket->create_order($create_order);

                if (isset($response['status_code']) && $response['status_code'] == 1) {
                    $courier_company_id = $get_currier_id['courier_company_id'];
                    $order_tracking_data = [
                        'order_id' => $_POST['order_id'],
                        'order_item_id' => $order_item_ids,
                        'shiprocket_order_id' => $response['order_id'],
                        'shipment_id' => $response['shipment_id'],
                        'courier_company_id' => $courier_company_id,
                        'pickup_status' => 0,
                        'pickup_scheduled_date' => '',
                        'pickup_token_number' => '',
                        'status' => 0,
                        'others' => '',
                        'pickup_generated_date' => '',
                        'data' => '',
                        'date' => '',
                        'manifest_url' => '',
                        'label_url' => '',
                        'invoice_url' => '',
                        'is_canceled' => 0,
                        'tracking_id' => '',
                        'url' => ''
                    ];
                    $this->db->insert('order_tracking', $order_tracking_data);
                }
                if (isset($response['status_code']) && $response['status_code'] == 1) {
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Shiprocket order created successfully';
                    $this->response['data'] = $response;
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Shiprocket order not created successfully';
                    $this->response['data'] = $response;
                }
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function generate_awb()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $res = generate_awb($_POST['shipment_id']);
            if (!empty($res) && $res['status_code'] != 400) {
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'AWB generated successfully';
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'AWB not generated';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function send_pickup_request()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {


            $res = send_pickup_request($_POST['shipment_id']);

            if (!empty($res)) {
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Request send successfully';
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Request not sent';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function generate_label()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $res = generate_label($_POST['shipment_id']);
            if (!empty($res)) {
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Label generated successfully';
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Label not generated';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function generate_invoice()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $res = generate_invoice($_POST['order_id']);
            if (!empty($res) && isset($res['is_invoice_created']) && $res['is_invoice_created'] == 1) {
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Invoice generated successfully';
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Invoice not generated';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function cancel_shiprocket_order()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $res = cancel_shiprocket_order($_POST['shiprocket_order_id']);
            if (!empty($res) && $res['status'] == 200) {
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Order cancelled successfully';
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Order not cancelled';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function edit_transactions()
    {
        if($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
		{			
            $this->form_validation->set_rules('status', 'status', 'trim|required|xss_clean');
            $this->form_validation->set_rules('txn_id', 'txn_id', 'trim|required|xss_clean');
            $this->form_validation->set_rules('id', 'id', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $_POST['message'] = (isset($_POST['message']) && trim($_POST['message']) != "") ? $this->input->post('message', true) : "";
                $this->Transaction_model->edit_transactions($_POST);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "Transaction Updated Successfuly.";
                print_r(json_encode($this->response));
            }
		}
		else{
			redirect('admin/login','refresh');
		}
    }
}
