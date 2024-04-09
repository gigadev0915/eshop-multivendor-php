<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authentication_settings extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Setting_model', 'notification_model', 'category_model']);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'notification_setting')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            $this->data['main_page'] = FORMS . 'authentication-settings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Authentication Settings | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Authentication Settings  | ' . $settings['app_name'];
            $this->data['authentication_config'] = get_settings('authentication_settings', true);
            $this->data['time_slot_config'] = get_settings('time_slot_config', true);


            // $this->data['fcm_server_key'] = get_settings('fcm_server_key');
            // $this->data['vap_id_Key'] = get_settings('vap_id_Key');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function update_authentication_settings()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'authentication_settings')) {
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
            if (print_msg(!has_permissions('update', 'authentication_settings'), PERMISSION_ERROR_MSG, 'authentication_settings')) {
                return false;
            }

                // print_r($_POST);
                // echo " ---------";
                // $data = json_encode($_POST);
                // print_r($data);
                $this->Setting_model->update_authentication_setting($_POST);
                // $this->Setting_model->update_authentication_setting(json_encode($_POST));
                // $this->Setting_model->update_vapkey($_POST);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Authentication Setting Updated Successfully';
                print_r(json_encode($this->response));
            // }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

}
