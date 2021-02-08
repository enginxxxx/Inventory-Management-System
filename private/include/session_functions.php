<?php
// Source: http://www.lynda.com/PHP-tutorials/Session-hijacking-fixation/133321/180311-4.html

session_start();

function end_session() {
	session_unset();
  	session_destroy();
}

function request_ip_matches_session() {
	if(!isset($_SESSION['ip']) || !isset($_SERVER['REMOTE_ADDR'])) {
		return false;
	}
	if($_SESSION['ip'] === $_SERVER['REMOTE_ADDR']) {
		return true;
	} else {
		return false;
	}
}

function request_user_agent_matches_session() {
	if(!isset($_SESSION['user_agent']) || !isset($_SERVER['HTTP_USER_AGENT'])) {
		return false;
	}
	if($_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT']) {
		return true;
	} else {
		return false;
	}
}

function last_login_is_recent() {
	// Expires in an hour
	$max_elapsed = 60 * 60 * 1; // sec * min * hour
	if(!isset($_SESSION['last_login'])) {
		return false;
	}
	if(($_SESSION['last_login'] + $max_elapsed) >= time()) {
		return true;
	} else {
		return false;
	}
}

function is_session_valid() {
	$check_ip = true;
	$check_user_agent = true;
	$check_last_login = true;

	if($check_ip && !request_ip_matches_session()) {
                after_successful_logout();
		return false;
	}
	if($check_user_agent && !request_user_agent_matches_session()) {
                after_successful_logout();
		return false;
	}
	if($check_last_login && !last_login_is_recent()) {
                after_successful_logout();
		return false;
	}
	return true;
}

function confirm_session_is_valid() {
	if(!is_session_valid()) {
		end_session();
		header("Location: index.php");
		exit;
	}
}

function is_logged_in() {
	return (isset($_SESSION['logged_in']) && $_SESSION['logged_in']);
}

function confirm_user_logged_in() {
	if(!is_logged_in()) {
		end_session();
		header("Location: login.php");
		exit;
	}
}

function after_successful_login() {
	session_regenerate_id();
	
	$_SESSION['logged_in'] = true;
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$_SESSION['last_login'] = time();
	
}

function after_successful_logout() {
	$_SESSION['logged_in'] = false;
	end_session();
}

function before_every_protected_page() {
	confirm_user_logged_in();
	confirm_session_is_valid();
}
?>
