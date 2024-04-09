<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Chat_Api extends CI_Controller
{

    /*
---------------------------------------------------------------------------
Defined Methods:-
---------------------------------------------------------------------------

    1. get_groups    
    2. get_chat_history
    3. Load_chat
    4. delete_msg
    
---------------------------------------------------------------------------
---------------------------------------------------------------------------

*/
    private  $user_details = [];

    protected $excluded_routes = [
        "seller/app/v1/Chat_Api/search_user",
        "seller/app/v1/Chat_Api/get_supporters",

    ];

    public function __construct()
    {
        parent::__construct();
        header("Content-Type: application/json");
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $this->load->library(['upload', 'jwt', 'ion_auth', 'form_validation', 'Key']);
        $this->load->model(['Customer_model', 'chat_model', 'notification_model', 'Setting_model', 'media_model']);
        $this->load->helper(['language', 'string']);
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        $response = $temp = $bulkdata = array();
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        // initialize db tables data
        $this->tables = $this->config->item('tables', 'ion_auth');

        $current_uri =  uri_string();
        if (!in_array($current_uri, $this->excluded_routes)) {
            $token = verify_app_request();
            if ($token['error']) {
                header('Content-Type: application/json');
                http_response_code($token['status']);
                print_r(json_encode($token));
                die();
            }
            $this->user_details = $token['data'];
        }
    }

    function test()
    {
        $fcmMsg = array(
            'content_available' => true,
            'title' => "test",
            'body' => "test message",
            'image' => '',
            "from_id" => '2',
            'type' => "typing",
            'type_id' => "1",
            'link' => "",
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        );
        $token = "ewA4IJ5Scom09AFbUSr9UD:APA91bEc_0wBzuYE_gG51s_9vSxXJ3esK8BQTPP0kTtlJvHCmh-ueogGHWegXlIEjDuEbbd7kQuc_yDGytaQC737Jbwu9RHA8T0LSeHTsN_56YrhLytO2pdnp0UVivcrmNCzfxVM7XlX";
        print_r(send_notification($fcmMsg, [$token]));
    }
    public function index()
    {
        $this->load->helper('file');
        $this->output->set_content_type(get_mime_by_extension(base_url('api-doc.txt')));
        $this->output->set_output(file_get_contents(base_url('api-doc.txt')));
    }
    public function generate_token()
    {
        $payload = [
            'iat' => time(), /* issued at time */
            'iss' => 'eshop',
            'exp' => time() + (60 * 60 * 24 * 365), /* expires after 1 minute */
        ];
        $token = $this->jwt->encode($payload, JWT_SECRET_KEY);
        print_r(json_encode($token));
    }

    public function verify_token()
    {
        try {
            $token = $this->jwt->getBearerToken();
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
            print_r(json_encode($response));
            return false;
        }

        if (!empty($token)) {

            $api_keys = fetch_details('client_api_keys', ['status' => 1]);
            if (empty($api_keys)) {
                $response['error'] = true;
                $response['message'] = 'No Client(s) Data Found !';
                print_r(json_encode($response));
                return false;
            }
            JWT::$leeway = 6000000000;
            $flag = true; //For payload indication that it return some data or throws an expection.
            $error = true; //It will indicate that the payload had verified the signature and hash is valid or not.
            try {
                // $payload = $this->jwt->decode($token, $row['secret'], ['HS256']);
                $payload = $this->jwt->decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
                if (isset($payload->iss) && $payload->iss == 'eshop') {
                    $error = false;
                    $flag = false;
                } else {
                    $error = true;
                    $flag = false;
                    $message = 'Invalid Hash';
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
            }

            if ($flag) {
                $response['error'] = true;
                $response['message'] = $message;
                print_r(json_encode($response));
                return false;
            } else {
                if ($error == true) {
                    $response['error'] = true;
                    $response['message'] = $message;
                    print_r(json_encode($response));
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Unauthorized access not allowed";
            print_r(json_encode($response));
            return false;
        }
    }

    public function get_groups()
    {
        /*
            user_id:15 
            
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('user_id', 'User Id', 'trim|numeric|required|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $groups = $this->chat_model->get_groups($_POST['user_id']);

            $i = 0;
            foreach ($groups as $grp) {
                $groups[$i] = $grp;
                $grp['group_members'] = [];
                $users = $this->chat_model->get_group_members($grp['group_id']);
                $j = 0;
                foreach ($users as $row) {
                    $users[$j]['image'] = isset($row['image'])  && !empty($row['image']) && $row['image'] != null ? $row['image'] : '';
                    $j++;
                }
                $groups[$i]['group_members'] = $users;
                $i++;
            }
            if (!empty($groups)) {
                $this->response['error'] = false;
                $this->response['message'] = "Groups retrieved successfully !";
                $this->response['data'] = $groups;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "groups Not Found !";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }
    public function get_chat_history()
    {
        /*
            user_id:15   
            limit : 10
            offset : 0
        */
        if (!$this->verify_token()) {
            return false;
        }

        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';


        $limit = (isset($_POST['limit'])) ? $this->input->post('limit', true) : 10;
        $offset = (isset($_POST['offset'])) ? $this->input->post('offset', true) : 0;
        $user = array();
        $i = 0;
        $type = 'person';


        $members = $this->chat_model->get_chat_history($user_id, $limit, $offset);



        foreach ($members as $row) {
            //  print_R($row);

            $to_id = (isset($user_id)) ? $user_id : '';
            $from_id = $row['from_id'];
            // $to_id = $row['to_id'];
            // $from_id = $row['id'];

            if (isset($from_id) && !empty($from_id)) {
                $unread_meg = $this->chat_model->get_unread_msg_count($type, $from_id, $to_id);
            }


            $user[$i] = $row;
            $user[$i]['unread_msg'] = $unread_meg;

            $date = strtotime('now');
            if ($to_id == $row['opponent_user_id']) {
                $user[$i]['is_online'] = 1;
            } else {
                if ($row['last_online'] > $date) {
                    $user[$i]['is_online'] = 1;
                } else {
                    $user[$i]['is_online'] = 0;
                }
            }
            $i++;
        }




        if (isset($members)) {
            $this->response['error'] = false;
            $this->response['message'] = "chat retrieved successfully !";
            $this->response['data'] = $user;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = "chat Not Found !";
            $this->response['data'] = array();
        }

        print_r(json_encode($this->response));
    }

    public function load_chat()
    {
        /*
            
            from_id : 1 // if type is person then pass user id or pass group id
            to_id : 2 //current user_id
            type : person {person / group}
            offset : 0  
            limit : 10
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('from_id', 'From Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('to_id', 'To Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $limit = (isset($_POST['limit'])) ? $this->input->post('limit', true) : 50;
            $offset = (isset($_POST['offset'])) ? $this->input->post('offset', true) : 0;
            $from_id = (isset($_POST['from_id'])) ? $this->input->post('from_id', true) : '';
            $to_id = (isset($_POST['to_id'])) ? $this->input->post('to_id', true) : '';
            $type = (isset($_POST['type'])) ? $this->input->post('type', true) : '';

            $messages = $this->chat_model->load_chat($from_id, $to_id, $type,  $offset, $limit, 'id', 'DESC');

            if ($messages['total_msg'] == 0) {
                $message['error'] = false;
                $message['message'] = "message Not Found !";
                $message['data']['total_msg'] = "0";
                $message['data']['msg'] = [];

                print_r(json_encode($message));
                return false;
            }

            $i = 0;
            $message['total_msg'] = $messages['total_msg'];
            foreach ($messages['msg'] as $row) {
                $message['msg'][$i] = $row;
                $media_files = $this->chat_model->get_media($row['id']);
                if (isset($media_files) && !empty($media_files)) {
                    $file_extention = explode('.', $media_files[0]['original_file_name']);
                    $media_files[0]['file_extension'] = end($file_extention);
                    $media_files[0]['file_url'] = base_url('uploads/chat_media/' . $media_files[0]['original_file_name']);
                }
                $message['msg'][$i]['media_files'] = !empty($media_files) ? $media_files : [];
                $message['msg'][$i]['text'] = $row['message'];
                if ($row['from_id'] == $to_id) {
                    $message['msg'][$i]['position'] = 'right';
                } else {
                    $message['msg'][$i]['position'] = 'left';
                }
                $i++;
            }

            if (!empty($message)) {
                $this->response['error'] = false;
                $this->response['message'] = "message retrieved successfully !";
                $this->response['data'] = $message;
            } else {
                $this->response['error'] = false;
                $this->response['message'] = "message Not Found !";
                $this->response['data']['total_msg'] = "0";
                $this->response['data']['msg'] = [];
            }
        }
        print_r(json_encode($this->response));
    }
    public function delete_msg()
    {
        /*
           msg_id : 13
           from_id : 1 // current user_id
            
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('msg_id', 'Message Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('from_id', 'From Id', 'trim|numeric|required|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $from_id = (isset($_POST['from_id'])) ? $this->input->post('from_id') : '';
            $msg_id = (isset($_POST['msg_id'])) ? $this->input->post('msg_id') : '';

            if ($this->chat_model->delete_msg($from_id, $msg_id)) {
                $this->response['error'] = false;
                $this->response['message'] = "Message deleted successfully !";
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Message not deleted !";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

    public function switch_chat()
    {
        /*
            from_id : 1 // if type is person then pass user id or pass group id
            type : person {person / group}
            user_id : 1 {current user_id} //pass when type is group
            
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('from_id', 'From Id', 'trim|numeric|required|xss_clean');
        // if (isset($_POST['type']) && strtolower($_POST['type']) == 'group') {
        //     $this->form_validation->set_rules('user_id', 'User Id', 'trim|numeric|required|xss_clean');
        // }
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $from_id = (isset($_POST['from_id'])) ? $this->input->post('from_id') : '';
            $type = (isset($_POST['type'])) ? $this->input->post('type') : '';
            $users = $this->chat_model->switch_chat($from_id, $type);
            $user = array();
            $i = 0;
            foreach ($users as $row) {

                $user[$i] = $row;
                if ($type == 'person') {
                    $user[$i]['picture'] = $row['username'];

                    $date = strtotime('now');

                    if ($row['last_online'] > $date) {
                        $user[$i]['is_online'] = 1;
                    } else {
                        $user[$i]['is_online'] = 0;
                    }
                } else {
                    $user[$i]['picture'] = '#';

                    if ($this->chat_model->check_group_admin($row['id'], $user_id)) {
                        $user[$i]['is_admin'] = true;
                    } else {
                        $user[$i]['is_admin'] = false;
                    }
                }

                $i++;
            }

            if (!empty($user)) {
                $this->response['error'] = false;
                $this->response['message'] = "Data fetched successfully !";
                $this->response['data'] = $user;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Data not available !";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

    public function mark_msg_read()
    {
        /*
            from_id : 1 // if type is person then pass user id or pass group id
            type : person {person / group}
            user_id : 1 {current user_id} //pass when type is group
            
        */
        if (!$this->verify_token()) {
            return false;
        }
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('from_id', 'From Id', 'trim|numeric|required|xss_clean');
        // if (isset($_POST['type']) && strtolower($_POST['type']) == 'group') {
        //     $this->form_validation->set_rules('user_id', 'User Id', 'trim|numeric|required|xss_clean');
        // }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $from_id = (isset($_POST['from_id'])) ? $this->input->post('from_id') : '';
            $to_id = (isset($user_id)) ? $user_id : '';
            $type = (isset($_POST['type'])) ? $this->input->post('type') : '';

            if ($this->chat_model->mark_msg_read($type, $from_id, $to_id)) {
                $this->response['error'] = false;
                $this->response['message'] = "Message marked as read !";
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Message not marked as read !";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

    public function send_msg()
    {

        /*
            type : person {person / group}
            from_id : 1 // current user id
            to_id : 1 // receiver user id
            message : this is test msg
            documents[] : FILE {optional}
            
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('from_id', 'From Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('to_id', 'To Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'trim|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'trim|xss_clean');
        $this->form_validation->set_rules('documents', 'documents', 'trim|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $type = (isset($_POST['type'])) ? $this->input->post('type') : '';
            $from_id = (isset($_POST['from_id'])) ? $this->input->post('from_id') : '';
            $to_id = (isset($_POST['to_id'])) ? $this->input->post('to_id') : '';
            $message = (isset($_POST['message'])) ? $this->input->post('message') : '';

            $data = array(
                'type' => $type,
                'from_id' => $from_id,
                'to_id' => $to_id,
                'message' => $message
            );
            $msg_id = $this->chat_model->send_msg($data);

            if (!empty($_FILES['documents']['name'])) {

                $year = date('Y');
                $target_path = FCPATH . CHAT_MEDIA_PATH  . '/';
                $sub_directory = CHAT_MEDIA_PATH  . '/';

                if (!file_exists($target_path)) {
                    mkdir($target_path, 0777, true);
                }

                $temp_array = $media_ids = $other_images_new_name = array();
                $files = $_FILES;
                $other_image_info_error = "";
                $allowed_media_types = implode('|', allowed_media_types());
                $config['upload_path'] = $target_path;
                $config['allowed_types'] = $allowed_media_types;
                $other_image_cnt = count((array)$_FILES['documents']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);
                for ($i = 0; $i < $other_image_cnt; $i++) {
                    if (!empty($_FILES['documents']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $other_image_info_error = $other_image_info_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array = $other_img->data();
                            $temp_array['sub_directory'] = $sub_directory;
                            $media_ids[] = $media_id = $this->media_model->set_media($temp_array); /* set media in database */
                            if (strtolower($temp_array['image_type']) != 'gif')
                                resize_image($temp_array,  $target_path, $media_id);
                            $other_images_new_name[$i] = $temp_array['file_name'];
                        }
                        $data = array(
                            'original_file_name' => $_FILES['temp_image']['name'],
                            'file_name' => $_FILES['temp_image']['tmp_name'],
                            'file_extension' => $_FILES['temp_image']['type'],
                            'file_size' => $_FILES['temp_image']['size'],
                            'user_id' => $from_id,
                            'message_id' => $msg_id
                        );
                        $file_id = $this->chat_model->add_file($data);
                        $this->chat_model->add_media_ids_to_msg($msg_id, $file_id);
                    } else {

                        $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $other_image_info_error = $other_img->display_errors();
                        }
                        $data = array(
                            'original_file_name' => $_FILES['temp_image']['name'],
                            'file_name' => $_FILES['temp_image']['tmp_name'],
                            'file_extension' => $_FILES['temp_image']['type'],
                            'file_size' => $_FILES['temp_image']['size'],
                            'user_id' => $from_id,
                            'message_id' => $msg_id
                        );
                        $file_id = $this->chat_model->add_file($data);
                        $this->chat_model->add_media_ids_to_msg($msg_id, $file_id);
                    }
                }

                // Deleting Uploaded Images if any overall error occured
                if ($other_image_info_error != NULL) {
                    if (isset($other_images_new_name) && !empty($other_images_new_name)) {
                        foreach ($other_images_new_name as $key => $val) {
                            unlink($target_path . $other_images_new_name[$key]);
                        }
                    }
                }
            }


            $messages = $this->chat_model->get_msg_by_id($msg_id, $to_id, $from_id, $type);
            $message = array();
            $i = 0;
            if ($messages == 1) {
                $response['error'] = true;
                $response['message'] = 'User Not Found';
            } else {
                foreach ($messages as $row) {
                    $message[$i] = $row;
                    $media_files = $this->chat_model->get_media($row['id']);
                    if (isset($media_files) && !empty($media_files)) {
                        $file_extention = explode('.', $media_files[0]['original_file_name']);
                        $media_files[0]['file_extension'] = end($file_extention);
                        $media_files[0]['file_url'] = base_url('uploads/chat_media/' . $media_files[0]['original_file_name']);
                    }
                    $message[$i]['media_files'] = !empty($media_files) ? $media_files : [];
                    $message[$i]['text'] = $row['message'];
                    $i++;
                }
                $new_msg = $message;

                if (!empty($msg_id)) {

                    $to_id = $to_id;
                    $from_id = $from_id;

                    // if ($to_id == $from_id && $this->input->post('chat_type') == 'person') {
                    //     return false;
                    // }

                    // single user msg
                    if ($type == 'person') {

                        // this is the user who going to recive FCM msg
                        // $user = $this->users_model->get_user_by_id($to_id);
                        $user = fetch_details('users', ['active' => 1, 'id' => $to_id]);

                        // this is the user who going to send FCM msg 
                        // $senders_info = $this->users_model->get_user_by_id($this->session->userdata('user_id'));
                        $senders_info = fetch_details('users', ['active' => 1, 'id' => $from_id]);

                        $data = $notification = array();
                        $notification['title'] = $senders_info[0]['username'];
                        // $notification['picture'] = mb_substr($senders_info[0]['first_name'], 0, 1) . '' . mb_substr($senders_info[0]['last_name'], 0, 1);

                        // $notification['profile'] = !empty($senders_info[0]['profile']) ? $senders_info[0]['profile'] : '';

                        $notification['senders_name'] = $senders_info[0]['username'];

                        $notification['type'] = 'message';
                        $notification['message_type'] = 'person';
                        $notification['from_id'] = $from_id;
                        $notification['to_id'] = $to_id;
                        $notification['msg_id'] = $msg_id;
                        $notification['new_msg'] = json_encode($new_msg);
                        $notification['body'] =  $this->input->post('message');
                        // $notification['icon'] = 'assets/icons/' . (!empty(get_half_logo()) ? get_half_logo() : 'logo-half.png');
                        $notification['base_url'] = base_url('admin/chat');
                        $data['data']['data'] = $notification;
                        $data['data']['webpush']['fcm_options']['link'] = base_url('chat');
                        $data['to'] = $user[0]['fcm_id'];
                        $results = fetch_details('users', null, 'fcm_id', 10000, 0, '', '', "id", $to_id);
                        $result = $res = array();
                        for ($i = 0; $i <= count($results); $i++) {
                            if (isset($results[$i]['fcm_id']) && !empty($results[$i]['fcm_id']) && ($results[$i]['fcm_id'] != 'NULL')) {
                                $res = array_merge($result, $results);
                            }
                        }

                        $fcm_ids = array();
                        foreach ($res as $fcm_id) {
                            if (!empty($fcm_id)) {
                                $fcm_ids[] = $fcm_id['fcm_id'];
                            }
                        }
                        $registrationIDs = $fcm_ids;
                        $fcm_admin_subject = 'New Message from ' . $senders_info[0]['username'];
                        $fcmMsg = array(
                            'title' => $fcm_admin_subject,
                            'body' => $this->input->post('message'),
                            'type' => "chat",
                            'message' => json_encode($new_msg),
                            'content_available' => true
                        );

                        $fcmFields = send_notification($fcmMsg, $registrationIDs);

                        $ch = curl_init();
                        $fcm_key = get_settings('fcm_server_key');


                        $fcm_key = !empty($fcm_key) ? $fcm_key : '';

                        // $fcm_key = !empty($fcm_key->fcm_server_key) ? $fcm_key->fcm_server_key : '';

                        curl_setopt($ch, CURLOPT_POST, 1);
                        $headers = array();
                        $headers[] = "Authorization: key = " . $fcm_key;
                        $headers[] = "Content-Type: application/json";
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                        $result['error'] = false;
                        $result['response'] = curl_exec($ch);
                        if (curl_errno($ch))
                            echo 'Error:' . curl_error($ch);

                        curl_close($ch);
                    } else {

                        // group user msg
                        $group_id = $to_id;

                        $users = $this->chat_model->get_group_members($group_id);
                        foreach ($users as $user) {
                            // $userdata = $this->users_model->get_user_by_id($user['user_id']);
                            $userdata = fetch_details('users', ['active' => 1, 'id' => $user['user_id']]);

                            if ($user['user_id'] != $from_id) {
                                $fcm_ids[] = $userdata[0]['fcm_id'];
                            }
                        }

                        $registrationIDs = $fcm_ids;


                        // this is the user who going to send FCM msg
                        // $senders_info = $this->users_model->get_user_by_id($this->session->userdata('user_id'));
                        $senders_info = fetch_details('users', ['active' => 1, 'id' => $from_id]);

                        $data = $notification = array();
                        $notification['title'] = '#' . $users[0]['title'] . ' - ' . $senders_info[0]['username'];
                        // $notification['picture'] = mb_substr($senders_info[0]['first_name'], 0, 1) . '' . mb_substr($senders_info[0]['last_name'], 0, 1);

                        // $notification['profile'] = !empty($senders_info[0]['profile']) ? $senders_info[0]['profile'] : '';

                        $notification['senders_name'] = $senders_info[0]['username'];
                        $notification['type'] = 'message';
                        $notification['message_type'] = 'group';
                        $notification['from_id'] = $from_id;
                        $notification['to_id'] = $group_id;
                        $notification['msg_id'] = $msg_id;
                        $notification['registrationIDs'] = $registrationIDs;
                        $notification['new_msg'] = json_encode($new_msg);
                        $notification['body'] = $this->input->post('chat-input-textarea');
                        // $notification['icon'] = 'assets/icons/' . (!empty(get_half_logo()) ? get_half_logo() : 'logo-half.png');
                        $notification['base_url'] = base_url('chat');
                        $data['data']['data'] = $notification;
                        $data['data']['webpush']['fcm_options']['link'] = base_url('chat');
                        $data['registration_ids'] = $registrationIDs;
                        $fcm_admin_subject = 'New Message from' . $senders_info[0]['username'];
                        $fcmMsg = array(
                            'title' => $fcm_admin_subject,
                            'body' => $this->input->post('message'),
                            'type' => "chat",
                            'message' => json_encode($new_msg),
                            'content_available' => true
                        );

                        $fcmFields = send_notification($fcmMsg, $registrationIDs);

                        $ch = curl_init();
                        $fcm_key = get_settings('firebase_settings');

                        $fcm_key = !empty($fcm_key) ? json_decode($fcm_key) : '';

                        $fcm_key = !empty($fcm_key->fcm_server_key) ? $fcm_key->fcm_server_key : '';

                        curl_setopt($ch, CURLOPT_POST, 1);
                        $headers = array();
                        $headers[] = "Authorization: key = " . $fcm_key;

                        $headers[] = "Content-Type: application/json";
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                        $result['error'] = false;

                        $this->chat_model->set_group_msg_as_unread($group_id, $from_id);

                        $result['response'] = curl_exec($ch);
                        if (curl_errno($ch))
                            echo 'Error:' . curl_error($ch);

                        curl_close($ch);
                    }

                    $response['error'] = false;
                    $response['message'] = 'Successful';
                    $response['msg_id'] = $msg_id;
                    $response['new_msg'] = $new_msg;
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Not Successful';
                }
            }
        }
        print_r(json_encode($response));
    }

    public function search_user()
    {
        /*
          search : test
          limit : 10
          offset : 10
          order : DESC/ASC
          sort : id
        */
        // if (!$this->verify_token()) {
        //     return false;
        // }

        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'u.id';
            if (isset($search) and $search != '') {
                $multipleWhere = ['u.`username`' => $search, 'u.`email`' => $search];
            }
            $where = ['u.active' => 1];

            $search_res = $this->db->select('u.id,u.username,u.email');


            if (isset($multipleWhere) && !empty($multipleWhere)) {
                $search_res->group_start();
                $search_res->or_like($multipleWhere);
                $search_res->group_end();
            }
            if (isset($where) && !empty($where)) {
                $search_res->where($where);
                // $search_res->where('ug.group_id !=', 4);
            }
            $res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('users u')->result_array();


            if (!empty($res)) {
                $this->response['error'] = false;
                $this->response['message'] = "Data fetched successfully !";
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Data not available !";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

    
    public function get_supporters()
    {
        // if (!$this->verify_token()) {
        //     return false;
        // }

        $data = $this->chat_model->get_supporters();
        $items = [];
        $res = [];
        $i = 0;
        foreach ($data as $key => $value) {

            $items['user_permission_id'] = (!empty($value['user_permission_id']) && isset($value['user_permission_id'])) ? $value['user_permission_id'] : "";
            $items['user_role'] = (!empty($value['user_role']) && isset($value['user_role'])) ? $value['user_role'] : "";
            $items['userto_id'] = (!empty($value['userto_id']) && isset($value['userto_id'])) ? $value['userto_id'] : "";
            $items['username'] = (!empty($value['username']) && isset($value['username'])) ? $value['username'] : "";
            $items['last_online'] = (!empty($value['last_online']) && isset($value['last_online'])) ? $value['last_online'] : "";
            $items['id'] = (!empty($value['id']) && isset($value['id'])) ? $value['id'] : "";
            $items['from_id'] = (!empty($value['from_id']) && isset($value['from_id'])) ? $value['from_id'] : "";
            $items['to_id'] = (!empty($value['to_id']) && isset($value['to_id'])) ? $value['to_id'] : "";
            $items['is_read'] = (!empty($value['is_read']) && isset($value['is_read'])) ? $value['is_read'] : "";
            $items['message'] = (!empty($value['message']) && isset($value['message'])) ? $value['message'] : "";
            $items['type'] = (!empty($value['type']) && isset($value['type'])) ? $value['type'] : "";
            $items['media'] = (!empty($value['media']) && isset($value['media'])) ? $value['media'] : "";
            $items['date_created'] = (!empty($value['date_created']) && isset($value['date_created'])) ? $value['date_created'] : "";

            array_push($res, $items);
        }
        // print_r($res);
        if (!empty($items)) {
            $this->response['error'] = false;
            $this->response['message'] = "Data fetched successfully !";
            $this->response['data'] = $res;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = "Data not available !";
            $this->response['data'] = array();
        }
        print_r(json_encode($this->response));
    }
}
