<?php 
	global $wpdb, $current_user, $pmpro_invoice, $pmpro_msg, $pmpro_msgt;
	
	$lid = $_REQUEST['lid'];
	if(empty($lid))
		// should probably get last invoice here in case someone orders a 
		// product they've already ordered before - because then it shows
		// them the product they bought on last order, not this one.
		$ifi_invoices = get_user_meta($current_user->ID, "ifi_customer_invoices", true);  // returns all ifi invoices as array
		// look in the array to find customer invoice with same invoice_id and get the
		// lid from that array - this gets the lid based on what they just bought
		$lid_array = search($ifi_invoices, 'invoice_id', $pmpro_invoice->code); // find the lid in the array that has the same invoice number
		foreach($lid_array as $ifi_item)
		foreach($ifi_item as $key=>$value){ 
			$$key = $value;  // gives me $lid
			}

	$level_lid_info = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = $lid",ARRAY_A); // returns level info as array
		foreach($level_lid_info as $key=>$value) { 
			$$key = $value;  // gives me all keys as variables - $id, $name, $description, $confirmation, $initial_payment, $billing_amount, etc.
			}
	
	if($pmpro_msg)
	{
	?>
		<div class="pmpro_message <?php echo $pmpro_msgt?>"><?php echo $pmpro_msg?></div>
	<?php
	}
	
	if(empty($current_user->membership_level))
		$confirmation_message = "<p>" . __('Your payment has been submitted. Your membership will be activated shortly.', 'pmpro') . "</p>";
	else
		$confirmation_message = "<div style=\"margin: 30px auto 40px;\"><h1 style=\"text-align:center;\">" . sprintf(__('Awesome... It worked!</h1><p>Thank you for ordering %s.', 'pmpro'), $name) . "</p>";		
	
	//confirmation message for this level
		// if level is 1, do this
	if(!empty($current_user->membership_level) && $current_user->membership_level == 1)
		$level_message = $wpdb->get_var("SELECT confirmation FROM $wpdb->pmpro_membership_levels WHERE id = $lid");
		// else, do this
	else
		$level_message = $wpdb->get_var("SELECT l.confirmation FROM $wpdb->pmpro_membership_levels l LEFT JOIN $wpdb->pmpro_memberships_users mu ON l.id = mu.membership_id WHERE mu.status = 'active' AND mu.user_id = '" . $current_user->ID . "' LIMIT 1");
	if(!empty($level_message))
		$confirmation_message .= "\n";
?>	

<?php if(!empty($pmpro_invoice)) { ?>		
	
	<?php
		$pmpro_invoice->getUser();
		$pmpro_invoice->getMembershipLevel();			
				
		$confirmation_message .= "<p>" . sprintf(__('Below are details about your purchase, and a receipt for your records. &nbsp;A welcome email with a copy of your initial invoice has been sent to you at %s.', 'pmpro'), $pmpro_invoice->user->user_email) . "</p></div>" . stripslashes($level_message) . "\n";
		
		//check instructions		
		if($pmpro_invoice->gateway == "check" && !pmpro_isLevelFree($pmpro_invoice->membership_level))
			$confirmation_message .= wpautop(pmpro_getOption("instructions"));
		
		$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, $pmpro_invoice);				
		
		echo apply_filters("the_content", $confirmation_message);		
	?>
	
	<div style="width:700px; margin: 20px auto 0; position:relative;">
	<h3 style="font-size:18pt;">
		<?php printf(__('Invoice #%s on %s', 'pmpro'), $pmpro_invoice->code, date_i18n(get_option('date_format'), $pmpro_invoice->timestamp));?>		
	</h3>

	<ul style="padding-left:30px;">
		<?php do_action("pmpro_invoice_bullets_top", $pmpro_invoice); ?>
		<li><strong><?php _e('Account', 'pmpro');?>:</strong> <?php echo $current_user->display_name?> (<?php echo $current_user->user_email?>)</li>
		<li><strong><?php _e('Access Level', 'pmpro');?>:</strong> <?php echo $current_user->membership_level->name?></li>
		<?php if($current_user->membership_level->enddate) { ?>
			<li><strong><?php _e('Membership Expires', 'pmpro');?>:</strong> <?php echo date(get_option('date_format'), $current_user->membership_level->enddate)?></li>
		<?php } ?>
		<?php if($pmpro_invoice->getDiscountCode()) { ?>
			<li><strong><?php _e('Discount Code', 'pmpro');?>:</strong> <?php echo $pmpro_invoice->discount_code->code?></li>
		<?php } ?>
		<li><strong><?php _e('Purchase', 'pmpro');?>:</strong> <?php echo $name?></li>
		<?php do_action("pmpro_invoice_bullets_bottom", $pmpro_invoice); ?>
	</ul>

	<div style="position:absolute; top:0px; right:0px;"><a class="pmpro_a-print" href="javascript:window.print()"><?php _e('Print', 'pmpro');?></a>
	</div>	</div>
	
	<table id="pmpro_confirmation_table" class="pmpro_invoice"  style="margin-top:50px; margin-left:-20px;" width="100%" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<?php if(!empty($pmpro_invoice->billing->name)) { ?>
				<th><?php _e('Billing Address', 'pmpro');?></th>
				<?php } ?>
				<th><?php _e('Payment Method', 'pmpro');?></th>
				<th><?php _e('Purchase', 'pmpro');?></th>
				<th><?php _e('Total Billed', 'pmpro');?></th>
			</tr>
		</thead>
		<tbody>
			<tr style="font-size:.8em;text-align:center;">
				<?php if(!empty($pmpro_invoice->billing->name)) { ?>
				<td style="width:30%">
					<?php echo $pmpro_invoice->billing->name?><br />
					<?php echo $pmpro_invoice->billing->street?><br />						
					<?php if($pmpro_invoice->billing->city && $pmpro_invoice->billing->state) { ?>
						<?php echo $pmpro_invoice->billing->city?>, <?php echo $pmpro_invoice->billing->state?> <?php echo $pmpro_invoice->billing->zip?> <?php echo $pmpro_invoice->billing->country?><br />												
					<?php } ?>
					<?php echo formatPhone($pmpro_invoice->billing->phone)?>
				</td>
				<?php } ?>
				<td style="width:25%;">
					<?php if($pmpro_invoice->accountnumber) { ?>
						<?php echo $pmpro_invoice->cardtype?> <?php _e('ending in', 'pmpro');?> <?php echo last4($pmpro_invoice->accountnumber)?><br />
						<small><?php _e('Expiration', 'pmpro');?>: <?php echo $pmpro_invoice->expirationmonth?>/<?php echo $pmpro_invoice->expirationyear?></small>
					<?php } elseif($pmpro_invoice->payment_type) { ?>
						<?php echo $pmpro_invoice->payment_type?>
					<?php } ?>
				</td>
				<td style="width:25%;"><?php echo $name?></td>					
				<td style="width:20%;"><?php if($pmpro_invoice->total) echo pmpro_formatPrice($pmpro_invoice->total); else echo "---";?></td>
			</tr>
		</tbody>
	</table>
<?php 
	} 
	else 
	{
		$confirmation_message .= "<p>" . sprintf(__('Below are details about your purchase.  A welcome email has been sent to %s.', 'pmpro'), $current_user->user_email) . "</p>";
		
		$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, false);
		
		echo $confirmation_message;
	?>	
	<ul>
		<li><strong><?php _e('Account', 'pmpro');?>:</strong> <?php echo $current_user->display_name?> (<?php echo $current_user->user_email?>)</li>
		<li><strong><?php _e('Access Level', 'pmpro');?>:</strong> <?php if(!empty($current_user->membership_level)) echo $current_user->membership_level->name; else _ex("Pending", "User without membership is in {pending} status.", "pmpro");?></li>
	</ul>	
<?php 
	} 
?>  
<nav id="nav-below" class="navigation" role="navigation">
	<div class="nav-next alignright" style="padding-bottom:30px;padding-top:16px;">
		<?php if(!empty($current_user->membership_level)) { ?>
			<a href="<?php echo pmpro_url("account")?>"><?php _e('View Your Account &rarr;', 'pmpro');?></a>
		<?php } else { ?>
			<?php _e('If your account is not activated within a few minutes, please contact us at support [at] internetfitnessincome.com.', 'pmpro');?>
		<?php } ?>
	</div>
</nav>
