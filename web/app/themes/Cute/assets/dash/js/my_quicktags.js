/**
 * Copyright (c) 2014-2018, www.kuacg.com
 * All right reserved.
 *
 * @since LTS-181021
 * @package Cute
 * @author 酷ACG资源网
 * @date 2018/10/21 10:00
 * @link https://www.kuacg.com/23856.html
 */
jQuery.noConflict();   
jQuery(document).ready(function(){     
    hijack_media_uploader();   
    hijack_preview_pic();   
});   
  
function hijack_preview_pic(){   
    jQuery('.kriesi_preview_pic_input').each(function(){   
        jQuery(this).bind('change focus blur ktrigger', function(){    
            $select = '#' + jQuery(this).attr('name') + '_div';   
            $value = jQuery(this).val();   
            $image = '<img src ="'+$value+'" />';   
            var $image = jQuery($select).html('').append($image).find('img');   
            //set timeout because of safari   
            window.setTimeout(function(){   
                if(parseInt($image.attr('width')) < 20){       
                    jQuery($select).html('');   
                }   
            },500);   
        });   
    });   
}   
  
function hijack_media_uploader(){          
        $buttons = jQuery('.k_hijack');   
        $realmediabuttons = jQuery('.media-buttons a');   
        window.custom_editor = false;   
        $buttons.click(function(){     
            window.custom_editor = jQuery(this).attr('id');            
        });   
        $realmediabuttons.click(function(){   
            window.custom_editor = false;   
        });   
        window.original_send_to_editor = window.send_to_editor;   
        window.send_to_editor = function(html){    
            if (custom_editor) {       
                $img = jQuery(html).attr('src') || jQuery(html).find('img').attr('src') || jQuery(html).attr('href');   
                   
                jQuery('input[name='+custom_editor+']').val($img).trigger('ktrigger');   
                custom_editor = false;   
                window.tb_remove();   
            }else{   
                window.original_send_to_editor(html);   
            }   
        };   
}
QTags.addButton( 'hr', '水平线', "\n<hr />\n", '' );//添加横线
QTags.addButton( 'h2', '标题2', "\n<h2>", "</h2>\n" ); //添加标题2
QTags.addButton( 'h3', '标题3', "\n<h3>", "</h3>\n" ); //添加标题3
QTags.addButton( 'p', '段落', '\n<p>\n\n</p>', "" );//添加段落
QTags.addButton( 'paged', '分页', '\n<!--nextpage-->\n', "" );//添加文章分页
QTags.addButton( 'pre', 'Pre', '\n<div class="precode clearfix"><pre class="lang:default decode:true " title="这里是标题" >\n\n</pre></div>', "" );//添加代码块
QTags.addButton( 'php', 'PHP', '\n<div class="precode clearfix"><pre class="lang:php decode:true " title="这里是标题" >\n\n</pre></div>', "" );//添加php代码
QTags.addButton( 'js', 'JS', '\n<div class="precode clearfix"><pre class="lang:js decode:true " title="这里是标题" >\n\n</pre></div>', "" );//添加js代码
QTags.addButton( 'css', 'CSS', '\n<div class="precode clearfix"><pre class="lang:css decode:true " title="这里是标题" >\n\n</pre></div>', "" );//添加css代码
QTags.addButton( 'toggle', '折叠板', '\n[toggle hide="no" title="" color=""][/toggle]', "" );//添加Toggle内容块
QTags.addButton( 'button', '按钮', '\n[button class="default或primary或success或info或warning或danger或inverse或elegant" size="lg或sm或xs" href="" title=""][/button]', "" );//添加按钮短代码
QTags.addButton( 'callout', '信息条', '\n[callout class="info或success或warning或danger" title=""][/callout]', "" );//添加提示信息短代码
QTags.addButton( 'infobg', '背景块', '\n[infobg class="info或success或warning或error或lightbulb" showicon="yes" title="这里是标题"][/infobg]', "" );//添加可背景块短代码
QTags.addButton( 'l2v', '登录可见', "\n[ttl2v]", "[/ttl2v]\n" );//添加登录可见短代码
QTags.addButton( 'r2v', '回复可见', "\n[ttr2v]", "[/ttr2v]\n" );//添加回复可见短代码
QTags.addButton( 'salev', '付费可见', "\n[tt_sale_content]", "[/tt_sale_content]\n" );//添加付费可见短代码
QTags.addButton( 'salep', '购买某商品可见', '\n[tt_sale_product id="商品ID"]', "[/tt_sale_product]\n" );//添加付费可见短代码
QTags.addButton( 'vipv', '会员可见', "\n[ttvipv]", "[/ttvipv]\n" );//添加会员可见短代码
QTags.addButton( 'vip1v', '月费会员可见', "\n[ttvip1v]", "[/ttvip1v]\n" );//添加月费会员可见短代码
QTags.addButton( 'vip2v', '年费会员可见', "\n[ttvip2v]", "[/ttvip2v]\n" );//添加年费会员可见短代码
QTags.addButton( 'vip3v', '永久会员可见', "\n[ttvip3v]", "[/ttvip3v]\n" );//添加永久会员可见短代码
QTags.addButton( 'download', '下载', '\n[button class="download" size="lg或sm或xs" href="" title=""]此下载为直接跳转下载地址页，若要跳转站内专用下载页，请使用编辑器下方下载资源meta-box\n[/button]', "" );//添加下载按钮短代码
QTags.addButton( 'demo', '演示', '\n[button class="demo" size="lg或sm或xs" href="" title=""]此演示为直接跳转演示网站页\n[/button]', "" );//添加演示按钮短代码
QTags.addButton( 'product', '商品', '\n[product id="商品ID"][/product]', "" );//添加商品按钮短代码
QTags.addButton( 'post', '文章', '\n[post id="文章ID"][/post]', "" );//添加文章按钮短代码
//QTags.addButton( 'iframe', '网页弹窗', '\n[iframe class="iframe" width="720" height="500" href="网页url" title="按钮文字"][/iframe]', "" );//添加嵌入网页短代码
//这儿共有四对引号，分别是按钮的ID、显示名、点一下输入内容、再点一下关闭内容（此为空则一次输入全部内容），\n表示换行。