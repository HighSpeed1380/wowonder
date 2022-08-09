<?php
if ($wo['loggedin'] == false || $wo['config']['funding_system'] != 1) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
if (!$wo['config']['can_use_funding']) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'create_funding';
$wo['title']       = $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('create_funding/content');
