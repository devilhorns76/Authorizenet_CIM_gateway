<?php
	global $wpdb, $pmpro_msg, $pmpro_msgt, $pmpro_levels, $current_user, $levels;

	//if a member is logged in, show them some info here (1. past invoices. 2. billing information with button to update.)
	if($current_user->membership_level->ID)
	{
		$ssorder = new MemberOrder();
		$ssorder->getLastMemberOrder();
		$invoices = $wpdb->get_results("SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp FROM $wpdb->pmpro_membership_orders WHERE user_id = '$current_user->ID' ORDER BY timestamp DESC LIMIT 6");	
		$ifi_invoices = get_user_meta($current_user->ID, "ifi_customer_invoices", true);
		$all_ifi_subs = $wpdb->get_results("SELECT * FROM $wpdb->pmpro_ifi_subscriptions WHERE type= 'subscription' AND user_id = '$current_user->ID'", ARRAY_A);
		$all_ifi_payplans = $wpdb->get_results("SELECT * FROM $wpdb->pmpro_ifi_subscriptions WHERE type= 'payment plan' AND user_id = '$current_user->ID'", ARRAY_A);
		?>	
	<div id="pmpro_account">		
				<div id="pmpro_account-profile" class="pmpro_box">	
			<?php get_currentuserinfo(); ?> 
			<h3><?php _e("My Account", "pmpro");?></h3>
			<?php if($current_user->user_firstname) { ?>
				<ul class="pmpro_account">
					<li><strong>Name:  </strong><?php echo $current_user->user_firstname?> <?php echo $current_user->user_lastname?></li>
					<li><strong>Membership Level:  </strong><?php echo $current_user->membership_level->name?></li>
				</ul>
			<?php } ?>
			<ul class="pmpro_account">
				<li><strong><?php _e("Username", "pmpro");?>:</strong> <?php echo $current_user->user_login?></li>
				<li><strong><?php _e("Email", "pmpro");?>:</strong> <?php echo $current_user->user_email?></li>
			</ul>
			<div class="pmpro_actionlinks_details">
				<a href="<?php echo site_url('your-profile/'); ?>"><?php _e("Edit Profile", "pmpro");?></a> | <a href="<?php echo site_url('your-profile/'); ?>"><?php _e('Change Password', 'pmpro');?></a>
			</div>
		</div> <!-- end pmpro_account-profile -->
		
		<!-- begin Member Links area -->
		<?php if(has_filter('pmpro_member_links_top') || has_filter('pmpro_member_links_bottom')) { ?>
		<div id="pmpro_account-links" class="pmpro_box">
			<h3><?php _e("Member Access Links", "pmpro");?></h3>
			<ul>
				<?php do_action("pmpro_member_links_top");?>				
				<?php do_action("pmpro_member_links_bottom");?>
			</ul>
		</div> <!-- end pmpro_account-links -->		
		<?php } ?>
		<!-- end Member Links area -->
		
		<!-- begin My Payment Plans area -->
		<div id="pmpro_account-membership2" class="pmpro_box" <?php if(empty($all_ifi_payplans)){?>style="display:none;"<?php }?>>
			
			<h3><?php _e("My Payment Plans", "pmpro");?></h3>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th><?php _e("Payment Plan Name", "pmpro");?></th>
						<th><?php _e("Billing Cycle", "pmpro"); ?></th>
						<th style="padding-bottom:20px;"><?php _e("Update Billing", "pmpro"); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						//TODO: v2.0 will loop through levels here
						$level = $current_user->membership_level;
						$ifi_lids = get_user_meta($current_user->ID, "ifi_membership_levels", true);
						if(is_array($ifi_lids))
						{
							foreach($ifi_lids as $ifi_lid)
							{
							foreach($ifi_lid as $key=>$value)
							{
								$$key = $value;
							}
							$ifi_level_info = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = $lid",ARRAY_A); // returns level info as array
								foreach($ifi_level_info as $key=>$value)
									{ 
										$$key = $value;  // gives me the actual LEVEL id, name, description, $billing_amount, etc for this lid
									}
								if($billing_amount > 0 && $billing_limit > 0)
								{
								?>
								<tr>
									<td class="pmpro_account-membership-levelname">
										<?php echo $name?>
										<!-- action links were here-->
									</td>
									<td class="pmpro_account-membership-levelfee">
										<?php if($cycle_number > 1) {
											printf(__('%s every %d %s', 'pmpro'), pmpro_formatPrice($billing_amount), $cycle_number, pmpro_translate_billing_period($cycle_period, $cycle_number));
										} elseif($cycle_number == 1) {
											printf(__('%s per %s', 'pmpro'), pmpro_formatPrice($billing_amount), pmpro_translate_billing_period($cycle_period));
										} else {
											echo pmpro_formatPrice($billing_amount);
										}?>
									</td>
									<td class="pmpro_account-membership-expiration">
									<?php 
										//if($enddate) 
										//	echo date(get_option('date_format'), $enddate);
										//else
										//	echo "---";
									?>
										<div class="pmpro_actionlinks_ifisubs">
											<?php do_action("pmpro_member_action_links_before"); ?>
											<!--<a href="<?php echo pmpro_url("checkout", "?level=" . $current_user->membership_level->id . "&lid=" . $lid, "https")?>" style="display:none;"><?php _e("Renew", "pmpro");?></a> -->
											
											<?php if((isset($ssorder->status) && $ssorder->status == "success") && (isset($ssorder->gateway) && in_array($ssorder->gateway, array("authorizenet_CIM", "authorizenet", "paypal", "stripe", "braintree", "payflow", "cybersource")))) { ?>
												<a href="<?php echo pmpro_url("billing", "?lid=" .$lid, "https")?>"><?php _e("Update Billing Info", "pmpro"); ?></a>
											<?php } ?>
											
											<?php 
												//To do: Only show CHANGE link if this level is in a group that has upgrade/downgrade rules
												//if(count($pmpro_levels) > 1 && !defined("PMPRO_DEFAULT_LEVEL")) { ?>
												<!--<a href="<?php echo pmpro_url("levels")?>" style="display:none;"><?php _e("Change", "pmpro");?></a>-->
											<?php //} ?>
											<!--<a href="<?php echo pmpro_url("cancel", "?level=" . $current_user->membership_level->id . "&lid=" . $lid)?>" style="display:none;"><?php _e("Cancel", "pmpro");?></a>-->
											<?php do_action("pmpro_member_action_links_after"); ?>
										</div> <!-- end pmpro_actionlinks -->
									</td>
								</tr> <!-- looping code will end here -->
								<?php
								}
							}
						}
					?>
				</tbody>
			</table>
			<?php //Todo: If there are multiple levels defined that aren't all in the same group defined as upgrades/downgrades ?>
			<div class="pmpro_actionlinks_options" style="display:none;">
				<a href="<?php echo pmpro_url("levels")?>"><?php _e("View all Membership Options", "pmpro");?></a>
			</div>
			<?php
			//}}
			?>
		</div>
		<!-- end My Payment Plans area -->
		
		<!-- begin My Subscriptions area -->
		<div id="pmpro_account-membership" class="pmpro_box" <?php if(empty($all_ifi_subs)){?>style="display:none;"<?php }?>>
			<h3><?php _e("My Subscriptions", "pmpro");?></h3>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th><?php _e("Subscription Name", "pmpro");?></th>
						<th><?php _e("Billing Cycle", "pmpro"); ?></th>
						<th style="padding-bottom:20px;"><?php _e("Update Billing", "pmpro"); ?></th>
					</tr>
				</thead>
				<tbody>
				
					<?php
						//TODO: v2.0 will loop through levels here
						$level = $current_user->membership_level;
						$ifi_lids = get_user_meta($current_user->ID, "ifi_membership_levels", true);
						if(is_array($ifi_lids))
						{
							foreach($ifi_lids as $ifi_lid)
							{
							foreach($ifi_lid as $key=>$value)
								{
								$$key = $value;
								}
							$ifi_level_info = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = $lid",ARRAY_A); // returns level info as array
								foreach($ifi_level_info as $key=>$value)
									{ 
										$$key = $value;  // gives me the actual LEVEL id, name, description, $billing_amount, etc for this lid
									}
								if($billing_amount > 0 && $billing_limit < 1)
								{
								?>
								<tr>
									<td class="pmpro_account-membership-levelname">
										<?php echo $name?>
										<!-- action links were here-->
									</td>
									<td class="pmpro_account-membership-levelfee">
										<?php if($cycle_number > 1) {
											printf(__('%s every %d %s', 'pmpro'), pmpro_formatPrice($billing_amount), $cycle_number, pmpro_translate_billing_period($cycle_period, $cycle_number));
										} elseif($cycle_number == 1) {
											printf(__('%s per %s', 'pmpro'), pmpro_formatPrice($billing_amount), pmpro_translate_billing_period($cycle_period));
										} else {
											echo pmpro_formatPrice($billing_amount);
										}?>
									</td>
									<td class="pmpro_account-membership-expiration">
									<?php 
										//if($enddate) 
										//	echo date(get_option('date_format'), $enddate);
										//else
										//	echo "---";
									?>
										<div class="pmpro_actionlinks_ifisubs">
											<?php do_action("pmpro_member_action_links_before"); ?>
											<!--<a href="<?php echo pmpro_url("checkout", "?level=" . $current_user->membership_level->id . "&lid=" . $lid, "https")?>" style="display:none;"><?php _e("Renew", "pmpro");?></a> -->
											
											<?php if((isset($ssorder->status) && $ssorder->status == "success") && (isset($ssorder->gateway) && in_array($ssorder->gateway, array("authorizenet_CIM", "authorizenet", "paypal", "stripe", "braintree", "payflow", "cybersource")))) { ?>
												<a href="<?php echo pmpro_url("billing", "?lid=" .$lid, "https")?>"><?php _e("Update Billing Info", "pmpro"); ?></a>
											<?php } ?>
											
											<?php 
												//To do: Only show CHANGE link if this level is in a group that has upgrade/downgrade rules
												//if(count($pmpro_levels) > 1 && !defined("PMPRO_DEFAULT_LEVEL")) { ?>
												<!--<a href="<?php echo pmpro_url("levels")?>" style="display:none;"><?php _e("Change", "pmpro");?></a>-->
											<?php //} ?>
											<!--<a href="<?php echo pmpro_url("cancel", "?level=" . $current_user->membership_level->id . "&lid=" . $lid)?>" style="display:none;"><?php _e("Cancel", "pmpro");?></a>-->
											<?php do_action("pmpro_member_action_links_after"); ?>
										</div> <!-- end pmpro_actionlinks -->
									</td>
								</tr> <!-- looping code will end here -->
								<?php
								}
							}
						}
					?>
				</tbody>
			</table>
			<?php //Todo: If there are multiple levels defined that aren't all in the same group defined as upgrades/downgrades ?>
			<div class="pmpro_actionlinks_options" style="display:none;">
				<a href="<?php echo pmpro_url("levels")?>"><?php _e("View all Membership Options", "pmpro");?></a>
			</div>
		</div>
		<!-- -- end My Subscriptions area -->

		<!-- begin Invoices section -->
		<?php if(!empty($invoices)) { ?>
		<div id="pmpro_account-invoices" class="pmpro_box">
			<h3><?php _e("My Invoices", "pmpro");?></h3>
			<table class="pmpro_invoice" width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th class="pmpro_invoice"><?php _e("Date", "pmpro"); ?></th>
						<th><?php _e("Purchase", "pmpro"); ?></th>
						<th><?php _e("Amount", "pmpro"); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$count = 0;
					foreach($invoices as $invoice) 
					{ 
						if($count++ > 4)
							break;

						//get an member order object
						$invoice_id = $invoice->id;
						$invoice = new MemberOrder;
						$invoice->getMemberOrderByID($invoice_id);
						$invoice->getMembershipLevel();	
						
						// look in the array to find customer invoice with same invoice_id and get lid
						$lid_array = search($ifi_invoices, 'invoice_id', $invoice->code);

						foreach($lid_array as $ifi_item)
						foreach($ifi_item as $key=>$value){ 
						$$key = $value;
						}
						// call db to lookup name of $lid level
						$ifi_level_name = $wpdb->get_var("SELECT name FROM $wpdb->pmpro_membership_levels WHERE id = $lid",0,0); // returns level name as variable
						?>
						<tr id="pmpro_account-invoice-<?php echo $invoice->code; ?>">
							<td class="pmpro_invoice"><a href="<?php echo pmpro_url("invoice", "?invoice=" . $invoice->code)?>"><?php echo date(get_option("date_format"), $invoice->timestamp)?></td>
							<td class="pmpro_invoice"><?php echo $ifi_level_name?></td>
							<td class="pmpro_invoice"><?php echo pmpro_formatPrice($invoice->total)?></td>
						</tr>
						<?php 
					}
				?>
				</tbody>
			</table>						
			<?php if($count == 6) { ?>
				<div class="pmpro_actionlinks_invoice"><a href="<?php echo pmpro_url("invoice"); ?>"><?php _e("View All Invoices", "pmpro");?></a></div>
			<?php } ?>
		</div> <!-- end pmpro_account-invoices -->
		<?php } ?>
	</div> <!-- end pmpro_account -->		
	<?php } ?>
