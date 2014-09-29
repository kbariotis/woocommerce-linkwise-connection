<?php
/*
Plugin Name: WooCommerce Linkwise Connection
Plugin URI: http://github.com/stakisko/wordpress-linkwise-connection
Description: Inject appropriate scripts for connecting with Linkwise's platform
Author: Kostas Bariotis
Version: 0.1
Author URI: http://kostasbariotis.com/
*/

/*  Copyright 2014  KOSTAS BARIOTIS  (email : konmpar@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'woocommerce-linkwise-settings.php' );

if (!function_exists('get_home_path')) {
    require_once( ABSPATH . '/wp-admin/includes/file.php' );
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    function return_order_script($orderId) {
        $order = new WC_Order($orderId);

        $orderTotals = $order->get_total() - ($order->get_total_tax() + $order->get_total_shipping());
        $orderTotals = $orderTotals / 1.23;

        $options = get_option('linkwise_settings_option');
        $linkwiseId = $options['linkwise_id'];
        
        echo '<script type="text/javascript" src="https://go.linkwi.se/delivery/lwc/lwc.js"></script>
                <script type="text/javascript">
                    Linkwise.load_action("' . $linkwiseId . '", "' . $orderId . '", "1::' . $orderTotals . '", "", "pending");
                </script>
                <noscript>
                    <img src="https://go.linkwi.se/delivery/acl.php?cam_id=' . $linkwiseId .'&trans_id=' . $orderId . 
                    '&sale_amount=1::' . $orderTotals . '&adv_subid=&status=pending" style="width:0px;height:0px;"/>
                </noscript>';
    }
    
    function return_script() {
        echo '<script type="text/javascript" src="https://go.linkwi.se/delivery/js/tl.js"></script>';
    }

    add_action( 'woocommerce_thankyou', 'return_order_script' );
    add_action( 'wp_footer', 'return_script' );
}