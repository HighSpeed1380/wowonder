<?php
if ($wo['config']['website_mode'] != 'instagram') {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}

$explore_posts = $db->where('postPrivacy','0')->where('multi_image_post','0')->where('active',1)->where("((postFile LIKE '%.jpg%' || postFile LIKE '%.jpeg%' || postFile LIKE '%.png%' || postFile LIKE '%.gif%' || postFile LIKE '%.mp4%' || postFile LIKE '%.mkv%' || postFile LIKE '%.avi%' || postFile LIKE '%.webm%' || postFile LIKE '%.mov%' || postFile LIKE '%.m3u8%' || postSticker != '' || postPhoto != '' || album_name != '' || multi_image = '1'))")->orderBy('id','DESC')->get(T_POSTS,15,array('id'));
$wo['explore_posts'] = array();
foreach ($explore_posts as $key => $value) {
    $post = Wo_PostData($value->id);
    $post['model_photo'] = $post['post_id'];
    $post['model_type'] = 'image';
    if (!empty($post['postFile']) && (strpos($post['postFile'], '.jpg') !== false || strpos($post['postFile'], '.jpeg') !== false || strpos($post['postFile'], '.png') !== false || strpos($post['postFile'], '.gif') !== false) ) {
        $post['main_thumb'] = Wo_GetMedia($post['postFile']);
    }
    if (!empty($post['postFile']) && (strpos($post['postFile'], '.mp4') !== false || strpos($post['postFile'], '.mkv') !== false || strpos($post['postFile'], '.avi') !== false || strpos($post['postFile'], '.webm') || strpos($post['postFile'], '.mov') || strpos($post['postFile'], '.m3u8') !== false )) {
        $post['model_type'] = 'video';
        if (!empty($post['postFileThumb'])) {
            $post['main_thumb'] = Wo_GetMedia($post['postFileThumb']);
        }
        else{
            $post['main_thumb'] = Wo_GetMedia('upload/photos/d-film.jpg');
        }
    }
    if (!empty($post['postSticker'])) {
        $post['main_thumb'] = $post['postSticker'];
    }
    if (!empty($post['album_name'])) {
        $post['model_type'] = 'album';
        if (!empty($post['photo_album'][0]['parent_id'])) {
            $post['model_photo'] = $post['photo_album'][0]['parent_id'];
        }
        $post['main_thumb'] = Wo_GetMedia($post['photo_album'][0]['image_org']);
    }
    if (!empty($post['multi_image'])) {
        $post['model_type'] = 'multi_image';
        $post['model_photo'] = $post['photo_multi'][0]['parent_id'];
        $post['main_thumb'] = Wo_GetMedia($post['photo_multi'][0]['image_org']);
    }
    $wo['explore_posts'][] = $post;
}
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'explore';
$wo['title']       = $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('mode_instagram/explore/explore');