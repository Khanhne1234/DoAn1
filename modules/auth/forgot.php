<?php
if(!defined('_Khanh'))
{
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Quên mật khẩu'
];
layout('header-auth', $data);
?>
<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="<?php echo _HOST_URL_TEMPLATES ?>/assets/image/AnhLogin.webp" class="img-fluid"
                    alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form>
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <h2 class=" fw-normal mb-5 me-3">Quên mật khẩu</h2>
                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" id="form3Example3" class="form-control form-control-lg"
                            placeholder="Nhập địa chỉ email" />
                    </div>
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
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