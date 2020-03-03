<?php
/**
 * Copyright (c) 2014-2018, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since LTS-181021
 * @package BBCat
 * @author 哔哔猫
 * @date 2018/10/21 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php

$user_id = get_current_user_id();

if(!$user_id){
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// Make sure file is not cached (as it happens for example on iOS devices)
header("HTTP/1.1 200 OK");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Support CORS
// header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
//if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//    exit; // finish preflight CORS requests here
//}

$imageFor = htmlspecialchars($_POST['imgFor']);
$imageForTypes = array('avatar', 'cover', 'default');
if (!in_array($imageFor, $imageForTypes)) {
    $imageFor = 'default';
}

// 1 minutes execution time
@set_time_limit(1 * 60);

// Settings
$tmpDir = WP_CONTENT_DIR . '/uploads/tmp';
//$uploadDir = $imageFor=='avatar' ? AVATARS_PATH : WP_CONTENT_DIR . '/uploads/images';
$uploadDir = WP_CONTENT_DIR . '/uploads/images';
$uploadUrl = home_url('wp-content/uploads/images');
$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds
// Create target dir
if (!file_exists($tmpDir)) {
    @mkdir($tmpDir);
}
// Create upload dir
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir);
}

// Create dir of year/month/day
$year = date('Y');
$month = date('m');
$day = date('d');
$year_dir = WP_CONTENT_DIR . '/uploads/' . $year;
$month_dir = $year_dir . '/' .$month;
$day_dir = $month_dir . '/' . $day;
if (!file_exists($year_dir)) {
    @mkdir($year_dir);
}
if (!file_exists($month_dir)) {
    @mkdir($month_dir);
}
if (!file_exists($day_dir)) {
    @mkdir($day_dir);
}
$date_dir_url = home_url('wp-content/uploads/' . $year . '/' . $month . '/' . $day);

// Get a file name
if (isset($_POST["name"])) { // for avatars use [user_id].jpg
    $fileName = $_POST["name"];
} elseif (!empty($_FILES)) {
    $fileName = $_FILES["file"]["name"];
} else {
    $fileName = uniqid("file_");
}

$fileName = tt_unique_img_name($fileName, isset($_POST['type']) ? trim($_POST['type']) : 'image/jpg');

$filePath = $tmpDir . DIRECTORY_SEPARATOR . $fileName;
$uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
$chunk = isset($_POST["chunk"]) ? intval($_POST["chunk"]) : 0;
$chunks = isset($_POST["chunks"]) ? intval($_POST["chunks"]) : 1;
// Remove old temp files
if ($cleanupTargetDir) {
    if (!is_dir($tmpDir) || !$dir = opendir($tmpDir)) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
    }
    while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $tmpDir . DIRECTORY_SEPARATOR . $file;
        // If temp file is current file proceed to the next
        if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
            continue;
        }
        // Remove temp file if it is older than the max age and is not the current file
        if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
            @unlink($tmpfilePath);
        }
    }
    closedir($dir);
}
// Open temp file
if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}
if (!empty($_FILES)) {
    if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    }
    // Read binary input stream and append it to temp file
    if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    }
} else {
    if (!$in = @fopen("php://input", "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    }
}
while ($buff = fread($in, 4096)) {
    fwrite($out, $buff);
}
@fclose($out);
@fclose($in);
rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
$index = 0;
$done = true;
for( $index = 0; $index < $chunks; $index++ ) {
    if ( !file_exists("{$filePath}_{$index}.part") ) {
        $done = false;
        break;
    }
}
if ( $done ) {
    if (!$out = @fopen($uploadPath, "wb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    }
    if ( flock($out, LOCK_EX) ) {
        for( $index = 0; $index < $chunks; $index++ ) {
            if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                break;
            }
            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }
            @fclose($in);
            @unlink("{$filePath}_{$index}.part");
        }
        flock($out, LOCK_UN);
    }
    @fclose($out);

    // 转为jpg, avatar移动到专用文件夹
    if($imageFor == 'avatar'){
        $avatar_path = AVATARS_PATH . DIRECTORY_SEPARATOR . $user_id . '.jpg';
        tt_resize_img($uploadPath, $avatar_path, 100, 100, true);
        // TODO uploads/images删除临时文件
        tt_update_user_avatar_by_upload($user_id);
        echo json_encode(array(
            'success' => true,
            'message' => '',
            'data' => array(
                'avatar' => AVATARS_URL . '/' . $user_id . '.jpg?_=' . time() // 加时间戳防止缓存
            )
        ));
        exit;
    }elseif($imageFor == 'cover'){
        $cover_base_path = AVATARS_PATH . DIRECTORY_SEPARATOR . $user_id . '_cover_';
        $cover_path = $cover_base_path . 'full.jpg';
        $cover_mini_path = $cover_base_path . 'mini.jpg';
        tt_resize_img($uploadPath, $cover_path, 1200, 400, false);
        tt_resize_img($uploadPath, $cover_mini_path, 350, 145, true);
        tt_update_user_cover_by_upload($user_id, AVATARS_URL . '/' . $user_id . '_cover_');
        echo json_encode(array(
            'success' => true,
            'message' => '',
            'data' => array(
                'cover' => AVATARS_URL . '/' . $user_id . '_cover_full.jpg?_=' . time() // 加时间戳防止缓存
            )
        ));
        exit;
    }else{
        $img_name = md5(time() . '_' . $user_id);
        $img_big_path = $day_dir . DIRECTORY_SEPARATOR . $img_name . '.jpg';
        $img_medium_path = $day_dir . DIRECTORY_SEPARATOR . $img_name . '_medium.jpg';
        $img_small_path = $day_dir . DIRECTORY_SEPARATOR . $img_name . '_small.jpg';
        tt_resize_img($uploadPath, $img_medium_path, 960, 640, false);
        tt_resize_img($uploadPath, $img_small_path, 375, 250, false);
        tt_copy_img($uploadPath, $img_big_path, true);

        echo json_encode(array(
            'code' => 200,
            'success' => true,
            'message' => '',
            'data' => array(
                'image' => $date_dir_url . '/' . $img_name . '.jpg',
                'set' => array(
                    'large' => $date_dir_url . '/' . $img_name . '.jpg',
                    'medium' => $date_dir_url . '/' . $img_name . '_medium.jpg',
                    'small' => $date_dir_url . '/' . $img_name . '_small.jpg'
                )
            )
        ));
        exit;
    }
}
// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');