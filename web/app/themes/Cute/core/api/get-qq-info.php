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
 header('Content-Type: text/html;charset=utf-8');
 $QQ=$_GET["qq"];
 if($QQ!=''){
 $urlPre='http://r.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?g_tk=1518561325&uins=';
 $data=file_get_contents($urlPre.$QQ);
 $data=iconv("GB2312","UTF-8",$data);
 $pattern = '/portraitCallBack\((.*)\)/is';
 preg_match($pattern,$data,$result);
 $result=$result[1];
 echo  $result;
 }else{
 echo "请输入qq号！";
 }