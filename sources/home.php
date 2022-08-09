<?php
if ($wo['loggedin'] == false) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
if ($wo['loggedin'] == true) {
	if (!empty($_COOKIE['last_sidebar_update'])) {
        if ($_COOKIE['last_sidebar_update'] < (time() - 120)) {
            Wo_CleanCache();
        }
    }
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'home';
$wo['title']       = $wo['config']['siteTitle'];
$pg = 'home/content';
if ($wo['config']['website_mode'] == 'instagram') {
    $pg = 'mode_instagram/home/content';
}
$wo['content']     = Wo_LoadPage($pg);
