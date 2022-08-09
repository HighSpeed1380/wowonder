<?php 
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2017 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
require_once('assets/init.php');

$query_one = "SELECT `user_id`, `pro_type`, `pro_time` FROM " . T_USERS . " WHERE `is_pro` = '1' ORDER BY `user_id` ASC";
$sql       = mysqli_query($sqlConnect, $query_one);
if (mysqli_num_rows($sql)) {
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $update_data = false;
        foreach ($wo['pro_packages'] as $key => $value) {
            if ($value['id'] == $fetched_data["pro_type"] && $value['ex_time'] > 0 && $fetched_data["pro_time"] < time() - $value['ex_time']) {
                $update_data = true;
            }
        }
        if ($update_data == true) {
            $user_id     = $fetched_data["user_id"];
            $mysql_query = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `is_pro` = '0',`verified` = '0' WHERE `user_id` = {$user_id}");
            $mysql_query = mysqli_query($sqlConnect, "UPDATE " . T_PAGES . " SET `boosted` = '0' WHERE `user_id` = {$user_id}");
            $mysql_query = mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `boosted` = '0' WHERE `user_id` = {$user_id}");
            $mysql_query = mysqli_query($sqlConnect, "UPDATE " . T_POSTS . " SET `boosted` = '0' WHERE `page_id` IN (SELECT `page_id` FROM " . T_PAGES . " WHERE `user_id` = {$user_id})");
        }
    }
}