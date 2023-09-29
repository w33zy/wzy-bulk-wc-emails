<?php

/**
 * Plugin Name:     wzy Bulk WooCommerce Emails
 * Plugin URI:      https://wzymedia.com
 * Description:     Bulk send WooCommerce order status emails from the WooCommerce orders page.
 * Author:          w33zy
 * Author URI:      https://wzymedia.com
 * Text Domain:     wzy-media
 * Version:         1.0.0
 *
 * @package         wzy_Bulk_WC_Emails
 */
class wzy_Bulk_WC_Emails {

	public static array $wc_emails = [
		'send_new_order_email'               => 'WC_Email_New_Order',
		'send_customer_cancelled_email'      => 'WC_Email_Cancelled_Order',
		'send_customer_failed_email'         => 'WC_Email_Failed_Order',
		'send_customer_on_hold_email'        => 'WC_Email_Customer_On_Hold_Order',
		'send_processing_order_email'        => 'WC_Email_Customer_Processing_Order',
		'send_completed_order_email'         => 'WC_Email_Customer_Completed_Order',
		'send_customer_refund_email'         => 'WC_Email_Customer_Refunded_Order',
		'send_customer_invoice_email'        => 'WC_Email_Customer_Invoice',
		'send_customer_note_email'           => 'WC_Email_Customer_Note',
		'send_customer_reset_password_email' => 'WC_Email_Customer_Reset_Password',
		'send_customer_new_account_email'    => 'WC_Email_Customer_New_Account',
  ];

	public static function start(): void {
		static $started = false;

		if ( ! $started ) {
			self::add_filters();
			self::add_actions();

			$started = true;
		}
	}

	public static function add_filters(): void {
		add_filter( 'bulk_actions-edit-shop_order', [ __CLASS__, 'add_send_email_action' ] );
	}

	public static function add_actions(): void {
		add_action( 'handle_bulk_actions-edit-shop_order', [ __CLASS__, 'process_bulk_send_email_action' ], 10, 3 );
	}

	public static function add_send_email_action( array $actions ): array {
		$actions['send_new_order_email']          = __( 'Send New Order Email', 'wzy-media' );
		$actions['send_customer_invoice_email']   = __( 'Send Customer Invoice Email', 'wzy-media' );
		$actions['send_processing_order_email']   = __( 'Send Processing Order Email', 'wzy-media' );
		$actions['send_completed_order_email']    = __( 'Send Completed Order Email', 'wzy-media' );
		$actions['send_customer_refund_email']    = __( 'Send Customer Refund Email', 'wzy-media' );
		$actions['send_customer_failed_email']    = __( 'Send Customer Failed Email', 'wzy-media' );
		$actions['send_customer_cancelled_email'] = __( 'Send Customer Cancelled Email', 'wzy-media' );
		$actions['send_customer_note_email']      = __( 'Send Customer Note Email', 'wzy-media' );

		return $actions;
	}

	/**
	 * @param  string  $send_back  The redirect URL.
	 * @param  string  $do_action  The action being taken.
	 * @param  array   $items     The items to take the action on. Accepts an array of IDs of posts,
	 *                            comments, terms, links, plugins, attachments, or users.
	 *
	 * @return void
	 */
	public static function process_bulk_send_email_action( string $send_back, string $do_action, array $items ): void {

		foreach ( $items as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order instanceof \WC_Order ) {
				WC()->mailer()->emails[ self::$wc_emails[ $do_action ] ]->trigger( $order_id, $order );
			}
		}

		wp_redirect( $send_back );
	}

}

wzy_Bulk_WC_Emails::start();
