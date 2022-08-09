<?php
if ($wo['config']['forum_visibility'] == 1) {
	if ($wo['loggedin'] == false) {
	  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
	  exit();
	}
}
if ($wo['config']['forum'] == 0) {
	header("Location: " . $wo['config']['site_url']);
    exit();
}
if (isset($_GET['tid']) && is_numeric($_GET['tid'])) {
	$reply  = Wo_GetThreadReplies(array("id" => $_GET['tid']));
	if (count($reply) > 0) {
		if ($reply[0]['poster_id'] == $wo['user']['id'] || $wo['user']['admin'] == 1) {
			$wo['description'] = $wo['config']['siteDesc'];
			$wo['keywords']    = $wo['config']['siteKeywords'];
			$wo['page']        = 'forum';
			$wo['active']      = null;
			$wo['bbcodeditor'] = true;
			$wo['reply']       = $reply[0];
			$wo['title']       = $wo['config']['siteTitle'] ;
			$wo['content']     = Wo_LoadPage('forum/editreply');
		}

	}

}
