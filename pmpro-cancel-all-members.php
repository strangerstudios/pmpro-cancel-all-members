<?php
/*
Plugin Name: PMPro Cancel All Members
Plugin URI: http://www.paidmembershipspro.com/wp/pmpro-cancel-all-members/
Description: Cancel all PMPro Members
Version: .1
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
*/

/*
	Activate this plugin, then navigate to /wp-admin/?cancelallmembers=1 as an admin.
	To cancel only a few members at a time (to avoid time outs) add a limit parameter,
	e.g. /wp-admin/?cancelallmembers=1&limit=10 will cancel 10 members.
*/
function pmprocam_init()
{
	if(!empty($_REQUEST['cancelallmembers']) && current_user_can('manage_options'))
	{
		//get all members
		global $wpdb;
		$sqlQuery = "SELECT user_id FROM $wpdb->pmpro_memberships_users WHERE status = 'active'";
		if(!empty($_REQUEST['limit']))
			$sqlQuery .= " LIMIT " . intval($_REQUEST['limit']);
		$member_ids = $wpdb->get_col($sqlQuery);
		
		//no members?
		if(empty($member_ids))
			die("No members found.");
		
		//cancel them
		foreach($member_ids as $member_id)
		{
			if(!pmpro_hasMembershipLevel(NULL, $member_id))
				continue;	//not really a member
			
			$user = get_userdata($member_id);
			echo "Cancelling user #" . $user->ID . ", " . $user->display_name . " (" . $user->user_email .  ")...<br />";
			pmpro_changeMembershipLevel(0, $member_id);
		}
	
		exit;
	}
}
add_action('init', 'pmprocam_init');