<?php
/**
 * Plugin Name: Maybe Pass requests through Moesif
 * Description: Uses the WordPress provided `pre_http_request` to route some known URLs through Moesif for troubleshooting purposes
 * Plugin URI: https://www.thathandsomebeardedguy.com
 * Version: 0.1b
 * Author: Will Brubaker
 * Author URI: https://www.thathandsomebeardedguy.com
 *
 * @package Maybe Pass requests through Moesif
 */

/**
 * Filters whether to preempt an HTTP request's return value.
 *
 * Returning a non-false value from the filter will short-circuit the HTTP request and return
 * early with that value. A filter should return either:
 *
 *  - An array containing 'headers', 'body', 'response', 'cookies', and 'filename' elements
 *  - A WP_Error instance
 *  - boolean false (to avoid short-circuiting the response)
 *
 * Returning any other value may result in unexpected behaviour.
 *
 * @since 2.9.0
 *
 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
 * @param array               $r        HTTP request arguments.
 * @param string              $url      The request URL.
 */
add_filter( 'pre_http_request', 'handsome_bearded_guy_maybe_reroute_http_request', 10, 3 );

/**
 * Function handsome_bearded_guy_maybe_reroute_http_request
 *
 * @param false|array|WP_Error $return_value is passed by the filter, expect false.
 * @param array                $args HTTP request arguments. @see WP_Http class.
 * @param bool|array|WP_Error  $url The request URL.
 *
 * @return false|array|WP_Error
 */
function handsome_bearded_guy_maybe_reroute_http_request( $return_value, $args, $url ) {
	$moesif_id = get_option( 'moesif_collector_id', '' );
	if ( empty( $moesif_id ) ) {
		return;
	}
	$patterns = array(
		'paypal-standard'         => '/https:\/\/api-3t.paypal.com/',
		'paypal-standard-sandbox' => '/https:\/\/api-3t.sandbox.paypal.com/',
		'stripe'                  => '/https:\/\/api.stripe.com/',
		'usps'                    => '/https:\/\/secure.shippingapis.com/',
		'woocommerce'             => '/https:\/\/woocommerce.com/',
		'connect-woo'             => '/https:\/\/connect.woocommerce.com/',
		'fb'                      => '/https:\/\/graph.facebook.com/',
		'gmaps'                   => '/https:\/\/maps.googleapis.com/',
		'square'                  => '/https:\/\/connect.squareup.com/',
		'auspost'                 => '/https:\/\/digitalapi.auspost.com.au/',
	);

	$replacements = array(
		'paypal-standard'         => 'https://https-api--3t-paypal-com-3.moesif.net/' . $moesif_id,
		'paypal-standard-sandbox' => 'https://https-api--3t-sandbox-paypal-com-3.moesif.net/' . $moesif_id,
		'stripe'                  => 'https://https-api-stripe-com-3.moesif.net/' . $moesif_id,
		'usps'                    => 'https://https-secure-shippingapis-com-3.moesif.net/' . $moesif_id,
		'woocommerce'             => 'https://https-woocommerce-com-3.moesif.net/' . $moesif_id,
		'connect-woo'             => 'https://https-connect-woocommerce-com-3.moesif.net/' . $moesif_id,
		'fb'                      => 'https://https-graph-facebook-com-3.moesif.net/' . $moesif_id,
		'gmaps'                   => 'https://https-maps-googleapis-com-3.moesif.net/' . $moesif_id,
		'square'                  => 'https://https-connect-squareup-com-3.moesif.net/' . $moesif_id,
		'auspost'                 => 'https://https-digitalapi-auspost-com-au-3.moesif.net/' . $moesif_id,
	);
	$replaced_url = preg_replace( $patterns, $replacements, $url );
	/**
	 * To avoid recursion, check if the replaced URL is the same as the URL that was passed. If not, re-route this request through Moesif and return the $response of that call as the return value.
	 */
	if ( $replaced_url !== $url ) {
		/**
		* Fires after an HTTP API response is received and before the response is returned.
		*
		* @since 2.8.0
		*
		* @param array|WP_Error $response    HTTP response or WP_Error object.
		* @param string         $context     Context under which the hook is fired.
		* @param string         $class       HTTP transport used.
		* @param array          $parsed_args HTTP request arguments.
		* @param string         $url         The request URL.
		*/
		add_action( 'http_api_debug', 'handsome_bearded_guy_http_api_debug', 10, 5 );
		return wp_safe_remote_request( $replaced_url, $args );
	}
	return $return_value;
}

/**
 * Function handsome_bearded_guy_http_api_debug
 *          Logs any WP_Error responses to any URLs that have been replaced to the WC_Logger
 *
 * @param array|WP_error $response HTTP response or WP_Error object.
 * @param string         $context     Context under which the hook is fired.
 * @param string         $class       HTTP transport used.
 * @param array          $parsed_args HTTP request arguments.
 * @param string         $url         The request URL.
 *
 * @return void
 */
function handsome_bearded_guy_http_api_debug( $response, $context, $class, $parsed_args, $url ) {
	if ( is_wp_error( $response ) && class_exists( 'WC_Logger' ) ) {
		$logger = new WC_Logger();
		$handle = 'http-response-is-wp-error';
		$logger->add( $handle, 'HTTP request failure for ' . $url . PHP_EOL );
		$logger->add( $handle, print_r( $response, true ) . PHP_EOL );
	}
}

add_action( 'admin_menu', 'handsome_bearded_guy_http_proxy_config' );

function handsome_bearded_guy_http_proxy_config() {
	add_submenu_page( 'tools.php', 'HTTP Proxy Configuration', 'HTTP Proxy Configuration', 'manage_woocommerce', 'http-proxy-config', 'handsome_bearded_guy_proxyconfig_view' );
}

function handsome_bearded_guy_proxyconfig_view() {
	include 'includes/admin/views/configuration.php';
}
