<?php

/* 
	The page where you have the button/link for your users to grant your app access to their SocialAdr data 
	
	To find your App ID, go to http://socialadr.com/pg/apps/manage and click "Manage" for your app
*/

	$appId = ''; // Your App ID
	$socialadr_url = "http://socialadr.com/pg/apps/details";

	echo "<a class=\"button\" href=\"$socialadr_url?id=$appId\">Authorize App</a>";
	
?>