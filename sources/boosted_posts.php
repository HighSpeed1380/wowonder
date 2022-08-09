<?php
if ($wo['loggedin'] == false) {
   header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
   exit();
}
if ($wo['config']['pro'] == 0) {
	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
if ($wo['user']['is_pro'] == 0) {
	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
// if ($wo['user']['pro_type'] == 1) {
// 	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
//     exit();
// }
if (in_array($wo['user']['pro_type'], array_keys($wo['pro_packages'])) && $wo['pro_packages'][$wo['user']['pro_type']]['posts_promotion'] < 1) {
	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'boosted_posts';
$wo['title']       = $wo['lang']['boosted_posts'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('boosted-posts/content');