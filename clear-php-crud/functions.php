<?php if(!defined('ROOT'))  die('Direct request not allowed!'); ?>
<?php


function db_query($query = false){
    // $query = trim($query);
	if(!$query) return DB::getInstance();

    if (stripos($query, 'select') === 0 || stripos($query, 'show') === 0 || stripos($query, 'describe') === 0) {
        return DB::getInstance()->get_results($query);
    }else{
        return DB::getInstance()->query($query);
    }
}

function db_get_row($query)
{
    $res = db_query($query);
    if($res) return $res[0];
    return [];
}

function db_escape($string)
{
    return DB::getInstance()->escape($string);
}
// echo '<pre>';
// print_r($_GET);
// echo '</pre>';

function _get($key, $defaul = '')
{
    if(!empty($_GET[$key])){
        return $_GET[$key];
    }else{
        return $defaul;
    }
}

function is_tab_active($current_tab)
{
    if(isset($_GET['tab']) && $_GET['tab'] == $current_tab){
        return 'active';
    }else{
        return '';
    }
}

function menu_item_active($current_tab)
{
    if(isset($_GET['action']) && $_GET['action'] == $current_tab){
        return 'active';
    }else{
        return '';
    }
}

function menu_sub_active($menu_name)
{
    if (isset($_GET['action']) && strpos($_GET['action'], $menu_name) === 0) {
        return 'active';
    }
    return '';
}

function tovarov($count)
{
    if($count === 0) return 'Ничего не найдено';

    if($count === 1) return 'Найден 1 товар';
    
    if($count < 5) return "Найдено $count товара";

    if($count > 49) return "Показано первые $count товаров";

    else return "Найдено $count товаров";
}


function get_product_params($product)
{
    return http_build_query($product) . '&tab=1';
}

function auth_check()
{
    if (isset($_SESSION['user'])) {
        return true;
    }else{
        return false;
    }
}

function auth_admin($if_true = 1, $if_false = '')
{
    if (auth_check() && $_SESSION['user']['role'] === 'admin') {
        return $if_true;
    }
    return $if_false;
}

function auth_user($key = false)
{
    if(!auth_check()) return null;

    if($key) return $_SESSION['user'][$key];

    return $_SESSION['user'];
}

function sklonenie($count, $p1, $p2, $p3)
{
    // if($count > 10 && $count < 15) return $p3;
    if(in_array($count, [11,12,13,14])) return $p3;
    // $count = 234
    $last_digit = $count % 10; // 1 % 10 = 0.[1]
    if ($last_digit == 1) {
        return $p1;
    }
    if ($last_digit == 2 || $last_digit == 3 || $last_digit == 4) {
        return $p2;
    }
    // if (in_array($last_digit, [2, 3, 4])) {
    //     return $p2;
    // }
    return $p3;
}

function bs_pagination($offset, $limit, $total_count)
{
    // if($total_count < $limit) return '';
    $new_offset = in_range($offset - $limit, 0, $total_count);
    $prev_link = query_add(['offset' => $new_offset]);
    $new_offset = in_range($offset + $limit, 0, $total_count);
    $next_link = query_add(['offset' => $new_offset]);
?>
<div>
    <ul class="pagination">
        <?php if($offset > 0): ?>
            <li class='page-item'><a href="<?= $prev_link ?>" class="page-link active"><i class="bi bi-arrow-left"></i></a></li>
        <?php else: ?>
            <li class='page-item disabled'><a class="page-link disabled"><i class="bi bi-arrow-left"></i></a></li>
        <?php endif ?>
        <?php
        $left_prefix = $right_prefix = '';
        $link_first_page = query_add(['offset' => 0]);
        $last_page = ceil($total_count / $limit);
        $link_last_page = query_add(['offset' => $limit * ($last_page - 1)]);
        $current_page = floor($offset / $limit);
        $from = $current_page - 2;
        if($current_page >= 3) $left_prefix = "
            <li class='page-item'><a class='page-link' href='$link_first_page'>1</a></li>
            <li class='page-item'><span class='page-link'>...</span></li>";
        if($current_page == 3) $left_prefix = "
            <li class='page-item'><a class='page-link' href='$link_first_page'>1</a></li>";
        if($from < 0) $from = 0;
        $to = $current_page + 3;
        if($to > $last_page) $to = $last_page;
        if($last_page - $current_page > 3) $right_prefix = "
            <li class='page-item'><span class='page-link'>...</span></li>
            <li class='page-item'><a class='page-link' href='$link_last_page'>$last_page</a></li>";
        if($last_page - $current_page == 4) $right_prefix = "
            <li class='page-item'><a class='page-link' href='$link_last_page'>$last_page</a></li>";
        echo $left_prefix;
        for ($i=$from; $i < $to; $i++):
            $link = query_add(['offset' => $i * $limit]);
        ?>
            <?php if($current_page == $i): ?>
                <li class='page-item active'><a class="page-link active"><?= $i + 1 ?></a></li>
            <?php else: ?>
                <li class='page-item'><a class='page-link' href="<?= $link ?>"><?= $i + 1 ?></a></li>
            <?php endif ?>
        <?php endfor;
        echo $right_prefix; ?>
        <?php if($total_count > $offset + $limit): ?>
            <li class='page-item'><a href="<?= $next_link ?>" class="page-link active"><i class="bi bi-arrow-right"></i></a></li>
        <?php else: ?>
            <li class='page-item disabled'><a class="page-link disabled"><i class="bi bi-arrow-right"></i></a></li>
        <?php endif ?>
    </ul>
</div>
<?php
}




function in_range($number, $min, $max)
{
    if($number < $min) return $min;
    if($number > $max) return $max;
    return $number;
}

function query_add($params = [])
{
    $new_array = array_merge($_GET, $params);
    return '?' . http_build_query($new_array);
}

/*
['asd', 'qwerty']
*/
function query_del($params)
{
    $get = $_GET;
    foreach ($params as $param_key => $param_val) { // $param_key === 'brands'
        if(is_array($param_val)){ // [ 'brands' => ['Apple', 'Samsung'] ]
            foreach ($param_val as &$value) { // $value === 'Apple'
                foreach ($get[$param_key] as $indx => $get_key_value) {
                    if ($value === $get_key_value) {
                        unset($get[$param_key][$indx]);
                    }
                }
            }
        }else{
            if(isset($get[$param_val])) unset($get[$param_val]);
        }
    }
    return '?' . http_build_query($get);
}

// $str = '10';
// $num = 10;

// $str === $num // false
// $str == $num // true


function if_selected($name, $value)
{
    return $name == $value ? 'selected' : '';
}

function pa($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function esc_attr($str)
{
     return htmlspecialchars($str, ENT_QUOTES);
}


function flash_set($message)
{
    $_SESSION['flash_message'] = $message;
}

function flash_get()
{
    $message = $_SESSION['flash_message'] ?? '';
    unset($_SESSION['flash_message']);
    return $message;
}

function redirect($link)
{
    header("Location: $link");
    exit;
}


function session_take_post($key)
{
    return $_SESSION['post'][$key] ?? '';
}

function session_take_get($key)
{
    return $_SESSION['get'][$key] ?? '';
}

function session_post_clear()
{
    unset($_SESSION['post']);
}

function alert($type, $message)
{
    return
    '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
      ' . $message . '
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

function flash_alert($type, $message)
{
    return flash_set(alert($type, $message));
}


function resizeImage($src, $dst, $width, $height = 0, $crop=0){

  if(!$height) $height = $width;

  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

  $type = strtolower(substr(strrchr($src,"."),1));
  if($type == 'jpeg') $type = 'jpg';
  switch($type){
    case 'bmp': $img = imagecreatefromwbmp($src); break;
    case 'gif': $img = imagecreatefromgif($src); break;
    case 'jpg': $img = imagecreatefromjpeg($src); break;
    case 'png': $img = imagecreatefrompng($src); break;
    default : return "Unsupported picture type!";
  }

  // resize
  if($crop){
    if($w < $width or $h < $height) return "Picture is too small!";
    $ratio = max($width/$w, $height/$h);
    $h = $height / $ratio;
    $x = ($w - $width / $ratio) / 2;
    $w = $width / $ratio;
  }
  else{
    if($w < $width and $h < $height) return "Picture is too small!";
    $ratio = min($width/$w, $height/$h);
    $width = $w * $ratio;
    $height = $h * $ratio;
    $x = 0;
  }

  $new = imagecreatetruecolor($width, $height);

  // preserve transparency
  if($type == "gif" or $type == "png"){
    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
    imagealphablending($new, false);
    imagesavealpha($new, true);
  }

  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

  switch($type){
    case 'bmp': imagewbmp($new, $dst); break;
    case 'gif': imagegif($new, $dst); break;
    case 'jpg': imagejpeg($new, $dst); break;
    case 'png': imagepng($new, $dst); break;
  }
  return true;
}


function resizeSaveImage($input, $output, $new_size)
{
    resizeImage($input, $output, $new_size);
}

function get_product_image_src(&$product)
{
    if(strpos($product['card'], 'rozetka.com.ua')) return $product['card'];
    return $product['card'] ? 'cards/'.$product['card'] : 'images/no-image.jpg';
}

function get_gallery_image_src(&$product)
{
    $gallery = json_decode($product['gallery'], true);
    return $gallery[0] ?? 'images/no-image.jpg';
}


function bi($icon_name)
{
    return "<i class='bi bi-$icon_name'></i>";
}

function edit_product_link($product_id)
{
if(auth_admin()): ?>
    <form class="redirect-edit-form" action="admin.php" method="GET">
        <input type="hidden" name="action" value="products-edit">
        <input type="hidden" name="product_id" value="<?= $product_id ?>">
        <button class="redirect-edit-button" type="submit"><?= bi('pencil') ?></button>
    </form>
<?php endif;
}


function first_letter($string)
{
    return $string[0];
}


if(auth_check()){
    $user_id = (int)auth_user('id');
    $user_favs_g = db_query("SELECT favorites FROM users WHERE id = '$user_id' ");
    $user_favs_g = $user_favs_g[0]['favorites'];
    $user_favs_g = explode('|', $user_favs_g);
}else{
    $user_favs_g = [];
}

function product_heart(&$product)
{
global $user_favs_g;
if(auth_check()):
    if(in_array($product['id'], $user_favs_g)): ?>
        <div class="heart-wrapper" onclick="add_to_favorites(event)" data-productid="<?= $product['id'] ?>">
            <a href="<?= query_add(['product_id' => $product['id'], 'favorite' => 'remove']) ?>" class="heart"></a>
        </div>
    <?php else: ?>
        <div class="heart-wrapper" onclick="add_to_favorites(event)" data-productid="<?= $product['id'] ?>">
            <a href="<?= query_add(['product_id' => $product['id'], 'favorite' => 'add']) ?>" class="heart heart-empty"></a>
        </div>
    <?php endif;
endif;
}


function cart_items_count()
{
    $count = 0;
    if (isset($_SESSION['cart']['items']) && is_array($_SESSION['cart']['items'])) {
        foreach ($_SESSION['cart']['items'] as $cart_item_count) {
            $count = $count + $cart_item_count;
        }
    }
    return $count;
}


function cart_item_count(&$product)
{
    if (isset($_SESSION['cart']['items']) && is_array($_SESSION['cart']['items']) && isset($_SESSION['cart']['items'][$product['id']])) {
        return $_SESSION['cart']['items'][$product['id']];
    }
    return '';
}

function in_the_cart(&$product)
{
    if(isset($_SESSION['cart']['items']) && 
        is_array($_SESSION['cart']['items']) && 
        isset($_SESSION['cart']['items'][$product['id']]))
    {
        return 'в корзине <span class="cart-add-check">' . file_get_contents("svg/bootstrap/check-lg.svg") . '</span>' ;
    }
    return '';
}

function thousands($number)
{
    return number_format($number, 0, '', ' ');
}

function is_cart_empty($if_true = 1, $if_false = '')
{
    if (isset($_SESSION['cart']['items']) && is_array($_SESSION['cart']['items']) && $_SESSION['cart']['items']) {
        return $if_false;
    }else{
        return $if_true;
    }
}

function langs($message, $message_default = '')
{
    global $langs;
    if (isset($_GET['lang']) && $_GET['lang'] === 'ru') {
        $lang = 'ru';
    }else{
        $lang = 'ua';
    }
    if(isset($langs[$message][$lang])){
        return $langs[$message][$lang];
    }elseif($message_default){
        return $message_default;
    }else{
        return $message;
    }
}

function lang_is($lang_is, $if_true = 1, $if_false = '')
{
    if (isset($_GET['lang']) && $_GET['lang'] === $lang_is) {
        return $if_true;
    }
    if(!isset($_GET['lang']) && $lang_is === 'ua'){
        return $if_true;
    }
    return $if_false;
}

function show_more_btn($offset, $limit)
{
    return '<div class="show-more-btn">
        <button onclick="show_more_products(this, '. ($offset + $limit) .','. $limit .')"> <span>'.bi('arrow-repeat').'</span> '. langs('show-more-btn.button').' </button>
    </div>';
}

function product_link($product_id)
{
    return '?action=product&tab=1&id=' . $product_id;
}

