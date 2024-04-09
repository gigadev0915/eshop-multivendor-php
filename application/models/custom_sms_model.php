<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Custom_sms_model extends CI_Model
{

    public function add_custom_sms($data)
    {
        $data = escape_array($data);
        // print_r($data);
        $custom_sms_data = [
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type']
        ];
        if (isset($data['edit_custom_sms'])) {
            $this->db->set($custom_sms_data)->where('id', $data['edit_custom_sms'])->update('custom_sms');
        } else {
            $this->db->insert('custom_sms', $custom_sms_data);
        }
    }

    public function get_custom_sms_data($offset = 0, $limit = 10, $sort = 'id', $order = 'ASC')
    {

        $multipleWhere = '';
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['id' => $search, 'title' => $search, 'message' => $search];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        $city_count = $count_res->get('custom_sms')->result_array();

        foreach ($city_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' * ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('custom_sms')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            // $operate = ' <a class="delete_custom_sms btn action-btn btn-danger btn-xs mr-1 mb-1 ml-1" title="Delete" href="javascript:void(0)"  data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';
            // $operate .= '<a href="javascript:void(0)" class="edit_sms_modal action-btn btn btn-primary btn-xs mr-1 mb-1 ml-1" data-id="' . $row['id'] . '" data-target="#sms-gateway-modal" data-toggle="modal" data-url="admin/sms-gateway-settings"  title="View SMS" ><i class="fa fa-pen"></i></a>';
            $operate = '<button type="button" class="btn btn-xs btn-primary edit_sms_modal" data-toggle="modal" data-id="' . $row['id'] . '" data-target="#sms-gateway-modal" data-url="admin/sms-gateway-settings" title="View SMS"><i class="fa fa-pen"></i></button>';


            $tempRow['id'] = $row['id'];
            $tempRow['title'] = $row['title'];
            $tempRow['message'] = $row['message'];
            $tempRow['type'] = ucwords(str_replace('_', " ", $row['type']));
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
