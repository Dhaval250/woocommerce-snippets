<?php
/**
 * Plugin name: Package Cost Extra fee
 * Description: Package Cost Extra fee information for Web designer.
 * Author: Laurence Bahiirwa
 * Plugin URI: https://omukiguy.com
 * Author URI: https://omukiguy.com
 * text-domain: om-service-widget
 */

add_action( 'woocommerce_after_checkout_billing_form', 'techiepress_add_vat_cancel_button' );

function techiepress_add_vat_cancel_button( $checkout ) {
    echo '<div id="vat-cancel">'; 
    
    woocommerce_form_field(
        'techiepress_vat_cancel',
        array(
            'label'  => 'I am VAT exempt',
            'class'  => array( 'vat-cancel-button' ),
            'type'   => 'checkbox'
        ),
        $checkout->get_value( 'techiepress_vat_cancel' )
    );
    
    echo '</div>';    
}

add_action( 'wp_footer', 'techiepress_vat_cancel_ajax' );

function techiepress_vat_cancel_ajax() {
    
    if ( is_checkout() ) {
        ?>
        <script type="text/javascript">
            jQuery( document ).ready(
                function($) {
                    $('#techiepress_vat_cancel').click(
                        function() {
                            jQuery('body').trigger('update_checkout');
                        }    
                    );
                }
            );
        </script>
        <?php
    }
}

function lab_pacakge_cost() {
    
    global $woocommerce;
    
    $flat_fee    = get_option( 'techiepress_vat_pricing_flat_fee' );
    $dynamic_fee = get_option( 'techiepress_vat_pricing_dynamic_fee' );
    
    if ( ! $_POST || ( is_admin() && ! is_ajax() ) ) {
        return;
    }
    
    if ( isset( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
    } else {
        $post_data = $_POST;
    }
    
    if ( isset( $post_data['techiepress_vat_cancel'] ) ) {
        return;
    }
    
    $taxable = $flat_rate + ( $woocommerce->cart->cart_contents_total * $dynamic_fee );

    $woocommerce->cart->add_fee( __( 'VAT', 'om-service-widget' ), $taxable );
    
}

add_action( 'woocommerce_cart_calculate_fees', 'lab_pacakge_cost');


add_filter( 'woocommerce_settings_tabs_array', 'techiepress_add_vat_pricing', 50 );

function techiepress_add_vat_pricing( $settings_tab ) {
    
    $settings_tab['techiepress_vat_pricing'] = __( 'VAT Pricing', 'om-service-widget' );
    
    return $settings_tab;
}


add_action( 'woocommerce_settings_tabs_techiepress_vat_pricing', 'techiepress_add_vat_pricing_settings' );

function techiepress_add_vat_pricing_settings() {
    woocommerce_admin_fields( get_techiepress_vat_pricing_settings() );
}

add_action( 'woocommerce_update_options_techiepress_vat_pricing', 'techiepress_update_options_vat_pricing_settings' );

function techiepress_update_options_vat_pricing_settings() {
    woocommerce_update_options( get_techiepress_vat_pricing_settings() );
}

function get_techiepress_vat_pricing_settings() {
    
    $settings = array(
        
        'section_title' => array(
            'id'   => 'techiepress_vat_pricing_settings_title',
            'desc' => 'Section for handlign VAT information',
            'type' => 'title',
            'name' => 'VAT Pricing Information',
        ),
        
        'vat_pricing_flat_fee' => array(
            'id'   => 'techiepress_vat_pricing_flat_fee',
            'desc' => 'Flat Fee number',
            'type' => 'text',
            'name' => 'Flat Fee',
        ),
        
        'vat_pricing_dynamic_fee' => array(
            'id'   => 'techiepress_vat_pricing_dynamic_fee',
            'desc' => 'Percentage of Tax',
            'type' => 'text',
            'name' => 'Dynamic Fee',
        ),
        
        'section_end' => array(
            'id'   => 'techiepress_vat_pricing_sectionend',
            'type' => 'sectionend',
        ),
    );
    
    return apply_filters( 'filter_techiepress_vat_pricing_settings', $settings );
}
