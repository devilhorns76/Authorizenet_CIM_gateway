<?php
	//load classes init method
	add_action('init', array('PMProGateway_authorizenet_CIM', 'init'));
	
	class PMProGateway_authorizenet_CIM extends PMProGateway
	{
		function PMProGateway_authorizenet_CIM($gateway = NULL)
		{
			$this->gateway = $gateway;
			$this->gateway = pmpro_getOption("gateway_environment");
			return $this->gateway;
		}										
		/**
		 * Run on WP init
		 *		 
		 * @since 1.0
		 */
		static function init()
		{			
			//make sure Authorize.net CIM is a gateway option
			add_filter('pmpro_gateways', array('PMProGateway_authorizenet_CIM', 'pmpro_gateways'));
			
			//add fields to payment settings
			add_filter('pmpro_payment_options', array('PMProGateway_authorizenet_CIM', 'pmpro_payment_options'));
			add_filter('pmpro_payment_option_fields', array('PMProGateway_authorizenet_CIM', 'pmpro_payment_option_fields'), 10, 2);	

			//add some fields to edit user page (CIM Information)
			//add_action( 'show_user_profile', array('PMProGateway_authorizenet_CIM', 'ifi_CIM_profile_fields' ));
			//add_action( 'edit_user_profile', array('PMProGateway_authorizenet_CIM', 'ifi_CIM_profile_fields' ));
			//add_action('pmpro_after_membership_level_profile_fields', array('PMProGateway_stripe', 'user_profile_fields'));
			//add_action('profile_update', array('PMProGateway_stripe', 'user_profile_fields_save'));

			//old global RE showing billing address or not
			//global $pmpro_stripe_lite;
			//$pmpro_stripe_lite = apply_filters("pmpro_stripe_lite", !pmpro_getOption("stripe_billingaddress"));	//default is oposite of the stripe_billingaddress setting

			//updates cron
			//add_action('pmpro_activation', array('PMProGateway_stripe', 'pmpro_activation'));
			//add_action('pmpro_deactivation', array('PMProGateway_stripe', 'pmpro_deactivation'));
			//add_action('pmpro_cron_stripe_subscription_updates', array('PMProGateway_stripe', 'pmpro_cron_stripe_subscription_updates'));

			//code to add at checkout if Authorize.net CIM is the current gateway
			$gateway = pmpro_getOption("gateway");
			if($gateway == "authorizenet_CIM")
			{
				add_action('pmpro_checkout_preheader', array('PMProGateway_authorizenet_CIM', 'pmpro_checkout_preheader'));
				add_filter('pmpro_checkout_order', array('PMProGateway_authorizenet_CIM', 'pmpro_checkout_order'));
				//add_action('pmpro_after_checkout', array('PMProGateway_authorizenet_CIM', 'pmpro_after_checkout'));
				//add_filter('pmpro_include_billing_address_fields', array('PMProGateway_authorizenet_CIM', 'pmpro_include_billing_address_fields'));
				//add_filter('ifi_pmpro_CIM_required_billing_fields', array('PMProGateway_authorizenet_CIM', 'ifi_pmpro_CIM_required_billing_fields'));
				//add_filter('pmpro_include_cardtype_field', array('PMProGateway_authorizenet_CIM', 'pmpro_include_billing_address_fields'));
				//add_filter('pmpro_include_payment_information_fields', array('PMProGateway_authorizenet_CIM', 'pmpro_include_payment_information_fields'));
			}
			
		}
		
		/**
		 * Make sure this gateway is in the gateways list
		 *		 
		 * @since 1.0
		 */
		static function pmpro_gateways($gateways)
		{
			if(empty($gateways['authorizenet_CIM']))
				$gateways['authorizenet_CIM'] = __('Authorize.net CIM', 'pmpro');
		
			return $gateways;
		}
		
		/**
		 * Get a list of payment options that the this gateway needs/supports.
		 *		 
		 * @since 1.0
		 */
		static function getGatewayOptions()
		{			
			$options = array(
				'sslseal',
				'nuclear_HTTPS',
				'gateway_environment',
				'loginname',
				'transactionkey',
				'currency',
				'use_ssl',
				'tax_state',
				'tax_rate',
				'accepted_credit_cards'
			);
			
			return $options;
		}
		
		/**
		 * Set payment options for payment settings page.
		 *		 
		 * @since 1.0
		 */
		static function pmpro_payment_options($options)
		{			
			//get Authorize.net CIM options
			$authorizenet_CIM_options = PMProGateway_authorizenet_CIM::getGatewayOptions();
			
			//merge with others.
			$options = array_merge($authorizenet_CIM_options, $options);
			
			return $options;
		}
		
		/**
		 * Display fields for this gateway's options.
		 *		 
		 * @since 1.0
		 */
		static function pmpro_payment_option_fields($values, $gateway)
		{
		?>
		<tr class="pmpro_settings_divider gateway gateway_authorizenet_CIM" <?php if($gateway != "authorizenet_CIM") { ?>style="display: none;"<?php } ?>>
			<td colspan="2">
				<?php _e('Authorize.net CIM Settings', 'pmpro'); ?>
			</td>
		</tr>
		<tr class="gateway gateway_authorizenet_CIM" <?php if($gateway != "authorizenet_CIM") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="loginname"><?php _e('Login Name', 'pmpro');?>:</label>
			</th>
			<td>
				<input type="text" id="loginname" name="loginname" size="60" value="<?php echo esc_attr($values['loginname'])?>" />
			</td>
		</tr>
		<tr class="gateway gateway_authorizenet_CIM" <?php if($gateway != "authorizenet_CIM") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="transactionkey"><?php _e('Transaction Key', 'pmpro');?>:</label>
			</th>
			<td>
				<input type="text" id="transactionkey" name="transactionkey" size="60" value="<?php echo esc_attr($values['transactionkey'])?>" />
			</td>
		</tr>
		<tr class="gateway gateway_authorizenet_CIM" <?php if($gateway != "authorizenet_CIM") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label><?php _e('Silent Post URL', 'pmpro');?>:</label>
			</th>
			<td>
				<p><?php _e('To fully integrate with Authorize.net CIM, be sure to set your Silent Post URL to', 'pmpro');?> <pre><?php echo admin_url("admin-ajax.php") . "?action=authnet_silent_post";?></pre></p>
			</td>
		</tr>
		<?php
		}
		
		/**
		 * Code added to checkout preheader.
		 *
		 * @since 1.0
		 */
		// see Adding a Gateway Notes in onenote
		static function pmpro_checkout_preheader()
		{
		?>
		<script type="text/javascript" src="https://code.jquery.com/jquery.js"></script>
		<?php
		} 
		
	
		
		/**
		 * Filtering orders at checkout.
		 *
		 * @since 1.0
		 */
		static function pmpro_checkout_order($morder)
		{
			//echo "<br>" . "=== pmpro checkout order var dump below ===" . "<br>";
			//var_dump($morder);
			//echo "<br>" . "=== pmpro checkout order var dump above ===" . "<br>";
			//die;
			//if(isset($_SESSION['CIM_profile_id']))
			//	$morder->CIM_profile_id = $_SESSION['CIM_profile_id'];
			if(isset($_REQUEST['CIM_profile_id']))
				$morder->CIM_profile_id = $_REQUEST['CIM_profile_id'];
			else
				$morder->CIM_profile_id = "";
			//var_dump($_REQUEST['CIM_profile_id']);
			
			//if(isset($_SESSION['payment_profile_id']))
			//	$morder->payment_profile_id = $_SESSION['payment_profile_id'];
			if(isset($_REQUEST['payment_profile_id']))
				$morder->payment_profile_id = $_REQUEST['payment_profile_id'];
			else
				$morder->payment_profile_id = "";
			//var_dump($payment_profile_id);
			
			if(isset($_SESSION['shipping_profile_id']))
				$morder->shipping_profile_id = $_SESSION['shipping_profile_id'];
			elseif(isset($_REQUEST['shipping_profile_id']))
				$morder->shipping_profile_id = $_REQUEST['shipping_profile_id'];
			else
				$morder->shipping_profile_id = "";
			//var_dump($shipping_profile_id);

			//echo "from pmpro_checkout_order function in my gateway class" . "<br>";
			//var_dump($_SESSION['CIM_profile_id']);
			//var_dump($morder->CIM_profile_id);
			//var_dump($_SESSION['chargeerror']);
			//var_dump($_SESSION['payment_profile_id']);
			//var_dump($morder->payment_profile_id);
			
			// if we have a CIM_profile_id, a payment_profile_id, and NO accountnumber... we're billing existing customer using on-file card
			if(!empty($morder->CIM_profile_id) && !empty($morder->payment_profile_id) && $morder->accountnumber == "0000000000000000")
				{
				//look up the payment_profile_id info in user_meta so we can use right card info in order
				global $current_user;
				$ifi_payment_methods = get_user_meta($current_user->ID, "ifi_payment_profile_ids", true);
				//var_dump($ifi_payment_methods);
				foreach($ifi_payment_methods as $ifi_payment_method)
				{
				//echo "=====";
				//var_dump($ifi_payment_method);
				//echo "=====";
					if(in_array($morder->payment_profile_id, $ifi_payment_method))
					{
					foreach($ifi_payment_method as $key=>$value)
						{
						$$key = $value;
						}
					$morder->accountnumber = $accountnumber;
					$morder->expirationmonth = $expiration_month;
					$morder->expirationyear = $expiration_year;
					$morder->ExpirationDate_YdashM = $expiration_YdashM;
					$morder->cardtype = $cardtype;
					}
				}
					//var_dump($morder);
					//die;
				return $morder; // okay to send to process
				}
				
			// if we have accountnumber AND a CIM_profile_id, but no payment_profile_id...
			if(!empty($morder->accountnumber) && !empty($morder->CIM_profile_id) && empty($morder->payment_profile_id)) // we're adding a new payment profile to existing customer and charging it
				{
				if(pmprogateway_authorizenet_CIM::ifi_create_payment_profile($morder)) // run that function and if positive, do this...
					{
					return $morder;
					//return $morder; // it worked - payment profile added to order // maybe we have to return true instead
					}
				else // if negative, do this
					{
					return false; // let it know it failed to create payment profile
					}
				}
			// if we have an account number AND CIM_profile_id is empty...
			if(!empty($morder->accountnumber) && empty($morder->CIM_profile_id)) // we're adding a new complete CIM profile (with payment profile) for new customer and charging it
				{
				// run that function and if positive, do this...
				if(pmprogateway_authorizenet_CIM::ifi_create_CIM_profile($morder))
					{
					//$morder->CIM_profile_id = $CIM_profile_id;
					//echo "<br>" . "=== checkout order filter var dump below here ===";
					//var_dump($morder);
					//echo "<br>" . "=== checkout order filter var dump above here ===";
					return $morder;
					}
				// if negative, do this...
				else
					{
					//var_dump();
					return false; // let it know it failed to create CIM profile
					}
				}
			//return false;
		}
		 

		 
		 /**
		 * Check level to see if shipping info should be shown.
		 * @since 1.1
		 */
		// gotta activate the shipping profile fields plugin
		// gotta add new spot on levels setup for checkbox to require shipping or not - like I did for salespageURL and memberpageURL
		// add code that checks if shipping is required for this lid
		// if so, get users shipping_profile_ids array
		// if !empty, loop thru them like with the payment profiles
		//		last - radio for adding new shipping profile - displays form when checked, hides when not checked
		// if empty, display form
		

		 
		 
		/**
		 * Process fields from the edit user page
		 *
		 * @since 1.1
		 */
		 
		 
		 
		/**
		 * Cron activation for subscription billings.
		 *
		 * @since 1.8
		 */
		//static function pmpro_activation()
		//{
		//	wp_schedule_event(time(), 'daily', 'ifi_pmpro_cron_CIM_subscription_billings');
		//}

		/**
		 * Cron deactivation for subscription updates.
		 *
		 * @since 1.8
		 */
		//static function pmpro_deactivation()
		//{
		//	wp_clear_scheduled_hook('ifi_pmpro_cron_CIM_subscription_billings');
		//}

		/**
		 * Cron job for subscription billings.
		 *
		 * @since 1.8
		 */
		//static function ifi_pmpro_cron_CIM_subscription_billings()
		//{
		// should setup an external cron to make sure wp gets triggered at right time
		// must pull the info out of subscriptions table
		// must create a new order object for each one we're going to bill
		// must pass each order object off to charge (or maybe process)
		// must save the invoice
		//}		
		 
		
		/**
		 * Customer Profile Stuff
		 *
		 */
		 
		 		
		// ======================
		// CIM Customer Profiles
		// ======================
		function ifi_create_CIM_profile($morder) // create a new COMPLETE customer profile in CIM // originally (&$order)
		{
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");
		if(empty($morder->code))
			$morder->code = pmprogateway_authorizenet_CIM::getRandomCode();
		if(empty($gateway_environment))
			$gateway_environment = pmpro_getOption("gateway_environment");
		if($gateway_environment == "live")
			$host = "api.authorize.net";		
		else
			$host = "apitest.authorize.net";	
			
		$path = "/xml/v1/request.api";
		
		$refid = $morder->code;
		$cardNumber = $morder->accountnumber;			
		$expirationDate = $morder->ExpirationDate_YdashM;						
		$cardCode = $morder->CVV2;
		
		
		$firstName = $morder->FirstName;
		$lastName = $morder->LastName;

		//do address stuff then?
		$address = $morder->Address1;
		if(!empty($morder->Address2))
			$address .= "\n" . $morder->Address2;
		$city = $morder->billing->city;
		$state = $morder->billing->state;
		$zip = $morder->billing->zip;
		$country = $morder->billing->country;						
			
		//customer stuff
		$customer_email = $morder->Email;
		if(strpos($order->billing->phone, "+") === false)
			$customer_phone = $morder->billing->phone;
		else
			$customer_phone = "";
				
		//make sure the phone is in an okay format
		$customer_phone = preg_replace("/[^0-9]/", "", $customer_phone);
		if(strlen($customer_phone) > 10)
			$customer_phone = "";

			//var_dump($morder);
			
		$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
			"<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
				"<merchantAuthentication>".
				"<name>" . $loginname . "</name>".
				"<transactionKey>" . $transactionkey . "</transactionKey>".
				"</merchantAuthentication>".
			"<refId>" . $refid . "</refId>".
			"<profile>".
				"<merchantCustomerId></merchantCustomerId>". // leave blank - update after checkout.
				"<description>CIM Profile for " . $firstName . " " . $lastName . "</description>".
				"<email>" . $customer_email . "</email>".
				"<paymentProfiles>".
					"<billTo>".
						 "<firstName>" . $firstName . "</firstName>".
						 "<lastName>" . $lastName . "</lastName>".
						 "<address>" . $address . "</address>".
						 "<city>" . $city . "</city>".
						 "<state>" . $state . "</state>".
						 "<zip>" . $zip . "</zip>".
						 "<country>" . $country . "</country>".
						 "<phoneNumber>" . $customer_phone . "</phoneNumber>".
					"</billTo>".
					"<payment>".
						"<creditCard>".
							"<cardNumber>" . $cardNumber . "</cardNumber>".
							"<expirationDate>" . $expirationDate . "</expirationDate>".
							"<cardCode>" . $cardCode . "</cardCode>".
						"</creditCard>".
					"</payment>".
				"</paymentProfiles>".
				// conditional shipping profile info can go here
			"</profile>".
			"</createCustomerProfileRequest>";
		$response = pmprogateway_authorizenet_CIM::send_request_via_curl($host,$path,$content);
		//$parsedresponse = pmprogateway_authorizenet_CIM::parse_CIM_response($response);
		//$parsedresponse = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOWARNING);
		//var_dump($content);
		//echo $content;
		//echo "--content above here --";
		//var_dump($response);
		//echo $response;
		//echo "-- response above here --";
		//echo $this->response;
//echo "Raw response: " . "<br>" . htmlspecialchars($response) . "<br>";
		//echo "-- raw response above here --" . "<br><br>";

		// new
				list ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response) = pmprogateway_authorizenet_CIM::parse_CIM_customer_profile_response($response);
				
				//echo "<br><br>" . "-- create customer profile parsed response below here --" . "<br><br>";
				//echo "-- refId --";
				//var_dump($refId);
				//echo "-- resultCode below here --";
				//var_dump($resultCode);
				//echo $resultCode;
				//echo "-- resultCode above here --" . "<br>";
				//echo "-- code --";
				//var_dump($code);
				//echo "-- text --";
				//var_dump($text);
				//echo "-- CIM profile ID --";
				//var_dump($CIM_profile_id);
				//echo "-- payment_profile_id --";
				//var_dump($payment_profile_id);
				//echo "-- shipping_profile_id --";
				//var_dump($shipping_profile_id);
				//echo "-- CIM direct response --";
				//var_dump($CIM_direct_response);
				//echo "<br><br>" . "-- create customer profile parsed parsed response above here --" . "<br><br>";
				//die;
				
				if ($resultCode === "Ok") 
					{
						// add profile ids to the order
						$morder->CIM_profile_id = $CIM_profile_id;
						$morder->payment_profile_id = $payment_profile_id;
						$morder->CIM_refid = $refId;
						$morder->shipping_profile_id = $shipping_profile_id;
						
						// store profile ids to session for use on checkout in case card is invalid on first attempt
						$_SESSION["CIM_profile_id"] = $CIM_profile_id;
						//$_SESSION["payment_profile_id"] = $payment_profile_id;
						//$_SESSION["shipping_profile_id"] = $shipping_profile_id;

						
				//echo "-- success response from authnet creating customer profile -- below" . "<br>";		
				//echo "-- CIM profile ID --";
				//		var_dump($morder->CIM_profile_id);
				//echo "-- payment_profile_id --";
				//		var_dump( $morder->payment_profile_id);
				//echo "-- refId --";
				//		var_dump( $morder->CIM_refid);
				//echo "-- shipping_profile_id --";
				//		var_dump( $morder->shipping_profile_id);
				//echo "session stored info here" . "<br>";
				//		var_dump($_SESSION['CIM_profile_id']);
				//		var_dump($_SESSION['payment_profile_id']);
				//echo "<br><br>" . "-- Successful morder below here --" . "<br><br>";
				//		var_dump($morder);
				//echo "<br><br>" . "-- Successful morder above here --" . "<br><br>";
				//echo "-- success response from authnet creating customer profile -- above" . "<br>";		
				//die;
						
						//return the order for actual processing of transaction
						return $morder;
					}
				else
					{
						$morder->status = "error";
						$morder->errorcode = $code;
						$morder->error = $text;
						$morder->shorterror = $text;	
		//echo "<br><br>" . "-- Error morder below here --" . "<br><br>";
		//var_dump($morder);
		//echo "<br><br>" . "-- Error morder above here --" . "<br><br>";
				//echo "-- error response from authnet creating customer profile -- below" . "<br>";		
				//echo "-- CIM profile ID --";
				//		var_dump($morder->CIM_profile_id);
				//echo "-- payment_profile_id --";
				//		var_dump( $morder->payment_profile_id);
				//echo "-- refId --";
				//		var_dump( $morder->CIM_refid);
				//echo "-- shipping_profile_id --";
				//		var_dump( $morder->shipping_profile_id);
				//echo "session stored info here" . "<br>";
				//		var_dump($_SESSION['CIM_profile_id']);
				//		var_dump($_SESSION['payment_profile_id']);
				//echo "<br><br>" . "-- Successful morder below here --" . "<br><br>";
				//		var_dump($morder);
				//echo "<br><br>" . "-- Successful morder above here --" . "<br><br>";
				//echo "-- error response from authnet creating customer profile -- above" . "<br>";		
		
						return $morder;
					}
		//return $morder;
		}
		
		
		function ifi_update_CIM_profile($user_id, $order) // updates customer profile in CIM with correct customer id number AFTER checkout completes successfully
		{
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");
		if(empty($gateway_environment))
			$gateway_environment = pmpro_getOption("gateway_environment");
		if($gateway_environment == "live")
			$host = "api.authorize.net";		
		else
			$host = "apitest.authorize.net";	
			
		$path = "/xml/v1/request.api";
		
		$CIM_profile_id = $order->CIM_profile_id;
		$refid = $order->CIM_refid;
		$firstName = $order->FirstName;
		$lastName = $order->LastName;
		$customer_email = $order->Email;

		$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
			"<updateCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
				"<merchantAuthentication>".
				"<name>" . $loginname . "</name>".
				"<transactionKey>" . $transactionkey . "</transactionKey>".
				"</merchantAuthentication>".
			"<refId>" . $refid . "</refId>".
			"<profile>".
				"<merchantCustomerId>" . $user_id . "</merchantCustomerId>".
				"<description>CIM Profile for " . $firstName . " " . $lastName . "</description>".
				"<email>" . $customer_email . "</email>".
				"<customerProfileId>" . $CIM_profile_id . "</customerProfileId>".
			"</profile>".
			"</updateCustomerProfileRequest>";
			
		$response = pmprogateway_authorizenet_CIM::send_request_via_curl($host,$path,$content);
		//$parsedresponse = parse_api_response($response);
		
		list ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response) = pmprogateway_authorizenet_CIM::parse_CIM_customer_profile_response($response);
						
		if ("Ok" == $resultCode) 
			{
				return true;
			}
		else
			{
				return false;
			}
		}
		
			
		// =================
		// Payment Profiles
		// =================

		function ifi_create_payment_profile($morder) // add a new payment profile to existing CIM profile
		{
		//echo "<br>" . "=== create payment profile beginning var dump below ===" . "<br>";
		//echo "account number below this line";
		//var_dump($morder->accountnumber);
		//echo "account number above this line";
		//var_dump($morder);
		//echo "<br>" . "=== create payment profile beginning var dump above ===" . "<br>";

			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");		
		if(empty($morder->code))
			$morder->code = $morder->getRandomCode();
		if(empty($gateway_environment))
			$gateway_environment = pmpro_getOption("gateway_environment");
		if($gateway_environment == "live")
			$host = "api.authorize.net";		
		else
			$host = "apitest.authorize.net";	
			
		$path = "/xml/v1/request.api";
		
		$refid = $morder->code;
		$CIM_profile_id = $morder->CIM_profile_id;
		$cardNumber = $morder->accountnumber;			
		$expirationDate = $morder->ExpirationDate_YdashM;						
		$cardCode = $morder->CVV2;
		
		$firstName = $morder->FirstName;
		$lastName = $morder->LastName;

		//do address stuff then?
		$address = $morder->Address1;
		if(!empty($morder->Address2))
			$address .= "\n" . $morder->Address2;
		$city = $morder->billing->city;
		$state = $morder->billing->state;
		$zip = $morder->billing->zip;
		$country = $morder->billing->country;						
			
		//customer stuff
		$customer_email = $morder->Email;
		if(strpos($morder->billing->phone, "+") === false)
			$customer_phone = $morder->billing->phone;
		else
			$customer_phone = "";
				
		//make sure the phone is in an okay format
		$customer_phone = preg_replace("/[^0-9]/", "", $customer_phone);
		if(strlen($customer_phone) > 10)
			$customer_phone = "";

		//build xml to post
		$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
			"<createCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
				"<merchantAuthentication>".
				"<name>" . $loginname . "</name>".
				"<transactionKey>" . $transactionkey . "</transactionKey>".
				"</merchantAuthentication>".
				"<refId>" . $refid . "</refId>".
			"<customerProfileId>" . $CIM_profile_id . "</customerProfileId>".
			"<paymentProfile>".
				"<billTo>".
						 "<firstName>" . $firstName . "</firstName>".
						 "<lastName>" . $lastName . "</lastName>".
						 "<address>" . $address . "</address>".
						 "<city>" . $city . "</city>".
						 "<state>" . $state . "</state>".
						 "<zip>" . $zip . "</zip>".
						 "<country>" . $country . "</country>".
						 "<phoneNumber>" . $customer_phone . "</phoneNumber>".
				"</billTo>".
			"<payment>".
				 "<creditCard>".
							"<cardNumber>" . $cardNumber . "</cardNumber>".
							"<expirationDate>". $expirationDate . "</expirationDate>".
							"<cardCode>" . $cardCode . "</cardCode>".
				 "</creditCard>".
			"</payment>".
			"</paymentProfile>".
			"<validationMode>liveMode</validationMode>". // or testMode
			"</createCustomerPaymentProfileRequest>";

		$response = pmprogateway_authorizenet_CIM::send_request_via_curl($host,$path,$content);
				list ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response) = pmprogateway_authorizenet_CIM::parse_create_payment_profile_response($response);

				//echo "=== create payment profile response ===" . "<br>";
				//var_dump($cardNumber);
				//var_dump($resultCode);
				//var_dump($code);
				//var_dump($text);
				//var_dump($CIM_profile_id);
				//var_dump($payment_profile_id);
				//var_dump($CIM_direct_response);
				//echo "=== create payment profile response above ===" . "<br>";
				//die;
				
			if ($resultCode === "Ok") 
					{
						$morder->payment_profile_id = $payment_profile_id;
						//$morder->CIM_refid = $refId;
				//echo "=== create payment profile success response ===" . "<br>";
				//var_dump($resultCode);
				//var_dump($code);
				//var_dump($text);
				//var_dump($CIM_profile_id);
				//var_dump($payment_profile_id);
				//var_dump($CIM_direct_response);
				//echo "=== create payment profile success response above ===" . "<br>";
						return $morder;
					}
				else
					{
						$morder->status = "error";
						$morder->errorcode = $code;
						$morder->error = $text;
						$morder->shorterror = $text;	
						// new try
						
						// store the error in session so you can display it
						$_SESSION["create_payment_profile_error"] = $code . ":&nbsp;;&nbsp;" . $text;
						$_SESSION["CIM_profile_id"] = $CIM_profile_id;
						
				//echo "<br>" . "=== create payment profile error response ===" . "<br>";
				//var_dump($resultCode);
				//var_dump($code);
				//var_dump($text);
				//var_dump($CIM_profile_id);
				//var_dump($payment_profile_id);
				//var_dump($CIM_direct_response);
				//echo "<br>" . "--- error morder below ---" . "<br>";
				//var_dump($morder);
				//echo "<br>" . "--- error morder above ---" . "<br>";
				//echo "=== create payment profile error response above ===" . "<br>";
						return $morder; 
					}
		}		
		
		// NOT USED - HERE FOR NOTHING SO FAR AND NEVER TESTED
		function ifi_update_payment_profile($payment_profile_id) // update existing customer payment profile to change exp etc.
		{
		global $current_user;
		if(empty($gateway_environment))
			$gateway_environment = pmpro_getOption("gateway_environment");
		if($gateway_environment == "live")
			$host = "api.authorize.net";		
		else
			$host = "apitest.authorize.net";	
			
		$path = "/xml/v1/request.api";
		
		
		if (isset($_REQUEST['bfirstname']))
			$bfirstname = sanitize_text_field(stripslashes($_REQUEST['bfirstname']));	
		else
			$bfirstname = "";
		if (isset($_REQUEST['blastname']))
			$blastname = sanitize_text_field(stripslashes($_REQUEST['blastname']));	
		else
			$blastname = "";
		if (isset($_REQUEST['fullname']))
			$fullname = $_REQUEST['fullname'];		//honeypot for spammers
		if (isset($_REQUEST['baddress1']))
			$baddress1 = sanitize_text_field(stripslashes($_REQUEST['baddress1']));		
		else
			$baddress1 = "";
		if (isset($_REQUEST['baddress2']))
			$baddress2 = sanitize_text_field(stripslashes($_REQUEST['baddress2']));
		else
			$baddress2 = "";
		if (isset($_REQUEST['bcity']))
			$bcity = sanitize_text_field(stripslashes($_REQUEST['bcity']));
		else
			$bcity = "";
		
		if (isset($_REQUEST['bstate']))
			$bstate = sanitize_text_field(stripslashes($_REQUEST['bstate']));
		else
			$bstate = "";
		
		//convert long state names to abbreviations
		if (!empty($bstate))
		{
			global $pmpro_states;
			foreach($pmpro_states as $abbr => $state)
			{
				if ($bstate == $state)
				{
					$bstate = $abbr;
					break;
				}
			}
		}
		
		if (isset($_REQUEST['bzipcode']))
			$bzipcode = sanitize_text_field(stripslashes($_REQUEST['bzipcode']));
		else
			$bzipcode = "";
		if (isset($_REQUEST['bcountry']))
			$bcountry = sanitize_text_field(stripslashes($_REQUEST['bcountry']));
		else
			$bcountry = "";
		if (isset($_REQUEST['bphone']))
			$bphone = sanitize_text_field(stripslashes($_REQUEST['bphone']));
		else
			$bphone = "";
		if (isset($_REQUEST['bemail']))
			$bemail = sanitize_email(stripslashes($_REQUEST['bemail']));
		else
			$bemail = "";
		if (isset($_REQUEST['bconfirmemail_copy']))
			$bconfirmemail = $bemail;	
		elseif (isset($_REQUEST['bconfirmemail']))
			$bconfirmemail = sanitize_email(stripslashes($_REQUEST['bconfirmemail']));
		else
			$bconfirmemail = "";
			
		if (isset($_REQUEST['CardType']) && !empty($_REQUEST['AccountNumber']))
			$CardType = sanitize_text_field($_REQUEST['CardType']);
		else
			$CardType = "";
		if (isset($_REQUEST['AccountNumber']))
			$AccountNumber = sanitize_text_field($_REQUEST['AccountNumber']);
		else
			$AccountNumber = "";		
		
		if (isset($_REQUEST['ExpirationMonth']))
			$ExpirationMonth = sanitize_text_field($_REQUEST['ExpirationMonth']);
		else
			$ExpirationMonth = "";
		if (isset($_REQUEST['ExpirationYear']))
			$ExpirationYear = sanitize_text_field($_REQUEST['ExpirationYear']);
		else
			$ExpirationYear = "";
		if (isset($_REQUEST['CVV']))
			$CVV = sanitize_text_field($_REQUEST['CVV']);
		else
			$CVV = "";
		
		$refid = getRandomCode();
		$CIM_profile_id = get_user_meta($current_user->ID, "CIM_profile_id", true);
		$cardNumber = $AccountNumber;			
		$expirationDate = $ExpirationYear . "-" . $ExpirationMonth;						
		$cardCode = $CVV;
		
		$firstName = $bfirstname;
		$lastName = $blastname;

		//do address stuff then?
		$address = $baddress1;
		if(!empty($baddress2))
			$address .= "\n" . $baddress2;
		$city = $bcity;
		$state = $bstate;
		$zip = $bzip;
		$country = $bcountry;						
			
		//customer stuff
		$customer_email = $bemail;
		if(strpos($bphone, "+") === false)
			$customer_phone = $bphone;
		else
			$customer_phone = "";
				
		//make sure the phone is in an okay format
		$customer_phone = preg_replace("/[^0-9]/", "", $customer_phone);
		if(strlen($customer_phone) > 10)
			$customer_phone = "";

		//build xml to post
		$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
			"<updateCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
			$this->MerchantAuthenticationBlock().
			"<refId>" . $refid . "</refId>".
			"<customerProfileId>" . $CIM_profile_id . "</customerProfileId>".
			"<paymentProfile>".
				"<billTo>".
						 "<firstName>" . $firstName . "</firstName>".
						 "<lastName>" . $lastName . "</lastName>".
						 "<address>" . $address . "</address>".
						 "<city>" . $city . "</city>".
						 "<state>" . $state . "</state>".
						 "<zip>" . $zip . "</zip>".
						 "<country>" . $country . "</country>".
						 "<phoneNumber>" . $customer_phone . "</phoneNumber>".
				"</billTo>".
			"<payment>".
				 "<creditCard>".
							"<cardNumber>" . $cardnumber . "</cardNumber>".
							"<expirationDate>". $expirationDate . "</expirationDate>".
							"<cardCode>" . $cardCode . "</cardCode>".
				 "</creditCard>".
				"<customerPaymentProfileId>" . $payment_profile_id . "</customerPaymentProfileId>".
			"</payment>".
			"</paymentProfile>".
			"<validationMode>liveMode</validationMode>". // or testMode
			"</updateCustomerPaymentProfileRequest>";

		$response = send_request_via_curl($host,$path,$content);
		$parsedresponse = parse_api_response($response);
		
		if ("Ok" == $parsedresponse->messages->resultCode) 
			{
				// card info updated in CIM
				$status = "success";
				// probably need to update the payment_profile_ids user_meta to include the new updated info
				return true;
			}
		else
			{
				foreach ($parsedresponse->messages->message as $msg) 
					{
					$status = "error";
					$errorcode = $msg->code;
					$error = $msg->text;
					$shorterror = $msg->text;									
					return false;
					}
			}

		
		
		}
		
		//function ifi_get_payment_profiles() // get all customer payment profiles from CIM profile ID
		//{
		//}
		
		// =================
		// Shipping Profiles - only needed on physical products that get shipped out
		// =================
		//function ifi_create_shipping_profile(&$order) // create shipping profile in CIM
		//{
		//}
		
		//function ifi_update_shipping_profile(&$order) // update shipping profile
		//{
		//}
		
		//function ifi_get_shipping_profiles() // get customer shipping profiles from profile ID
		//{
		//}
		
		
		/**
		 * Process checkout.
		 *		- should remain relatively unchanged
		 *		- just makes decisions about where to send the order for actual processing
		 */
		function process(&$order) // starts actual order processing - splits into authorize, charge, and subscribe
		{
			//check for initial payment
			if(floatval($order->InitialPayment) == 0)  // if InitialPayment is zero, do this stuff, beginning with authorize the card they entered
			{
				//auth first because we need to know card is good and this is a zero price.
				if($this->authorize($order)) // call the authorize step and get response from it... if positive, do this stuff...
				{						
					$this->void($order);		// void that authorized amount								
					if(!pmpro_isLevelTrial($order->membership_level)) // if there's no trial for this level, do this...
					{
						// setup sub part of order object
						//subscription will start today with a 1 period trial
						$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";
						$order->TrialBillingPeriod = $order->BillingPeriod;
						$order->TrialBillingFrequency = $order->BillingFrequency;													
						$order->TrialBillingCycles = 1;
						$order->TrialAmount = 0;
						
						//add a billing cycle to make up for the trial, if applicable
						if(!empty($order->TotalBillingCycles))
							$order->TotalBillingCycles++;
					}
					elseif($order->InitialPayment == 0 && $order->TrialAmount == 0) // if initialpayment AND trial are free...
					{
						//it has a trial, but the amount is the same as the initial payment, so we can squeeze it in there
						$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";														
						$order->TrialBillingCycles++;
						
						//add a billing cycle to make up for the trial, if applicable
						if(!empty($order->TotalBillingCycles))
							$order->TotalBillingCycles++;
					}
					else // there is a trial, but it's not a free trial
					{
						//add a period to the start date to account for the initial payment
						$order->ProfileStartDate = date("Y-m-d", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod, current_time("timestamp"))) . "T0:0:0";
					}
					
					$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
					return $this->subscribe($order); // call subscribe function to set up that end of the created order object
				}
				else // if authorize response is negative, do this...
				{					
					if(empty($order->error))
						$order->error = __("Unknown error: Authorization failed.", "pmpro");
					return false;
				}
			}
			else // intialPayment is more than zero, so do this...
			{
				//charge first payment to card and get response. 
				if($this->charge($order)) // if positive response, do this...
				{							
					//setup recurring billing
					if(pmpro_isLevelRecurring($order->membership_level)) // if this level is recurring, do this...
					{						
						if(!pmpro_isLevelTrial($order->membership_level)) // no trial period
						{
							//subscription will start today with a 1 period trial
							$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";
							$order->TrialBillingPeriod = $order->BillingPeriod;
							$order->TrialBillingFrequency = $order->BillingFrequency;													
							$order->TrialBillingCycles = 1;
							$order->TrialAmount = 0;
							
							//add a billing cycle to make up for the trial, if applicable
							if(!empty($order->TotalBillingCycles))
								$order->TotalBillingCycles++;
						}
						elseif($order->InitialPayment == 0 && $order->TrialAmount == 0) // free trial period
						{
							//it has a trial, but the amount is the same as the initial payment, so we can squeeze it in there
							$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";														
							$order->TrialBillingCycles++;
							
							//add a billing cycle to make up for the trial, if applicable
							if(!empty($order->TotalBillingCycles))
								$order->TotalBillingCycles++;
						}
						else // there is a trial period, but it's not free
						{
							//add a period to the start date to account for the initial payment
							$order->ProfileStartDate = date("Y-m-d", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod, current_time("timestamp"))) . "T0:0:0";
						}
						
						$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
						if($this->subscribe($order)) // call the subscribe function and get response.  if positive, do this...
						{
							return true; // let it know it succeeded
						}
						else // otherwise, it failed, so do this...
						{
							if($this->void($order)) // void it out and get response.  if positive, do this...
							{
								if(!$order->error)
									$order->error = __("Unknown error: Void Payment failed.", "pmpro");
							}
							else // failed to void it out
							{
								if(!$order->error)
									$order->error = __("Unknown error: Payment failed.", "pmpro");								
								$order->error .= " " . __("A partial payment was made that we could not void. Please contact the site owner immediately to correct this.", "pmpro");
							}
														
							return false;	// let it know it failed							
						}
					}
					else // it's a one-time charge, so do this...
					{
						//only a one time charge
						$order->status = "success";	//saved on checkout page
//echo "process - one time only charge success var dump below here";
//var_dump($order);						
						return true; // let it know it succeeded
					}
				}
				else // if negative response, do this...
				{					
					$order->payment_profile_id = "failed_invalid";
					//echo " == process one time charge negative response below ==";
					//var_dump($order->payment_profile_id);
					//echo " == process one time charge negative response above ==";
					if(empty($order->error))
						
							//$session_error = $_SESSION["chargeerror"];
							//if(!empty($session_error))
								//$order->error = $session_error;
							//if(empty($session_error))
								$order->error = __("Unknown error: One-Time Charge Payment failed.", "pmpro");
							//echo "<br>" . "=== from process function in my gateway below ===" . "<br>";
							//var_dump($morder);
							//echo "<br>" . "morder above this line... order below this line" . "<br>";
							//var_dump($order);
							//echo "<br>";
							//echo "=== from process function in my gateway above ===" . "<br>";
						
					//var_dump($order->CIM_profile_id);
					return false; // let it know it failed.
				}	
			}	
		}
		
		function authorize(&$order) // createCustomerProfileTransactionRequest - profileTransAuthOnly - 
		{
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");				
			if(empty($order->code))
				$order->code = $order->getRandomCode();
			if(!empty($order->gateway_environment))
				$gateway_environment = $order->gateway_environment;
			if(empty($gateway_environment))
				$gateway_environment = pmpro_getOption("gateway_environment");
			if($gateway_environment == "live")
				$host = "api.authorize.net";		
			else
				$host = "apitest.authorize.net";	
				
			$path = "/xml/v1/request.api";
			
			// load needed info
			$refid = $order->code;
			$membership_level_id = $order->membership_level;
			
			//who to charge?
			$CIM_profile_id = $order->CIM_profile_id;
			$payment_profile_id = $order->payment_profile_id;
			
			//what amount to authorize?			
			$amount = "1.00";
						
			//tax
			$order->subtotal = $amount;
			$tax = $order->getTax(true);
			$amount = round((float)$order->subtotal + (float)$tax, 2);
			
			//combine address			
			$address = $order->Address1;
			if(!empty($order->Address2))
				$address .= "\n" . $order->Address2;
			
			//customer stuff
			$customer_email = $order->Email;
			$customer_phone = $order->billing->phone;
			
			if(!isset($order->membership_level->name))
				$order->membership_level->name = "";
				
			$item_name = $order->membership_level->name;
			$abrev_item_name = substr($item_name, 0,30);

			//build xml to post
			$this->content =
				"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
				"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
				"<merchantAuthentication>".
				"<name>" . $loginname . "</name>".
				"<transactionKey>" . $transactionkey . "</transactionKey>".
				"</merchantAuthentication>".
				"<refId>" . $refid . "</refId>".
				"<transaction>".
					"<profileTransAuthOnly>".
					"<amount>" . $amount . "</amount>". // should include tax, shipping, and everything.
						// conditional shipping
						//"<shipping>".
						//	"<amount>0.00</amount>".
						//	"<name>Free Shipping</name>".
						//	"<description>Free UPS Ground shipping. Ships in 5-10 days.</description>".
						//"</shipping>".
						"<lineItems>".
							"<itemId>" . $membership_level_id ."</itemId>".
							"<name>" . $abrev_item_name . "</name>".
							"<description>" .$item_name . "</description>".
							"<quantity>1</quantity>".
							"<unitPrice>" . $amount . "</unitPrice>".
						"</lineItems>".
					"<customerProfileId>" . $CIM_profile_id . "</customerProfileId>".
					"<customerPaymentProfileId>" . $payment_profile_id . "</customerPaymentProfileId>".
					// conditional shipping profile id if shipping required
					//"<customerShippingAddressId>" . $shipping_profile_id . "</customerShippingAddressId>".
					"<order>".
						"<invoiceNumber>" . $refid . "</invoiceNumber>".
						"<description>Purchase of " . $item_name . "</description>".
					"</order>".
					"</profileTransAuthOnly>".
				"</transaction>".
				"</createCustomerProfileTransactionRequest>";
			
			//send the xml via curl
			$this->response = $this->send_request_via_curl($host,$path,$this->content);

			if(!empty($this->response)) 
			{				
				list ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response) = $this->parse_CIM_response2($this->response);
			}	
				if (isset($CIM_direct_response)) 
					{
						$directResponseFields = explode(",", $CIM_direct_response);
						$responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
						$responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
						$responseReasonText = $directResponseFields[3];
						$approvalCode = $directResponseFields[4]; // Authorization code
						$transId = $directResponseFields[6];
						
						if ("1" == $responseCode) // approved
						{
							$order->payment_transaction_id = $transId;
							$order->updateStatus("authorized");		
							return true;
						}
						else if ("2" == $responseCode) // declined
						{
							$order->errorcode = $responseReasonCode;
							$order->error = $responseReasonText;
							$order->shorterror = $responseReasonText;
							return false;
						}
						else // error
						{
							$order->errorcode = $responseReasonCode;
							$order->error = $responseReasonText;
							$order->shorterror = $responseReasonText;
							return false;
						}
					}
		}
		
		function void(&$order) // createCustomerProfileTransactionRequest - profileTransVoid
		{
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");						
			if(empty($order->code))
				$order->code = $order->getRandomCode();
			if(!empty($order->gateway_environment))
				$gateway_environment = $order->gateway_environment;
			if(empty($gateway_environment))
				$gateway_environment = pmpro_getOption("gateway_environment");
			if($gateway_environment == "live")
				$host = "api.authorize.net";		
			else
				$host = "apitest.authorize.net";	
				
			$path = "/xml/v1/request.api";
			
			// load info
			$refid = $order->code;
			$transId = $order->payment_transaction_id;
			$CIM_profile_id = $order->CIM_profile_id;
			$payment_profile_id = $order->payment_profile_id;
			
			//build xml to post
			$this->content =
				"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
				"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
				"<merchantAuthentication>".
				"<name>" . $loginname . "</name>".
				"<transactionKey>" . $transactionkey . "</transactionKey>".
				"</merchantAuthentication>".
				"<refId>" . $refid . "</refId>".
				"<transaction>".
					"<profileTransVoid>".
						"<customerProfileId>" . $CIM_profile_id . "</customerProfileId>".
						"<customerPaymentProfileId>" . $payment_profile_id . "</customerPaymentProfileId>".
						"<transId>" . $transId . "</transId>".
					"</profileTransVoid>".
				"</transaction>".
				"</createCustomerProfileTransactionRequest>";
			
			//send the xml via curl
			$this->response = $this->send_request_via_curl($host,$path,$this->content);
			
			if(!empty($this->response)) 
			{				
				list ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response) = $this->parse_CIM_response2($this->response);
			}	
				if (isset($CIM_direct_response)) 
					{
						$directResponseFields = explode(",", $CIM_direct_response);
						$responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
						$responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
						$responseReasonText = $directResponseFields[3];
						$approvalCode = $directResponseFields[4]; // Authorization code
						$transId = $directResponseFields[6];
						
						if ("1" == $responseCode) // approved
						{
							$order->payment_transaction_id = $transId;
							$order->updateStatus("voided");		
							return true;
						}
						else // error
						{
							$order->errorcode = $responseReasonCode;
							$order->error = $responseReasonText;
							$order->shorterror = $responseReasonText;
							return false;
						}
					}
		}	
		
		function charge(&$order) // createCustomerProfileTransactionRequest - profileTransAuthCapture -transaction type is will charge against the CIM billing profile, not AIM
		{
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");

			if(empty($order->code))
				$order->code = $order->getRandomCode();
			if(!empty($order->gateway_environment))
				$gateway_environment = $order->gateway_environment;
			if(empty($gateway_environment))
				$gateway_environment = pmpro_getOption("gateway_environment");
			if($gateway_environment == "live")
				$host = "api.authorize.net";		
			else
				$host = "apitest.authorize.net";	
				
			$path = "/xml/v1/request.api";
			
			// load needed info
			$refid = $order->code;
			$membership_level_id = $order->membership_level->id;
			
			
			//who to charge?
			$CIM_profile_id = $order->CIM_profile_id;
			$payment_profile_id = $order->payment_profile_id;
			$shipping_profile_id = $order->shipping_profile_id;
			$expirationmonth = $order->expirationmonth;
			$expirationyear = $order->expirationyear;
			
			//var_dump($CIM_profile_id);
			//var_dump($order->CIM_profile_id);
			//echo "=== charge function above here ===";
			//var_dump($payment_profile_id);
			//var_dump($order->payment_profile_id);
			//echo "=== charge function order stuff above here ===";

			//what amount to charge?			
			$amount = $order->InitialPayment;
						
			//tax
			$order->subtotal = $amount;
			$tax = $order->getTax(true);
			$amount = round((float)$order->subtotal + (float)$tax, 2);
			
			//combine address			
			$address = $order->Address1;
			if(!empty($order->Address2))
				$address .= "\n" . $order->Address2;
			
			//customer stuff
			$customer_email = $order->Email;
			$customer_phone = $order->billing->phone;
			
			if(!isset($order->membership_level->name))
				$order->membership_level->name = "";
				
			$item_name = $order->membership_level->name;
			$abrev_item_name = substr($item_name, 0,30);
			//echo "<br>" . "pre charge xml request var dump below here ---";
			//var_dump($order->CIM_profile_id);
			//var_dump($order->payment_profile_id);
			//var_dump($CIM_profile_id);
			//var_dump($payment_profile_id);
			//echo "<br>" . "pre charge xml request var dump above here ---";
			
			//build xml to post
			$this->content =
				"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
				"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
				"<merchantAuthentication>".
				"<name>" . $loginname . "</name>".
				"<transactionKey>" . $transactionkey . "</transactionKey>".
				"</merchantAuthentication>".
				"<refId>" . $refid . "</refId>".
				"<transaction>".
					"<profileTransAuthCapture>".
					"<amount>" . $amount . "</amount>". // should include tax, shipping, and everything.
						// conditional shipping
						//"<shipping>".
						//	"<amount>0.00</amount>".
						//	"<name>Free Shipping</name>".
						//	"<description>Free UPS Ground shipping. Ships in 5-10 days.</description>".
						//"</shipping>".
						"<lineItems>".
							"<itemId>" . $membership_level_id ."</itemId>".
							"<name>" . $abrev_item_name . "</name>".
							"<description>" . $item_name . "</description>".
							"<quantity>1</quantity>".
							"<unitPrice>" . $amount . "</unitPrice>".
						"</lineItems>".
					"<customerProfileId>" . $CIM_profile_id . "</customerProfileId>".
					"<customerPaymentProfileId>" . $payment_profile_id . "</customerPaymentProfileId>".
					// conditional shipping profile id if shipping required
					//"<customerShippingAddressId>" . $shipping_profile_id . "</customerShippingAddressId>".
					"<order>".
						"<invoiceNumber>" . $refid . "</invoiceNumber>".
						"<description>Purchase of " . $item_name . "</description>".
					"</order>".
					"</profileTransAuthCapture>".
				"</transaction>".
				"</createCustomerProfileTransactionRequest>";
			
			//send the xml via curl
			$this->response = $this->send_request_via_curl($host,$path,$this->content);

			//new
			if(!empty($this->response)) 
			{				
				list ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response) = $this->parse_CIM_response2($this->response);
				//echo "<br><br>" . "charge response var dumps below here ---";
				//var_dump($resultCode);
				//var_dump($code);
				//var_dump($text);
				//var_dump($CIM_direct_response);
				//var_dump($CIM_profile_id);
				//var_dump($payment_profile_id);
				//echo "<br>" . "charge response var dumps below here ---" . "<br><br>";

				if($resultCode == "Error")
					{
						$order->errorcode = $code;
						$order->error = $resultCode . ":&nbsp;&nbsp;" . $text;
						//"Invalid Credit Card Number.  Please double check your card number for missing or incorrect digits and try again.";
						$order->shorterror = $resultCode;
						//echo "charge result code was error";
						//$_SESSION["chargeerror"] = $order->error;
						return false;
					}
				
				// here already
				if (isset($CIM_direct_response)) 
					{
						$directResponseFields = explode(",", $CIM_direct_response);
						$responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
						$responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
						$responseReasonText = $directResponseFields[3];
						$approvalCode = $directResponseFields[4]; // Authorization code
						$transId = $directResponseFields[6];
						$account_number_response = $directResponseFields[50];
						$card_type_response = $directResponseFields[51];
						$transId = $directResponseFields[6];
						
						//echo "=== charge direct response var dump below here ===" . "<br>";
						//var_dump($directResponseFields);
						//var_dump($responseCode);
						//var_dump($responseReasonCode);
						//var_dump($responseReasonText);
						//var_dump($approvalCode);
						//var_dump($transId);
						//echo "<br>" . "=== charge direct response var dumps above here ===" . "<br>";
						
						if ("1" == $responseCode) // approved
						{
							$order->payment_transaction_id = $transId;
							//$order->accountnumber = $;
							$order->expirationmonth = $expirationmonth;
							$order->expirationyear = $expirationyear;
							//$order->CVV2 = $;
							//$order->cardtype = $;
							$order->updateStatus("success");		
							return true;
						}
						else if ("2" == $responseCode) // declined
						{
							$order->errorcode = $responseReasonCode;
							$order->error = $responseReasonText;
							$order->shorterror = $responseReasonText;
							//$_SESSION["chargeerror"] = $responseReasonText;
							//echo "from declined charge" . "<br>";
							//var_dump($responseReasonText);
							//var_dump($_SESSION["chargeerror"]);
							//die;
							//$order->payment_profile_id = "";
							return false;
						}
						else // error
						{
							$order->errorcode = $responseReasonCode;
							$order->error = $responseReasonText;
							$order->shorterror = $responseReasonText;
							//$_SESSION["chargeerror"] = $responseReasonText;
							//echo "from error charge" . "<br>";
							//var_dump($responseReasonText);
							//var_dump($_SESSION["chargeerror"]);
							//die;
							//$order->payment_profile_id = "";
							return false;
						}
					}
			}
			
			
		}
		
		function subscribe(&$order) // won't setup ARB sub - will setup info in subscriptions table so we can schedule billing CIM profile
		{
			//define variables to send
			global $current_user, $wpdb;
			if(empty($order->code))
				$order->code = pmprogateway_authorizenet_CIM::getRandomCode();
			
			//filter order before subscription. use with care.
			$order = apply_filters("pmpro_subscribe_order", $order, $this);
			
			if(!empty($order->gateway_environment))
				$gateway_environment = $order->gateway_environment;
			if(empty($gateway_environment))
				$gateway_environment = pmpro_getOption("gateway_environment");
			if($gateway_environment == "live")
					$host = "api.authorize.net";		
				else
					$host = "apitest.authorize.net";	
			
			$path = "/xml/v1/request.api";
			
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");
			
			$amount = $order->PaymentAmount;
			$refId = $order->code;
			$name = $order->membership_name;
			$length = (int)$order->BillingFrequency;
			
			if($order->BillingPeriod == "Month")
				$unit = "months";
			elseif($order->BillingPeriod == "Day")
				$unit = "days";
			elseif($order->BillingPeriod == "Year" && $order->BillingFrequency == 1)
			{
				$unit = "months";
				$length = 12;
			}
			elseif($order->BillingPeriod == "Week")
			{
				$unit = "days";
				$length = $length * 7;	//converting weeks to days
			}
			else
				return false;	//authorize.net only supports months and days
				
			$startDate = substr($order->ProfileStartDate, 0, 10);
			if(!empty($order->TotalBillingCycles))
			{
				$totalOccurrences = (int)$order->TotalBillingCycles;
				$subtype = "payment plan";
				$order->type = $subtype;
			}
			if(empty($totalOccurrences))
			{
				$totalOccurrences = 9999;	
				$subtype = "subscription";
				$order->type = $subtype;
			}
			if(isset($order->TrialBillingCycles))						
				$trialOccurrences = (int)$order->TrialBillingCycles;
			else
				$trialOccurrences = 0;
			if(isset($order->TrialAmount))
				$trialAmount = $order->TrialAmount;
			else
				$trialAmount = NULL;
			
			// next billing date
			$NextBillingLength = $length * $trialOccurrences;
			$NextBillingDate = Date('y:m:d', strtotime("+ $NextBillingLength $unit"));
			
			//taxes
			$amount_tax = $order->getTaxForPrice($amount);
			$trial_tax = $order->getTaxForPrice($trialAmount);
						
			$amount = round((float)$amount + (float)$amount_tax, 2);
			$trialAmount = round((float)$trialAmount + (float)$trial_tax, 2);
			
			//authorize.net doesn't support different periods between trial and actual
			
			if(!empty($order->TrialBillingPeriod) && $order->TrialBillingPeriod != $order->BillingPeriod)
			{
				echo "F";
				return false;
			}
			if(isset($order->user_id))
				$user_id = $order->user_id;
			else
				$user_id = $current_user->ID;
			$membership_id = $order->membership_id;
			$CIM_profile_id = $order->CIM_profile_id;
			$payment_profile_id = $order->payment_profile_id;
			
			
			if(isset($order->shipping_profile_id))
				$shipping_profile_id = $order->shipping_profile_id;
			else
				$shipping_profile_id = "";
				
			$cardNumber = $order->accountnumber;	
			$maskedCardNumber = formatCreditCard(maskCreditCard($order->accountnumber));
			$expirationmonth = $order->expirationmonth;
			$expirationyear = $order->expirationyear;
			$expirationDate = $order->ExpirationDate_YdashM;						
			$cardCode = $order->CVV2;
			$cardtype = $order->cardtype;
			
			$firstName = $order->FirstName;
			$lastName = $order->LastName;
			$customer_name = $firstName . " " . $lastName;

			//do address stuff then?
			$address = $order->Address1;
			if(!empty($order->Address2))
				$address .= "\n" . $order->Address2;
			$city = $order->billing->city;
			$state = $order->billing->state;
			$zip = $order->billing->zip;
			$country = $order->billing->country;						
			
			//customer stuff
			$customer_email = $order->Email;
			if(strpos($order->billing->phone, "+") === false)
				$customer_phone = $order->billing->phone;
			else
				$customer_phone = "";
				
			//make sure the phone is in an okay format
			$customer_phone = preg_replace("/[^0-9]/", "", $customer_phone);
			if(strlen($customer_phone) > 10)
				$customer_phone = "";
				
			
			$subscription_transaction_id = pmprogateway_authorizenet_CIM::getRandomCode();
			
			if(empty($order->last_billingdate))
				$lastBilling = "";
			if(empty($order->affiliate_id))
				$affiliate_id = "";
			if(empty($order->affiliate_subid))
				$affiliate_sub_id = "";
			
				
			// fixes some warnings
			if(empty($order->successful_billings))
				$successful_billings = "";
			if(empty($order->failed_billings))
				$failed_billings = "";
			if(empty($order->subscription_status))
				$sub_status = "active";
			if(empty($order->couponamount))
				$couponamount = "";
			if(empty($order->payment_transaction_id))
				$payment_transaction_id = ""; // should kill this
			if(empty($order->cardtype)) // should look up cardtype of the payment profile id and use that here - same with account number and expiration date
				$cardtype = "";
			if(empty($order->notes))
				$notes = "";
			
			global $wpdb;
				$wpdb->show_errors();

			// store the subscription in the subscriptions table
			// build query
			$table_name = $wpdb->prefix . 'pmpro_ifi_subscriptions';
			
			$subresult = $wpdb->insert(
					$table_name,
					array(
							'code' => $refId,
							'user_id' => $user_id, 
							'membership_id' => $membership_id, 
							'membership_name' => $name, 
							'type' => $subtype, 
							'cim_profile_id' => $CIM_profile_id, 
							'payment_profile_id' => $payment_profile_id, 
							'shipping_profile_id' => $shipping_profile_id, 
							'billing_name' => $customer_name, 
							'billing_street' => $address, 
							'billing_city' => $city, 
							'billing_state' => $state, 
							'billing_zip' => $zip, 
							'billing_country' => $country, 
							'billing_phone' => $customer_phone, 
							'billing_amount' => $amount, 
							'cycle_number' => $length, 
							'cycle_period' => $unit, 
							'billing_limit' => $totalOccurrences, 
							'trial_amount' => $trialAmount, 
							'trial_limit' => $trialOccurrences, 
							'status' => $sub_status, 
							'couponamount' => $couponamount, 
							'certificate_id' => $certificate_id, 
							'certificateamount' => $certificateamount, 
							'cardtype' => $cardtype, 
							'accountnumber' => $maskedCardNumber, 
							'expirationmonth' => $expirationmonth, 
							'expirationyear' => $expirationyear, 
							'payment_transaction_id' => $payment_transaction_id, // can kill this - its stored in the order for initial payment
							'subscription_transaction_id' => $subscription_transaction_id, 
							'sub_created' => $startDate, 
							'next_billingdate' => $NextBillingDate,
							'last_successful_billingdate' => $lastBilling, 
							'successful_billings' => $successful_billings, 
							'failed_billings' => $failed_billings, 
							'affiliate_id' => $affiliate_id, 
							'affiliate_subid' => $affiliate_subid, 
							'notes' => $notes
					),
					array(
						'%s', //code
						'%d', //user_id
						'%d', //membership_id
						'%s', //membership_name
						'%s', //type
						'%d', //cim_profile_id
						'%d', //payment_profile_id
						'%d', //shipping_profile_id
						'%s', //billing_name
						'%s', //billing_street
						'%s', //billing_city
						'%s', //billing_state
						'%s', //billing_zip
						'%s', //billing_country
						'%s', //billing_phone
						'%f', //billing_amount
						'%d', //cycle_number
						'%s', //cycle_period
						'%d', //billing_limit
						'%f', //trial_amount
						'%d', //trial_limit
						'%s', //status
						'%f', //couponamount
						'%d', //certificate_id
						'%f', //certificateamount
						'%s', //cardtype
						'%s', //accountnumber
						'%d', //expirationmonth
						'%d', //expirationyear
						'%s', //payment_transaction_id ---- KILL THIS
						'%s', //subscription_transaction_id
						'%s', //sub_created
						'%s', //next_billingdate
						'%s', //last_successful_billingdate
						'%d', //successful_billings
						'%d', //failed_billings
						'%d', //affiliate_id
						'%d', //affiliate_subid
						'%s' //notes
					)
			);
		if($subresult == false)
			{
			$order->status = "error";
			$order->errorcode = "SUB000000";
			$order->error = "Failed to create subscription in subscriptions table";
			$order->shorterror = "Failed to create subscription in subscriptions table";	
			return false;
			}
		else
			{
			$wpdb->insert_id;
			$order->status = "success";	//saved on checkout page				
			$order->subscription_transaction_id = $subscription_transaction_id;	
			$order->expirationmonth = $expirationmonth;
			$order->expirationyear = $expirationyear;
			return true;
			}

		}	
		
		function update(&$order) // will update billing profile in use for sub in subscriptions table
		{
			//define variables to send
			global $current_user, $wpdb;
			
			//if(empty($order->code))
			//	$order->code = $order->getRandomCode();
				
			if(isset($_REQUEST['CIM_profile_id']))
				$CIM_profile_id = $_REQUEST['CIM_profile_id'];
			else
				$CIM_profile_id = $order->CIM_profile_id;
			
			if(isset($_REQUEST['payment_profile_id']))
				$ifi_payment_profile_id = $_REQUEST['payment_profile_id'];
			else
				$ifi_payment_profile_id = "";
			
			if(isset($_REQUEST['shipping_profile_id']))
				$shipping_profile_id = $_REQUEST['shipping_profile_id'];
			else
				$shipping_profile_id = "";
				
			if(isset($_REQUEST['AccountNumber']))
				$accountnumber = $_REQUEST['AccountNumber'];
			else
				$accountnumber = "";
				
			if(isset($_REQUEST['CardType']))
				$cardtype = $_REQUEST['CardType'];
			else
				$cardtype = "";
				
			//var_dump($cardtype);
			//var_dump($accountnumber);
			//var_dump($ifi_payment_profile_id);
			
			// updating to an existing card already on file
			if($cardtype == "CIM Payment Profile" && $accountnumber == "0000000000000000" && !empty($ifi_payment_profile_id)) // using existing card
			{
				$payment_profiles = get_user_meta($current_user->ID, "ifi_payment_profile_ids", true);
				foreach($payment_profiles as $payment_profile)
					{
						if(in_array($ifi_payment_profile_id, $payment_profile))
							{
								foreach($payment_profile as $key=>$value)
									{
									$$key = $value;
									}
							
								$address = $street1;
								if(!empty($street2))
									$address .= "\n" . $street2;
								$table_name = $wpdb->prefix . 'pmpro_ifi_subscriptions';
								$sqlQuery = "UPDATE $wpdb->pmpro_ifi_subscriptions SET payment_profile_id = '$payment_profile_id', billing_name='$firstName $lastName', billing_street='" . esc_sql($address) . "', billing_city='$city', billing_state='$state', billing_zip='$zip', billing_country='$country', cardtype='$cardtype', accountnumber='$accountnumber', expirationmonth='$expiration_month', expirationyear='$expiration_year' WHERE subscription_transaction_id = '$order->subscription_transaction_id'";
								$results = $wpdb->query($sqlQuery);
								
								if($results === false) // failed to update database
									{
									//failed to update
									$order->status = "error";
									$order->errorcode = "SUBU000000";
									$order->error = "Failed to update subscription in subscriptions table";
									$order->shorterror = "Failed to update subscription in subscriptions table";									
									return false;
									}
								else // successfully updated database
									{
									$order->status = "updated";
									return true;
									}
							}
					}
				
			} // end of updating to existing card section
			else // adding a new card
			{
				global $current_user, $wpdb;
				
				if(!empty($order->gateway_environment))
					$gateway_environment = $order->gateway_environment;
				if(empty($gateway_environment))
					$gateway_environment = pmpro_getOption("gateway_environment");
				if($gateway_environment == "live")
						$host = "api.authorize.net";		
					else
						$host = "apitest.authorize.net";	
				
				$path = "/xml/v1/request.api";
				$loginname = pmpro_getOption("loginname");
				$transactionkey = pmpro_getOption("transactionkey");
				
				if(empty($order->code))
					$order->code = $order->getRandomCode();
					
				$refid = $order->code;
				
				// Authorize.net CIM stuff
				if(isset($_REQUEST['CIM_profile_id']))
					$CIM_profile_id = $_REQUEST['CIM_profile_id'];
				else
					$CIM_profile_id = $order->CIM_profile_id;	
				if(isset($_REQUEST['payment_profile_id']))
					$ifi_payment_profile_id = $_REQUEST['payment_profile_id'];
				else
					$ifi_payment_profile_id = "";
				if(isset($_REQUEST['shipping_profile_id']))
					$shipping_profile_id = $_REQUEST['shipping_profile_id'];
				else
					$shipping_profile_id = "";
				
				// card info
				if(isset($_REQUEST['ExpirationMonth']))
					$expirationmonth = $_REQUEST['ExpirationMonth'];
				else
					$expirationmonth = "";
				if(isset($_REQUEST['ExpirationYear']))
					$expirationyear = $_REQUEST['ExpirationYear'];
				else
					$expirationyear = "";
				if(isset($_REQUEST['AccountNumber']))
					$cardNumber = $_REQUEST['AccountNumber'];
				else
					$cardNumber = "";
				$expirationDate = $expirationyear . "-" . $expirationmonth;
				if(isset($_REQUEST['CVV']))
					$cardCode = $_REQUEST['CVV'];
				else
					$cardCode = "";
				if(isset($_REQUEST['CardType']))
					$cardType = $_REQUEST['CardType'];
				else
					$cardType = "";
				
				// contact info
				$firstName = $order->FirstName;
				$lastName = $order->LastName;
				$customer_name = $firstName . " " . $lastName;

				//do address stuff then?
				$address = $order->Address1;
				if(!empty($order->Address2))
					$address .= "\n" . $order->Address2;
				$city = $order->billing->city;
				$state = $order->billing->state;
				$zip = $order->billing->zip;
				$country = $order->billing->country;						
				
				//customer stuff
				$customer_email = $order->Email;
				if(strpos($order->billing->phone, "+") === false)
					$customer_phone = $order->billing->phone;
				else
					$customer_phone = "";
					
				//make sure the phone is in an okay format
				$customer_phone = preg_replace("/[^0-9]/", "", $customer_phone);
				if(strlen($customer_phone) > 10)
					$customer_phone = "";
				
				//build xml to post
				$content =
					"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
					"<createCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
						"<merchantAuthentication>".
						"<name>" . $loginname . "</name>".
						"<transactionKey>" . $transactionkey . "</transactionKey>".
						"</merchantAuthentication>".
						"<refId>" . $refid . "</refId>".
					"<customerProfileId>" . $CIM_profile_id . "</customerProfileId>".
					"<paymentProfile>".
						"<billTo>".
								 "<firstName>" . $firstName . "</firstName>".
								 "<lastName>" . $lastName . "</lastName>".
								 "<address>" . $address . "</address>".
								 "<city>" . $city . "</city>".
								 "<state>" . $state . "</state>".
								 "<zip>" . $zip . "</zip>".
								 "<country>" . $country . "</country>".
								 "<phoneNumber>" . $customer_phone . "</phoneNumber>".
						"</billTo>".
					"<payment>".
						 "<creditCard>".
									"<cardNumber>" . $cardNumber . "</cardNumber>".
									"<expirationDate>". $expirationDate . "</expirationDate>".
									"<cardCode>" . $cardCode . "</cardCode>".
						 "</creditCard>".
					"</payment>".
					"</paymentProfile>".
					"<validationMode>liveMode</validationMode>". // or testMode
					"</createCustomerPaymentProfileRequest>";

				$response = pmprogateway_authorizenet_CIM::send_request_via_curl($host,$path,$content);
					list ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response) = pmprogateway_authorizenet_CIM::parse_create_payment_profile_response($response);

					if ($resultCode === "Ok") // payment profile successfully added
						{
							// save new payment profile info to user_meta
							$last4 = formatCreditCard(maskCreditCard($order->accountnumber));								
							$ifi_payment_profile_ids = get_user_meta($current_user->ID, "ifi_payment_profile_ids", true);
							if(is_array($ifi_payment_profile_ids))
								{
									if(!in_array($payment_profile_id, $ifi_payment_profile_ids) && strpos($order->accountnumber, 'X') === false) 
										{							
											$ifi_payment_profile_ids[] = array("payment_profile_id" => $payment_profile_id, "cardtype" => $order->cardtype, "accountnumber" => $last4, "expiration_month" => $expirationmonth, "expiration_year" => $expirationyear, "expiration_YdashM" => $order->ExpirationDate_YdashM, "street1" => $order->Address1, "street2" => $order->Address2, "city" => $city, "state" =>$state, "zip" => $zip, "country" =>$country, "phone" => $customer_phone);
										}
								}
							else
								{
									$ifi_payment_profile_ids = array(array("payment_profile_id" => $payment_profile_id, "cardtype" => $order->cardtype, "accountnumber" => $last4, "expiration_month" => $expirationmonth, "expiration_year" => $expirationyear, "expiration_YdashM" => $order->ExpirationDate_YdashM, "street1" => $order->Address1, "street2" => $order->Address2, "city" => $city, "state" =>$state, "zip" => $zip, "country" =>$country, "phone" => $customer_phone));
								}	
							$did_id = update_user_meta($current_user->ID, "ifi_payment_profile_ids", $ifi_payment_profile_ids);
							
							// update subscription in database
							// probably needs sql escaped
							$table_name = $wpdb->prefix . 'pmpro_ifi_subscriptions';
							$sqlQuery = "UPDATE $wpdb->pmpro_ifi_subscriptions SET payment_profile_id = '$payment_profile_id', billing_name='$firstName $lastName', billing_street='" . esc_sql($address) . "', billing_city='$city', billing_state='$state', billing_zip='$zip', billing_country='$country', cardtype='$cardType', accountnumber='$last4', expirationmonth='$expirationmonth', expirationyear='$expirationyear' WHERE subscription_transaction_id = '$order->subscription_transaction_id'";
							$results = $wpdb->query($sqlQuery);
								
							// check results of updating database
							if($results === false) //failed to update
								{
									$order->status = "error";
									$order->errorcode = "Update Error";
									$order->error = "Failed to update subscription in subscriptions table";
									$order->shorterror = "Failed to update subscription in subscriptions table";									
									return false;
								}
							else //success
								{
									$order->status = "updated";
									return true;
								}
							//return $order;
						}
					else // resultCode for adding new payment profile is NOT ok
						{
							$order->status = "error";
							$order->errorcode = $code;
							$order->error = $text;
							$order->shorterror = $text;	
							return false;
						}
						//return $order;
			} // end of adding a new card section
		} //end of update function
		
		
		// NOT USED NOW -- NEVER TESTED
		function cancel(&$order) // won't cancel ARB sub - will cancel the active sub in the subscriptions table
		{
			//define variables to send					
			if(!empty($order->subscription_transaction_id))
				$subscriptionId = $order->subscription_transaction_id;
			else
				$subscriptionId = "";
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");
		
			if(!empty($order->gateway_environment))
				$gateway_environment = $order->gateway_environment;
			else
				$gateway_environment = pmpro_getOption("gateway_environment");			
			
			if($gateway_environment == "live")
				$host = "api.authorize.net";		
			else
				$host = "apitest.authorize.net";		
			
			$path = "/xml/v1/request.api";
			
			$sub_status = "cancelled";
		
			if(!$subscriptionId || !$loginname || !$transactionkey)
				return false;
		
			/// build query to cancel
			$this->sqlQuery = "UPDATE $wpdb->pmpro_ifi_subscriptions
								SET `status` = '" . esc_sql($sub_status) . "',
								WHERE subscription_transaction_id = '" . esc_sql($subscription_transaction_id) . "'";
								
			
			if($wpdb->query($this->sqlQuery) !== false)
			{
				return true;
			}
			else
			{
				return false;
			}

		}	
		
		function getSubscriptionStatus(&$order) // will get status from subscriptions table
		{			
			//define variables to send					
			if(!empty($order->subscription_transaction_id))
				$subscriptionId = $order->subscription_transaction_id;
			else
				$subscriptionId = "";
			$loginname = pmpro_getOption("loginname");
			$transactionkey = pmpro_getOption("transactionkey");
		
			if(!empty($order->gateway_environment))
				$gateway_environment = $order->gateway_environment;
			else
				$gateway_environment = pmpro_getOption("gateway_environment");			
			
			if($gateway_environment == "live")
				$host = "api.authorize.net";		
			else
				$host = "apitest.authorize.net";	
			
			$path = "/xml/v1/request.api";
		
			if(!$subscriptionId || !$loginname || !$transactionkey)
				return false;
		
			// query to check sub_status
			$subscription_status = $wpdb->get_var( "SELECT status FROM $wpdb->pmpro_ifi_subscriptions WHERE subscription_transaction_id = $subscriptionId");
			
			return $subscription_status;
		}		
		
		//Authorize.net Function
		//function to send xml request via fsockopen
		function send_request_via_fsockopen($host,$path,$content)
		{
			$posturl = "ssl://" . $host;
			$header = "Host: $host\r\n";
			$header .= "User-Agent: PHP Script\r\n";
			$header .= "Content-Type: text/xml\r\n";
			$header .= "Content-Length: ".strlen($content)."\r\n";
			$header .= "Connection: close\r\n\r\n";
			$fp = fsockopen($posturl, 443, $errno, $errstr, 30);
			if (!$fp)
			{
				$response = false;
			}
			else
			{
				error_reporting(E_ERROR);
				fputs($fp, "POST $path  HTTP/1.1\r\n");
				fputs($fp, $header.$content);
				fwrite($fp, $out);
				$response = "";
				while (!feof($fp))
				{
					$response = $response . fgets($fp, 128);
				}
				fclose($fp);
				error_reporting(E_ALL ^ E_NOTICE);
			}
			return $response;
		}

		//Authorize.net Function
		//function to send xml request via curl
		function send_request_via_curl($host,$path,$content)
		{
			$posturl = "https://" . $host . $path;
			$posturl = apply_filters("pmpro_authorizenet_post_url", $posturl, pmpro_getOption("gateway_environment"));
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $posturl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			return $response;
		}


		//Authorize.net Function
		//function to parse Authorize.net response from ARB api
		function parse_return($content)
		{
			$refId = $this->substring_between($content,'<refId>','</refId>');
			$resultCode = $this->substring_between($content,'<resultCode>','</resultCode>');
			$code = $this->substring_between($content,'<code>','</code>');
			$text = $this->substring_between($content,'<text>','</text>');
			$subscriptionId = $this->substring_between($content,'<subscriptionId>','</subscriptionId>');
			return array ($refId, $resultCode, $code, $text, $subscriptionId);
		}
		
		// Authorize.net Function
		// helper function to parse response from CIM api
		function parse_CIM_customer_profile_response($content)
		{
			$refId = pmprogateway_authorizenet_CIM::substring_between($content, '<refId>','</refId>');
			$resultCode = pmprogateway_authorizenet_CIM::substring_between($content, '<resultCode>','</resultCode>');
			$code = pmprogateway_authorizenet_CIM::substring_between($content,'<code>','</code>');
			$text = pmprogateway_authorizenet_CIM::substring_between($content,'<text>','</text>');
			$CIM_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerProfileId>','</customerProfileId>');
			//$payment_profile_ids_list = pmprogateway_authorizenet_CIM::substring_between($content,'<customerPaymentProfileIdList>','</customerPaymentProfileIdList>');
			//$shipping_profiles_ids_list = pmprogateway_authorizenet_CIM::substring_between($content,'<customerShippingAddressIdList>','</customerShippingAddressIdList>');
			$payment_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerPaymentProfileIdList><numericString>','</numericString></customerPaymentProfileIdList>');
			$shipping_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerShippingAddressIdList><numericString>','</numericString></customerShippingAddressIdList>');
			$CIM_direct_response = pmprogateway_authorizenet_CIM::substring_between($content,'<directResponse>','</directResponse>');
			//$payment_profile_id = substring_between($content,'<customerPaymentProfileId>','</customerPaymentProfileId>');
			//$shipping_profile_id = substring_between($content,'<customerAddressId>','</customerAddressId>');
			return array ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response);
		}

		
		// Authorize.net Function
		// helper function to parse response from update CIM profile api
		function parse_CIM_update_customer_profile_response($content)
		{
			$refId = pmprogateway_authorizenet_CIM::substring_between($content, '<refId>','</refId>');
			$resultCode = pmprogateway_authorizenet_CIM::substring_between($content, '<resultCode>','</resultCode>');
			$code = pmprogateway_authorizenet_CIM::substring_between($content,'<code>','</code>');
			$text = pmprogateway_authorizenet_CIM::substring_between($content,'<text>','</text>');
			$CIM_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerProfileId>','</customerProfileId>');
			//$payment_profile_ids_list = pmprogateway_authorizenet_CIM::substring_between($content,'<customerPaymentProfileIdList>','</customerPaymentProfileIdList>');
			//$shipping_profiles_ids_list = pmprogateway_authorizenet_CIM::substring_between($content,'<customerShippingAddressIdList>','</customerShippingAddressIdList>');
			$payment_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerPaymentProfileIdList><numericString>','</numericString></customerPaymentProfileIdList>');
			$shipping_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerShippingAddressIdList><numericString>','</numericString></customerShippingAddressIdList>');
			$CIM_direct_response = pmprogateway_authorizenet_CIM::substring_between($content,'<directResponse>','</directResponse>');
			//$payment_profile_id = substring_between($content,'<customerPaymentProfileId>','</customerPaymentProfileId>');
			//$shipping_profile_id = substring_between($content,'<customerAddressId>','</customerAddressId>');
			return array ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response);
		}
		
		
		// Authorize.net Function
		// helper function to parse response from CIM api
		function parse_CIM_response2($content)
		{
			$refId = $this->substring_between($content, '<refId>','</refId>');
			$resultCode = $this->substring_between($content, '<resultCode>','</resultCode>');
			$code = $this->substring_between($content,'<code>','</code>');
			$text = $this->substring_between($content,'<text>','</text>');
			$CIM_profile_id = $this->substring_between($content,'<customerProfileId>','</customerProfileId>');
			//$payment_profile_ids_list = $this->substring_between($content,'<customerPaymentProfileIdList>','</customerPaymentProfileIdList>');
			//$shipping_profiles_ids_list = $this->substring_between($content,'<customerShippingAddressIdList>','</customerShippingAddressIdList>');
			$payment_profile_id = $this->substring_between($content,'<customerPaymentProfileIdList><numericString>','</numericString></customerPaymentProfileIdList>');
			$shipping_profile_id = $this->substring_between($content,'<customerShippingAddressIdList><numericString>','</numericString></customerShippingAddressIdList>');
			$CIM_direct_response = $this->substring_between($content,'<directResponse>','</directResponse>');
			//$payment_profile_id = $this->substring_between($content,'<customerPaymentProfileId>','</customerPaymentProfileId>');
			//$shipping_profile_id = $this->substring_between($content,'<customerAddressId>','</customerAddressId>');
			return array ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response);
		}
		
		function parse_create_payment_profile_response($content)
		{
			$refId = pmprogateway_authorizenet_CIM::substring_between($content, '<refId>','</refId>');
			$resultCode = pmprogateway_authorizenet_CIM::substring_between($content, '<resultCode>','</resultCode>');
			$code = pmprogateway_authorizenet_CIM::substring_between($content,'<code>','</code>');
			$text = pmprogateway_authorizenet_CIM::substring_between($content,'<text>','</text>');
			$CIM_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerProfileId>','</customerProfileId>');
			//$payment_profile_ids_list = pmprogateway_authorizenet_CIM::substring_between($content,'<customerPaymentProfileIdList>','</customerPaymentProfileIdList>');
			//$shipping_profiles_ids_list = pmprogateway_authorizenet_CIM::substring_between($content,'<customerShippingAddressIdList>','</customerShippingAddressIdList>');
			//$payment_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerPaymentProfileIdList><numericString>','</numericString></customerPaymentProfileIdList>');
			$shipping_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerShippingAddressIdList><numericString>','</numericString></customerShippingAddressIdList>');
			$CIM_direct_response = pmprogateway_authorizenet_CIM::substring_between($content,'<directResponse>','</directResponse>');
			$payment_profile_id = pmprogateway_authorizenet_CIM::substring_between($content,'<customerPaymentProfileId>','</customerPaymentProfileId>');
			//$shipping_profile_id = substring_between($content,'<customerAddressId>','</customerAddressId>');
			return array ($refId, $resultCode, $code, $text, $CIM_profile_id, $payment_profile_id, $shipping_profile_id, $CIM_direct_response);
		}
		
		
		//Authorize.net Function
		//helper function for parsing response from AIM api
		function substring_between($haystack,$start,$end) 
		{
			if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) 
			{
				return false;
			} 
			else 
			{
				$start_position = strpos($haystack,$start)+strlen($start);
				$end_position = strpos($haystack,$end);
				return substr($haystack,$start_position,$end_position-$start_position);
			}
		}
		
		
		/**
		 * Get a random code to use as the order code.
		 */
		function getRandomCode()
		{
			global $wpdb;

			while(empty($code))
			{

				$scramble = md5(AUTH_KEY . current_time('timestamp') . SECURE_AUTH_KEY);
				$code = substr($scramble, 0, 10);
				$code = apply_filters("pmpro_random_code", $code, $this);	//filter
				$check = $wpdb->get_var("SELECT id FROM $wpdb->pmpro_membership_orders WHERE code = '$code' LIMIT 1");
				if($check || is_numeric($code))
					$code = NULL;
			}

			return strtoupper($code);
		}
		
		
		/**
		 * Get the most recent order for a user.
		 *
		 * @param int $user_id ID of user to find order for.
		 * @param string $status Limit search to only orders with this status. Defaults to "success".
		 * @param id $membership_id Limit search to only orders for this membership level. Defaults to NULL to find orders for any level.
		 *
		 */
		
	}
