<?php

	// =========
	// setup database tables
	// =========
function ifi_CIM_checkforupdates()
{
//ifi_pmpro_CIM_db_version
global $ifi_pmpro_CIM_db_version;
if($ifi_pmpro_CIM_db_version < 2.0)
ifi_pmpro_CIM_setDBTables();
}


function ifi_pmpro_CIM_setDBTables()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	global $wpdb;
	global $ifi_pmpro_CIM_db_version;

	//$wpdb->hide_errors();
	$wpdb->show_errors();
	$wpdb->pmpro_ifi_subscriptions = $wpdb->prefix . 'pmpro_ifi_subscriptions';
	$wpdb->pmpro_ifi_failed_subscriptions = $wpdb->prefix . 'pmpro_ifi_failed_subscriptions';
	
	//$charset_collate = $wpdb->get_charset_collate();
	//wp_pmpro_ifi_subscriptions
	$sqlQuery = "
		CREATE TABLE " . $wpdb->pmpro_ifi_subscriptions . " (
		  id int(11) unsigned NOT NULL AUTO_INCREMENT,
		  code varchar(10) NOT NULL,
		  user_id int(11) unsigned NOT NULL DEFAULT '0',
		  membership_id int(11) unsigned NOT NULL DEFAULT '0',
		  membership_name varchar(128) NOT NULL DEFAULT '',
		  type enum('subscription', 'payment plan') NOT NULL DEFAULT 'subscription',
		  cim_profile_id int(11) unsigned NOT NULL DEFAULT '0',
		  payment_profile_id int(11) unsigned NOT NULL DEFAULT '0',
		  shipping_profile_id int(11) unsigned NOT NULL DEFAULT '0',
		  billing_name varchar(128) NOT NULL DEFAULT '',
		  billing_street varchar(128) NOT NULL DEFAULT '',
		  billing_city varchar(128) NOT NULL DEFAULT '',
		  billing_state varchar(32) NOT NULL DEFAULT '',
		  billing_zip varchar(16) NOT NULL DEFAULT '',
		  billing_country varchar(128) NOT NULL,
		  billing_phone varchar(32) NOT NULL,
		  billing_amount decimal(10,2) NOT NULL,
		  cycle_number int(11) NOT NULL,
		  cycle_period enum('days','weeks','months','years') NOT NULL DEFAULT 'months',
		  billing_limit int(11) NOT NULL,
		  trial_amount decimal(10,2) NOT NULL,
		  trial_limit int(11) NOT NULL,
		  status enum('active', 'cancelled', 'cancelled by admin', 'suspended', 'rebill', 'paused by customer', 'pending approval') NOT NULL DEFAULT 'active',
		  couponamount varchar(16) NOT NULL DEFAULT '',
		  certificate_id int(11) NOT NULL DEFAULT '0',
		  certificateamount varchar(16) NOT NULL DEFAULT '',
		  cardtype varchar(32) NOT NULL DEFAULT '',
		  accountnumber varchar(32) NOT NULL DEFAULT '',
		  expirationmonth int(2) NOT NULL DEFAULT '00',
		  expirationyear int(4) NOT NULL DEFAULT '0000',
		  payment_transaction_id varchar(32) NOT NULL,
		  subscription_transaction_id varchar(32) NOT NULL,
		  sub_created varchar(10) NOT NULL DEFAULT '0000-00-00',
		  next_billingdate datetime NOT NULL,
		  last_successful_billingdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  successful_billings int(11) unsigned NOT NULL DEFAULT '0',
		  failed_billings int(11) unsigned NOT NULL DEFAULT '0',
		  affiliate_id varchar(32) NOT NULL,
		  affiliate_subid varchar(32) NOT NULL,
		  notes TEXT NOT NULL,
		  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  UNIQUE KEY code (code),
		  KEY user_id (user_id),
		  KEY membership_id (membership_id),
		  KEY type (type),
		  KEY cim_profile_id (cim_profile_id),
		  KEY payment_profile_id (payment_profile_id),
		  KEY shipping_profile_id (shipping_profile_id),
		  KEY status (status),
		  KEY sub_created (sub_created),
		  KEY next_billingdate (next_billingdate),
		  KEY last_successful_billingdate (last_successful_billingdate),
		  KEY subscription_transaction_id (subscription_transaction_id),
		  KEY affiliate_id (affiliate_id),
		  KEY affiliate_subid (affiliate_subid),
		  KEY timestamp (timestamp),
		  KEY modified (modified)
		);	
	";
	dbDelta($sqlQuery);	
	
	//wp_pmpro_ifi_failed_subscriptions
	$sqlQuery = "
		CREATE TABLE " . $wpdb->pmpro_ifi_failed_subscriptions . " (
		  id int(11) unsigned NOT NULL AUTO_INCREMENT,
		  code varchar(10) NOT NULL,
		  user_id int(11) unsigned NOT NULL DEFAULT '0',
		  membership_id int(11) unsigned NOT NULL DEFAULT '0',
		  membership_name varchar(128) NOT NULL DEFAULT '',
		  type enum('subscription', 'payment plan') NOT NULL DEFAULT 'subscription',
		  cim_profile_id int(11) unsigned NOT NULL DEFAULT '0',
		  payment_profile_id int(11) unsigned NOT NULL DEFAULT '0',
		  shipping_profile_id int(11) unsigned NOT NULL DEFAULT '0',
		  billing_name varchar(128) NOT NULL DEFAULT '',
		  billing_street varchar(128) NOT NULL DEFAULT '',
		  billing_city varchar(128) NOT NULL DEFAULT '',
		  billing_state varchar(32) NOT NULL DEFAULT '',
		  billing_zip varchar(16) NOT NULL DEFAULT '',
		  billing_country varchar(128) NOT NULL,
		  billing_phone varchar(32) NOT NULL,
		  billing_amount decimal(10,2) NOT NULL,
		  cycle_number int(11) NOT NULL,
		  cycle_period enum('days','weeks','months','years') NOT NULL DEFAULT 'months',
		  billing_limit int(11) NOT NULL,
		  trial_amount decimal(10,2) NOT NULL,
		  trial_limit int(11) NOT NULL,
		  status enum('active', 'paid', 'failed') NOT NULL DEFAULT 'active',
		  couponamount varchar(16) NOT NULL DEFAULT '',
		  certificate_id int(11) NOT NULL DEFAULT '0',
		  certificateamount varchar(16) NOT NULL DEFAULT '',
		  cardtype varchar(32) NOT NULL DEFAULT '',
		  accountnumber varchar(32) NOT NULL DEFAULT '',
		  expirationmonth int(2) NOT NULL DEFAULT '00',
		  expirationyear int(4) NOT NULL DEFAULT '0000',
		  subscription_transaction_id varchar(32) NOT NULL,
		  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  next_billingdate datetime NOT NULL,
		  affiliate_id varchar(32) NOT NULL,
		  affiliate_subid varchar(32) NOT NULL,
		  notes TEXT NOT NULL,
		  modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  UNIQUE KEY code (code),
		  KEY user_id (user_id),
		  KEY membership_id (membership_id),
		  KEY cim_profile_id (cim_profile_id),
		  KEY payment_profile_id (payment_profile_id),
		  KEY shipping_profile_id (shipping_profile_id),
		  KEY status (status),
		  KEY timestamp (timestamp),
		  KEY next_billingdate (next_billingdate),
		  KEY subscription_transaction_id (subscription_transaction_id),
		  KEY affiliate_id (affiliate_id),
		  KEY affiliate_subid (affiliate_subid)
		);
	";
	dbDelta($sqlQuery);	
	
	add_option( 'ifi_pmpro_CIM_db_version', $ifi_pmpro_CIM_db_version );
}
