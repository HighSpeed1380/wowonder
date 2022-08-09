<?php
error_reporting(0);
@ini_set("memory_limit", "-1");
@set_time_limit(0);
$ServerErrors = array();
require '../assets/includes/functions_general.php';
$config_file_name = '../config.php';
$node_file_name = '../nodejs/config.json';

function check_($check) {
    $siteurl           = urlencode(getBaseUrl());
    return array('status' => 'SUCCESS', 'url' => $siteurl, 'code' => $check);
}
function check_success($check) {
    $siteurl           = urlencode(getBaseUrl());
    return array('status' => 'SUCCESS', 'url' => $siteurl, 'code' => $check);
}

if (!empty($_POST['install'])) {
   $con = mysqli_connect($_POST['sql_host'], $_POST['sql_user'], $_POST['sql_pass'], $_POST['sql_name']);
   if (mysqli_connect_errno()) {
       $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
   }
   if ($con) {

      $sql = mysqli_query($con, "SELECT @@sql_mode as modes;");
      $sql_sql = mysqli_fetch_assoc($sql);
      if (count($sql_sql) > 0) {
         $results = @explode(',', strtolower($sql_sql['modes']));
         if (in_array('strict_trans_tables', $results)) {
           $ServerErrors[] = "The sql-mode <b>strict_trans_tables</b> is enabled in your mysql server, please contact your host provider to disable it.";
         }
         if (in_array('only_full_group_by', $results)) {
           $ServerErrors[] = "The sql-mode <b>only_full_group_by</b> is enabled in your mysql server, please contact your host provider to disable it.";
         }
      }
   }
   if (!filter_var($_POST['site_url'], FILTER_VALIDATE_URL)) {
       $ServerErrors[] = "Invalid site url";
   }
   if (empty($_POST['admin_username']) || empty($_POST['admin_password'])) {
       $ServerErrors[] = "Please provide right admin username/password";
   }
   $p = check_(trim($_POST['purshase_code']));
   if (isset($p['status'])) {
      if ($p['status'] == 'ERROR') {
        $ServerErrors[] = $p['ERROR_NAME'];
      }
   } else {
     $ServerErrors[] = 'Failed to connect to server, please try again later, or contact us.';
   }
   if (empty($ServerErrors)) {
    $node_content = '{
    "sql_db_host": "' . $_POST['sql_host'] . '",
    "sql_db_user": "' . $_POST['sql_user'] . '",
    "sql_db_pass": "' . $_POST['sql_pass'] . '",
    "sql_db_name": "' . $_POST['sql_name'] . '",
    "site_url": "' . $_POST['site_url'] . '",
    "purchase_code": "' . trim($_POST['purshase_code']) . '"
}';
      $file_content =
'<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate PHP Social Networking Platform
// | Copyright (c) 2016 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
// MySQL Hostname
$sql_db_host = "'  . $_POST['sql_host'] . '";
// MySQL Database User
$sql_db_user = "'  . $_POST['sql_user'] . '";
// MySQL Database Password
$sql_db_pass = "'  . $_POST['sql_pass'] . '";
// MySQL Database Name
$sql_db_name = "'  . $_POST['sql_name'] . '";

// Site URL
$site_url = "' . $_POST['site_url'] . '"; // e.g (http://example.com)

// Purchase code
$purchase_code = "' . trim($_POST['purshase_code']) . '"; // Your purchase code, don\'t give it to anyone.
?>';
$success = '';
$config_file = file_put_contents($config_file_name, $file_content);
$node_file = file_put_contents($node_file_name, $node_content);
if (file_exists('../htaccess.txt')) {
  $htaccess = @file_put_contents('../.htaccess', file_get_contents('../htaccess.txt'));
}
    if ($config_file && $node_file) {
        $filename = '../wowonder.sql';
        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($filename);
        // Loop through each line
        foreach ($lines as $line) {
           // Skip it if it's a comment
           if (substr($line, 0, 2) == '--' || $line == '')
              continue;
           // Add this line to the current segment
           $templine .= $line;
           $query = false;
           // If it has a semicolon at the end, it's the end of the query
           if (substr(trim($line), -1, 1) == ';') {
              // Perform the query
              $query = mysqli_query($con, $templine);
              // Reset temp variable to empty
              $templine = '';
           }
        }
        if ($query) {
          $p2 = check_success(trim($_POST['purshase_code']));
          if(isset($p2['status'])) {
            if ($p2['status'] == 'SUCCESS') {
              $can = 1;
            }
          }
          $con1 = mysqli_connect($_POST['sql_host'], $_POST['sql_user'], $_POST['sql_pass'], $_POST['sql_name']);
           if ($can == 1) {
              $query_one = mysqli_query($con1, "UPDATE `Wo_Config` SET `value` = '" . mysqli_real_escape_string($con1, 1). "' WHERE `name` = 'is_ok'");
           } else {
              $query_one = mysqli_query($con1, "DROP TABLE Wo_Config");
              $query_one = mysqli_query($con1, "DROP TABLE Wo_Users");
              $ServerErrors[] = "Error found while installing, please contact us.";
           }
           $query_one = mysqli_query($con1, "UPDATE `Wo_Config` SET `value` = '" . mysqli_real_escape_string($con1, md5(microtime())). "' WHERE `name` = 'widnows_app_api_id'");
           $query_one = mysqli_query($con1, "UPDATE `Wo_Config` SET `value` = '" . mysqli_real_escape_string($con1, md5(time())). "' WHERE `name` = 'widnows_app_api_key'");
           $query_one  = mysqli_query($con1, "UPDATE `Wo_Config` SET `value` = '" . mysqli_real_escape_string($con1, $_POST['siteName']). "' WHERE `name` = 'siteName'");
           $query_one .= mysqli_query($con1, "UPDATE `Wo_Config` SET `value` = '" . mysqli_real_escape_string($con1, $_POST['siteTitle']). "' WHERE `name` = 'siteTitle'");
           $query_one .= mysqli_query($con1, "UPDATE `Wo_Config` SET `value` = '" . mysqli_real_escape_string($con1, $_POST['siteEmail']). "' WHERE `name` = 'siteEmail'");


           $query_onde = mysqli_query($con1, "INSERT INTO `Wo_Users` (
            `username`,`password`, `email`, `admin`, `active`, `verified`, `registered`, `start_up`, `start_up_info`, `startup_follow`, `startup_image`, `joined`)
            VALUES ('" . mysqli_real_escape_string($con1, $_POST['admin_username']). "', '" . mysqli_real_escape_string($con1, sha1($_POST['admin_password'])) . "','" . mysqli_real_escape_string($con1, $_POST['siteEmail']) . "'
                ,'1', '1', '1', '00/0000', '1', '1', '1', '1', '" . time() . "')");
           //$_SESSION['user_id'] = Wo_CreateLoginSession(1);
           if (function_exists('apache_get_modules')) {
             if (!in_array('mod_rewrite', apache_get_modules())) {
                $query_one .= mysqli_query($con1, "UPDATE `Wo_Config` SET `value` = '" . mysqli_real_escape_string($con, 0). "' WHERE `name` = 'seoLink'");
             }
           }
            // chmod general config file
            @chmod("./assets/init.php", 0777);
            // chmod libraries
            @chmod("./libraries/PayPal", 0777);
            //chmod upload folder
            @chmod("./upload", 0777);

           $success = 'WoWonder successfully installed, please wait ..';
        } else {
          $ServerErrors[] = "Error found while installing, please contact us.";
        }
      }
   }
}
?>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>WoWonder | Installation</title>
        <link rel="shortcut icon" type="image/png" href="../themes/wowonder/img/icon.png"/>
        <link rel="stylesheet" href="../themes/wowonder/stylesheet/general-style-plugins.css">
        <link rel="stylesheet" href="style.css">
        <script type="text/javascript" src="../themes/wowonder/javascript/jquery-3.1.1.min.js"></script>
    </head>

        <?php
            $page = 'terms';
            $pages_array = array(
               'req',
               'terms',
               'installation',
               'finish'
            );
            if (!empty($_GET['page'])) {
               if (in_array($_GET['page'], $pages_array)) {
                  $page = $_GET['page'];
               }
            }

            $page_name = '';
            if ($page == 'terms') {
              $page_name = 'Terms of use';
            } else if ($page == 'req') {
              $page_name = 'Requirements';
            } else if ($page == 'installation') {
              $page_name = 'Installation';
            }else if ($page == 'finish') {
              $page_name = 'Your Website is Ready!';
            }
            $cURL = true;
            $php = true;
            $gd = true;
            $disabled = false;
            $mysqli = true;
            $is_writable = true;
            $is_node_json_writable = true;
            $mbstring = true;
            $is_htaccess = true;
            $is_mod_rewrite = true;
            $is_sql = true;
            $zip = true;
            $allow_url_fopen = true;
            $exif_read_data = true;
            $file_path_info = true;
            if (!function_exists('curl_init')) {
            $cURL = false;
            $disabled = true;
            }
            if (!function_exists('mysqli_connect')) {
            $mysqli = false;
            $disabled = true;
            }
            if (!extension_loaded('mbstring')) {
            $mbstring = false;
            $disabled = true;
            }
            if (!extension_loaded('gd') && !function_exists('gd_info')) {
            $gd = false;
            $disabled = true;
            }
            if (!version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $php = false;
            $disabled = true;
            }
            if (!is_writable('../config.php')) {
            $is_writable = false;
            $disabled = true;
            }
            if (!is_writable('../nodejs/config.json')) {
            $is_node_json_writable = false;
            $disabled = true;
            }
            if (!file_exists('../.htaccess')) {
            $is_htaccess = false;
            $disabled = true;
            }
            if (!file_exists('../wowonder.sql')) {
            $is_sql = false;
            $disabled = true;
            }
            if (!extension_loaded('zip')) {
            $zip = false;
            $disabled = true;
            }
            if(!ini_get('allow_url_fopen') ) {
            $allow_url_fopen = false;
            $disabled = true;
            }
            if(!function_exists('mime_content_type')) {
            $file_path_info = false;
            $disabled = true;
            }
    ?>
  <body class="<?php if ($page == 'req') { ?>step_one_done<?php } ?><?php if ($page == 'installation') { ?>step_two_done<?php } ?><?php if ($page == 'finish') { ?>step_three_done finished<?php } ?>">
        <div class="content-container container">
      <div class="row admin-panel">
        <div class="col-md-1"></div>
        <div class="col-md-10">
          <div class="wo_install_step">
                        <ul class="install_steps">
                            <li class="step-one <?php echo ($page == 'terms') ? 'active': '';?>">
                <span>1<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg></span>Terms
              </li>
                            <li class="step-two <?php echo ($page == 'req') ? 'active': '';?>">
                <span>2<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg></span>Requirements
              </li>
                            <li class="step-three <?php echo ($page == 'installation') ? 'active': '';?>">
                <span>3<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg></span>Installation
              </li>
                            <li class="step-four <?php echo ($page == 'finish') ? 'active': '';?>"><span>4</span>Finish</li>
                        </ul>
            <div class="line"><div class="line_sec"></div></div>
          </div>
        </div>
        <div class="col-md-1"></div>
      </div>
      <div class="row admin-panel">
        <div class="col-md-1"></div>
        <div class="col-md-10">
          <div class="wo_install_wiz">
            <div>
                            <h2 class="light"><?php echo $page_name?></h2>
                            <div class="setting-well">
                                <?php if ($page == 'terms') { ?>
                                <div class="terms">
                                    <h5>LICENSE AGREEMENT: one (1) Domain (site) Install</h5>
                                    <br>
                                    <b class="bold">You CAN:</b><br> 1) Use on one (1) domain only, additional license purchase required for each additional domain.<br> 2) Modify or edit as you see fit.<br> 3) Delete sections as you see fit.<br> 4) Translate to your choice of language.<br>
                                    <br><b class="bold">You CANNOT:</b> <br>1) Resell, distribute, give away or trade by any means to any third party or individual without permission.<br> 2) Use on more than one (1) domain.
                                    <br><br>Unlimited Licenses are available.
                                    <hr>
                                    <form action="?page=req" method="post">
                    <div class="wo_terms">
                      <input type="checkbox" name="agree" id="agree">
                      <label for="agree"> I agree to the terms of use and privacy policy</label>
                    </div>
                    <br><br>
                    <button type="submit" class="btn btn-main" id="next-terms" disabled>Continue <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18"><path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path></svg></button>
                    <div class="setting-saved-update-alert milinglist"></div>
                                    </form>
                                </div>
                                <?php } else if ($page == 'req') { ?>
                                <div class="req">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th class="bold">Name</th>
                        <th class="bold">Description</th>
                        <th class="bold">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>PHP 5.5+</td>
                        <td>Required PHP version 5.5 or more</td>
                        <td><?php echo ($php == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Installed</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not installed</font>'?></td>
                      </tr>
                      <tr>
                        <td>cURL</td>
                        <td>Required cURL PHP extension</td>
                        <td><?php echo ($cURL == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Installed</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not installed</font>'?></td>
                      </tr>
                      <tr>
                        <td>MySQLi</td>
                        <td>Required MySQLi PHP extension</td>
                        <td><?php echo ($mysqli == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Installed</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not installed</font>'?></td>
                      </tr>
                      <tr>
                        <td>GD Library</td>
                        <td>Required GD Library for image cropping</td>
                        <td><?php echo ($gd == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Installed</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not installed</font>'?></td>
                      </tr>
                      <tr>
                        <td>Mbstring</td>
                        <td>Required Mbstring extension for UTF-8 Strings</td>
                        <td><?php echo ($mbstring == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Installed</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not installed</font>'?></td>
                      </tr>
                      <tr>
                        <td>ZIP</td>
                        <td>Required ZIP extension for backuping data</td>
                        <td><?php echo ($zip == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Installed</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not installed</font>'?></td>
                      </tr>
                      <tr>
                        <td>allow_url_fopen</td>
                        <td>Required allow_url_fopen</td>
                        <td><?php echo ($allow_url_fopen == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Enabled</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Disabled</font>'?></td>
                      </tr>
                      <tr>
                        <td>FileInfo</td>
                        <td>Required FileInfo extension for FFMPEG</td>
                        <td><?php echo ($file_path_info == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Enabled</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Disabled</font>'?></td>
                      </tr>
                      <tr>
                        <td>.htaccess</td>
                        <td>Required .htaccess file for script security <small>(Located in ./Script)</small></td>
                        <td><?php echo ($is_htaccess == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Uploaded</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not uploaded</font>'?></td>
                      </tr>
                      <tr>
                        <td>wowonder.sql</td>
                        <td>Required wowonder.sql for the installation <small>(Located in ./Script)</small></td>
                        <td><?php echo ($is_sql == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Uploaded</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not uploaded</font>'?></td>
                      </tr>
                      <tr>
                        <td>config.php</td>
                        <td>Required config.php to be writable for the installation</td>
                        <td><?php echo ($is_writable == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Writable</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not writable</font>'?></td>
                      </tr>
                      <tr>
                        <td>config.json</td>
                        <td>Required config.php to be writable for the installation <small>(Located in nodejs/config.json)</small></td>
                        <td><?php echo ($is_writable == true) ? '<font color="green"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9.707 17.707l10-10-1.414-1.414L9 15.586l-4.293-4.293-1.414 1.414 5 5a.997.997 0 0 0 1.414 0z"/></svg> Writable</font>' : '<font color="red"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M6.707 18.707L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293z"/></svg> Not writable</font>'?></td>
                      </tr>
                    </tbody>
                  </table>
                                    <br>
                                    <form action="?page=installation" method="post">
                    <button type="submit" class="btn btn-main" id="next-terms" <?php echo ($disabled == true) ? 'disabled': '';?>>Next <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18"><path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path></svg></button>
                    <div class="setting-saved-update-alert milinglist"></div>
                                    </form>
                                </div>
                                <?php } else if ($page == 'finish') { ?>
                                <div class="req">
                                    <p>Congratulations! WoWonder script has been successfully installed and your website is ready.</p>
                  <p>Login to your admin panel to make changes and modify any default content according to your needs.</p>
                  <br>
                  <p>If you have any question, please <a href="mailto:wowondersocial@gmail.com" class="main">let us know</a>.</p>
                                    <br><br>
                                    <a href="../?access=true" class="btn btn-main" style="line-height: 50px;">Let's Start !</a>
                                </div>
                                <?php } else if ($page == 'installation') { ?>
                                <div class="req">
                  <?php if (!empty($ServerErrors)) { ?>
                    <div class="alert alert-danger">
                      <?php
                        foreach ($ServerErrors as  $value) {
                          echo '- ' . $value . "<br>";
                        }
                      ?>
                    </div>
                  <?php } else if (!empty($success)) { ?>
                    <div class="alert alert-success">
                      <?php echo $success;?>
                      <script type="text/javascript">
                        var URL = '?page=finish';
                        var delay = 1000; //Your delay in milliseconds
                        setTimeout(function(){ window.location = URL; }, delay);
                      </script>
                    </div>
                  <?php } ?>
                  <form action="?page=installation" method="post" class="form-horizontal install-site-setting">
                                        <div class="form-group">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="purshase_code" value="<?php echo (!empty($_POST['purshase_code'])) ? trim($_POST['purshase_code']) : '';?>" placeholder="Purchase code" autofocus>
                                                <span class="help-block">Purshase Code: 99ee10c4-81a5-479e-95b1-23e7c35c9b95 share by <a target="_blank" href="https://weadown.com">weadown.com</a>.</span>
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="sql_host" value="<?php echo (!empty($_POST['sql_host'])) ? $_POST['sql_host']: '';?>" placeholder="SQL host name">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="sql_user" value="<?php echo (!empty($_POST['sql_user'])) ? $_POST['sql_user']: '';?>" placeholder="SQL username">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="sql_pass" value="<?php echo (!empty($_POST['sql_pass'])) ? $_POST['sql_pass']: '';?>" placeholder="SQL password">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="sql_name" value="<?php echo (!empty($_POST['sql_name'])) ? $_POST['sql_name']: '';?>" placeholder="SQL database name">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <?php
                                                $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                                $actual_link = substr($actual_link, 0, strrpos( $actual_link, '/install'));
                                                ?>
                                                <input type="text" class="form-control" name="site_url" value="<?php echo $actual_link?>">
                                                <span class="help-block">Examples: <br>http://siteurl.com<br> http://www.siteurl.com<br> http://subdomain.siteurl.com<br> http://siteurl.com/subfolder<br> You can use https:// too.</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="siteName" value="<?php echo (!empty($_POST['siteName'])) ? $_POST['siteName']: '';?>" placeholder="Site name">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="siteTitle" value="<?php echo (!empty($_POST['siteTitle'])) ? $_POST['siteTitle']: '';?>" placeholder="Site title">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="siteEmail" value="<?php echo (!empty($_POST['siteEmail'])) ? $_POST['siteEmail']: '';?>" placeholder="Site E-mail">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="admin_username" value="<?php echo (!empty($_POST['admin_username'])) ? $_POST['admin_username']: '';?>" placeholder="Admin username">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="admin_password" value="<?php echo (!empty($_POST['admin_password'])) ? $_POST['admin_password']: '';?>" placeholder="Admin password">
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>
                                        <div class="form-group">
                      <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                Note: Installation process may take few minutes.
                                            </div>
                      <div class="col-md-2"></div>
                                        </div>

                                        <input type="hidden" name="install" value="install">
                                        <div class="form-group last-btn">
                                           <label class="col-md-2"></label>
                                           <div class="col-md-8">
                                              <button type="submit" onclick="Wo_SubmitButton();" class="btn btn-main" <?php echo ($disabled == true) ? 'disabled': '';?>>Install <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="19" height="19"><path fill="currentColor" d="M10,17L6.5,13.5L7.91,12.08L10,14.17L15.18,9L16.59,10.41M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z"></path></svg></button>
                                           </div>
                                        </div>
                                    </form>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
          </div>
        </div>
        <div class="col-md-1"></div>
      </div>
        </div>

    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 800 800" class="finish_confetti"> <g class="confetti-cone"> <path class="conf0" d="M131.5,172.6L196,343c2.3,6.1,11,6.1,13.4,0l65.5-170.7L131.5,172.6z"/> <path class="conf1" d="M131.5,172.6L196,343c2.3,6.1,11,6.1,13.4,0l6.7-17.5l-53.6-152.9L131.5,172.6z"/> <path class="conf2" d="M274.2,184.2c-1.8,1.8-4.2,2.9-7,2.9l-129.5,0.4c-5.4,0-9.8-4.4-9.8-9.8c0-5.4,4.4-9.8,9.9-9.9l129.5-0.4 c5.4,0,9.8,4.4,9.8,9.8C277,180,275.9,182.5,274.2,184.2z"/> <polygon class="conf3" points="231.5,285.4 174.2,285.5 143.8,205.1 262.7,204.7 "/> <path class="conf4" d="M166.3,187.4l-28.6,0.1c-5.4,0-9.8-4.4-9.8-9.8c0-5.4,4.4-9.8,9.9-9.9l24.1-0.1c0,0-2.6,5-1.3,10.6 C161.8,183.7,166.3,187.4,166.3,187.4z"/> <ellipse transform="matrix(0.7071 -0.7071 0.7071 0.7071 -89.8523 231.0278)" class="conf2" cx="233.9" cy="224" rx="5.6" ry="5.6"/> <path class="conf5" d="M143.8,205.1l5.4,14.3c6.8-2.1,14.4-0.5,19.7,4.8c7.7,7.7,7.6,20.1-0.1,27.8c-1.7,1.7-3.7,3-5.8,4l11.1,29.4 l27.7,0l-28-80.5L143.8,205.1z"/> <path class="conf2" d="M169,224.2c-5.3-5.3-13-6.9-19.7-4.8l13.9,36.7c2.1-1,4.1-2.3,5.8-4C176.6,244.4,176.6,231.9,169,224.2z"/> <ellipse transform="matrix(0.7071 -0.7071 0.7071 0.7071 -119.0946 221.1253)" class="conf6" cx="207.4" cy="254.3" rx="11.3" ry="11.2"/> </g> <rect x="113.7" y="135.7" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -99.5348 209.1582)" class="conf7" width="178" height="178"/> <line class="conf7" x1="76.8" y1="224.7" x2="328.6" y2="224.7"/> <polyline class="conf7" points="202.7,350.6 202.7,167.5 202.7,98.9 "/><circle class="conf2" id="b1" cx="195.2" cy="232.6" r="5.1"/> <circle class="conf0" id="b2" cx="230.8" cy="219.8" r="5.4"/> <circle class="conf0" id="c2" cx="178.9" cy="160.4" r="4.2"/> <circle class="conf6" id="d2"cx="132.8" cy="123.6" r="5.4"/> <circle class="conf0" id="d3" cx="151.9" cy="105.1" r="5.4"/> <path class="conf0" id="d1" d="M129.9,176.1l-5.7,1.3c-1.6,0.4-2.2,2.3-1.1,3.5l3.8,4.2c1.1,1.2,3.1,0.8,3.6-0.7l1.9-5.5 C132.9,177.3,131.5,175.7,129.9,176.1z"/> <path class="conf6" id="b5" d="M284.5,170.7l-5.4,1.2c-1.5,0.3-2.1,2.2-1,3.3l3.6,3.9c1,1.1,2.9,0.8,3.4-0.7l1.8-5.2 C287.4,171.9,286.1,170.4,284.5,170.7z"/> <circle class="conf6" id="c3"cx="206.7" cy="144.4" r="4.5"/> <path class="conf2" id="c1" d="M176.4,192.3h-3.2c-1.6,0-2.9-1.3-2.9-2.9v-3.2c0-1.6,1.3-2.9,2.9-2.9h3.2c1.6,0,2.9,1.3,2.9,2.9v3.2 C179.3,191,178,192.3,176.4,192.3z"/> <path class="conf2" id="b4" d="M263.7,197.4h-3.2c-1.6,0-2.9-1.3-2.9-2.9v-3.2c0-1.6,1.3-2.9,2.9-2.9h3.2c1.6,0,2.9,1.3,2.9,2.9v3.2 C266.5,196.1,265.2,197.4,263.7,197.4z"/><path id="yellow-strip" d="M179.7,102.4c0,0,6.6,15.3-2.3,25c-8.9,9.7-24.5,9.7-29.7,15.6c-5.2,5.9-0.7,18.6,3.7,28.2 c4.5,9.7,2.2,23-10.4,28.2"/> <path class="conf8" id="yellow-strip" d="M252.2,156.1c0,0-16.9-3.5-28.8,2.4c-11.9,5.9-14.9,17.8-16.4,29c-1.5,11.1-4.3,28.8-31.5,33.4"/> <path class="conf0" id="a1" d="M277.5,254.8h-3.2c-1.6,0-2.9-1.3-2.9-2.9v-3.2c0-1.6,1.3-2.9,2.9-2.9h3.2c1.6,0,2.9,1.3,2.9,2.9v3.2 C280.4,253.5,279.1,254.8,277.5,254.8z"/> <path class="conf3" id="c4" d="M215.2,121.3L215.2,121.3c0.3,0.6,0.8,1,1.5,1.1l0,0c1.6,0.2,2.2,2.2,1.1,3.3l0,0c-0.5,0.4-0.7,1.1-0.6,1.7v0 c0.3,1.6-1.4,2.8-2.8,2l0,0c-0.6-0.3-1.2-0.3-1.8,0h0c-1.4,0.7-3.1-0.5-2.8-2v0c0.1-0.6-0.1-1.3-0.6-1.7l0,0 c-1.1-1.1-0.5-3.1,1.1-3.3l0,0c0.6-0.1,1.2-0.5,1.5-1.1v0C212.5,119.8,214.5,119.8,215.2,121.3z"/> <path class="conf3" id="b3" d="M224.5,191.7L224.5,191.7c0.3,0.6,0.8,1,1.5,1.1l0,0c1.6,0.2,2.2,2.2,1.1,3.3v0c-0.5,0.4-0.7,1.1-0.6,1.7l0,0 c0.3,1.6-1.4,2.8-2.8,2h0c-0.6-0.3-1.2-0.3-1.8,0l0,0c-1.4,0.7-3.1-0.5-2.8-2l0,0c0.1-0.6-0.1-1.3-0.6-1.7v0 c-1.1-1.1-0.5-3.1,1.1-3.3l0,0c0.6-0.1,1.2-0.5,1.5-1.1l0,0C221.7,190.2,223.8,190.2,224.5,191.7z"/> <path class="conf3" id="a2" d="M312.6,242.1L312.6,242.1c0.3,0.6,0.8,1,1.5,1.1l0,0c1.6,0.2,2.2,2.2,1.1,3.3l0,0c-0.5,0.4-0.7,1.1-0.6,1.7v0 c0.3,1.6-1.4,2.8-2.8,2l0,0c-0.6-0.3-1.2-0.3-1.8,0h0c-1.4,0.7-3.1-0.5-2.8-2v0c0.1-0.6-0.1-1.3-0.6-1.7l0,0 c-1.1-1.1-0.5-3.1,1.1-3.3l0,0c0.6-0.1,1.2-0.5,1.5-1.1v0C309.9,240.6,311.9,240.6,312.6,242.1z"/> <path class="conf8" id="yellow-strip" d="M290.7,215.4c0,0-14.4-3.4-22.6,2.7c-8.2,6.2-8.2,23.3-17.1,29.4c-8.9,6.2-19.8-2.7-32.2-4.1 c-12.3-1.4-19.2,5.5-20.5,10.9"/> </g> </svg>

    </body>
</html>

<script>
function Wo_SubmitButton() {
    $('button').attr('disabled', true);
    $('button').text('Please wait..');
    $('form').submit();
}
    $(function() {
        $('#agree').change(function() {
            if($(this).is(":checked")) {
                $('#next-terms').attr('disabled', false);
            } else {
              $('#next-terms').attr('disabled', true);
            }
        });
    });
</script>
