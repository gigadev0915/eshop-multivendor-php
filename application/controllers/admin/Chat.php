<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model(['Customer_model', 'chat_model', 'notification_model', 'Setting_model', 'media_model']);

        if (!has_permissions('read', 'chat')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'notification_setting')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            $this->data['main_page'] = VIEW . 'chat';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Chat | ' . $settings['app_name'];
            $this->data['meta_description'] = ' chat  | ' . $settings['app_name'];
            $this->data['fcm_server_key'] = get_settings('fcm_server_key');
            // $users = fetch_details('users', ['active' => 1]);
            $users = $this->chat_model->get_chat_history($_SESSION['user_id'], 10, 0);

            $user = array();
            $i = 0;
            $type = 'person';
            $to_id = $this->session->userdata('user_id');
            // $unread_meg = 0;

            foreach ($users as $row) {
                // echo "<pre>";
                // print_r($row);

                $from_id = $row['opponent_user_id'];

                // print_r($from_id);
                // print_r($to_id);
                // die;
                if (isset($from_id) && !empty($from_id)) {
                    $unread_meg = $this->chat_model->get_unread_msg_count($type, $from_id, $to_id);
                }

                $user[$i] = $row;
                $user[$i]['unread_msg'] = $unread_meg;
                $user[$i]['picture']  = $row['opponent_username'];

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
            // if ($this->ion_auth->is_admin()) {
            //     $this->data['not_in_groups'] = $this->chat_model->get_groups_all($to_id);
            // } else {
            //     $this->data['not_in_groups'] = '';
            // }

            $this->data['supporters'] = $this->chat_model->get_supporters();
            $this->data['users'] = $user;
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function make_me_online()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {

            $user_id = $this->session->userdata('user_id');
            $date = strtotime('now');
            $date = $date + 60;
            $data = array(
                'last_online' => $date
            );

            if ($this->chat_model->make_me_online($user_id, $data)) {

                $response['error'] = false;
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }
    public function get_system_settings()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $response = get_settings('firebase_settings');
            $configData = json_decode($response, true);
            $configData['fcm_server_key'] = get_settings('fcm_server_key');
            $configData['vap_id_Key'] = get_settings('vap_id_Key');

            $data = json_encode($configData);

            // print_r($data);
            // print_r($configData);
            // die;
            echo json_encode($data);
        }
    }

    public function get_online_members()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $user_id = $this->session->userdata('user_id');

            $date = strtotime('now');
            $date = $date + 15;
            $data = array(
                'last_online' => $date
            );

            $this->chat_model->make_me_online($user_id, $data);

            $users = $this->chat_model->get_chat_history($user_id, 20, 0);


            $user_ids = explode(',', $users[0]['id']);
            $section = array_map('trim', $user_ids);
            $user_ids = $section;


            // $members = $this->chat_model->get_members($user_ids);
            // print_r($users);
            $member = array();
            $i = 0;

            $type = 'person';
            $to_id = $this->session->userdata('user_id');

            foreach ($users as $row) {

                $from_id = $row['id'];

                $unread_meg = $this->chat_model->get_unread_msg_count($type, $from_id, $to_id);

                $member[$i] = $row;
                $member[$i]['unread_msg'] = $unread_meg;
                $member[$i]['picture']  = isset($row['image']) ? $row['image'] : '';
                $date = strtotime('now');

                if ($row['last_online'] > $date) {
                    $member[$i]['is_online'] = 1;
                } else {
                    $member[$i]['is_online'] = 0;
                }
                $i++;
            }

            // $data1['groups'] = $this->chat_model->get_groups($to_id);
            $data1['members'] = $member;

            if (!empty($member)) {
                $response['error'] = false;
                $response['data'] = $data1;
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    // public function edit_group()
    // {
    //     if (!$this->ion_auth->logged_in()) {
    //         redirect('auth', 'refresh');
    //     }
    //     $user_id = $this->session->userdata('user_id');


    //     $this->form_validation->set_rules('update_id', str_replace(':', '', 'ID is empty.'), 'trim');
    //     $this->form_validation->set_rules('title', str_replace(':', '', 'Title is empty.'), 'trim|required');
    //     $this->form_validation->set_rules('description', str_replace(':', '', 'description is empty.'), 'trim|required');

    //     if ($this->form_validation->run() === TRUE) {

    //         $admin_id = $this->session->userdata('user_id');
    //         $group_id = $this->input->post('update_id');

    //         if (!empty($this->input->post('users'))) {
    //             $group_mem_ids = implode(",", $this->input->post('users')) . ',' . $admin_id;
    //             $group_mem_ids = explode(",", $group_mem_ids);
    //         } else {
    //             $group_mem_ids = array($this->session->userdata('user_id'));
    //         }

    //         $no_of_mem = count($group_mem_ids);

    //         if (!empty($this->input->post('admins'))) {
    //             $admins_ids = implode(",", $this->input->post('admins')) . ',' . $admin_id;
    //             $admins_ids = explode(",", $admins_ids);
    //         } else {
    //             $admins_ids = array($this->session->userdata('user_id'));
    //         }

    //         $data = array(
    //             'title' => strip_tags($this->input->post('title', true)),
    //             'description' => strip_tags($this->input->post('description', true)),
    //             'no_of_members' => $no_of_mem
    //         );

    //         if ($this->chat_model->edit_group($data, $group_id)) {

    //             foreach ($group_mem_ids as $user_id) {
    //                 $data1 = array(
    //                     'group_id' => $group_id,
    //                     'user_id' => $user_id,
    //                 );

    //                 $this->chat_model->add_group_members($data1);
    //             }

    //             $this->chat_model->remove_all_group_members($group_id, $group_mem_ids);

    //             $this->chat_model->make_group_admin($group_id, $admins_ids);

    //             $response['error'] = false;
    //             $response['message'] = 'Group Edited successfully';


    //         } else {
    //             $response['error'] = true;
    //             $response['message'] = 'Group could not Edited! Try again!';
    //         }

    //      return json_encode($response);
    //     } else {
    //         $response['error'] = true;
    //         $response['message'] = validation_errors();
    //         print_r(json_encode($response));
    //     }
    // }

    // public function create_group()
    // {

    //     if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {


    //         $user_id = $this->session->userdata('user_id');

    //         $this->form_validation->set_rules('title', 'Titel', 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
    //         if (!$this->form_validation->run()) {

    //             $this->response['error'] = true;
    //             $this->response['csrfName'] = $this->security->get_csrf_token_name();
    //             $this->response['csrfHash'] = $this->security->get_csrf_hash();
    //             $this->response['message'] = validation_errors();
    //             print_r(json_encode($this->response));
    //         } else {
    //             $admin_id = $this->session->userdata('user_id');

    //             if (!empty($this->input->post('users'))) {
    //                 $group_mem_ids = implode(",", $this->input->post('users')) . ',' . $admin_id;
    //                 $group_mem_ids = explode(",", $group_mem_ids);
    //             } else {
    //                 $group_mem_ids = array($this->session->userdata('user_id'));
    //             }


    //             $no_of_mem = count($group_mem_ids);

    //             $data = array(
    //                 'title' => strip_tags($this->input->post('title', true)),
    //                 'description' => strip_tags($this->input->post('description', true)),
    //                 'created_by' => $this->session->userdata('user_id'),
    //                 'no_of_members' => $no_of_mem
    //             );

    //             $group_id = $this->chat_model->create_group($data);

    //             if ($group_id != false) {

    //                 foreach ($group_mem_ids as $user_id) {
    //                     $data1 = array(
    //                         'group_id' => $group_id,
    //                         'user_id' => $user_id,
    //                     );
    //                     $this->chat_model->add_group_members($data1);
    //                 }
    //                 $admins_ids = array($admin_id);
    //                 $this->chat_model->make_group_admin($group_id, $admins_ids);

    //                 $this->session->set_flashdata('message', 'Group Created successfully.');
    //                 $this->session->set_flashdata('message_type', 'success');
    //             } else {
    //                 $this->session->set_flashdata('message', 'Group could not Created! Try again!');
    //                 $this->session->set_flashdata('message_type', 'error');
    //             }

    //             $response['error'] = false;
    //             $response['message'] = 'Successful';
    //             print_r(json_encode($response));
    //         }
    //     } else {
    //         redirect('admin/login', 'refresh');
    //     }
    // }

    public function update_web_fcm()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $fcm = $this->input->post('web_fcm');
            $user_id = $this->session->userdata('user_id');
            if ($this->chat_model->update_web_fcm($user_id, $fcm)) {

                $response['error'] = false;
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    // public function get_group_members()
    // {
    //     if (!$this->ion_auth->logged_in()) {
    //         redirect('auth', 'refresh');
    //     } else {
    //         $group_id = $this->input->post('group_id');
    //         $users = $this->chat_model->get_group_members($group_id);
    //         if (!empty($users)) {

    //             $response['error'] = false;
    //             $response['data'] = $users;
    //             echo json_encode($response);
    //         } else {
    //             $response['error'] = true;
    //             $response['message'] = 'Not Successful';
    //             echo json_encode($response);
    //         }
    //     }
    // }

    public function send_msg()
    {

        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $user_id = $this->session->userdata('user_id');

            $data = array(
                'type' => $this->input->post('chat_type'),
                'from_id' => $this->session->userdata('user_id'),
                'to_id' => $this->input->post('opposite_user_id'),
                'message' => $this->input->post('chat-input-textarea')
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
                $other_image_cnt = count($_FILES['documents']['name']);
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
                            'user_id' => $this->session->userdata('user_id'),
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
                            'user_id' => $this->session->userdata('user_id'),
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


            $messages = $this->chat_model->get_msg_by_id($msg_id, $this->input->post('opposite_user_id'), $this->session->userdata('user_id'), $this->input->post('chat_type'));
            $message = array();
            $i = 0;
            foreach ($messages as $row) {
                $message[$i] = $row;
                $media_files = $this->chat_model->get_media($row['id']);
                $message[$i]['media_files'] = !empty($media_files) ? $media_files : '';
                $message[$i]['text'] = $row['message'];
                $i++;
            }
            $new_msg = $message;

            if (!empty($msg_id)) {

                $to_id = $this->input->post('opposite_user_id');
                $from_id = $this->session->userdata('user_id');

                // if ($to_id == $from_id && $this->input->post('chat_type') == 'person') {
                //     return false;
                // }

                // single user msg
                // if (($this->input->post('chat_type') == 'person') || ($this->input->post('chat_type') == 'supporter')) {
                if (($this->input->post('chat_type') == 'person')) {

                    // this is the user who going to recive FCM msg
                    // $user = $this->users_model->get_user_by_id($to_id);
                    $user = fetch_details('users', ['active' => 1, 'id' => $to_id]);

                    // this is the user who going to send FCM msg 
                    // $senders_info = $this->users_model->get_user_by_id($this->session->userdata('user_id'));
                    $senders_info = fetch_details('users', ['active' => 1, 'id' => $this->session->userdata('user_id')]);

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
                    $notification['body'] = $this->input->post('chat-input-textarea');
                    // $notification['icon'] = 'assets/icons/' . (!empty(get_half_logo()) ? get_half_logo() : 'logo-half.png');
                    $notification['base_url'] = base_url('chat');
                    $data['data']['data'] = $notification;
                    $data['data']['webpush']['fcm_options']['link'] = base_url('chat');
                    $data['to'] = isset($user[0]['web_fcm']) ? $user[0]['web_fcm'] : '';



                    //send notification in app

                    $results = fetch_details('users', null, 'fcm_id', 10000, 0, '', '', "id", $this->input->post('opposite_user_id'));
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
                    $fcmMsg = array(
                        'content_available' => true,
                        'title' => 'New Message from Admin',
                        'body' => $this->input->post('chat-input-textarea'),
                        'type' => "chat",
                        'message' => json_encode($new_msg),
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
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
                    $group_id = $this->input->post('opposite_user_id');

                    $users = $this->chat_model->get_group_members($group_id);
                    foreach ($users as $user) {
                        // $userdata = $this->users_model->get_user_by_id($user['user_id']);
                        $userdata = fetch_details('users', ['active' => 1, 'id' => $user['user_id']]);
                        if ($user['user_id'] != $this->session->userdata('user_id')) {
                            $fcm_ids[] = $userdata[0]['web_fcm'];
                        }
                    }

                    $registrationIDs = $fcm_ids;

                    // this is the user who going to send FCM msg
                    // $senders_info = $this->users_model->get_user_by_id($this->session->userdata('user_id'));
                    $senders_info = fetch_details('users', ['active' => 1, 'id' => $this->session->userdata('user_id')]);

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

                    //send notification to members 

                    $fcmMsg = array(
                        'content_available' => true,
                        'title' => $senders_info[0]['username'],
                        'body' => $this->input->post('chat-input-textarea'),
                        'type' => "group",
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
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

                    $this->chat_model->set_group_msg_as_unread($group_id, $this->session->userdata('user_id'));

                    $result['response'] = curl_exec($ch);
                    if (curl_errno($ch))
                        echo 'Error:' . curl_error($ch);

                    curl_close($ch);
                }

                $response['error'] = false;
                $response['message'] = 'Successful';
                $response['msg_id'] = $msg_id;
                $response['new_msg'] = $new_msg;

                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    public function mark_msg_read()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {

            $type = $this->input->post('type');
            $to_id = $this->session->userdata('user_id');
            $from_id = $this->input->post('from_id');
            if ($this->chat_model->mark_msg_read($type, $from_id, $to_id)) {
                $response['error'] = false;
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    // public function delete_group()
    // {
    //     if (!$this->ion_auth->logged_in()) {
    //         redirect('auth', 'refresh');
    //     } else {

    //         $user_id = $this->session->userdata('user_id');
    //         $group_id = $this->input->post('grp_id');


    //         if ($this->chat_model->delete_group($group_id, $user_id)) {
    //             $response['error'] = false;
    //             $response['message'] = 'Successful';
    //             echo json_encode($response);
    //         } else {
    //             $response['error'] = true;
    //             $response['message'] = 'Not Successful';
    //             echo json_encode($response);
    //         }
    //     }
    // }

    public function delete_msg()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {

            $workspace_id = $this->session->userdata('workspace_id');
            $from_id = $this->session->userdata('user_id');
            $msg_id = $this->uri->segment(4);

            if (empty($msg_id) || !is_numeric($msg_id) || $msg_id < 1) {
                redirect('chat', 'refresh');
                return false;
                exit(0);
            }

            if ($this->chat_model->delete_msg($from_id, $msg_id)) {
                $response['error'] = false;
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    public function load_chat()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $user_id = $this->session->userdata('user_id');

            $type = $this->input->post('type');
            $to_id = $this->session->userdata('user_id');
            $from_id = $this->input->post('from_id');

            $offset = (!empty($_POST['offset'])) ? $this->input->post('offset') : 0;
            $limit = (!empty($_POST['limit'])) ? $this->input->post('limit') : 100;

            $sort = (!empty($_POST['sort'])) ? $this->input->post('sort') : 'id';
            $order = (!empty($_POST['order'])) ? $this->input->post('order') : 'DESC';

            $search = (!empty($_POST['search'])) ? $this->input->post('search') : '';

            $message = array();

            $messages = $this->chat_model->load_chat($from_id, $to_id, $type,  $offset, $limit, $sort, $order, $search);
            // print_r($from_id);
            // print_r($to_id);
            // print_r($user_id);
            // print_r($offset);
            // print_r($limit);
            // print_r($sort);
            // print_r($order);
            // print_r($search);
            // print_r($messages);
            if ($messages['total_msg'] == 0) {

                $message['error'] = true;
                $message['error_msg'] = 'No Chat OR Msg Found';
                print_r(json_encode($message));
                return false;
            }

            $i = 0;
            $message['total_msg'] = $messages['total_msg'];
            foreach ($messages['msg'] as $row) {
                $message['msg'][$i] = $row;
                $media_files = $this->chat_model->get_media($row['id']);
                $message['msg'][$i]['media_files'] = !empty($media_files) ? $media_files : '';
                $message['msg'][$i]['text'] = $row['message'];
                if ($row['from_id'] == $to_id) {
                    $message['msg'][$i]['position'] = 'right';
                } else {
                    $message['msg'][$i]['position'] = 'left';
                }
                $i++;
            }
            print_r(json_encode($message));
        }
    }

    public function switch_chat()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $type = $this->input->post('type');
            $id = $this->input->post('from_id');
            $users = $this->chat_model->switch_chat($id, $type);
            // $grp_members = $this->chat_model->get_group_members($id);
            // print_R($users);
            // die;

            $user = array();
            $i = 0;
            foreach ($users as $row) {

                $user[$i] = $row;
                if (($type == 'person') || ($type == 'supporter')) {
                    $user[$i]['picture'] = $row['username'];

                    $date = strtotime('now');

                    if ($row['last_online'] > $date) {
                        $user[$i]['is_online'] = 1;
                    } else {
                        $user[$i]['is_online'] = 0;
                    }
                }

                $i++;
            }
            // $user['grp_members'] = $grp_members;

            print_r(json_encode($user));
        }
    }

    public function send_fcm()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {

            $to_id = $this->input->post('receiver_id');
            $from_id = $this->session->userdata('user_id');

            if ($to_id == $from_id) {
                return false;
            }

            $title = $this->input->post('title');
            $type = $this->input->post('type');
            $msg = $this->input->post('msg');
            // $user = $this->users_model->get_user_by_id($to_id);
            $user = fetch_details('users', ['active' => 1, 'id' => $to_id]);

            $message_type = !empty($this->input->post('message_type')) ? $this->input->post('message_type') : 'other';

            $data = $notification = array();
            $fcmFields = [];

            $fcmMsg = array(
                'content_available' => true,
                'title' => 'test',
                'body' => $msg,
                'type' => $type,
                "from_id" => $from_id,
                "to_id" => $to_id,
                "chat_type" => "person"
            );

            $fcmFields = array(
                'registration_ids' => [$user[0]['web_fcm']],  // expects an array of ids
                'priority' => 'high',
                'notification' => $fcmMsg,
                'data' => $fcmMsg,
            );
            // print_r($fcmMsg);
            $headers = array(
                'Authorization: key=' . get_settings('fcm_server_key'),
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
            $result = curl_exec($ch);
            curl_close($ch);
            echo $result;


            print_r(json_encode($fcmFields));
        }
    }
}
