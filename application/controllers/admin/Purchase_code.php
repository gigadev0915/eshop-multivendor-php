<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_code extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Setting_model');

        if (!has_permissions('read', 'contact_us')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'purchase-code';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'System Regsitration | Purchase Code Validation | ' . $settings['app_name'];
            $this->data['meta_description'] = 'System Regsitration | Purchase Code Validation |  | ' . $settings['app_name'];
            $this->data['doctor_brown'] = get_settings('doctor_brown');
            $this->data['web_doctor_brown'] = get_settings('web_doctor_brown');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function validator()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if ((isset($_POST['app_purchase_code']) && !empty($_POST['app_purchase_code'])) || (isset($_POST['web_purchase_code']) && !empty($_POST['web_purchase_code']))) {
                $purchase_code = $this->input->post("app_purchase_code", true);
                $app_url = "https://wrteam.in/validator/home/validator_new?purchase_code=$purchase_code&domain_url=" . base_url() . "&item_id=" . APP_CODE;

                $app_result = curl($app_url);
                if (isset($app_result['body']) && !empty($app_result['body'])) {
                    if (isset($app_result['body']['error']) && $app_result['body']['error'] == 0) {
                        $doctor_brown = get_settings('doctor_brown');
                        if (empty($doctor_brown)) {
                            $doctor_brown['code_bravo'] = $app_result["body"]["purchase_code"];
                            $doctor_brown['time_check'] = $app_result["body"]["token"];
                            $doctor_brown['code_adam'] = $app_result["body"]["username"];
                            $doctor_brown['dr_firestone'] = $app_result["body"]["item_id"];

                            $data['variable'] = "doctor_brown";
                            $data['value'] = json_encode($doctor_brown);
                            insert_details($data, 'settings');
                        }
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "Somthing Went wrong. Please contact Super admin.";
                    print_r(json_encode($this->response));
                }

                // code for web purchase code 

                $purchase_code = $this->input->post("web_purchase_code", true);
                $web_url = "https://wrteam.in/validator/home/validator_new?purchase_code=$purchase_code&domain_url=" . base_url() . "&item_id=" . WEB_CODE;
                $web_result = curl($web_url);
                if (isset($web_result['body']) && !empty($web_result['body'])) {

                    if (isset($web_result['body']['error']) && $web_result['body']['error'] == 0) {
                        $doctor_brown = get_settings('web_doctor_brown');
                        if (empty($doctor_brown)) {
                            $doctor_brown['code_bravo'] = $web_result["body"]["purchase_code"];
                            $doctor_brown['time_check'] = $web_result["body"]["token"];
                            $doctor_brown['code_adam'] = $web_result["body"]["username"];
                            $doctor_brown['dr_firestone'] = $web_result["body"]["item_id"];

                            $data['variable'] = "web_doctor_brown";
                            $data['value'] = json_encode($doctor_brown);
                            insert_details($data, 'settings');
                        }
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "Somthing Went wrong. Please contact Super admin.";
                    print_r(json_encode($this->response));
                }

                if (isset($web_result['body']['error']) && $web_result['body']['error'] == 0 && isset($app_result['body']['error']) && $app_result['body']['error'] == 0) {
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = $web_result['body']['message'];
                    print_r(json_encode($this->response));
                } elseif (isset($web_result['body']['error']) && $web_result['body']['error'] == 0 && !empty($_POST['web_purchase_code'])) {
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = $web_result['body']['message'];
                    print_r(json_encode($this->response));
                } elseif (isset($app_result['body']['error']) && $app_result['body']['error'] == 0 && !empty($_POST['app_purchase_code']) ) {
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = $app_result['body']['message'];
                    print_r(json_encode($this->response));
                }

                if (isset($web_result['body']['error']) && $web_result['body']['error'] != 0 && isset($app_result['body']['error']) && $app_result['body']['error'] != 0) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = $web_result['body']['message'];
                    print_r(json_encode($this->response));
                } elseif (isset($web_result['body']['error']) && $web_result['body']['error'] != 0 && !empty($_POST['web_purchase_code'])) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = $web_result['body']['message'];
                    print_r(json_encode($this->response));
                } elseif (isset($app_result['body']['error']) && $app_result['body']['error'] != 0 && !empty($_POST['app_purchase_code']) ) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = $app_result['body']['message'];
                    print_r(json_encode($this->response));
                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}
