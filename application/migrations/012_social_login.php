<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_social_login extends CI_Migration
{

    public function up()
    {
        $fields = array(
            'type' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '1024',
                'NULL'           => FALSE,
                'default'        => 'phone',
                'after'          => 'longitude'
            ),
        );
        $this->dbforge->add_column('users', $fields);

        $fields = array(
            'active_status' => array(
                'type'      => 'VARCHAR',
                'constraint' => '1024',
            ),
        );
        $this->dbforge->modify_column('order_items', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_column('users', 'type');
        $this->dbforge->drop_column('order_items', 'active_status');
    }
}
