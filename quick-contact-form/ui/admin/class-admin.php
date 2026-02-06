<?php
/**
 * @copyright (c) 2020.
 * @author            Alan Fuller (support@fullworks)
 * @licence           GPL V3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link                  https://fullworks.net
 *
 * This file is part of  a Fullworks plugin.
 *
 *   This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with  this plugin.  https://www.gnu.org/licenses/gpl-3.0.en.html
 */

namespace Quick_Contact_Form\UI\Admin;

class Admin {

	private $plugin_name;
	private $version;
	/**
	 * @param \Freemius $freemius Object for freemius.
	 */
	private $freemius;

	public function __construct( $plugin_name, $version, $freemius ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->freemius = $freemius;
	}

	public function hooks() {
		// @TODO Add in when needed for non legacy
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice_freemius' ) );
		
		// Add Post SMTP admin notice
		add_action( 'admin_notices', array( $this, 'post_smtp_admin_notice' ) );
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
	}

	public function admin_notice_freemius() {
		
		// Don't display notices to users that can't do anything about it.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		// Notices are only displayed on the dashboard, plugins, tools, and settings admin pages.
		$page             = get_current_screen()->base;
		$display_on_pages = array(
			'dashboard',
			'plugins',
			'tools',
			'options-general',
		);
		$display          = false;
		if ( preg_match( '#quick-contact-form#i', $page ) ) {
			$display = true;
		}
		if ( in_array( $page, $display_on_pages, true ) ) {
			$display = true;
		}
		if ( ! $display ) {
			return;
		}
        $notice='';
		// Output notice HTML.
		if ( ! empty( $notice ) ) {
			printf( '<div id="message" class="notice notice-warning" style="overflow:hidden;font-size: 150%;"><p>%1$s</p></div>', wp_kses_post($notice) );
		}

	}
	
	public function post_smtp_admin_notice() {
		// Don't display notices to users that can't do anything about it.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		
		// Check if Post SMTP plugin is active
		if( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		
		$is_post_smtp_active = is_plugin_active( 'post-smtp/postman-smtp.php' );
		
		// Don't show notice if Post SMTP is active
		if( $is_post_smtp_active ) {
			return;
		}
		
		// Check if global notice is hidden
		$is_notice_hidden = get_option( 'post_smtp_global_recommendation_notice_hidden', false );
		if( $is_notice_hidden ) {
			return;
		}
		
		// Include and initialize Post SMTP admin notice  
		$notice_file = trailingslashit( QUICK_CONTACT_FORM_PLUGIN_DIR ) . 'control/post-smtp-notice/recommend-post-smtp-admin-notice.php';
		if( file_exists( $notice_file ) && ! class_exists( 'Recommend_Post_SMTP_Admin_Notice' ) ) {
			require_once( $notice_file );
			$notice = \Recommend_Post_SMTP_Admin_Notice::get_instance();
			$notice->set_plugin_info( 'quick-contact-form', 'png' );
			$notice->admin_head();
			$notice->admin_enqueue_scripts();
		}
	}

}
