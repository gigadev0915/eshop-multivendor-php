<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_order_attachments extends CI_Migration
{
    public function up()
    {
        /* adding new table chat_media */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE,
                'NULL'           => FALSE
            ],
            'message_id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => TRUE
            ],
            'original_file_name' => [
                'type'           => 'TEXT',
                'NULL'           => FALSE
            ],
            'file_name' => [
                'type'           => 'TEXT',
                'NULL'           => FALSE
            ],
            'file_extension' => [
                'type'           => 'VARCHAR',
                'constraint'     => '64',
                'NULL'           => FALSE
            ],
            'file_size' => [
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE
            ],
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',

        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('chat_media');

        /* adding new table custom sms */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE,
                'NULL'           => FALSE
            ],
            'title' => [
                'type'           => 'VARCHAR',
                'constraint'     => '2048',
                'NULL'           => FALSE
            ],
            'message' => [
                'type'           => 'VARCHAR',
                'constraint'     => '4096',
                'NULL'           => FALSE
            ],
            'type' => [
                'type'           => 'VARCHAR',
                'constraint'     => '64',
                'NULL'           => FALSE
            ],
            'date_sent TIMESTAMP default CURRENT_TIMESTAMP',

        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('custom_sms');


        /* adding new table messages */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE,
                'NULL'           => FALSE
            ],
            'from_id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE
            ],
            'to_id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE
            ],
            'is_read' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'default'        => '1',
            ],
            'message' => [
                'type'           => 'TEXT',
                'NULL'           => FALSE
            ],
            'type' => [
                'type'           => 'VARCHAR',
                'constraint'     => '128',
                'NULL'           => FALSE
            ],
            'media' => [
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE
            ],
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',

        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('messages');


        /* adding new table otps */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE,
                'NULL'           => FALSE
            ],
            'mobile' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
                'NULL'           => FALSE
            ],
            'otp' => [
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => FALSE
            ],
            'varified' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'default'        => '0',
                'comment' => '1 : verify | 0: not verify	'
            ],
            'created_at' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
            ],

        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('otps');

        /* adding new fields in users table */
        $fields = array(
            'last_online' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'after' => 'last_login'
            ),
            'web_fcm' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,
                'after' => 'last_login'
            ),
        );
        $this->dbforge->add_column('users', $fields);

        /* adding new fields in orders table */
        $fields = array(
            'attachments' => array(
                'type' => 'VARCHAR',
                'constraint' => '2048',
                'null' => TRUE,
                'after' => 'notes'
            ),
        );
        $this->dbforge->add_column('orders', $fields);
        /* adding new fields in products table */
        $fields = array(
            'is_attachment_required' => array(
                'type' => 'TINYINT',
                'default' => '0',
                'null' => TRUE,
                'after' => 'cancelable_till'
            ),
        );
        $this->dbforge->add_column('products', $fields);

        $data = array(
            array(
                'title' => 'Your Order Has Been Placed - Order #{order.id}',
                'message' => 'Dear {user.username},\r\n\r\nThank you for placing your order with {system.company_name}. We are thrilled to confirm that your order has been successfully placed and is now being processed. Please find the details of your order below:\r\n\r\nOrder Number: #{order.id}\r\nOrder Date: {order.date_added}\r\nDelivery Address: {order.address}\r\n\r\n...\r\nTotal Amount: ${order.total}\r\n\r\nPayment Information:\r\n----------------------------------------\r\nPayment Method: {order.payment_method}\r\n\r\nDelivery Information:\r\n----------------------------------------\r\nYour order will be delivered to the following address:\r\n{order.address}\r\n\r\nEstimated Delivery Date: {order.delivery_date}\r\n\r\nIf you have any questions or need further assistance with your order, please don\'t hesitate to contact our customer support team at \r\n{system.support_email} or {system.support_number}.\r\n\r\nThank you for choosing {system.company_name}. We appreciate your business and look forward to serving you. Your satisfaction is our priority.\r\n\r\nSincerely,\r\nThe {system.company_name} Team',
                'type' => 'place_order',
                'date_sent' => '2023-11-02 14:49:07',
            ),
            array(
                'title' => 'Cashback/Discount Settlement',
                'message' => 'Hello {user.username},\r\n\r\nGood news! Your cashback/discount has been successfully settled:\r\n\r\nYour cashback/discount has been credited to your account. Enjoy the savings!\r\n\r\nFor any questions, contact us at {system.support_number}. Thanks for choosing {system.company_name}.',
                'type' => 'settle_cashback_discount',
                'date_sent' => '2023-11-02 14:52:51',
            ),
            array(
                'title' => 'Seller Commission Settlement',
                'message' => 'Hello {user.username},\r\n\r\nGood news! Your recent sale on {system.company_name} has earned you a commission payout.\r\n\r\nYour payment will be processed and deposited into your account within [Payment Processing Time]. For assistance, contact us at {system.support_number}.\r\n\r\nThank you for being a seller on {system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'settle_seller_commission',
                'date_sent' => '2023-11-02 14:56:30',
            ),
            array(
                'title' => 'Order Confirmation - Your Order Is Received',
                'message' => 'Hello {user.username},\r\n\r\nYour order #{order.id} has been received by {system.company_name} We\'re working diligently to prepare and deliver your items.\r\n\r\nOrder Date: {order.date_added}\r\nTotal Amount: {system.currency}{order.total}\r\nEstimated Delivery Date: {order.delivery_date}\r\n\r\nWe\'ll keep you informed on your order\'s progress. For questions or support, contact us at {system.support_number}.\r\n\r\nThank you for shopping with us.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_received',
                'date_sent' => '2023-11-02 14:59:35',
            ),
            array(
                'title' => 'Order Processing Update',
                'message' => 'Hello {user.username},\r\n\r\nGreat news! Your order #{order.id} is now being processed. Here are the details:\r\n- Order Date: {order.date_added}\r\n- Estimated Delivery Date: {order.delivery_date}\r\n\r\nYour order will arrive by {order.delivery_date}. Any questions? Contact us at {system.support_number} Thank you for shopping with us.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_processed',
                'date_sent' => '2023-11-02 15:02:04',
            ),
            array(
                'title' => 'Your Order Has Been Shipped',
                'message' => 'Hello {user.username},\r\n\r\nExciting news! Your order #{order.id}has been shipped. \r\n\r\nEstimated Delivery: {order.delivery_date}\r\n\r\n\r\nFor any questions, contact us at {system.support_number}. We\'re here to help!\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_shipped',
                'date_sent' => '2023-11-02 15:13:09',
            ),
            array(
                'title' => 'Your Order Has Been Delivered',
                'message' => 'Hello {user.username},\r\n\r\nGreat news! Your order #{order.id} has been delivered. We hope you\'re enjoying your purchase. Here are the delivery\r\n\r\nDelivery Date: {order.delivery_date}\r\n\r\nIf you have any questions or need assistance, please contact us at {system.support_number}. Your satisfaction is important to us.\r\n\r\nThank you for choosing {system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_delivered',
                'date_sent' => '2023-11-02 15:17:33',
            ),
            array(
                'title' => 'Order Cancellation Confirmation',
                'message' => 'Hello {user.username},\r\n\r\nWe\'ve received your order cancellation request for order #{order.id}. Here are the details:\r\nOrder Date: {order.date_added}\r\n\r\nYou will receive a refund of {transactions.currency_code}{transactions.amount} via {transactions.type}. Please take a note of it .\r\n\r\nFor assistance, contact us at {system.support_number}. Thank you for choosing {system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_cancelled',
                'date_sent' => '2023-11-02 15:22:54',
            ),
            array(
                'title' => 'Order Return Confirmation',
                'message' => 'Hello {user.mobile},\r\n\r\nYour order #{return_requests.order_id} has been successfully returned. Return Details:\r\n- Return Date: {return_requests.date_created}\r\n- Reason: {return_requests.remarks}\r\n- Status: {return_requests.status}\r\n\r\n\r\nFor assistance, contact us at {system.support_number}.\r\n\r\nThank you for choosing {system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_returned',
                'date_sent' => '2023-11-02 15:26:29',
            ),
            array(
                'title' => 'Your Order Return Request - Declined',
                'message' => 'Hello {user.username},\r\n\r\nWe\'ve reviewed your return request for Order #{return_requests.order_item_id}, and unfortunately, it has been declined for the following reason: {return_requests.remarks}.\r\n\r\nWe understand this may be disappointing. If you have questions or need assistance, please contact our support team at {system.support_number}. We\'re here to help.\r\n\r\nThank you for choosing{system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_returned_request_decline',
                'date_sent' => '2023-11-02 15:29:45',
            ),
            array(
                'title' => 'Your Order Return Request - Approved',
                'message' => 'Hello {user.username},\r\n\r\nGreat news! Your order return request has been approved. Here are the details:\r\nOrder ID: {return_requests.order_item_id}\r\nReturn Reason: {return_requests.remarks}\r\n\r\nYour return process is now in progress. Please follow the provided instructions for returning the item. If you have any questions or need assistance, contact our support team at {system.support_number}.\r\n\r\nThank you for choosing {system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'customer_order_returned_request_approved',
                'date_sent' => '2023-11-02 15:31:43',
            ),
            array(
                'title' => 'Order Delivery Confirmation',
                'message' => 'Hello {user.username},\r\n\r\nGreat news! Your order #{order.id} from {system.company_name} has been successfully delivered to {order.address}. If you have any questions, contact us at {system.support_number}.\r\n\r\nThanks for choosing {system.company_name}!\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'delivery_boy_order_deliver',
                'date_sent' => '2023-11-02 15:35:59',
            ),
            array(
                'title' => 'Wallet Transaction Confirmation',
                'message' => 'Hello {user.username},\r\n\r\nYour wallet has been updated with a recent transaction.\r\nType: {transactions.type}\r\nAmount: {transactions.currency_code}{transactions.amount}\r\nDate: {transactions.transaction_date}\r\n\r\nFor assistance, contact us at{system.company_name}.\r\n\r\nThank you for {system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}',
                'type' => 'wallet_transaction',
                'date_sent' => '2023-11-02 15:38:37',
            ),
            array(
                'title' => 'Bank Transfer Receipt Status - Update',
                'message' => 'Hello {user.username},\r\n\r\nWe have an update regarding your bank transfer receipt:\r\nTransfer ID: {transactions.id}\r\nAmount Transferred: {transactions.currency_code}{transactions.amount}\r\nStatus: {transactions.status}\r\n\r\nFor questions or assistance, contact us at {system.support_number}.\r\n\r\nThank you for choosing {system.company_name}.\r\n\r\nBest regards,\r\n{system.company_name}\r\n{transactions.id}',
                'type' => 'bank_transfer_receipt_status',
                'date_sent' => '2023-11-02 15:46:35',
            ),
            // Add more data arrays as needed
        );
        $this->db->insert_batch('custom_sms', $data);

         $data = array(
            array(
                'variable' => 'sms_gateway_method',
                'value' => '',
            ),
            array(
                'variable' => 'authentication_settings',
                'value' => '{"authentication_method":"firebase"}',
            ),
            array(
                'variable' => 'vap_id_Key',
                'value' => '',
            ),
            array(
                'variable' => 'sms_gateway_settings',
                'value' => '{}',
            ),
            array(
                'variable' => 'send_notification_settings',
                'value' => '',
            ),
         );
         $this->db->insert_batch('settings', $data);
        // $this->db->query('INSERT INTO `settings` (`variable`, `value`) VALUES ("sms_gateway_method","")');
        // $this->db->query('INSERT INTO `settings` (`variable`, `value`) VALUES ("authentication_settings","")');
        // $this->db->query('INSERT INTO `settings` (`variable`, `value`) VALUES ("vap_id_Key","")');
        // $this->db->query('INSERT INTO `settings` (`variable`, `value`) VALUES ("sms_gateway_settings","")');
        // $this->db->query('INSERT INTO `settings` (`variable`, `value`) VALUES ("send_notification_settings","")');
        
    }

    public function down()
    {
        $this->dbforge->drop_table('chat_media');
        $this->dbforge->drop_table('custom_sms');
        $this->dbforge->drop_table('messages');
        $this->dbforge->drop_table('otps');
        $this->dbforge->drop_column('users', 'last_online');
        $this->dbforge->drop_column('users', 'web_fcm');
        $this->dbforge->drop_column('orders', 'attachments');
        $this->dbforge->drop_column('products', 'is_attachment_required');
    }
}
