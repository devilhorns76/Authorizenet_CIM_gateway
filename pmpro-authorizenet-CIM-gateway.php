<?php
/*
Plugin Name: PMPro Authorize.net CIM Gateway
Plugin URI: http://www.internetfitnessincome.com
Description: Connects PMPro to Authorize.net CIM as a Gateway.  Allows customers to checkout using credit cards kept on file in Authorize.net's secure Customer Information Manager.
Version: 1.0
Author: Internet Fitness Income
Author URI: http://www.internetfitnessincome.com
*/

	// =============================================
	// Includes
	// =============================================
define("PMPRO_AUTHORIZENET_CIM_DIR", dirname(__FILE__));
require_once(PMPRO_AUTHORIZENET_CIM_DIR . "/classes/gateways/class.pmprogateway_authorizenet_CIM.php");
require_once(PMPRO_AUTHORIZENET_CIM_DIR . "/includes/functions.php");


	// =============================================
	// Setup Database Tables for CIM Stuff
	// =============================================
	//setup wpdb for the tables we need
global $ifi_pmpro_CIM_db_version;
$ifi_pmpro_CIM_db_version = '1.0';

if(is_admin())
	//ifi_pmpro_CIM_setDBTables();
	ifi_CIM_checkforupdates();

		// make sure database table names work
$wpdb->pmpro_ifi_subscriptions = $wpdb->prefix . 'pmpro_ifi_subscriptions';
$wpdb->pmpro_ifi_failed_subscriptions = $wpdb->prefix . 'pmpro_ifi_failed_subscriptions';
	

	// =============================================
	// - Don't show confirm password or email fields on the checkout page.
	// =============================================
add_filter("pmpro_checkout_confirm_password", "__return_false");
add_filter("pmpro_checkout_confirm_email", "__return_false");


	// =============================================
	// - ADD LEVEL NAME TO PAGE TITLE TAG
	// =============================================
function my_pmpro_membership_checkout_head_title($title, $sep)
{
	global $pmpro_pages;
	if(!empty($pmpro_pages) && is_page($pmpro_pages['checkout']))
	{
		global $pmpro_level;
		$title = $pmpro_level->name . " " . $sep . " " . $title;
	}
	return $title;
}
add_filter( 'wp_title', 'my_pmpro_membership_checkout_head_title', 20, 2 );


	// ===============================
	// Register Helper Global Options
	// ===============================
global $pmprorh_options;
$pmprorh_options["use_email_for_login"] = true;



	// =============================================
	// - LOAD CUSTOM CHECKOUT PAGE
	// =============================================
function ifi_CIM_pmpro_pages_shortcode_checkout($content)
{
	ob_start();
	include(plugin_dir_path(__FILE__) . "pages/checkout.php");
	$temp_content = ob_get_contents();
	ob_end_clean();
	return $temp_content;
}
add_filter("pmpro_pages_shortcode_checkout", "ifi_CIM_pmpro_pages_shortcode_checkout");


	// =============================================
	// - LOAD CUSTOM CONFIRMATION PAGE
	// =============================================
function ifi_CIM_pmpro_pages_shortcode_confirmation($content)
{
	ob_start();
	include(plugin_dir_path(__FILE__) . "pages/confirmation.php");
	$temp_content = ob_get_contents();
	ob_end_clean();
	return $temp_content;
}
add_filter("pmpro_pages_shortcode_confirmation", "ifi_CIM_pmpro_pages_shortcode_confirmation");


	// =============================================
	// - LOAD CUSTOM ACCOUNT PAGE
	// =============================================
function ifi_CIM_pmpro_pages_shortcode_account($content)
{
	ob_start();
	include(plugin_dir_path(__FILE__) . "pages/account.php");
	$temp_content = ob_get_contents();
	ob_end_clean();
	return $temp_content;
}
add_filter("pmpro_pages_shortcode_account", "ifi_CIM_pmpro_pages_shortcode_account");


	// =============================================
	// - LOAD CUSTOM BILLING PAGE
	// =============================================
function ifi_CIM_pmpro_pages_shortcode_billing($content)
{
	ob_start();
	include(plugin_dir_path(__FILE__) . "pages/billing.php");
	$temp_content = ob_get_contents();
	ob_end_clean();
	return $temp_content;
}
//add_filter("pmpro_pages_shortcode_billing", "ifi_CIM_pmpro_pages_shortcode_billing");
	
	
	// =============================================
	// - LOAD CUSTOM Invoice PAGE
	// =============================================
function ifi_CIM_pmpro_pages_shortcode_invoice($content)
{
	ob_start();
	include(plugin_dir_path(__FILE__) . "pages/invoice.php");
	$temp_content = ob_get_contents();
	ob_end_clean();
	return $temp_content;
}
add_filter("pmpro_pages_shortcode_invoice", "ifi_CIM_pmpro_pages_shortcode_invoice");
	
	
	// =============================================
	// - USE SHORTCODES IN TEXT WIDGETS
	// =============================================
//add_filter( 'widget_text', 'do_shortcode' );
add_filter('widget_text', 'php_text', 99);

function php_text($text) {
 if (strpos($text, '<' . '?') !== false) {
 ob_start();
 eval('?' . '>' . $text);
 $text = ob_get_contents();
 ob_end_clean();
 }
 return $text;
}

/**
 * Fields shown on edit user page
 *
 * @since 1.1
 */
 //show CIM_profile_id
 //show list of payment_profile_ids
 //show list of shipping_profile_ids
function ifi_CIM_profile_fields($user_id)
{
	global $wpdb;
	if(is_object($user_id))
		$user_id = $user_id->ID;

	if(!current_user_can("administrator"))
		return false;
	?>
	<h3><?php _e("Authorize.net CIM Info", "pmproifi"); ?></h3>
	<table class="form-table">
		<?php
		$user_CIM_profile_id = get_user_meta($user_id, "CIM_profile_id", true);
		?>
	<tr>
		<th>CIM Profile Id</th>
		<td>
			<p><?php echo $user_CIM_profile_id ?></p>
		</td>
	</tr>
		<?php
		$user_payment_profile_ids = get_user_meta($user_id, "ifi_payment_profile_ids", true);
		$images_path = plugins_url( 'pmpro-CIM-gateway/images/' , dirname(__FILE__) );
		?>
	<tr>
		<th>Payment Profile Ids</th>
		<td>
			<?php
			foreach ($user_payment_profile_ids as $user_payment_profile_id)
				{
				foreach($user_payment_profile_id as $key=>$value)
					{
					$$key = $value;
					}
				?>
				<p><?php echo $payment_profile_id ?></p>
				<img class="ifi_card_sm" src="<?php echo $images_path . $cardtype . '_sm.png' ?>" width="80px" height="50px" alt="<?php echo $cardtype ?>">&nbsp;&nbsp;
				<?php echo $cardtype ?>&nbsp;<?php echo $accountnumber ?>&nbsp;expires:&nbsp;<?php echo $expiration_month . " /" ?>&nbsp;<?php echo $expiration_year ?>
				<p>&nbsp;</p>
				<?php
				}
			?>
		</td>
	</tr>
		<?php
		$user_shipping_profile_ids = get_user_meta($user_id, "ifi_shipping_profile_ids", true);
		?>
	<tr>
		<th>Shipping Profile Ids</th>
		<td>
			<ul>
			<?php
			foreach ($user_shipping_profile_ids as $user_shipping_profile_id)
				{
				foreach($user_shipping_profile_id as $key=>$value)
					{
					$$key = $value;
					}
				?>
					<li><?php echo $shipping_profile_id ?></li>
				<?php
				}
			?>
			</ul>
		</td>
	</tr>
	</table>

	<?php
}
add_action( 'show_user_profile', 'ifi_CIM_profile_fields' );
add_action( 'edit_user_profile', 'ifi_CIM_profile_fields' );

	
// ===========================================
// try to replace the preheaders with custom preheaders
// =============================================

//this code runs after $post is set, but before template output
//this code runs after $post is set, but before template output
function ifi_CIM_pmpro_wp()
{
	if(!is_admin())
	{
		global $post, $pmpro_pages, $pmpro_page_name, $pmpro_page_id, $pmpro_body_classes;		
		
		//no pages yet?
		if(empty($pmpro_pages))
			return;
		
		//run the appropriate preheader function
		foreach($pmpro_pages as $pmpro_page_name => $pmpro_page_id)
		{						
			if(!empty($post->post_content) && strpos($post->post_content, "[pmpro_" . $pmpro_page_name . "]") !== false)
			{
				//preheader (plugin_dir_path(__FILE__) . "templates/confirmation.php");
				require_once(plugin_dir_path(__FILE__) . "preheaders/" . $pmpro_page_name . ".php");
				
				//add class to body
				$pmpro_body_classes[] = "pmpro-" . str_replace("_", "-", $pmpro_page_name);
								
				//shortcode
				function ifi_pmpro_pages_shortcode($atts, $content=null, $code="")
				{
					global $pmpro_page_name;
					ob_start();
					if(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/pages/" . $pmpro_page_name . ".php"))
						include(get_stylesheet_directory() . "/paid-memberships-pro/pages/" . $pmpro_page_name . ".php");
					else
						include(PMPRO_DIR . "/pages/" . $pmpro_page_name . ".php");
					
					$temp_content = ob_get_contents();
					ob_end_clean();
					return apply_filters("ifi_pmpro_pages_shortcode_" . $pmpro_page_name, $temp_content);
				}
				add_shortcode("pmpro_" . $pmpro_page_name, "pmpro_pages_shortcode");
				break;	//only the first page found gets a shortcode replacement
			}
		}				
	}
}
add_action("wp", "ifi_CIM_pmpro_wp", 11);
	
	
	// =============================================
	// Add CIM profile stuff to user_meta after successful order completes
	// =============================================
function ifi_pmpro_add_CIM_meta_after_order($order)
{
	global $pmpro_pages;
		
		// Make sure this is a checkout page AND it has a lid
	if(is_page($pmpro_pages['checkout']) &&	!empty($_REQUEST['lid']))
	{		
		global $wpdb, $current_user;
			// get customer profile id
		$CIM_profile_id = $order->CIM_profile_id;
			// get payment_profile_id
		$payment_profile_id = $order->payment_profile_id;
			// get shipping_profile_id
		$shipping_profile_id = $order->shipping_profile_id;
		$user_id = $current_user->ID;
		$last4 = formatCreditCard(maskCreditCard($order->accountnumber));
		
					//update user_meta for CIM_profile_id
					update_user_meta($user_id, "CIM_profile_id", $CIM_profile_id);
					
					//update user_meta for payment_profile_ids array
					//if($morder->accountnumber != "0000000000000000")
					//{
					$ifi_payment_profile_ids = get_user_meta($user_id, "ifi_payment_profile_ids", true);
					if(is_array($ifi_payment_profile_ids))
						{
							if(!in_array($payment_profile_id, $ifi_payment_profile_ids) && strpos($order->accountnumber, 'X') === false) 
							{
								$address = $order->Address1;
								if(!empty($order->Address2))
									$address .= "\n" . $order->Address2;							
								$ifi_payment_profile_ids[] = array("payment_profile_id" => $payment_profile_id, "cardtype" => $order->cardtype, "accountnumber" => $last4, "expiration_month" => $order->expirationmonth, "expiration_year" => $order->expirationyear, "expiration_YdashM" => $order->ExpirationDate_YdashM, "street1" => $order->Address1, "street2" => $order->Address2, "city" => $order->billing->city, "state" =>$order->billing->state, "zip" => $order->billing->zip, "country" =>$order->billing->country, "phone" => $order->billing->phone);
							}
						}
					else
						{
							$ifi_payment_profile_ids = array(array("payment_profile_id" => $payment_profile_id, "cardtype" => $order->cardtype, "accountnumber" => $last4, "expiration_month" => $order->expirationmonth, "expiration_year" => $order->expirationyear, "expiration_YdashM" => $order->ExpirationDate_YdashM, "street1" => $order->Address1, "street2" => $order->Address2, "city" => $order->billing->city, "state" =>$order->billing->state, "zip" => $order->billing->zip, "country" =>$order->billing->country, "phone" => $order->billing->phone));
						}	
					update_user_meta($user_id, "ifi_payment_profile_ids", $ifi_payment_profile_ids);
					//}
					//update user_meta for shipping_profile_ids array if present
					if(!empty($shipping_profile_id))
					{
						$ifi_shipping_profile_ids = get_user_meta($user_id, "ifi_shipping_profile_ids", true);
						if(is_array($ifi_shipping_profile_ids))
							{
								if(!in_array($shipping_profile_id, $ifi_shipping_profile_ids))
								{
									$ifi_shipping_profile_ids[] = array("shipping_profile_id" => $shipping_profile_id, "shipping_firstname" => $order->shipping->firstName, "shipping_lastname" => $order->shipping->lastName, "shipping_address_1" => $order->shipping->Address1, "shipping_address_2" => $order->shipping->Address2, "shipping_city" => $order->shipping->city, "shipping_state" => $order->shipping->state, "shipping_zip" => $order->shipping->zip, "shipping_country" => $order->shipping->country);
								}
							}
						else
							{
								$ifi_shipping_profile_ids = array(array("shipping_profile_id" => $shipping_profile_id, "shipping_firstname" => $order->shipping->firstName, "shipping_lastname" => $order->shipping->lastName, "shipping_address_1" => $order->shipping->Address1, "shipping_address_2" => $order->shipping->Address2, "shipping_city" => $order->shipping->city, "shipping_state" => $order->shipping->state, "shipping_zip" => $order->shipping->zip, "shipping_country" => $order->shipping->country));
							}	
						update_user_meta($user_id, "ifi_shipping_profile_ids", $ifi_shipping_profile_ids);
					}
					
					//run ifi_update_CIM_profile() to add customer_id to CIM Customer Profile
					if(!empty($order->CIM_refid))
						{
						$CIM_update = pmprogateway_authorizenet_CIM::ifi_update_CIM_profile($user_id, $order);
						}

					//update card expiration date
					if(is_array($ifi_payment_profile_ids))
						{
						foreach($ifi_payment_profile_ids as $ifi_payment_method)
							{
							if(!in_array($payment_profile_id, $ifi_payment_method))
								{
								foreach($ifi_payment_method as $key=>$value)
									{
									$$key = $value;
									}
								global $wpdb, $current_user;
								$order->expriationmonth = $expiration_month;
								$sqlQuery = "UPDATE $wpdb->pmpro_membership_orders SET expirationmonth = '" . esc_sql($order->expirationmonth) . "' WHERE id = '" . $order->id . "' LIMIT 1";
								$wpdb->query($sqlQuery);
								$order->expriationyear = $expiration_year;
								$sqlQuery2 = "UPDATE $wpdb->pmpro_membership_orders SET expirationyear = '" . esc_sql($order->expirationyear) . "' WHERE id = '" . $order->id . "' LIMIT 1";
								$wpdb->query($sqlQuery2);								
								}
							}
						}
		//update subscription in db
		$sqlQuery3 = "UPDATE $wpdb->pmpro_ifi_subscriptions SET user_id = '$order->user_id' WHERE subscription_transaction_id = '$order->subscription_transaction_id'";
		$wpdb->query($sqlQuery3);
	}

	return $order;
}
add_filter('pmpro_added_order', 'ifi_pmpro_add_CIM_meta_after_order');
	
	
	// =============================================
	// Mask Credit Card Numbers
	// =============================================
/**
 * Replaces all but the last for digits with x's in the given credit card number
 * @param int|string $cc The credit card number to mask
 * @return string The masked credit card number
 */
function MaskCreditCard($cc){
	// Get the cc Length
	$cc_length = strlen($cc);
	// Replace all characters of credit card except the last four and dashes
	for($i=0; $i<$cc_length-4; $i++){
		if($cc[$i] == '-'){continue;}
		$cc[$i] = 'X';
	}
	// Return the masked Credit Card #
	return $cc;
}
/**
 * Add dashes to a credit card number.
 * @param int|string $cc The credit card number to format with dashes.
 * @return string The credit card with dashes.
 */
function FormatCreditCard($cc)
{
	// Clean out extra data that might be in the cc
	$cc = str_replace(array('-',' '),'',$cc);
	// Get the CC Length
	$cc_length = strlen($cc);
	// Initialize the new credit card to contian the last four digits
	$newCreditCard = substr($cc,-4);
	// Walk backwards through the credit card number and add a dash after every fourth digit
	for($i=$cc_length-5;$i>=0;$i--){
		// If on the fourth character add a dash
		if((($i+1)-$cc_length)%4 == 0){
			$newCreditCard = '-'.$newCreditCard;
		}
		// Add the current character to the new credit card
		$newCreditCard = $cc[$i].$newCreditCard;
	}
	// Return the formatted credit card number
	return $newCreditCard;
}
	
	
	// =============================================
	// Remove confirm password and email from checkout page
	// =============================================
add_filter("pmpro_checkout_confirm_password", "__return_false");
add_filter("pmpro_checkout_confirm_email", "__return_false");

// wpautop off
add_filter('the_content', 'specific_no_wpautop', 9);
function specific_no_wpautop($content) {
    if (is_page_template('account.php')) { // or whatever other condition you like
        remove_filter( 'the_content', 'wpautop' );
        return $content;
    } else {
        return $content;
    }
}
