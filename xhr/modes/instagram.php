<?php
if ($wo['config']['website_mode'] != 'instagram') {
	exit();
}
$data = array();
if ($f == 'explore') {
    if ($s == 'load_more_posts') {
    	$data['status'] = 400;
    	if (!empty($_POST['after_post_id']) && is_numeric($_POST['after_post_id'])) {
    		$after_post_id = Wo_Secure($_POST['after_post_id']);
    		$explore_posts = $db->where('postPrivacy','0')->where('multi_image_post','0')->where('active',1)->where('id',$after_post_id,'<')->where("((postFile LIKE '%.jpg%' || postFile LIKE '%.jpeg%' || postFile LIKE '%.png%' || postFile LIKE '%.gif%' || postFile LIKE '%.mp4%' || postFile LIKE '%.mkv%' || postFile LIKE '%.avi%' || postFile LIKE '%.webm%' || postFile LIKE '%.mov%' || postFile LIKE '%.m3u8%' || postSticker != '' || postPhoto != '' || album_name != '' || multi_image = '1'))")->orderBy('id','DESC')->get(T_POSTS,20,array('id'));
			$html = '';
			foreach ($explore_posts as $key => $value) {
				$wo['story'] = Wo_PostData($value->id);
				$wo['story']['model_photo'] = $wo['story']['post_id'];
			    $wo['story']['model_type'] = 'image';
			    if (!empty($wo['story']['postFile']) && (strpos($wo['story']['postFile'], '.jpg') !== false || strpos($wo['story']['postFile'], '.jpeg') !== false || strpos($wo['story']['postFile'], '.png') !== false || strpos($wo['story']['postFile'], '.gif') !== false) ) {
			        $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['postFile']);
			    }
			    if (!empty($wo['story']['postFile']) && (strpos($wo['story']['postFile'], '.mp4') !== false || strpos($wo['story']['postFile'], '.mkv') !== false || strpos($wo['story']['postFile'], '.avi') !== false || strpos($wo['story']['postFile'], '.webm') || strpos($wo['story']['postFile'], '.mov') || strpos($wo['story']['postFile'], '.m3u8') !== false )) {
			        $wo['story']['model_type'] = 'video';
			        if (!empty($wo['story']['postFileThumb'])) {
			            $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['postFileThumb']);
			        }
			        else{
			            $wo['story']['main_thumb'] = Wo_GetMedia('upload/photos/d-film.jpg');
			        }
			    }
			    if (!empty($wo['story']['postSticker'])) {
			        $wo['story']['main_thumb'] = $wo['story']['postSticker'];
			    }
			    if (!empty($wo['story']['album_name'])) {
			        $wo['story']['model_type'] = 'album';
			        if (!empty($wo['story']['photo_album'][0]['parent_id'])) {
			            $wo['story']['model_photo'] = $wo['story']['photo_album'][0]['parent_id'];
			        }
			        $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['photo_album'][0]['image_org']);
			    }
			    if (!empty($wo['story']['multi_image'])) {
			        $wo['story']['model_type'] = 'multi_image';
			        $wo['story']['model_photo'] = $wo['story']['photo_multi'][0]['parent_id'];
			        $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['photo_multi'][0]['image_org']);
			    }
			    $html .= Wo_LoadPage('mode_instagram/explore/list');
			}
			$data['html'] = $html;
			$data['status'] = 200;
    	}
    	else{
    		$data['message'] = $error_icon . $wo['lang']['id_empty'];
    	}
    	header("Content-type: application/json");
	    echo json_encode($data);
	    exit();
    }
    elseif ('load_more_profile_posts') {
    	$data['status'] = 400;
    	if (!empty($_POST['after_post_id']) && is_numeric($_POST['after_post_id']) && !empty($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    		$after_post_id = Wo_Secure($_POST['after_post_id']);
    		$explore_posts = $db->where('postPrivacy','0')->where('multi_image_post','0')->where('user_id',Wo_Secure($_POST['user_id']))->where('active',1)->where('id',$after_post_id,'<')->where("((postFile LIKE '%.jpg%' || postFile LIKE '%.jpeg%' || postFile LIKE '%.png%' || postFile LIKE '%.gif%' || postFile LIKE '%.mp4%' || postFile LIKE '%.mkv%' || postFile LIKE '%.avi%' || postFile LIKE '%.webm%' || postFile LIKE '%.mov%' || postFile LIKE '%.m3u8%' || postSticker != '' || postPhoto != '' || album_name != '' || multi_image = '1'))")->orderBy('id','DESC')->get(T_POSTS,null,array('id'));
			$html = '';
			foreach ($explore_posts as $key => $value) {
				$wo['story'] = Wo_PostData($value->id);
				$wo['story']['model_photo'] = $wo['story']['post_id'];
			    $wo['story']['model_type'] = 'image';
			    if (!empty($wo['story']['postFile']) && (strpos($wo['story']['postFile'], '.jpg') !== false || strpos($wo['story']['postFile'], '.jpeg') !== false || strpos($wo['story']['postFile'], '.png') !== false || strpos($wo['story']['postFile'], '.gif') !== false) ) {
			        $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['postFile']);
			    }
			    if (!empty($wo['story']['postFile']) && (strpos($wo['story']['postFile'], '.mp4') !== false || strpos($wo['story']['postFile'], '.mkv') !== false || strpos($wo['story']['postFile'], '.avi') !== false || strpos($wo['story']['postFile'], '.webm') || strpos($wo['story']['postFile'], '.mov') || strpos($wo['story']['postFile'], '.m3u8') !== false )) {
			        $wo['story']['model_type'] = 'video';
			        if (!empty($wo['story']['postFileThumb'])) {
			            $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['postFileThumb']);
			        }
			        else{
			            $wo['story']['main_thumb'] = Wo_GetMedia('upload/photos/d-film.jpg');
			        }
			    }
			    if (!empty($wo['story']['postSticker'])) {
			        $wo['story']['main_thumb'] = $wo['story']['postSticker'];
			    }
			    if (!empty($wo['story']['album_name'])) {
			        $wo['story']['model_type'] = 'album';
			        if (!empty($wo['story']['photo_album'][0]['parent_id'])) {
			            $wo['story']['model_photo'] = $wo['story']['photo_album'][0]['parent_id'];
			        }
			        $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['photo_album'][0]['image_org']);
			    }
			    if (!empty($wo['story']['multi_image'])) {
			        $wo['story']['model_type'] = 'multi_image';
			        $wo['story']['model_photo'] = $wo['story']['photo_multi'][0]['parent_id'];
			        $wo['story']['main_thumb'] = Wo_GetMedia($wo['story']['photo_multi'][0]['image_org']);
			    }
			    $html .= Wo_LoadPage('mode_instagram/explore/list');
			}
			$data['html'] = $html;
			$data['status'] = 200;
    	}
    	else{
    		$data['message'] = $error_icon . $wo['lang']['id_empty'];
    	}
    	header("Content-type: application/json");
	    echo json_encode($data);
	    exit();
    }
}
