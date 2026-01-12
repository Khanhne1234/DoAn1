<?php
if (!defined('_Khanh')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Quên mật khẩu'
];
layout('header-auth', $data);

if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate email
    if (empty($filter['email'])) {
        $errors['email']['required'] = 'Email bắt buột phải nhập';
    } else {
        //Đúng định dạng email 
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
        }
    }

    if (empty($errors)) {
        //xử lý và gửi email
        if (!empty($filter['email'])) {
            $email = $filter['email'];

            $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'");
            if (!empty($checkEmail)) {
                //Update forget_token vào bảng users
                $forget_token = sha1(uniqid() . time());
                $data = [
                    'forget_token' => $forget_token
                ];
                $condition = "id= " . $checkEmail['id'];
                $updateStatus = update('users', $data, $condition);
                if ($updateStatus) {
                    $emailTo = $email;
                    $subject = 'Reset mật khẩu tài khoản hệt thống khóa học!!';
                    $content = 'Bạn đang cầu reset lại mật khẩu trên hệ thống khóa học. </br>';
                    $content .= 'Để thay đổi mật khẩu, bạn hãy click vào đường link bên dưới: </br>';
                    $content .= _HOST_URL . '/?module=auth&action=reset&token=' . $forget_token . '</br>';
                    $content .= 'Cảm ơn bạn đã ủng hộ khóa học này!!';
                    //gửi mail
                    sendMail($emailTo, $subject, $content);

                    setSessionFlash('msg', 'Gửi yêu cầu thành công, vui lòng kiểm tra email để thay đổi mật khẩu.');
                    setSessionFlash('msg_type', 'success');
                } else {
                    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại sau.');
                    setSessionFlash('msg_type', 'danger');
                }
            }
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorsArr =   getSessionFlash('errors');

?>
<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="<?php echo _HOST_URL_TEMPLATES ?>/assets/image/AnhLogin.webp" class="img-fluid"
                    alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <?php
                if (!empty($msg) & !empty($msg_type)) {
                    getMsg($msg, $msg_type);
                }
                ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <h2 class=" fw-normal mb-5 me-3">Quên mật khẩu</h2>
                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" name="email" value="<?php
                                                                if (!empty($oldData)) {
                                                                    echo oldData($oldData, 'email');
                                                                }
                                                                ?>" id="form3Example3" class="form-control form-control-lg"
                            placeholder="Nhập địa chỉ email" />
                        <?php
                        if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'email');
                        }
                        ?>
                    </div>
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Quay lại trang đăng nhập!<a
                                href="<?php echo _HOST_URL; ?>?module=auth&action=login" class="link-danger">Đăng nhập
                                ngay</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
layout('footer');
