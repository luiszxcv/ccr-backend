<?php
$api_endpoint = get_home_url();

$builder_meta = get_post_meta( get_the_ID(), '_cpb_builder_data', true );
$default_locations = apply_filters( 'cp_location_builder', [] );

$cpb_single_product_meta = get_post_meta( get_the_ID(), '_cpb_single_product', true );
$cpb_my_account_meta = get_post_meta( get_the_ID(), '_cpb_my_account', true );
$cpb_checkout_meta = get_post_meta( get_the_ID(), '_cpb_checkout', true );

$single_product_location_meta = empty( $cpb_single_product_meta ) ? [] : json_decode( $cpb_single_product_meta );
$my_account_location_meta = empty( $cpb_my_account_meta ) ? [] : json_decode( $cpb_my_account_meta );
$checkout_location_meta = empty( $cpb_checkout_meta ) ? [] : json_decode( $cpb_checkout_meta );

$single_product_location = empty( $single_product_location_meta ) ? $default_locations[ 'single_product' ] : ["location" => $single_product_location_meta];
$my_account_location = empty( $my_account_location_meta ) ? $default_locations[ 'my_account' ] : ["location" => $my_account_location_meta];
$checkout_location = empty( $checkout_location_meta ) ? $default_locations[ 'checkout' ] : ["location" => $checkout_location_meta];

$location_meta = [
    'single_product' => $single_product_location,
    'checkout' => $checkout_location,
    'my_account' => $my_account_location
];

$wc_publish_products = wc_get_products( array('status' => 'publish') );
$products_meta = array_map( function ( $product ) {
    return [
        'id' => $product->get_id(),
        'name' => $product->get_name(),
        'variants' => array_map( function ( $attr ) {
            return $attr[ 'data' ];
        }, array_values( $product->get_attributes() ) )
    ];
}, $wc_publish_products );
?>
<div class="body-builder">
    <div class="appBuilder" id="appBuilder">
        <div>
            <cp-steps :layouts="old" :locations="oldLocations" :hooks="hooks" :api_endpoint="api_endpoint"
                      :valiable_elements="elements"></cp-steps>
        </div>
    </div>
</div>
<script>
    const $ = jQuery;
    new Vue({
        el: "#appBuilder",
        data: {
            old: <?php echo( $builder_meta ? $builder_meta : '[]' ); ?>,
            api_endpoint: <?php echo("'" . $api_endpoint . "'"); ?>,
            elements: <?php echo( json_encode( apply_filters( 'cp_element_builder', [] ) ) ) ?>,
            oldLocations: <?php echo( json_encode( $location_meta ) ) ?>,
            hooks: <?php echo( json_encode( apply_filters( 'cp_hooks_data', [] ) ) ) ?>,
        }
    });
</script>