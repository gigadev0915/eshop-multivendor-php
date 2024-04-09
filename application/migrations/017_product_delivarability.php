<?php
defined('BASEPATH') or exit ('No direct script access allowed');
class Migration_product_delivarability extends CI_Migration
{

    public function up()
    {
        /* adding new column in users status */

        $fields = array(
            'deliverable_city_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'NULL' => FALSE,
                'default' => '0',
                'after' => 'deliverable_zipcodes'
            ),
            'deliverable_cities' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'NULL' => TRUE,
                'default' => NULL,
                'after' => 'deliverable_city_type'
            ),
        );
        $this->dbforge->add_column('products', $fields);

        $fields = array(
            'apikey' => array(
                'type' => 'VARCHAR',
                'constraint' => '2048',
                'NULL' => TRUE,
                'default' => NULL
            ),
        );
        $this->dbforge->modify_column('users', $fields);
        $fields = array(
            'deliverable_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'NULL' => TRUE,
                'default' => '0',
                'comment' => '(0:none, 1:all, 2:include, 3:exclude)'
            ),
        );
        $this->dbforge->modify_column('products', $fields);

    }
    public function down()
    {
        $this->dbforge->drop_column('users', 'apikey');
        $this->dbforge->drop_column('products', 'deliverable_type');
        $this->dbforge->drop_column('products', 'deliverable_city_type');
        $this->dbforge->drop_column('products', 'deliverable_cities');

    }
}
