<?php
/*
Plugin Name: CM Newsletter
Plugin URI: http://rapprich.com/wordpress/cm-newsletter
Description: Create a front-end page for your Campaign Monitor newsletter subscribers.
Version: 0.9
Author: Erik Rapprich
Author URI: http://rapprich.com
*/


/*  
	Copyright 2009  Erik Rapprich  (email : erik@rapprich.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
    
    
    ### CREDITS ###
    Icon in main heading from function social icon set (http://www.wefunction.com)
    Jquery Tabs & Accordion from http://www.sohtanaka.com/
*/


define('cmnewsletterPATH',WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
define('Version',"0.9");
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'cmnewsletter', 'wp-content/plugins/'.$plugin_dir.'/lang', $plugin_dir.'/lang' ); 
$adminOptionsName = "cmNewsletterAdminOption";
add_shortcode('cmnewsletter', 'cmnewsletter_active_shortcode');
function cmnewsletter_active_shortcode($atts) {
	return cmn_display_NewsletterPage($atts);
}

//Returns an array of admin options
function cmnewsletter_getAdminOptions() {
	
	$devOptions = get_option($adminOptionsName);
	if (!empty($devOptions)) {
		foreach ($devOptions as $key => $option)
			$devcmnewsletterAdminOptions[$key] = $option;
	}
	return $devcmnewsletterAdminOptions;
}




### Function: Display Pages
function cmn_display_NewsletterPage($atts) {
	wp_register_style('cmn', cmnewsletterPATH.'css/style.css', false, Version, 'all');
	wp_print_styles('cmn');
	
	if(get_option('cmn_cssedit_display') != ''){ ?>
	  <style type="text/css">
	  <?php echo get_option('cmn_additionalcss');?>
	  </style>
	 <?php }	
	if(get_option('cmn_useajax') != ''){ ?>
	  <script type="text/javascript">
	 	function checkEmail(email)
	{	
		var pattern = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var emailVal = $("#" + email).val();
		return pattern.test(emailVal);
	}
	$(function()
	{
		$("#subForm input:submit").click(function() {	
			
			// First, disable the form from submitting
			$('form#subForm').submit(function() { return false; });
			
			// Grab form action
			var formAction = $("form#subForm").attr("action");
			
			// Hacking together id for email field
			// Replace the xxxxx below:
			// If your form action were http://mysiteaddress.createsend.com/t/r/s/abcde/, then you'd enter "abcde" below
			var id = "xxxxx";
			var emailId = id + "-" + id;
			
			// Validate email address with regex
			if (!checkEmail(emailId)) 
			{
				alert("Please enter a valid email address");
				return;
			}
			
			// Serialize form values to be submitted with POST
			var str = $("form#subForm").serialize();
			
			// Add form action to end of serialized data
			var serialized = str + "&action=" + formAction;
			
			// Submit the form via ajax
			$.ajax({
				url: "<?php bloginfo( 'siteurl' ); ?>/wp-content/plugins/cm-newsletter/ajax.php",
				type: "POST",
				data: serialized,
				success: function(data){
					// Server-side validation
					if (data.search(/invalid/i) != -1) {
						alert('The email address you supplied is invalid and needs to be fixed before you can subscribe to this list.');
					}
					else
					{
						$("#theForm").hide(); // If successfully submitted hides the form
						$("#confirmation").slideDown("slow");  // Shows "Thanks for subscribing" div
						$("#confirmation").tabIndex = -1;
						$("#confirmation").focus(); // For screen reader accessibility
					}
				}
			});
		});
	});
	  </script>
	   <?php }
	   
			if(!isset($_REQUEST['cmn'])){
				
				?>
		
<?php echo get_option('cmn_subscribeform_before'); ?>
<?php if(get_option('cmn_useajax') != ''){ ?><div id="theForm">
<!-- ////////////////////////////////////////////////
	Paste your subscribe form below this line
/////////////////////////////////////////////////////-->
<form action="http://YOURSITEADDRESS.createsend.com/t/r/s/xxxxx/" method="post" id="subForm">
<div>
<label for="name">Name:</label><br /><input type="text" name="cm-name" id="name" /><br />
<label for="xxxxx-xxxxx">Email:</label><br /><input type="text" name="cm-xxxxx-xxxxx" id="xxxxx-xxxxx" /><br />

<input type="submit" value="Subscribe" />
</div>
</form>
<!-- ////////////////////////////////////////////////
	Your subscribe form should be pasted above
/////////////////////////////////////////////////////-->
</div>
<div id="confirmation" style="display: none;"><?php echo get_option('cmn_confirm1'); ?></div>
<?php } else {
 echo get_option('cmn_subscribeform'); 
}?>
<?php echo get_option('cmn_subscribeform_after'); ?>

 <?php if(get_option('cmn_unsubscribe_display') != ''){ echo '<a href="?cmn=unsubscribe-form">Unsubscribe from this list</a>'; }?>

				
				
				
				<div style="float:left;width:70%;display:none;">		<h3 style="width:100%;border-bottom:1px solid #ddd;">Archives</h3>
					
					<script type="text/javascript" src="http://accounts.ethosmail.com/t/y/p/kdhjj/0/1/1/1/1/"></script></div>

	
			</div>
					<!-- CSS -->
					
					
					<?php } else { 
				
				if($_REQUEST['cmn'] == 'confirm1'){
					
			?>
			<?php echo get_option('cmn_confirm1'); ?>			
			
			<?php
				} elseif($_REQUEST['cmn'] == 'confirm2'){
			?>
			
			<?php echo get_option('cmn_confirm2'); ?>			
			
			
			<?php 		} elseif($_REQUEST['cmn'] == 'unsubscribe'){
				?>
				
					<?php echo get_option('cmn_confirm2'); ?>				<?php 		} elseif($_REQUEST['action'] == 'unsubscribe-form'){
				?>
				
								<?php echo get_option('cmn_unsubscribeform_before'); ?>
								<?php echo get_option('cmn_unsubscribeform'); ?>
								<?php echo get_option('cmn_unsubscribeform_after'); ?>
				<? }
				
			

				}
		
	}
add_action( 'admin_init', 'register_cmn_settings' );
## CM Newsletter Options Page

function register_cmn_settings() {
	//register our settings
	register_setting( 'cmn-settings-group', 'cmn_subscribeform' );
	register_setting( 'cmn-settings-group', 'cmn_subscribeform_before' );
	register_setting( 'cmn-settings-group', 'cmn_subscribeform_after' );
	
	register_setting( 'cmn-settings-group', 'cmn_unsubscribeform' );
	register_setting( 'cmn-settings-group', 'cmn_unsubscribeform_before' );
	register_setting( 'cmn-settings-group', 'cmn_unsubscribeform_after' );



	register_setting( 'cmn-settings-group', 'cmn_confirm1' );
	register_setting( 'cmn-settings-group', 'cmn_confirm1_2' );
	register_setting( 'cmn-settings-group', 'cmn_confirm2' );
	
	
	
	
	register_setting( 'cmn-settings-group', 'cmn_opt_status' );
		register_setting( 'cmn-settings-group', 'cmn_unsubscribe_display' );
		register_setting( 'cmn-settings-group', 'cmn_cssedit_display' );
		register_setting( 'cmn-settings-group', 'cmn_additionalcss' );	
		register_setting( 'cmn-settings-group', 'cmn_useajax' );	
		

}


###  CM NEWSLETTER OPTIONS PAGE ###
function cmn_settings_page() {
?>


<link rel="stylesheet" href="<?php bloginfo( 'siteurl' ); ?>/wp-content/plugins/cm-newsletter/css/structure.css" type="text/css" />
<link rel="stylesheet" href="<?php bloginfo( 'siteurl' ); ?>/wp-content/plugins/cm-newsletter/css/form.css" type="text/css" />
<link rel="stylesheet" href="<?php bloginfo( 'siteurl' ); ?>/wp-content/plugins/cm-newsletter/css/style.css" type="text/css" />

<script type="text/javascript"
src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'siteurl' ); ?>/wp-content/plugins/cm-newsletter/js/wufoo.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
		
		
		
	});
	
		//Default Action
	$(".tab_content2").hide(); //Hide all content
	$("ul.tabs2 li:first").addClass("active").show(); //Activate first tab
	$(".tab_content2:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs2 li").click(function() {
		$("ul.tabs2 li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content2").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
		
		
		
	});

$(".toggle_container").hide();

	$("h2.trigger").toggle(function(){
		$(this).addClass("active"); 
		}, function () {
		$(this).removeClass("active");
	});
	
	$("h2.trigger").click(function(){
		$(this).next(".toggle_container").slideToggle("slow,");
	});

});
</script>
<div class="wrap">
<h2 style="border-bottom:1px solid #bdbdbd;margin-bottom:20px;"><big><em><img src="/wp-content/plugins/cm-newsletter/images/email.png" class="headeriamge"/> CM-Newsletter Options</em></big></h2><div style="float:right;width:15%;margin-top:0;padding:10px;border:1px solid #bdbdbd;text-align:left;"><h3 style="margin-top:5px;"><em>Relevant Links:</em></h3>
- <a href="http://www.rapprich.com/wordpress/cm-newsletter">Plugin Page</a><br />
- <a href="http://wordpress.org/tags/cm-newsletter">Support Forum</a><br />
- <a href="http://www.campaignmonitor.com">Author's Blog</a><br />
- <a href="http://www.rapprich.com/wordpress/cm-newsletter#credits">Credits</a><br />
<h3 style="margin-top:15px;border-top:1px solid #bdbdbd;padding-top:15px;"><em>Works with:</em></h3>

- <a href="http://www.campaignmonitor.com">Campaign Monitor</a><br />
- <a href="http://www.ethosmail.com">EthosMail</a><br />
<h3 style="margin-top:15px;border-top:1px solid #bdbdbd;padding-top:15px;width:100%;text-align:center;"><em><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="446DFPNDQPNF4">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</em></h3><h3 style="margin-top:15px;border-top:1px solid #bdbdbd;padding-top:15px;text-align:left;"><em>Plugin Updates</em><a href="http://www.rapprich.com/tag/cm-newsletter/feed/"><img src="/wp-content/plugins/cm-newsletter/images/rss.png" style="float:right;width:15px;" /></a></h3><div style="width:100%;text-align:left;">
<?php
if ( function_exists( 'fetch_feed' ) ) {

// Next, Locate feed.php inside of your WP-INCLUDES folder
include_once( ABSPATH . WPINC . '/feed.php' );
$feed = fetch_feed('feed://www.rapprich.com/tag/cm-newsletter/feed/'); // specify the source feed
$limit = $feed->get_item_quantity( 3 ); // specify number of items
$items = $feed->get_items( 0, $limit ); // create an array of items

}
if ($limit == 0) echo '<div>The feed is either empty or unavailable.</div>';
else foreach ($items as $item) : ?>

<div>
- <a href="<?php echo $item->get_permalink(); ?>"
title="<?php echo $item->get_date('j F Y @ g:i a'); ?>">
<?php echo $item->get_title(); ?>
</a>
</div>


<?php endforeach; ?>
</div>

</div>
<div class="cmn-container">
<ul class="tabs">
        <li style="border-left:1px solid #bdbdbd;margin-left:-1px;"><a href="#tab1">General</a></li>
        <li><a href="#tab2">Subscribe</a></li>
      <?php if(get_option('cmn_unsubscribe_display') != ''){ echo ' <li><a href="#tab3">Unsubscribe</a></li>'; }?> 
        <li><a href="#tab4">Messages</a></li>
        <?php if(get_option('cmn_cssedit_display') != ''){ echo ' <li><a href="#tab5">Style</a></li>'; }?> 
        <li id="wizardtab"><a  href="#tab6">Support</a></li>
    </ul>
 <form method="post" class="cmn-form wufoo" action="options.php">
    <?php settings_fields( 'cmn-settings-group' ); ?>

    
    <div class="tab_container">
        
    <div id="tab1" class="tab_content">
           <h3>General Settings</h3>
<p>To transform any page into your newsletter page, just add the shortcode <strong>[cmnewsletter]</strong>.</p>
<ul>
	<li> <label class="desc" >General Preferences</label> <div class="col"> <span><input id="cmn_unsubscribe_display" 	name="cmn_unsubscribe_display" type="checkbox" class="field checkbox" value="I want to manage the unsubscribe process" <?php if(get_option('cmn_unsubscribe_display') != ''){ echo "checked='checked'"; }?>tabindex="1" />
	<label class="choice" for="Field8">Include unsubscribe process</label>
	</span> <span>
	<input id="Field9" name="cmn_cssedit_display" type="checkbox" class="field checkbox" value="Allow me to edit the css for my newsletter page" 	<?php if(get_option('cmn_cssedit_display') != ''){ echo "checked='checked'"; }?>	tabindex="2" />
	<label class="choice" for="Field9">Enable CSS editor</label>
	</span> <?php /*<span>
	<input id="Field10" name="Field10" type="checkbox" class="field checkbox" value="Display my newsletter archives on the newsletter page" tabindex="3" />
	<label class="choice" for="Field10">Display my newsletter archives on the newsletter page</label>
	</span>	<span>
	<input id="Field8" name="cmn_useajax" type="checkbox" class="field checkbox" value="I want to manage the unsubscribe process" <?php if(get_option('cmn_useajax') != ''){ echo "checked='checked'"; }?>tabindex="1" />
	<label class="choice" for="Field8">Use Ajax</label>
	</span>*/ ?> </div> <p class="instruct" id="instruct8"><small>Check all that apply.</small></p>
	</li>
	
		<li>
		<label class="desc">Opt-In Preferences <span class="req">*</span></label>
		<div class="col">
				<input id="radioDefault_7" name="Field7" type="hidden" value="" /> 
			
				<span>
					<input name="cmn_opt_status" type="radio" class="field radio" <?php if(get_option('cmn_opt_status') == 'single') {echo "checked"; }?> value="single" tabindex="4" checked="checked" />
					<label class="choice" for="Field7_0" > Single Opt-In</label> 
				</span> 
				
				<span>
					<input name="cmn_opt_status" type="radio" class="field radio" <?php if(get_option('cmn_opt_status') == 'double') {echo "checked"; }?>	tabindex="5" 	value="double" />
					<label class="choice">Double Opt-In</label>
				</span> 
		</div> 
		<p class="instruct"><small>Please select the option that you indicated when creating your subscription list.  If you aren't sure, see the <a href="http://rapprich.com/wordpress/cm-newsletter/faq">support documentation</a>.<br /><br /></small></p>
	</li>

</ul>
              
</div>
        
	
		<div id="tab2" class="tab_content">
        <h3>Subscription Options</h3>
			<ul>  
				<li>
					<label class="desc"  for="cmn_subscribeform_before"> Before 	</label>
	
	<div class="col"> 		<textarea id="cmn_subscribeform_before"  	name="cmn_subscribeform_before"  	class="field textarea small"  	rows="10" cols="50" 	tabindex="1" 	onkeyup=""
 	 ><?php echo get_option('cmn_subscribeform_before'); ?></textarea> 	 	</div>
	  <p class="instruct" id="instruct4"><small>Use this space to put any content you want <em>before></em> the subscription form.  This can be a sub-heading, intro text or an image.  HTML is permitted.</small></p>
	</li>
	
	<li>
	 
	<label class="desc"  for="cmn_subscribeform"> Subscribe Form 		<span id="req_4" class="req">*</span> 	</label>
	
	<div class="col"> 		<textarea id="cmn_subscribeform"  	name="cmn_subscribeform"  	class="field textarea large"  	rows="10" cols="50" 	tabindex="2" 	onkeyup=""
 	 ><?php echo get_option('cmn_subscribeform'); ?></textarea> 	 	</div>
	 <p class="instruct" id="instruct4"><small>HTML accepted</small></p>  
	</li>
	
	<li>
	 
	<label class="desc"for="cmn_subscribeform_after"> After 	</label>
	
	<div class="col"> 		<textarea id="cmn_subscribeform_after"  	name="cmn_subscribeform_after"  	class="field textarea small"  	rows="10" cols="50" 	tabindex="3" 	onkeyup=""
 	 ><?php echo get_option('cmn_subscribeform_after'); ?></textarea> 	 	</div>
	  
	</li>
	
	

	<li style="display:none"> <label for="comment">Do Not Fill This Out</label> <textarea name="comment" id="comment" rows="1" cols="1"></textarea> <input type="hidden" id="idstamp" name="idstamp" value="" /> 	</li>
</ul>
      
        </div>
        <div id="tab3" class="tab_content">
            <h3>Unsubscribe Process</h3>
  
             <ul>  
	<li>
	 
	<label class="desc"> Before 	</label>
	
	<div class="col"> 		<textarea id="cmn_unsubscribeform_before"  	name="cmn_unsubscribeform_before"  	class="field textarea small"  	rows="10" cols="50" 	tabindex="1" 	onkeyup=""
 	 ><?php echo get_option('cmn_unsubscribeform_before'); ?></textarea> 	 	</div>
	  <p class="instruct" ><small>Use this space to put any content you want <em>before></em> the unsubscribe form.  This can be a sub-heading, intro text or an image.  HTML is permitted.</small></p>
	</li>
	
	<li id="foli4"  >
	 
	<label class="desc"> Unsubscribe Form 		<span id="req_4" class="req">*</span> 	</label>
	
	<div class="col"> 		<textarea id="cmn_unsubscribeform"  	name="cmn_unsubscribeform"  	class="field textarea large"  	rows="10" cols="50" 	tabindex="2" 	onkeyup=""
 	 ><?php echo get_option('cmn_unsubscribeform'); ?></textarea> 	 	</div>
	 <p class="instruct" ><small>HTML accepted</small></p>  
	</li>
	
	<li id="foli5"  >
	 
	<label class="desc"> After 	</label>
	
	<div class="col"> 		<textarea id="cmn_unsubscribeform_after"  	name="cmn_unsubscribeform_after"  	class="field textarea small"  	rows="10" cols="50" 	tabindex="3" 	onkeyup=""
 	 ><?php echo get_option('cmn_unsubscribeform_after'); ?></textarea> 	 	</div>
	  
	</li>
	
	

	<li style="display:none"> <label for="comment">Do Not Fill This Out</label> <textarea name="comment" id="comment" rows="1" cols="1"></textarea> <input type="hidden" id="idstamp" name="idstamp" value="" /> 	</li>
</ul>    
  

        </div>
  

    <div id="tab4" class="tab_content">
            <h3>Confirmation Messages</h3>
                
          <ul>  
	
<li id="foli3"  >
	 
	<label class="desc" > Subscription Confirmation 	</label>
	
	<div class="col"> 		<textarea id="cmn_confirm1"  	name="cmn_confirm1"  	class="field textarea small"  	rows="10" cols="50" 	tabindex="1" 	onkeyup=""
 	 ><?php echo get_option('cmn_confirm1'); ?></textarea> 	 	</div>
	<p class="instruct"><small>This is message you'll display after a successful subscribe form has been submitted.  It can be tested at /path/to/your/newsletterpage/?cmn=confirm1.  <br /><small><em>Note:  Use this opportunity to guide the user back to your site or to other pertinent content by including valuable links.</em></small></small></p>  
	</li>

  <?php 
      
        	if(get_option('cmn_opt_status') == 'double') {
        	?>
<li id="foli4"  >
	 
	<label class="desc"> Address Verified 		<span id="req_4" class="req">*</span> 	</label>
	
	<div class="col"> 		<textarea id="cmn_confirm1_2"  	name="cmn_confirm1_2"  	class="field textarea small"  	rows="10" cols="50" 	tabindex="2" 	onkeyup=""
 	 ><?php echo get_option('cmn_confirm1_2'); ?></textarea> 	 	</div>
	 <p class="instruct" ><small>Enter the content you'd like to display after the subscriber has verified their email. You can preview this content at /path/to/your/newsletterpage/?cmn=confirm2. (<em><small>Note:  This only applies if your opt-in preferences is set to 'double opt-in'.</small></em></small></p>  
	</li>

     <?php } ?>
<li id="foli5"  >
	 
	<label class="desc"> Unsubscribe Confirmation 	</label>
	
	<div class="col"> 		<textarea id="cmn_confirm2"  	name="cmn_confirm2"  	class="field textarea small"  	rows="10" cols="50" 	tabindex="3" 	onkeyup=""
 	 ><?php echo get_option('cmn_confirm2'); ?></textarea> 	 	</div>
	 <p class="instruct" ><small>This is the message seen after the user successfully unsubscribes from your newsletter.  Test the message at /path/to/your/newsletterpage/?cmn=unsubscribe</small></p> 
	</li>
	
	

	<li style="display:none"> <label for="comment">Do Not Fill This Out</label> <textarea name="comment" id="comment" rows="1" cols="1"></textarea> <input type="hidden" id="idstamp" name="idstamp" value="" /> 	</li>
</ul>
      
     </div>
     
    <div id="tab5" class="tab_content">
            <h3>Edit/Add CSS</h3>
                
          <ul>  
	
<li id="foli3"  >
	 
	<label class="desc"> 	</label>
	
	<div class="col"> 		<textarea id="cmn_additionalcss"  	name="cmn_additionalcss"  	class="field textarea large"  	rows="10" cols="50" 	tabindex="1" 	onkeyup=""
 	 ><?php echo get_option('cmn_additionalcss'); ?></textarea> 	 	</div>
	  
	</li>

      </div>
      
    <div id="tab6" class="tab_content">
     <h2 class="trigger"><a href="#">Video Guides & Tutorials</a></h2>
	<div class="toggle_container">
     <ul class="tabs2" style="margin-bottom:-20px;border-top:none;">
        <li style="border-left:1px solid #bdbdbd;margin-left:-1px;border-top:none;"><a href="#tab2-1">Getting Started</a></li>
         <li style="border-left:1px solid #bdbdbd;margin-left:-1px;"><a href="#tab2-2">Subscribe Process</a></li>
          <li style="border-left:1px solid #bdbdbd;margin-left:-1px;"><a href="#tab2-3">Unsubscribe Process </a></li>
      
    </ul> 
    
     <div class="tab_container2" style="margin-top:-20px;padding-top:20px;">
        
    <div id="tab2-1" class="tab_content2">
           <h3>Setting Up</h3>
			<p>To get started you'll need a campaign monitor or EthosMail account. An in depth tutorial will be added shortly.</p>
	</div> 
       
 	<div id="tab2-2" class="tab_content2">
      
 		<div class="video"> 
			<center>
				<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,115,0' width='560' height='345'><param name='movie' value='http://screenr.com/Content/assets/screenr_1116090935.swf' ></param><param name='flashvars' value='i=53180' ></param><param name='allowFullScreen' value='true' ></param><embed src='http://screenr.com/Content/assets/screenr_1116090935.swf' flashvars='i=53180' allowFullScreen='true' width='560' height='345' pluginspage='http://www.macromedia.com/go/getflashplayer' ></embed></object>
	 
				<h4>Setting up your subscription form</h4>	 
				
				<div style="width:100%;border-top:1px solid #bdbdbd;"></div><br /><br /> 
				
				<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,115,0' width='560' height='345'><param name='movie' value='http://screenr.com/Content/assets/screenr_1116090935.swf' ></param><param name='flashvars' value='i=53182' ></param><param name='allowFullScreen' value='true' ></param><embed src='http://screenr.com/Content/assets/screenr_1116090935.swf' flashvars='i=53182' allowFullScreen='true' width='560' height='345' pluginspage='http://www.macromedia.com/go/getflashplayer' ></embed></object>  <h4>Setting up your confirmation urls in Campaign Monitor </h4> </center></div>  <div class="clear"></div> </div>   <div id="tab2-3" class="tab_content2">
         <center> <div class="video"><object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,115,0' width='560' height='345'><param name='movie' value='http://screenr.com/Content/assets/screenr_1116090935.swf' ></param><param name='flashvars' value='i=53179' ></param><param name='allowFullScreen' value='true' ></param><embed src='http://screenr.com/Content/assets/screenr_1116090935.swf' flashvars='i=53179' allowFullScreen='true' width='560' height='345' pluginspage='http://www.macromedia.com/go/getflashplayer' ></embed></object>
<h4>Creating an unsubscribe form</h4>
	 <div style="width:100%;border-top:1px solid #bdbdbd;"></div><br /><br />
	
	
<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,115,0' width='560' height='345'><param name='movie' value='http://screenr.com/Content/assets/screenr_1116090935.swf' ></param><param name='flashvars' value='i=53183' ></param><param name='allowFullScreen' value='true' ></param><embed src='http://screenr.com/Content/assets/screenr_1116090935.swf' flashvars='i=53183' allowFullScreen='true' width='560' height='345' pluginspage='http://www.macromedia.com/go/getflashplayer' ></embed></object>
<h4>Setting up your unsubscribe confirmation urls in Campaign Monitor </h4>
</center>
</div><div class="clear"></div>

    </div>
    </div>

	<h2 class="trigger" style="margin-top:-1px;"><a href="#">Step-by-step Instructions</a></h2>
	<div class="toggle_container">
	 <center><br /><br /><br /><h3>COMING SOON!</h3></center>
	 </div> <div class="clear"></div>
	</div>
	 
	<div style="width:100%;border-top:1px solid #bdbdbd;"><p class="submit" style="padding-left:20px;">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div></div>	<div style="clear:both;"></div>
	</div>  <div style="clear:both;"></div></div>

<?php } 
if (!function_exists("cmnewsletter_ap")){
	function cmnewsletter_ap() { if (function_exists('add_options_page')) { 	add_options_page('CM-Newsletter', 'CM-Newsletter', 8, basename(__FILE__), 'cmn_settings_page'); }
	}
}

add_action('admin_menu', 'cmnewsletter_ap');

?>