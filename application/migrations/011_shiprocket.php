<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_shiprocket extends CI_Migration
{

    public function up()
    { 
        /* adding new table order_charges */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE,
                'NULL'           => FALSE
            ],
            'seller_id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE
            ],
            'product_variant_ids' => [
                'type'           => 'VARCHAR',
                'constraint'     => '1024',
                'NULL'           => FALSE
            ],
            'order_id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE
            ],
            'order_item_ids' => [
                'type'           => 'VARCHAR',
                'constraint'     => '1024',
                'NULL'           => FALSE
            ],
            'delivery_charge' => [
                'type'           => 'DOUBLE',
                'NULL'           => TRUE
            ],
            'promo_code' => [
                'type'           => 'VARCHAR',
                'constraint'     => '1024',
                'default'        => 'NULL',
                'NULL'           => TRUE,
            ],
            'promo_discount' => [
                'type'           => 'DOUBLE',
                'NULL'           => TRUE,
            ],
            'sub_total' => [
                'type'           => 'DOUBLE',
                'NULL'           => TRUE,
            ],
            'total' => [
                'type'           => 'DOUBLE',
                'NULL'           => TRUE,
            ],
            'otp' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'default'        => '0',
                'NULL'           => FALSE,
            ],
            'date_added TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_charges');

        /* adding new table pickup_locations */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE,
                'NULL'           => FALSE
            ],
            'seller_id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE
            ],
            'pickup_location' => [
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE
            ],
            'phone' => [
                'type'           => 'VARCHAR',
                'constraint'     => '28',
                'NULL'           => FALSE
            ],
            'address' => [
                'type'           => 'TEXT',
            ],
            'address_2' => [
                'type'           => 'TEXT',
            ],
            'city' => [
                'type'           => 'VARCHAR',
                'constraint'     => '56',
                'NULL'           => FALSE
            ],
            'state' => [
                'type'           => 'VARCHAR',
                'constraint'     => '56',
                'NULL'           => FALSE
            ],
            'country' => [
                'type'           => 'VARCHAR',
                'constraint'     => '56',
                'NULL'           => FALSE
            ],
            'pin_code' => [
                'type'           => 'VARCHAR',
                'constraint'     => '56',
                'NULL'           => FALSE
            ],
            'latitude' => [
                'type'           => 'VARCHAR',
                'constraint'     => '128',
                'default'        => 'NULL',
                'NULL'           => TRUE
            ],
            'longitude' => [
                'type'           => 'VARCHAR',
                'constraint'     => '128',
                'default'        => 'NULL',
                'NULL'           => TRUE
            ],
            'status' => [
                'type'           => 'TINYINT',
                'constraint'     => '4',
                'default'        => '0',
                'NULL'           => FALSE
            ],
            'date_added TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('pickup_locations');

        $fields = array(
            'is_pos_order' => array(
                'type'           => 'TINYINT',
                'constraint'     => '4',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'notes'
            ), 
        );
        $this->dbforge->add_column('orders', $fields);

        $fields = array(
            'authorized_signature' => array(
                'type'           => 'MEDIUMTEXT',
                'NULL'           => FALSE,
                'after'          => 'address_proof'
            ), 
        );
        $this->dbforge->add_column('seller_data', $fields);

        $fields = array(
            'extra_description' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '2048',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'description'
            ), 
            'pickup_location' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'deliverable_zipcodes'
            ), 
        );
        $this->dbforge->add_column('products', $fields);

        $fields = array(
            'link' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'type_id'
            ), 
        );
        $this->dbforge->add_column('sliders', $fields);

        $fields = array(
            'link' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'type_id'
            ), 
        );
        $this->dbforge->add_column('offers', $fields);

        $fields = array(
            'link' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'image'
            ), 
        );
        $this->dbforge->add_column('notifications', $fields);

        $fields = array(
            'weight' => array(
                'type'           => 'FLOAT',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'stock'
            ), 
            'height' => array(
                'type'           => 'FLOAT',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'weight'
            ), 
            'breadth' => array(
                'type'           => 'FLOAT',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'height'
            ), 
            'length' => array(
                'type'           => 'FLOAT',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'breadth'
            ), 
        );
        $this->dbforge->add_column('product_variants', $fields);

        $fields = array(
            'city' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'city_id'
            ), 
            'area' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'city'
            ), 
        );
        $this->dbforge->add_column('addresses', $fields);

        $fields = array(
            'shiprocket_order_id' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'after'          => 'order_id'
            ), 
            'shipment_id' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'after'          => 'shiprocket_order_id'
            ), 
            'courier_company_id' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'shipment_id'
            ), 
            'awb_code' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '128',
                'NULL'           => FALSE,
                'default'        => 'NULL',
                'after'          => 'courier_company_id'
            ), 
            'pickup_status' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'after'          => 'awb_code'
            ), 
            'pickup_scheduled_date' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'NULL'           => FALSE,
                'after'          => 'pickup_status'
            ), 
            'pickup_token_number' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'NULL'           => FALSE,
                'after'          => 'pickup_scheduled_date'
            ), 
            'status' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'after'          => 'pickup_token_number'
            ), 
            'others' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'NULL'           => FALSE,
                'after'          => 'status'
            ), 
            'pickup_generated_date' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'NULL'           => FALSE,
                'after'          => 'others'
            ), 
            'data' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'NULL'           => FALSE,
                'after'          => 'pickup_generated_date'
            ), 
            'date' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'NULL'           => FALSE,
                'after'          => 'data'
            ), 
            'is_canceled' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'date'
            ), 
            'manifest_url' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE,
                'after'          => 'is_canceled'
            ), 
            'label_url' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE,
                'after'          => 'manifest_url'
            ), 
            'invoice_url' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => FALSE,
                'after'          => 'label_url'
            ), 
        );
        $this->dbforge->add_column('order_tracking', $fields);

        $fields = array(
            'order_item_id' => array(
                'type' => 'MEDIUMTEXT',
            ),
        );
        $this->dbforge->modify_column('order_tracking', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_table('order_charges');
        $this->dbforge->drop_table('pickup_locations');
        $this->dbforge->drop_column('orders', 'is_pos_order');
        $this->dbforge->drop_column('seller_data', 'authorized_signature');
        $this->dbforge->drop_column('products', 'pickup_location');
        $this->dbforge->drop_column('products', 'extra_description');
        $this->dbforge->drop_column('sliders', 'link');
        $this->dbforge->drop_column('offers', 'link');
        $this->dbforge->drop_column('notifications', 'link');
        $this->dbforge->drop_column('product_variants', 'weight');
        $this->dbforge->drop_column('product_variants', 'height');
        $this->dbforge->drop_column('product_variants', 'breadth');
        $this->dbforge->drop_column('product_variants', 'length');
        $this->dbforge->drop_column('addresses', 'area');
        $this->dbforge->drop_column('addresses', 'city');
        $this->dbforge->drop_column('order_tracking', 'shiprocket_order_id');
        $this->dbforge->drop_column('order_tracking', 'shipment_id');
        $this->dbforge->drop_column('order_tracking', 'courier_company_id');
        $this->dbforge->drop_column('order_tracking', 'awb_code');
        $this->dbforge->drop_column('order_tracking', 'pickup_status');
        $this->dbforge->drop_column('order_tracking', 'pickup_scheduled_date');
        $this->dbforge->drop_column('order_tracking', 'pickup_token_number');
        $this->dbforge->drop_column('order_tracking', 'status');
        $this->dbforge->drop_column('order_tracking', 'others');
        $this->dbforge->drop_column('order_tracking', 'pickup_generated_date');
        $this->dbforge->drop_column('order_tracking', 'data');
        $this->dbforge->drop_column('order_tracking', 'date');
        $this->dbforge->drop_column('order_tracking', 'is_canceled');
        $this->dbforge->drop_column('order_tracking', 'manifest_url');
        $this->dbforge->drop_column('order_tracking', 'label_url');
        $this->dbforge->drop_column('order_tracking', 'invoice_url');
        $this->dbforge->drop_column('order_tracking', 'order_item_id');
    }
}
