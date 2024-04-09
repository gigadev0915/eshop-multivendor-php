<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_orders_status extends CI_Migration
{

    public function up()
    {
        /* adding new column in users status */

        $fields = array(
            'status' => array(
                'type'           => 'varchar',
                'constraint'     => '50',
                'NULL'           => FALSE,
                'after'          => 'delivery_time'
            ),
        );
        $this->dbforge->add_column('orders', $fields);

    }
    public function down()
    {
        $this->dbforge->drop_column('status', 'orders');
    }
}
