<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SMS_gateway_settings extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper', 'sms_helper']);
        $this->load->model(['Setting_model', 'notification_model', 'category_model','custom_sms_model']);
    }

    function  test()
    {
        echo "Test<pre>";
        $fcm_id = "eYvDKNC58-A4WI5tpaK1Ns:APA91bGhM_YwxM4CKtSqQ_NMrvVy3PKXyQ7BdwpCfwNGgjPp555DLripOQmxVKE6W_M4wE0VdaIAN0TWKAhhV-Hlw-23pwVa0YIOknalYzwpjoHl8pSlUJ2tYYOHG9ROeD7xjiEInEeC";
        $fcmMsg = array(
            'title' => 'test',
            'body' => "Sample message",
            // 'type' => "typing",
            // "from_id" => "1",
            // "to_id" => "1255",
            // "chat_type" => "person"
        );

        $response = send_notification($fcmMsg, [$fcm_id]);
        print_r($_SESSION);
        print_r($response);
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'sms-gateway-settings')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            $this->data['main_page'] = FORMS . 'sms-gateway-settings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'SMS Gateway Settings | ' . $settings['app_name'];
            $this->data['meta_description'] = ' SMS Gateway Settings  | ' . $settings['app_name'];
            $this->data['sms_gateway_settings'] = get_settings('sms_gateway_settings', true);
            $this->data['send_notification_settings'] = get_settings('send_notification_settings', true);
            $this->data['notification_modules'] = $this->config->item('notification_modules');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_sms_data()
    {
        // echo "<pre>";
        // print_R($_POST);
        // die;
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'sms-gateway-settings')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            if (print_msg(!has_permissions('update', 'sms-gateway-settings'), PERMISSION_ERROR_MSG, 'sms-gateway-settings')) {
                return false;
            }

            // $this->form_validation->set_rules('base_url', 'Base URL', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('sms_gateway_method', 'Method ', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('var_header_key', 'Header', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('var_header_value', 'Header value', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('mobile_no_val', 'Mobile number', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('country_code_val', 'country code', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('mobile_with_country_key_val', 'Mobile number with country code', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('message_val', 'Message value', 'trim|required|xss_clean');

            // if (!$this->form_validation->run()) {

            //     $this->response['error'] = true;
            //     $this->response['csrfName'] = $this->security->get_csrf_token_name();
            //     $this->response['csrfHash'] = $this->security->get_csrf_hash();
            //     $this->response['message'] = validation_errors();
            //     print_r(json_encode($this->response));
            // } else {
            $this->Setting_model->update_smsgateway($_POST);
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'System Setting Updated Successfully';
            print_r(json_encode($this->response));
            // }


        }
    }

    public function update_notification_module()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'sms-gateway-settings')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }

            // echo "<pre>";
            // print_r($_POST);
            // die;

            $this->Setting_model->update_notification_setting($_POST);
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = (isset($edit_id)) ? ' Data Updated Successfully' : 'Data Added Successfully';

            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    
    function test_sms()
    {
        echo "<pre>";   
        $emails = [ "customer" => ['infinitie.roshni@gmail.com'], "admin" => ['infinitie.jay@gmail.com']];
        $phone= [ "customer" => ['7284938224'], "delivery_boy" => ['9898528257'] ];
        //   notify_event(
        //                 "place_order",
        //                 ["customer" => ['infinitie.jay@gmail.com']],
        //                 ["customer" => ['7284938224']],
        //                 ["orders.id" => "1"]
        //             );
        $res = set_user_otp(random_int(100000, 999999), date('Y-m-d H:i:s'));
        print_r($res);
    }

}
