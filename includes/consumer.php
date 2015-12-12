<?php

/**
 * WDS WP REST API Connect UI -- Create Consumer
 * @version 0.1.0
 * @package WDS WP REST API Connect UI
 * @author    Josh Pollock <Josh@JoshPress.net>
 */

class WDSRESTCUI_Consumer {

	private $user;

	private $consumer_id;

	private $consumer;

	public function __construct( $username = null, $id = null ) {
		if ( class_exists( 'WP_REST_OAuth1' ) ) {
			if( is_numeric( $id ) ){
				$user = get_user_by( 'id', $id );
			}else{
				$user = get_user_by( 'login', $username );
			}

			if ( is_object( $user ) ) {
				$this->user = $user;
				$this->get_or_create_consumer();
			}

		}

	}

	public function get_consumer(){
		return $this->consumer;
	}

	private function consumer_exists() {
		if ( 0 < absint( get_user_meta( $this->user->ID, '_oauth_consumer_id' ) ) ) {
			return true;
		}
	}

	private function get_or_create_consumer() {
		if ( $this->consumer_exists( $this->user->ID ) ) {
			$this->consumer_id = get_user_meta( $this->user->ID, '_oauth_consumer_id', true );
			$this->consumer = get_post_meta( $this->consumer_id );
			return $this->consumer_id;
		}

		$type          = 'oauth1';
		$authenticator = new WP_REST_OAuth1( $type );

		$params[ 'name' ] = $this->user->login;
		$_consumer  = $authenticator->add_consumer( $params );
		$this->consumer = get_post_meta( $_consumer->ID );
		if ( is_a( $_consumer ,  'WP_Post' ) ) {
			update_user_meta( $this->user->ID, '_oauth_consumer_id', $_consumer>ID );
			$this->consumer_id = $_consumer->ID;
			return $this->consumer_id;
		}

	}
}
