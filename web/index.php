<?php

/*
main web processing
*/

include('../includes/common.php');

// Figure out what page to render
$request = $_GET;

$nav_brand_url = BRAND_URL;
$nav_brand_name = BRAND_NAME;

$login = new Login();

if ($login->isUserLoggedIn() == false)
{
	if ($request['page'] == 'register')
	{
		$registration = new Registration();
		if (isset($registration)) {
	    if ($registration->errors) {
        	foreach ($registration->errors as $error) {
	            echo $error;
        	}
    	}
    	if ($registration->messages) {
	        foreach ($registration->messages as $message) {
            	echo $message;
        	}
    	}
	}

	$tpl_head = new Template("../includes/templates/head-login.tpl");
	$tpl_registration = new Template("../includes/templates/register.tpl");
	$tpl_foot = new Template("../includes/templates/foot.tpl");

	echo $tpl_head->fetch('../includes/templates/head-login.tpl');
	echo $tpl_registration->fetch('../includes/templates/register.tpl');
	echo $tpl_foot->fetch('../includes/templates/foot.tpl');
}
	else
	{
	if (isset($login)) {
		if ($login->errors) {
	   		foreach ($login->errors as $error) {
       		echo $error;
			}
		}
		if ($login->messages) {
       		foreach ($login->messages as $message) {
				echo $message;
       		}
		}
	}
	$tpl_head = new Template("../includes/templates/head-login.tpl");
	$tpl_login = new Template("../includes/templates/login.tpl");
	$tpl_foot = new Template("../includes/templates/foot.tpl");

	$tpl_head->set('title', "Nest Administration Tool");

	echo $tpl_head->fetch('../includes/templates/head-login.tpl');
	echo $tpl_login->fetch('../includes/templates/login.tpl');
	echo $tpl_foot->fetch('../includes/templates/foot.tpl');
	}
}

if ($login->isUserLoggedIn() == true)
{
	if ($request == null)
	{
		$tpl_head = new Template("../includes/templates/head.tpl");
		$tpl_nav = new Template("../includes/templates/nav.tpl");
		$tpl_foot = new Template("../includes/templates/foot.tpl");

		$tpl_head->set('title', "Nest Administration Tool");
		$tpl_nav->set('nav_brand_url', $nav_brand_url);
		$tpl_nav->set('nav_brand_name', $nav_brand_name);

		echo $tpl_head->fetch('../includes/templates/head.tpl');
		echo $tpl_nav->fetch('../includes/templates/nav.tpl');
		echo $tpl_foot->fetch('../includes/templates/foot.tpl');
	}
	if ($request['page'] == 'graphs' ) // add check to see if the user is logged in
	{
		$tpl_head = new Template("../includes/templates/head.tpl");
		$tpl_nav = new Template("../includes/templates/nav.tpl");
		$tpl_foot = new Template("../includes/templates/foot.tpl");
		$tpl_chart_nest_stats = new Template("../includes/templates/chart_nest_stats.tpl");
		$tpl_chart_unit_stats = new Template("../includes/templates/chart_unit_stats.tpl");

		$tpl_head->set('title', "Nest Administration Tool: Graphs");
		$tpl_nav->set('nav_brand_url', $nav_brand_url);
		$tpl_nav->set('nav_brand_name', $nav_brand_name);

		/*
		// Query database for collected data
		*/
		$db_connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$query = "select * from data ORDER BY timestamp";

		$result = mysqli_query($db_connect, $query);
		if (mysqli_connect_errno())
		{
	        echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		while ($row = mysqli_fetch_array($result))
		{
			$timestamp = $row['timestamp'];
			$heating = $row['heating'];
			$cooling = $row['cooling'];
			$setpoint = $row['target'];
			$temp = $row['current'];
			$humidity = $row['humidity'];
			$outside_temp = $row['outside_temp'];
			$outside_humidity = $row['outside_humidity'];

			$timestamp *= 1000; // convert from Unix timestamp to JavaScript time
			$data_temp[] .= "[$timestamp, $temp]";
			$data_humidity[] .= "[$timestamp, $humidity]";
			$data_setpoint[] .= "[$timestamp, $setpoint]";
			$data_outside_temp[] .= "[$timestamp, $outside_temp]";
			$data_outside_humidity[] .= "[$timestamp, $outside_humidity]";
			$data_cooling[] .= "[$timestamp, $cooling]";
			$data_heating[] .= "[$timestamp, $heating]";
		}

		$date_offset = 4;

		$tpl_chart_nest_stats->set('date_offset', $date_offset);
		$tpl_chart_nest_stats->set('data_temp', $data_temp);
		$tpl_chart_nest_stats->set('data_humidity', $data_humidity);
		$tpl_chart_nest_stats->set('data_setpoint', $data_setpoint);
		$tpl_chart_nest_stats->set('data_outside_temp', $data_outside_temp);
		$tpl_chart_nest_stats->set('data_outside_humidity', $data_outside_humidity);

		$tpl_chart_unit_stats->set('date_offset', $date_offset);
		$tpl_chart_unit_stats->set('data_cooling', $data_cooling);
		$tpl_chart_unit_stats->set('data_heating', $data_heating);

		echo $tpl_head->fetch('../includes/templates/head.tpl');
		echo $tpl_nav->fetch('../includes/templates/nav.tpl');
		echo $tpl_chart_nest_stats->fetch('../includes/templates/chart_nest_stats.tpl');
		echo $tpl_chart_unit_stats->fetch('../includes/templates/chart_unit_stats.tpl');
		echo $tpl_foot->fetch('../includes/templates/foot.tpl');
	}
}

if ($request['logout'])
{
	header('/');
}