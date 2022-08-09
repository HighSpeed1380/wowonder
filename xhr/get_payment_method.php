<?php 
if ($f == 'get_payment_method') {
    if (!empty($_GET['type'])) {
        $html            = '';
        if (in_array($_GET['type'], array_keys($wo["pro_packages"]))) {
            $type        = Wo_Secure($_GET['type']);
            $description = $wo["pro_packages"][$_GET['type']]['name'].' package';
            if (strpos($wo["pro_packages"][$_GET['type']]['price'], ".") !== false) {
                $price = str_replace('.', "", $wo["pro_packages"][$_GET['type']]['price']);
            } else {
                $price = $wo["pro_packages"][$_GET['type']]['price'] . '00';
            }
            $wo['hide'] = false;
            if (strpos($_SERVER["HTTP_REFERER"], 'wallet') !== false) {
                $wo['hide'] = true;
            }
            $load = Wo_LoadPage('modals/pay-go-pro');
            $load = str_replace('{pro_type}', $type, $load);
            $load = str_replace('{pro_type_id}', $_GET['type'], $load);
            $load = str_replace('{pro_type_description}', $description, $load);
            $load = str_replace('{pro_type_price}', $price, $load);
            
            if (!empty($load)) {
                $data = array(
                    'status' => 200,
                    'html' => $load
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
