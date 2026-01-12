<?php
if (!defined('_Khanh')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Danh sách người dùng'
];
layout('header', $data);
layout('sidebar');

$filter = filterData();
$chuoiWhere = '';
$group = '';
$keyword = '';

if (isGet()) {
    $keyword = isset($filter['keyword']) ? trim($filter['keyword']) : '';
    $group = isset($filter['group']) ? $filter['group'] : '';

    //escape ký tự đặc biệt
    $keyword_esc = addslashes($keyword);

    //kiểm tra từ khóa tìm kiếm
    if ($keyword !== '') {
        if (strpos($chuoiWhere, 'WHERE') === false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "(fullname LIKE '%$keyword_esc%' OR email LIKE '%$keyword_esc%') ";
    }
    //kiểm tra nhóm người dùng
    if ($group !== '' && $group !== null) {
        $group_id = (int)$group;
        if (strpos($chuoiWhere, 'WHERE') === false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "group_id = $group_id ";
    }
}

//xử lý phân trang
$maxData = getRows("SELECT id FROM users"); //tổng số bản ghi
$perPage = 3; //số bản ghi trên 1 trang
$maxPage = ceil($maxData / $perPage); //tổng số trang
$offSet = 0; //vị trí bắt đầu lấy dữ liệu
$page = 1; // trang hiện tại

//get page
if (isset($filter['page'])) {
    $page = $filter['page'];
}

if ($page > $maxPage || $page < 1) {
    $page = 1;
}

//vị trí bắt đầu lấy dữ liệu
if (isset($page)) {
    $offSet = ($page - 1) * $perPage;
}

//lấy dữ liệu người dùng
$getDataUser = getAll("SELECT a.id, a.fullname, a.email, a.created_at, b.name 
FROM users a INNER JOIN `groups` b
ON a.group_id = b.id $chuoiWhere
ORDER BY a.created_at ASC
LIMIT $offSet, $perPage
");

$getGroup = getAll("SELECT * FROM `groups`");
?>
<div class="container grid-user">
    <div class="container-fluid">
        <a href="?module=user&action=add" class="btn btn-success mb-3"><i class="fa-solid fa-plus"></i>Thêm mới người dùng</a>
        <form class="mb-3" action="" method="get">
            <input type="hidden" name="module" value="user">
            <input type="hidden" name="action" value="list">

            <div class="row">
                <div class="col-3">
                    <select class="form-select form-control" name="group" id="">
                        <option value="">Nhóm người dùng</option>
                        <?php
                        foreach ($getGroup as $item):
                        ?>
                            <option value="<?php echo $item['id']; ?>" <?php echo ($group == $item['id']) ? 'selected' : false; ?>><?php echo $item['name']; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-7">
                    <input type="text" class="form-control" value="<?php echo (!empty($keyword)) ? $keyword : false; ?>" name="keyword" placeholder="Nhập thông tin tìm kiếm...">
                </div>

                <div class="col-2">
                    <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Họ tên</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ngày đăng ký</th>
                    <th scope="col">Nhóm</th>
                    <th scope="col">Phân quyền</th>
                    <th scope="col">Sửa</th>
                    <th scope="col">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($getDataUser as $key => $item):

                ?>
                    <tr>
                        <th scope="row"><?php echo $key + 1; ?></th>
                        <td><?php echo $item['fullname']; ?></td>
                        <td><?php echo $item['email']; ?></td>
                        <td><?php echo $item['created_at']; ?></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><a href="?module=users&action=permission&id=<?php echo $item['id']; ?>" class="btn btn-primary">Phân quyền</a></td>
                        <td><a href="?module=users&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-warning"><i class="fa-solid fa-pencil"></i></a></td>
                        <td><a href="?module=users&action=permission&id=<?php echo $item['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa không')" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                    </tr>
                <?php
                endforeach;
                ?>
            </tbody>
        </table>
        <!--Phân trang -->
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <!--Xư lý nút trước -->
                <?php
                if ($page > 1):
                ?>
                    <li class="page-item"><a class="page-link" href="?module=user&action=list&page=<?php echo $page - 1; ?>">Trước</a></li>
                <?php
                endif;
                ?>
                <!--Xư lý nút ... -->
                <?php
                $start = $page - 1;
                if ($start < 1) {
                    $start = 1;
                }
                ?>
                <?php
                if ($start > 1):
                ?>
                    <li class="page-item"><a class="page-link" href="?module=user&action=list&page=<?php echo $page - 1; ?>">...</a></li>

                <?php
                endif;

                $end = $page + 1;
                if ($end > $maxPage) {
                    $end = $maxPage;
                }
                ?>

                <?php
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <li class="page-item <?php echo ( $page == $i) ? 'active' : false; ?>"><a class="page-link" 
                    href="?module=user&action=list&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php
                endfor;
                if ($end < $maxPage):
                ?>
                    <li class="page-item "><a class="page-link" href="?module=user&action=list&page=<?php echo $page - 1; ?>">...</a></li>
                <?php
                endif;
                ?>
                <!--Xư lý nút sau -->
                <?php

                if ($page < $maxPage):
                ?>
                    <li class="page-item"><a class="page-link" href="?module=user&action=list&page=<?php echo $page + 1; ?>">Sau</a></li>
                <?php
                endif;
                ?>

            </ul>
        </nav>
    </div>
</div>
<?php
layout('footer');
?>