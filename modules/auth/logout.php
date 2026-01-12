<?php
if (!defined('_Khanh')) {
    die('Truy cập không hợp lệ');
}

if (isLogin()) {
    $token = getSession('token_login');
    //xóa token trong bảng token_login
    $removeToken = delete('token_login', "token = '$token'");
    if ($removeToken) {
        removeSession('token_login');
        redirect('?module=auth&action=login');
    } else {
        setSessionFlash('msg', 'Lỗi hệ thống, vui lòng thử lại sau.');
        setSessionFlash('msg_type', 'danger');
    }
} else {
    setSessionFlash('msg', 'Lỗi hệ thống, vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'danger');
}
