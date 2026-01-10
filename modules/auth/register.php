<?php
if(!defined('_Khanh'))
{
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Đăng ký tài khoản'
];
layout('header-auth', $data);
//kiểm tra xử lý dữ liệu nhập vào
if(isPost())
{
    $filter = filterData();
    $errors = [];

    //validate fullname
    if(empty($filter['fullname']))
    {
        $errors['fullname']['required'] = 'Họ tên bắt buột phải nhập';
    }
    else
    {
        if(strlen($filter['fullname']) < 5)
        {
            $errors['fullname']['length'] = 'Họ tên phải lớn hơn 5 ký tự';
        }
    }

    //validate email
    if(empty($filter['email']))
    {
        $errors['email']['required'] = 'Email bắt buột phải nhập';
    }
    else
    {
        //Đúng định dạng email 
        if(!validateEmail(trim($filter['email'])))
        {
             $errors['email']['isEmail'] = 'Email không đúng định dạng';
        }
        else{
            //Email này đã tồn tại hay chưa
            $email = $filter['email'];
            $checkEmail = getRows("SELECT * FROM users WHERE email = '$email'");
            if($checkEmail > 0)
            {
                 $errors['email']['check'] = 'Email đã tồn tại';
            }
        }
    }

    //validate phone
    if(empty($filter['phone']))
    {
        $errors['phone']['required'] = 'Số điện thoại bắt buột phải nhập';
    }
    else
    {
        if(!isPhone($filter['phone']))
        {
            $errors['phone']['isPhone'] = 'Số điện thoại không đúng định dạng';
        }
    }

    //validate Password
    if(empty(trim($filter['password'])))
    {
         $errors['password']['required'] = 'Mật bắt buột phải nhập';
    }
    else
    {
        if(strlen(trim($filter['password'])) < 6)
        {
             $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 ký tự';
        }
    }

    //validate confirm_pass
     if(empty(trim($filter['password'])))
    {
         $errors['confirm_pass']['required'] = 'Vui lòng nhập lại mật khẩu';
    }
    else
    {
        if(trim($filter['password']) !== trim($filter['confirm_pass']) )
        {
             $errors['confirm_pass']['like'] = 'Mật khẩu nhập lại không khớp';
        }
    }
    //thông báo khi đăng kí không nhập sẽ hiện lỗi
    if(empty($errors))
    {
       //table: users, data
       $activeToken = sha1(uniqid().time());;
       $data = [
        'fullname' => $filter['fullname'],
        'address'  => $filter['address'],
        'phone'    => $filter['phone'],
        'password' => password_hash($filter['password'], PASSWORD_DEFAULT),
        'email'    => $filter['email'],
        'active_token' => $activeToken,
        'group_id' =>  1,
        'created_at' => date('Y:m:d H:i:s')
       ];

       $insertStatus = insert('users', $data);
       if($insertStatus)
       {
        $emailTo = $filter['email'];
        $subject = 'Kích hoạt tài khoản hệt thống khóa học!!';
        $content = 'Chúc mừng bạn đã đăng ký thành công tài khoản. </br>';
        $content .= 'Để kích hoạt tài khoản, bạn hãy click vào đường link bên dưới: </br>';
        $content .= _HOST_URL . '/?module=auth&action=active&token='.$activeToken . '</br>';
        $content .= 'Cảm ơn bạn đã ủng hộ khóa học này!!';


        //gửi mail
        sendMail($emailTo, $subject, $content);

            setSessionFlash('msg', 'Đăng ký thành công, vui lòng kích hoạt tài khoản');
            setSessionFlash('msg_type', 'success'); 
       }
       else
        {
            setSessionFlash('msg', 'Đăng ký không thành công, vui lòng thử lại sau.');
            setSessionFlash('msg_type', 'danger');
        }
    }
    else
    {
           setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');  
           setSessionFlash('msg_type', 'danger'); 
           setSessionFlash('oldData', $filter);
           setSessionFlash('errors', $errors);
    }
    //lưu lại dữ liệu cũ khi nhập vào ô input
    $msg = getSessionFlash('msg');
    $msg_type = getSessionFlash('msg_type');
    $oldData = getSessionFlash('oldData');
    $errorsArr =   getSessionFlash('errors');
}
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
                if(!empty($msg) & !empty($msg_type))
                {
                     getMsg($msg,$msg_type);
                }
                ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <h2 class=" fw-normal mb-5 me-3">Đăng ký tài khoản </h2>
                    </div>
                    <!-- Name input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input name="fullname" type="text" value="<?php
                        if(!empty($oldData))
                        {
                             echo oldData($oldData, 'fullname');
                        }
                        ?>" class="form-control form-control-lg" placeholder="Nhập họ tên" />
                        <!--hiển thị lỗi -->
                        <?php
                        if(!empty($errorsArr))
                        {
                             echo formError($errorsArr, 'fullname');
                        }
                          
                        ?>
                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input name="email" value="<?php 
                         if(!empty($oldData))
                        {
                             echo oldData($oldData, 'email');
                        }
                      ?>" type="text" class="form-control form-control-lg" placeholder="Nhập địa chỉ email" />
                        <?php
                         if(!empty($errorsArr))
                        {
                           echo formError($errorsArr, 'email');
                        }
                        ?>
                    </div>
                    <!-- sdt input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input name="phone" value="<?php 
                         if(!empty($oldData))
                        {
                             echo oldData($oldData, 'phone');
                        }
                        ?>" type="text" class="form-control form-control-lg" placeholder="Nhập số điện thoại" />
                        <?php
                         if(!empty($errorsArr))
                        {
                               echo formError($errorsArr, 'phone');
                        }
                        ?>
                    </div>
                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input name="password" type="password" class="form-control form-control-lg"
                            placeholder="Nhập mật khẩu" />
                        <?php
                         if(!empty($errorsArr))
                        {
                            echo formError($errorsArr, 'password');
                        }                   
                        ?>
                    </div>
                    <!--  Nhap lai mat khau input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input name="confirm_pass" type="password" class="form-control form-control-lg"
                            placeholder="Nhập lại mật khẩu" />
                        <?php
                         if(!empty($errorsArr))
                        {
                           echo formError($errorsArr, 'confirm_pass');
                        }
                        ?>
                    </div>
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Đăng ký</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Bạn đã có tài khoản?<a
                                href="<?php echo _HOST_URL; ?>?module=auth&action=login" class="link-danger">Đăng
                                nhập
                                ngay</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
layout('footer');