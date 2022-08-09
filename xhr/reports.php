<?php
if ($f == 'reports') {
    if ($s == 'report_user' && isset($_POST['user']) && is_numeric($_POST['user'])) {
        $user = Wo_Secure($_POST['user']);
        $text = (!empty($_POST['text']) ? Wo_Secure($_POST['text']) : '');
        $reason = (!empty($_POST['reason']) && in_array($_POST['reason'], $wo['config']['report_reasons']) ? Wo_Secure($_POST['reason']) : '');
        $code = Wo_ReportUser($user, $text,$reason);
        $data = array(
            'status' => 304
        );
        if ($code == 0) {
            $data['status'] = 200;
            $data['code']   = 0;
        } else if ($code == 1) {
            $data['status'] = 200;
            $data['code']   = 1;
            $data['message']   = $wo['lang']['request_submitted'];
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'unreport_user' && isset($_POST['user']) && is_numeric($_POST['user'])) {
        $user = Wo_Secure($_POST['user']);
        $code = Wo_ReportUser($user);
        $data['status'] = 200;
        $data['message']   = $wo['lang']['request_removed'];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'report_page' && isset($_POST['page']) && is_numeric($_POST['page']) && isset($_POST['text'])) {
        $page = Wo_Secure($_POST['page']);
        $text = Wo_Secure($_POST['text']);
        $code = Wo_ReportPage($page, $text);
        $data = array(
            'status' => 304
        );
        if ($code == 0) {
            $data['status'] = 200;
            $data['code']   = 0;
        } else if ($code == 1) {
            $data['status'] = 200;
            $data['code']   = 1;
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'report_group' && isset($_POST['group']) && is_numeric($_POST['group']) && isset($_POST['text'])) {
        $group = Wo_Secure($_POST['group']);
        $text  = Wo_Secure($_POST['text']);
        $code  = Wo_ReportGroup($group, $text);
        $data  = array(
            'status' => 304
        );
        if ($code == 0) {
            $data['status'] = 200;
            $data['code']   = 0;
        } else if ($code == 1) {
            $data['status'] = 200;
            $data['code']   = 1;
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
