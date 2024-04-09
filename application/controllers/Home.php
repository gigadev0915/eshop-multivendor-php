<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
defined('BASEPATH') or exit('No direct script access allowed');


class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->library(['Jwt', 'Key']);
        $this->load->model(['address_model', 'category_model', 'product_model', 'brand_model', 'cart_model', 'faq_model', 'blog_model', 'ion_auth_model']);
        $this->load->library(['pagination']);
        $this->data['is_logged_in'] = ($this->ion_auth->logged_in()) ? 1 : 0;
        $this->data['user'] = ($this->ion_auth->logged_in()) ? $this->ion_auth->user()->row() : array();
        $this->data['settings'] = get_settings('system_settings', true);
        $this->data['web_settings'] = get_settings('web_settings', true);
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
    }

    public function index()
    {

        $this->data['main_page'] = 'home';
        $this->data['title'] = 'Home | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Home, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Home | ' . $this->data['web_settings']['meta_description'];

        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $limit =  12;
        $offset =  0;
        $sort = 'row_order';
        $order =  'ASC';
        $has_child_or_item = 'false';
        $filters = [];
        /* Fetching Categories Sections */
        $categories = $this->category_model->get_categories('', $limit, $offset, $sort, $order, 'false');
        $brands = $this->brand_model->get_brands('', $limit, $offset, $sort, $order, 'false');
        // echo "<pre>";
        // print_r($brands);
        // die;
        /* Fetching Featured Sections */

        $sections = $this->db->limit($limit, $offset)->order_by('row_order')->get('sections')->result_array();

        $user_id = NULL;
        if ($this->data['is_logged_in']) {
            $user_id = $this->data['user']->id;
        }
        $filters['show_only_active_products'] = true;
        if (!empty($sections)) {
            for ($i = 0; $i < count($sections); $i++) {
                $product_ids = isset($sections[$i]['product_ids']) ? explode(',', (string)$sections[$i]['product_ids']) : '';
                $product_ids = array_filter((array)$product_ids);

                $product_categories = (isset($sections[$i]['categories']) && !empty($sections[$i]['categories']) && $sections[$i]['categories'] != NULL) ? explode(',', $sections[$i]['categories']) : null;
                if (isset($sections[$i]['product_type']) && !empty($sections[$i]['product_type'])) {
                    $filters['product_type'] = (isset($sections[$i]['product_type'])) ? $sections[$i]['product_type'] : null;
                }

                $theme = fetch_details('themes', ['is_default' => 1], 'name');
                // print_r($theme);

                if (isset($theme[0]['name']) && strtolower($theme[0]['name']) == 'modern') {
                    if ($sections[$i]['style'] == "default" || $sections[$i]['style'] == "style_3") {
                        $limit = 4;
                    } elseif ($sections[$i]['style'] == "style_1" || $sections[$i]['style'] == "style_2" || $sections[$i]['style'] == "style_4") {
                        $limit = 8;
                    } else {
                        $limit = null;
                    }
                } else {
                    if ($sections[$i]['style'] == "default") {
                        $limit = 10;
                    } elseif ($sections[$i]['style'] == "style_1" || $sections[$i]['style'] == "style_2") {
                        $limit = 4;
                    } elseif ($sections[$i]['style'] == "style_3" || $sections[$i]['style'] == "style_4") {
                        $limit = 5;
                    } else {
                        $limit = null;
                    }
                }

                $products = fetch_product($user_id, (isset($filters)) ? $filters : null, (isset($product_ids)) ? $product_ids : null, $product_categories, $limit, null, null, null);
                // print_R($products);
                $sections[$i]['title'] =  output_escaping($sections[$i]['title']);
                $sections[$i]['slug'] =  url_title($sections[$i]['title'], 'dash', true);
                $sections[$i]['short_description'] =  output_escaping($sections[$i]['short_description']);
                $sections[$i]['filters'] = (isset($products['filters'])) ? $products['filters'] : [];
                $sections[$i]['product_details'] =  $products['product'];
                unset($sections[$i]['product_details'][0]['total']);
                $sections[$i]['product_details'] = $products['product'];
                unset($product_details);
            }
        }

        $this->data['sections'] = $sections;
        $this->data['categories'] = $categories;
        $this->data['brands'] = $brands;
        $this->data['username'] = $this->session->userdata('username');
        $this->data['sliders'] = get_sliders();
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function error_404()
    {
        $this->data['main_page'] = 'error_404';
        $this->data['title'] = 'Product cart | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Product cart, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Product cart | ' . $this->data['web_settings']['meta_description'];
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function categories()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $limit =  50;
        $offset =  0;
        $sort = 'row_order';
        $order =  'ASC';
        $has_child_or_item = 'false';
        $this->data['main_page'] = 'categories';
        $this->data['title'] = 'Categories | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Categories, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Categories | ' . $this->data['web_settings']['meta_description'];
        $this->data['categories'] = $this->category_model->get_categories('', $limit, $offset, $sort, $order, 'false');
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function brands()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }

        $offset =  0;
        $sort = 'row_order';
        $order =  'ASC';
        $brands = $this->brand_model->get_brands('', $limit = NULL, $offset, $sort, $order, 'false');

        $limit = ($this->input->get('per-page')) ? $this->input->get('per-page', true) : 16;

        $total_rows = count($brands);


        $config['base_url'] = base_url('home/brands/');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $limit;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 3;
        $config['num_links'] = 3;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = FALSE;

        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';

        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_link'] = 'First';
        $config['first_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_link'] = 'Last';
        $config['last_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $page_no = (empty($this->uri->segment(3))) ? 1 : $this->uri->segment(3);
        if (!is_numeric($page_no)) {
            redirect(base_url('brands'));
        }
        $offset = ($page_no - 1) * $limit;
        $this->pagination->initialize($config);
        $this->data['links'] =  $this->pagination->create_links();


        $this->data['main_page'] = 'Brands';
        $this->data['title'] = 'Brands | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Brands, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Brands | ' . $this->data['web_settings']['meta_description'];
        $this->data['brands'] = $brands;
        // echo "<pre>";
        // print_r($this->data['brands']);
        // die;
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function get_products()
    {
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('id', 'Product ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search', 'trim|xss_clean');
        $this->form_validation->set_rules('category_id', 'Category id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean|alpha');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $limit = (isset($_GET['limit'])) ? $this->input->post('limit', true) : 25;
            if (isset($_GET['page']) && !empty($_GET['page'])) {
                $offset = ($_GET['page'] - 1) * $limit;
            } else {
                $offset = (isset($_GET['offset'])) ? $this->input->get('offset', true) : 0;
            }
            $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? $_GET['order'] : 'DESC';
            $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? $_GET['sort'] : 'p.id';
            $filters['search'] =  (isset($_GET['search'])) ? $_GET['search'] : null;
            $filters['attribute_value_ids'] = (isset($_GET['attribute_value_ids'])) ? $_GET['attribute_value_ids'] : null;
            $category_id = (isset($_GET['category_id'])) ? $_GET['category_id'] : null;
            $product_id = (isset($_GET['id'])) ? $_GET['id'] : null;
            $user_id = (isset($_GET['user_id'])) ? $_GET['user_id'] : null;

            $products = fetch_product($user_id, (isset($filters)) ? $filters : null, $product_id, $category_id, $limit, $offset, $sort, $order);
            $first_search_option[0] = array(
                'id' => 0,
                'image_sm' => base_url(get_settings('favicon')),
                'name' => 'Search Result for ' . $_GET['search'],
                'category_name' => 'all categories',
                'link' => base_url('products/search?q=' . $_GET['search']),
            );
            if (!empty($products['product'])) {
                $products['product'] = array_map(function ($d) {
                    $d['link'] = base_url('products/details/' . $d['slug']);
                    return $d;
                }, $products['product']);
                $this->response['error'] = false;
                $this->response['message'] = "Products retrieved successfully !";
                $this->response['filters'] = (isset($products['filters']) && !empty($products['filters'])) ? $products['filters'] : [];
                $this->response['total'] = (isset($products['total'])) ? strval($products['total']) : '';
                $this->response['offset'] = (isset($_GET['offset']) && !empty($_GET['offset'])) ? $_GET['offset'] : '0';
                if (isset($_GET['page']) && !empty($_GET['page'])) {
                    $products['product'] = $products['product'];
                } else {
                    $products['product'] = array_merge($first_search_option, $products['product']);
                }
                $this->response['data'] = $products['product'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Products Not Found !";
                $this->response['data'] =  $first_search_option;
            }
        }
        print_r(json_encode($this->response));
    }
    public function get_product_faqs()
    {
        $filters['search'] =  (isset($_GET['search'])) ? $_GET['search'] : null;
        $first_search_option[0] = array(
            'id' => 0,
            'image_sm' => base_url(get_settings('favicon')),
            'name' => 'Search Result for ' . $_GET['search'],
            'category_name' => 'all categories',
            'link' => base_url('products/search?q=' . $_GET['search']),
        );
    }
    public function address_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->address_model->get_address_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function checkout()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'checkout';
        $this->data['title'] = 'Checkout | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Checkout, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Checkout | ' . $this->data['web_settings']['meta_description'];
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function terms_and_conditions()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'terms-and-conditions';
        $this->data['title'] = 'Terms & Conditions | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Terms & Conditions, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Terms & Conditions | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'Terms & Conditions | ' . $this->data['web_settings']['site_title'];
        $this->data['terms_and_conditions'] = get_settings('terms_conditions');
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function privacy_policy()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'privacy-policy';
        $this->data['title'] = 'Privacy Policy | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Privacy Policy, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Privacy Policy | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'Privacy Policy | ' . $this->data['web_settings']['site_title'];
        $this->data['privacy_policy'] = get_settings('privacy_policy');
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }
    public function shipping_policy()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'shipping-policy';
        $this->data['title'] = 'Shipping Policy | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Shipping Policy, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Shipping Policy | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'Shipping Policy | ' . $this->data['web_settings']['site_title'];
        $this->data['shipping_policy'] = get_settings('shipping_policy');
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }
    public function return_policy()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'return-policy';
        $this->data['title'] = 'Return Policy | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Return Policy, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Return Policy | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'Return Policy | ' . $this->data['web_settings']['site_title'];
        $this->data['return_policy'] = get_settings('return_policy');
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }
    public function about_us()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'about-us';
        $this->data['title'] = 'About US | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'About US, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'About US | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'About US | ' . $this->data['web_settings']['site_title'];
        $this->data['about_us'] = get_settings('about_us');
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function contact_us()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'contact-us';
        $this->data['title'] = 'Contact US | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Contact US, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Contact US | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'Contact US | ' . $this->data['web_settings']['site_title'];
        $this->data['contact_us'] = get_settings('contact_us');
        $this->data['web_settings'] = get_settings('web_settings', true);
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function faq()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'faq';
        $this->data['title'] = 'FAQ | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'FAQ, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'FAQ | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'FAQ | ' . $this->data['web_settings']['site_title'];
        $this->data['faq'] = $this->faq_model->get_faqs(null, null, null, null);
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    public function blogs()
    {
        $web_doctor_brown = get_settings('web_doctor_brown', true);
        $system_settings = get_settings('system_settings', true);

        if ((!isset($web_doctor_brown) || empty($web_doctor_brown))) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("admin/purchase-code"));
        }
        if ((isset($system_settings['is_web_under_maintenance']) && $system_settings['is_web_under_maintenance'] == 1)) {
            /* redirect him to the page where he can enter the purchase code */
            redirect(base_url("maintenance"));
        }
        $this->data['main_page'] = 'blogs';
        $this->data['title'] = 'Blogs | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Blogs, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Blogs | ' . $this->data['web_settings']['meta_description'];
        $this->data['meta_description'] = 'Blogs | ' . $this->data['web_settings']['site_title'];
        $this->data['blogs'] = $this->blog_model->get_blogs(null, null, null, null);
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }

    /**
     * Log the user in
     */
    public function login()
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        $this->data['title'] = $this->lang->line('login_heading');

        if (preg_match($regex, $_POST['identity'])) {
            $identity_column = 'email';
        } else {
            $identity_column = $this->config->item('identity', 'ion_auth');
        }
        if (preg_match($regex, $_POST['identity'])) {
            $this->ion_auth_model->identity_column = 'email';
        } else {
            $this->ion_auth_model->identity_column = 'mobile';
        }

        // validate form input
        if ($_POST['type'] == 'phone') {
            if (preg_match($regex, $_POST['identity'])) {
                $this->form_validation->set_rules('identity', ucfirst($identity_column), 'trim|required|valid_email');
            } else {
                $this->form_validation->set_rules('identity', ucfirst($identity_column), 'required|numeric');
            }
        }
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

        if ($this->form_validation->run() === TRUE) {
            $tables = $this->config->item('tables', 'ion_auth');
            $identity = $this->input->post('identity', true);
            if (isset($_POST['type']) && $_POST['type'] != 'phone') {
                $res = $this->db->select('id')->where('email', $identity)
                    ->where('type', $_POST['type'])->get($tables['login_users'])->result_array();
            } else {
                $res = $this->db->select('id')->where($identity_column, $identity)->get($tables['login_users'])->result_array();
            }

            if (isset($_POST['type']) && $_POST['type'] != 'phone') {
                $user_data = fetch_details('users', ['email' => $identity]);
            } else {
                if (preg_match($regex, $_POST['identity'])) {
                    $user_data = fetch_details('users', ['email' => $identity]);
                } else {
                    $user_data = fetch_details('users', ['mobile' => $identity]);
                }
            }

            if (isset($user_data[0]['active']) && !empty($user_data) && $user_data[0]['active'] == 7) {
                $response['error'] = true;
                $response['message'] = 'User Not Found';
                echo json_encode($response);
                return false;
            }
            if (isset($user_data[0]['active']) && !empty($user_data) && $user_data[0]['active'] == 0) {
                $response['error'] = true;
                $response['message'] = 'You are not allowed to login your account is inactive.';
                echo json_encode($response);
                return false;
            }
            if (isset($_POST['type']) && $_POST['type'] != 'phone') {
                $this->ion_auth_model->identity_column = 'email';
            }

            if (!empty($res)) {
                // check to see if the user is logging in
                // check for "remember me"
                $remember = (bool)$this->input->post('remember');


                if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember, $this->input->post('type'))) {
                    //if the login is successful

                    if (!$this->input->is_ajax_request()) {
                        redirect('admin/home', 'refresh');
                    }
                    $this->response['error'] = false;
                    $this->response['message'] = $this->ion_auth->messages();
                    echo json_encode($this->response);
                } else {
                    // if the login was un-successful

                    $this->response['error'] = true;
                    $this->response['message'] = "Incorrect Number or password";
                    echo json_encode($this->response);
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Incorrect Number or password';
                echo json_encode($this->response);
            }
        } else {
            // the user is not logging in so display the login page
            if (validation_errors()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                echo json_encode($this->response);
                return false;
                exit();
            }
            if ($this->session->flashdata('message')) {
                $this->response['error'] = false;
                $this->response['message'] = $this->session->flashdata('message');
                echo json_encode($this->response);
                return false;
                exit();
            }

            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = [
                'name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            ];

            $this->data['password'] = [
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
            ];

            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'login', $this->data);
        }
    }

    public function verifyUser()
    {
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean|valid_email');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $user_data = fetch_details('users', ['email' => $_POST['email'], 'type' => $_POST['type']]);
            if (!empty($user_data)) {
                $this->response['error'] = false;
                $this->response['message'] = 'data retrived';
                $this->response['data'] = $user_data[0];
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'data not retrived';
                $this->response['data'] = $user_data;
                print_r(json_encode($this->response));
            }
        }
    }

    public function lang($lang_name = '')
    {
        if (empty($lang_name)) {
            redirect(base_url());
        }

        $language = get_languages(null, $lang_name);
        if (empty($language)) {
            redirect(base_url());
        }
        $this->lang->load('web_labels_lang', $lang_name);
        $cookie = array(
            'name'   => 'language',
            'value'  => $lang_name,
            'expire' => time() + 1000
        );
        $this->input->set_cookie($cookie);
        if (isset($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(base_url());
        }
    }

    public function reset_password()
    {
        /* Parameters to be passed
            mobile_no:7894561235            
            new: pass@123
        */
        $this->form_validation->set_rules('mobile', 'Mobile No', 'trim|numeric|required|xss_clean|max_length[16]');
        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }

        $identity_column = $this->config->item('identity', 'ion_auth');
        $res = fetch_details('users', ['mobile' => $_POST['mobile']]);
        if (!empty($res)) {
            $identity = ($identity_column  == 'email') ? $res[0]['email'] : $res[0]['mobile'];
            if (!$this->ion_auth->reset_password($identity, $_POST['new_password'])) {
                $this->response['error'] = true;
                $this->response['message'] = $this->ion_auth->messages();
                $this->response['data'] = array();
                echo json_encode($this->response);
                return false;
            } else {
                $this->response['error'] = false;
                $this->response['message'] = 'Reset Password Successfully';
                $this->response['data'] = array();
                echo json_encode($this->response);
                return false;
            }
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'User does not exists !';
            $this->response['data'] = array();
            echo json_encode($this->response);
            return false;
        }
    }

    public function send_contact_us_email()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = validation_errors();
            print_r(json_encode($this->response));
            return false;
        }

        $username = $this->input->post('username', true);
        $email = $this->input->post('email', true);
        $subject = $this->input->post('subject', true);
        $message = $this->input->post('message', true);
        $web_settings = get_settings('web_settings', true);
        $to = $web_settings['support_email'];
        $email_message = array(
            'username' => 'Hello, Dear <b>' . ucfirst($username) . '</b>, ',
            'subject' => $subject,
            'email' =>  'email : ' . $email,
            'message' => $message
        );
        $mail = send_mail($to,  $subject, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
        if ($mail['error'] == true) {
            $this->response['error'] = true;
            $this->response['message'] = "Cannot send mail. Please try again later.";
            $this->response['data'] = $mail['message'];
            echo json_encode($this->response);
            return false;
        } else {
            $this->response['error'] = false;
            $this->response['message'] = 'Mail sent successfully. We will get back to you soon.';
            $this->response['data'] = array();
            echo json_encode($this->response);
            return false;
        }
    }
}
