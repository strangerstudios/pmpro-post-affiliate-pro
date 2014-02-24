<?php
/*
Plugin Name: PMPro Post Affiliate Pro Integration
Plugin URI: http://www.paidmembershipspro.com/wp/pmpro-post-affiliate-pro/
Description: Process an affiliate via Post Affiliate Pro after a PMPro checkout.
Version: .2
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
		 
Paid Memberships Pro (http://wordpress.org/extend/plugins/paid-memberships-pro/) must be installed and activated. You will need a Post Affiliate Pro account.
*/
define('URL_TO_PAP', 'http://{account}.postaffiliatepro.com/');
define('PAP_LOGIN', '{email}');
define('PAP_PASS', '{pass}');
define('PAP_ACCOUNT', 'default1');

//login to API
function pap_login()
{
	require_once(dirname(__FILE__) . "/lib/PapApi.class.php");
	$session = new Gpf_Api_Session(URL_TO_PAP . "scripts/server.php");
	if(!$session->login(PAP_LOGIN, PAP_PASS)) {
		//if admin, show notice. else ignore
		if(current_user_can("manage_options"))
		{
		?>
			<p>ERROR: Can't authenticate with Post Affiliates Pro. Check that your PAP credentials are correct in the PMPro Post Affiliates Pro plugin and that the Post Affiliates Pro API is not experiencing an outage. Response from PAP: <?php echo $session->getMessage();?></p>
		<?php
		}
		else
		{	  
			//shhh... don't let users know
		}
	}
}

//function to save a sale with PAP
function pap_pmpro_track_sale($total, $orderid, $affiliate_code = NULL, $campaign_id = NULL, $channel_id = NULL, $visitor_id = NULL)
{
	pap_login();
	$saleTracker = new Pap_Api_SaleTracker(URL_TO_PAP . 'scripts/sale.php');
	$saleTracker->setAccountId(PAP_ACCOUNT);
	
	if(!empty($visitor_id))
		$saleTracker->setVisitorId($visitor_id);

	$sale1 = $saleTracker->createSale();	
	$sale1->setTotalCost($total);
	$sale1->setOrderID($orderid);
	$sale1->setProductID($membership_id);	
	
	if(!empty($affiliate_code))
		$sale1->setAffiliateID($affiliate_code);
	if(!empty($campaign_id))
		$sale1->setCampaignID($campaign_id);
	if(!empty($channel_id))
		$sale1->setChannelID($channel_id);
	
	try
	{
		$saleTracker->register();
	}
	catch(Exception $e)
	{				
		//die($e->getMessage);
		if(current_user_can("manage_options"))
		{
		?>
			<p>ERROR: <?php echo $e->getMessage();?></p>
		<?php
		}
		else
		{
			//shhhh... don't let normal users know
		}
	}	
}

//track sales after checkout
function pap_pmpro_after_checkout($user_id)
{	
	$morder = new MemberOrder();	
	$morder->getLastMemberOrder($user_id);
	
	if(empty($_COOKIE['pap_pmpro_affiliate']))
		return;
	
	$parts = explode(",", $_COOKIE['pap_pmpro_affiliate']);
	$affiliate_code = $parts[0];
	$campaign_id = $parts[1];
	$channel_id = $parts[2];
	$visitor_id = $parts[3];
	
	if(empty($visitor_id) && !empty($_COOKIE['PAPVisitorId']))
		$visitor_id = $_COOKIE['PAPVisitorId'];
		
	if(!empty($morder->total))
	{
		//api
		pap_pmpro_track_sale($morder->total, $morder->code, $affiliate_code, $campaign_id, $channel_id, $visitor_id);
		
		//save affiliate id in order
		$morder->affiliate_id = $affiliate_code;
		$morder->affiliate_subid = $campaign_id . "," . $channel_id . "," . $visitor_id;
		$morder->saveOrder();		
	}
}
add_action("pmpro_after_checkout", "pap_pmpro_after_checkout");

//track sales when orders are added
function pap_pmpro_add_order($morder)
{	
	if(!empty($morder->total))
	{			
		//need to get the last order before this
		$last_order = new MemberOrder();
		$last_order->getLastMemberOrder($morder->user_id);
				
		if(!empty($last_order->affiliate_id))
		{							
			$parts = explode(",", $last_order->affiliate_subid);
			$affiliate_code = $last_order->affiliate_id;
			$campaign_id = $parts[0];
			$channel_id = $parts[1];
			$visitor_id = $parts[2];
			
			//api
			pap_pmpro_track_sale($morder->total, $morder->code, $affiliate_code, $campaign_id, $channel_id, $visitor_id);
					
			//update the affiliate id for this order
			global $pap_pmpro_affiliate_id, $pap_pmpro_affiliate_subid;
			$pap_pmpro_affiliate_id = $affiliate_code;
			$pap_pmpro_affiliate_subid = $campaign_id . "," . $channel_id . "," . $visitor_id;
		}		
	}
}
add_action("pmpro_add_order", "pap_pmpro_add_order");

//save affiliate ids into orders
function pap_pmpro_added_order($morder)
{
	global $pap_pmpro_affiliate_id, $pap_pmpro_affiliate_subid;
		
	if(!empty($pap_pmpro_affiliate_id))
	{
		$morder->affiliate_id = $pap_pmpro_affiliate_id;
		$morder->affiliate_subid = $pap_pmpro_affiliate_subid;
		$morder->saveOrder();				
	}
}
add_action("pmpro_added_order", "pap_pmpro_added_order");

//show affiliate id on orders dashboard page
add_action("pmpro_orders_show_affiliate_ids", "__return_true");

//check for affiliate code
function pap_pmpro_wp_head()
{
	pap_login();
	
    // init session for PAP
    $session = new Gpf_Api_Session(URL_TO_PAP . "scripts/server.php");
    
    // register click
    $clickTracker = new Pap_Api_ClickTracker($session);

    $clickTracker->setAccountId(PAP_ACCOUNT);

    try {  
        $clickTracker->track();
        $clickTracker->saveCookies();                
    } catch (Exception $e) {
    	//stop here
    	
		//die($e->getMessage);
		if(current_user_can("manage_options"))
		{
		?>
			<p>ERROR: <?php echo $e->getMessage();?></p>
		<?php
		}
		else
		{
			//shhhh... don't let normal users know
		}
				
    	return;   	
    }
    
    //save some values from the click tracker for later
	if($clickTracker->getAffiliate())
	{
		$affiliate_id = $clickTracker->getAffiliate()->getValue('refid');
			
		if($clickTracker->getCampaign())
			$campaign_id = $clickTracker->getCampaign()->getValue('campaignid');
		else
			$campaign_id = "";
		
		if($clickTracker->getChannel())
			$channel_id = $clickTracker->getChannel()->getValue('channelid');
		else
			$channel_id = "";
		
		if(!empty($clickTracker->visitorId))
			$visitor_id = $clickTracker->visitorId;
		else
			$visitor_id = "";
		
		$pap_pmpro_affiliate =
				$affiliate_id . "," . $campaign_id . "," . $channel_id . "," . $visitor_id;
			
		//save our cookies	
		$cookielength = 90;
		?>
			<script>
				var today = new Date();
				today.setTime( today.getTime() );
				var expires = <?php echo intval($cookielength); ?> * 1000 * 60 * 60 * 24;
				var expires_date = new Date( today.getTime() + (expires) );
				document.cookie = 'pap_pmpro_affiliate=<?php echo $pap_pmpro_affiliate; ?>;path=/;expires=' + expires_date.toGMTString() + ';';		    
			</script>
		<?php
	}
}
add_action("wp_head", "pap_pmpro_wp_head");
