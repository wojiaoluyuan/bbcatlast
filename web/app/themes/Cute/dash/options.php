<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
function optionsframework_option_name() {
	// Change this to use your theme slug
	return 'options-framework-theme-cute';
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'tt'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {
    // 主题版本
    $theme_version = trim(wp_get_theme()->get('Version'));

    $theme_pro = defined('TT_PRO') ? TT_PRO : (bool)preg_match('/LTS([0-9-\.]+)/i', $theme_version);

    // 博客名
    $blog_name = trim(get_bloginfo('name'));

    // 博客主页
    $blog_home = home_url();

    // 定义选项面板图片引用路径
    $imagepath =  THEME_URI . '/dash/of_inc/images/';

    // 所有分类
    $options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

    $options = array();
    // 使用提示
   $options[] = array(
         'name' => __( '使用提示', 'tt' ),
          'desc' => '<p style="color: #00a0d2;font-size: 1rem;font-weight: 800;">修改主题选项内容，以及发布文章不显示不生效的情况请点击左上角的清理缓存，页面存在但提示404的请设置伪静态和固定链接</p>',
          'type' => 'info'
      );

	// 主题选项 - 基本设置
	$options[] = array(
		'name' => __( 'Basic', 'tt' ),
		'type' => 'heading'
    );

   // 用户QQ群
    $options[] = array(
        'name' => __( '用户QQ群', 'tt' ),
        'desc' => sprintf(__( '<br><h2><a href="%s" target="_blank">Tint-K与BBCAT主题用户/WordPress交流群：721910579</a></h2>', 'tt'), 'https://jq.qq.com/?_wv=1027&k=5n6NV2Z'),
        'type' => 'info'
    );
  
    // 授权码
    $options[] = array(
        'name' => __('授权码配置', 'tt'),
        'desc' => __('请填写授权码(非购买时获得的卡密)，否则无法正常使用，如未购买请<a href="https://bbcatga.herokuapp.com/shop" target="_blank">点击购买</a>', 'tt'),
        'id' => 'tt_auth_code',
        'std' => '已破解无需填写',
        'class' => 'mini',
        'type' => 'text',
    );

	// - 首页描述
    $options[] = array(
        'name' => __( 'Home Page Description', 'tt' ),
        'desc' => __( 'Home page description meta information, good for SEO', 'tt' ),
        'id' => 'tt_home_description',
        'std' => '最新高清影视剧集资源,中英字幕,在线观看.',
        'type' => 'text'
    );

    // - 首页关键词
    $options[] = array(
        'name' => __( 'Home Page Keywords', 'tt' ),
        'desc' => __( 'Home page keywords meta information, good for SEO', 'tt' ),
        'id' => 'tt_home_keywords',
        'std' => '电影,纪录片,线上看,资源,超清,stream,free',
        'type' => 'text'
    );

    // - 收藏夹图标
    $options[] = array(
        'name' => __( 'Favicon', 'tt' ),
        'desc' => __( 'Please upload an ico file', 'tt' ),
        'id' => 'tt_favicon',
        'std' => THEME_ASSET . '/img/favicon.ico',
        'type' => 'upload'
    );

    // - 收藏夹图标
    $options[] = array(
        'name' => __( 'Favicon(PNG)', 'tt' ),
        'desc' => __( 'Please upload an png file', 'tt' ),
        'id' => 'tt_png_favicon',
        'std' => THEME_ASSET . '/img/favicon.png',
        'type' => 'upload'
    );

    // - 本地化语言
    $options[] = array(
        'name' => __( 'I18n', 'tt' ),
        'desc' => __( 'Multi languages and I18n support', 'tt' ),
        'id' => 'tt_l10n',
        'std' => 'zh_CN',
        'type' => 'select',
        'options' => array(
            'zh_CN' => __( 'zh_cn', 'tt' ),
            'en_US' => __( 'en_us', 'tt' )
        )
    );

    // - Gravatar
    $options[] = array(
        'name' => __( 'Gravatar', 'tt' ),
        'desc' => __( 'Gravatar support', 'tt' ),
        'id' => 'tt_enable_gravatar',
        'std' => false,
        'type' => 'checkbox'
    );


    // - Timthumb
    $options[] = array(
        'name' => __( 'Timthumb Crop', 'tt' ),
        'desc' => __( 'Timthumb 裁剪支持（务必开启，否者图片大小不一致导致页面错乱）', 'tt' ),
        'id' => 'tt_enable_timthumb',
        'std' => false,
        'type' => 'checkbox'
    );

    // - Wp 图片裁剪
    $options[] = array(
        'name' => __( 'WP thumb image crop', 'tt' ),
        'desc' => __( 'Toggle WP thumb image crop', 'tt' ).'（勾选为启用剪裁）',
        'id' => 'tt_enable_wp_crop',
        'std' => false,
        'type' => 'checkbox'
    );

    // - jQuery 源
    $options[] = array(
        'name' => __( 'jQuery Source', 'tt' ),
        'desc' => __( 'Choose local or a CDN jQuery file', 'tt' ),
        'id' => 'tt_jquery',
        'std' => 'local_2',
        'type' => 'select',
        'options' => array(
            'local_1' => __('Local v1.12', 'tt'),
            'cdn_http' => __('CDN HTTP', 'tt'),
            'cdn_https' => __('CDN HTTPS', 'tt')
        )
    );

    // - jQuery 加载位置
//    $options[] = array(
//        'name' => __( 'jQuery Load Position', 'tt' ),
//        'desc' => __( 'Check to load jQuery on `body` end', 'tt' ),
//        'id' => 'tt_foot_jquery',
//        'std' => false,
//        'type' => 'checkbox'
//    );


	// 主题选项 - 样式设置
	$options[] = array(
		'name' => __( 'Style', 'tt' ),
		'type' => 'heading'
	);
    // 使用提示
   $options[] = array(
         'name' => __( '使用提示', 'tt' ),
          'desc' => '修改本页的颜色，透明度，背景图片等不生效请修改下方自定义样式版本后缀，其值为随意内容，不和之前一样即可',
          'type' => 'info'
      );
  
   // - 网站主色
    $options[] = array(
        'name' => __( '网站主色', 'tt' ),
        'desc' => '',
        'id' => 'tt_main_color',
        'std' => '#424242',
        'type' => 'color'
    );
  
  // 是否弹窗登录
   $options[] = array(
        'name' => __( '是否启用弹窗登录', 'tt' ),
        'desc' => __( '启用（不启用则点击登录按钮跳转到独立登录页面）', 'tt' ),
        'id' => 'tt_is_modloginform',
        'std' => true,
        'type' => 'checkbox'
    );
  
  // 是否loading
   $options[] = array(
        'name' => __( '全站loading加载动画', 'tt' ),
        'desc' => __( '启用（页面加载中css动画）', 'tt' ),
        'id' => 'tt_is_loading_css',
        'std' => false,
        'type' => 'checkbox'
    );
  
  // - 自定义样式
    $options[] = array(
        'name' => __('自定义样式', 'tt'),
        'desc' => __('自定义CSS样式填写此处', 'tt'),
        'id' => 'tt_custom_css',
        'std' => 'body {
            position:relative;
          }',
        'row' => 5,
        'type' => 'textarea',
    );
  // - 网站背景颜色
    $options[] = array(
        'name' => __( '自定义网页背景颜色', 'tt' ),
        'desc' => __( '如需网页单色背景需清空网页背景图片', 'tt' ),
        'id' => 'tt_custom_css_background_color',
        'std' => '#f5f6f8',
        'type' => 'color'
    );
        // - 自定义界面透明度
    $options[] = array(
        'name' => __( '自定义界面透明度', 'tt' ),
        'desc' => __( '修改网站界面透明度，1为不透明，0.8为80%', 'tt' ),
        'id' => 'tt_custom_css_transparent',
        'std' => '1',
        'class' => 'mini',
        'type' => 'text'
      );
       // - 自定义网页背景图片
    $options[] = array(
        'name' => __( '自定义网页背景图片', 'tt' ),
        'desc' => __( '自定义网页背景图片，不需要背景图片请留空', 'tt' ),
        'id' => 'tt_custom_css_background_img',
        'std' => '',
        'type' => 'upload'
      );

    // - 自定义样式缓存时间
    $options[] = array(
        'name' => __( '自定义样式版本后缀', 'tt' ),
        'desc' => __( '在修改网站主色等自定义样式后如果因为缓存未生效,请修改此值', 'tt' ),
        'id' => 'tt_custom_css_cache_suffix',
        'std' => Utils::generateRandomStr(5),
        'class' => 'mini',
        'type' => 'text'
    );
  
    // - 网站 logo-dark
    $options[] = array(
        'name' => __( 'logo-暗色', 'tt' ),
        'desc' => __( '菜单默认的LOGO', 'tt' ),
        'id' => 'tt_logo',
        'std' => THEME_ASSET . '/img/logo-dark.png',
        'type' => 'upload'
    );

    // - 网站 logo-light
    $options[] = array(
        'name' => __( 'logo-亮色', 'tt' ),
        'desc' => __( '菜单下拉时候显示的LOGO', 'tt' ), // 用于邮件、登录页Logo等
        'id' => 'tt_logo_light',
        'std' => THEME_ASSET . '/img/logo-light.png',
        'type' => 'upload'
    );

    // - 登录页背景
    $options[] = array(
        'name' => __( '登录页背景', 'tt' ),
        'desc' => '',
        'id' => 'tt_signin_bg',
        'std' => THEME_ASSET . '/img/login-bg.jpg',
        'type' => 'upload'
    );

    // - 注册页背景
    $options[] = array(
        'name' => __( '注册页背景', 'tt' ),
        'desc' => '',
        'id' => 'tt_signup_bg',
        'std' => THEME_ASSET . '/img/login-bg2.jpg',
        'type' => 'upload'
    );
  
   // 首页近期文章列表样式
    $options[] = array(
        'name' => '首页近期文章列表风格',
        'id' => 'post_item_style',
        'desc' => '可以选择首页近期文章列表的样式，搭配是否启用列文章表右侧侧边栏组合出多种布局风格',
        'options' => array(
            'style_0' => '列表风格',
            'style_1' => '卡片风格'
        ),
        'std' => 'style_0',
        'type' => "radio"
    );
    
    // - 首页右侧显示侧边栏
    $options[] = array(
        'name' => __( '首页列表右侧显示侧边栏', 'tt' ),
        'desc' => __( '首页文章列表右侧显示侧边栏', 'tt' ),
        'id' => 'post_item_is_sidebar',
        'std' => true,
        'type' => 'checkbox'
    );
  
    // - 首页样式选择
    $options[] = array(
            'name' => __('首页模板样式', 'tt'),
            'desc' => __('请选择首页模版样式', 'tt'),
            'id' => 'tt_cms_home_style',
            'std' => 'marketing',
            'type' => 'select',
            'options' => array(
                'tinection' => __('旧版Tinection首页', 'tt'),
                'marketing' => __('全新Marketing主题首页样式', 'tt')
            )
        );
     
     //开启文章页新样式
   $options[] = array(
        'name' => __( '文章页顶部不显示缩略图', 'tt' ),
        'desc' => __( '启用', 'tt' ),
        'id' => 'tt_enable_k_postnews',
        'std' => true,
        'type' => 'checkbox'
    );
   
     //启用旧版首页显示近期文章
     $options[] = array(
        'name' => __( '启用旧版首页近期文章模块', 'tt' ),
        'desc' => __( '启用旧版首页显示新首页的近期文章模块', 'tt' ),
        'id' => 'tt_enable_home_latestpost',
        'std' => false,
        'type' => 'checkbox'
    );

    // 首页-要显示的分类ID列表
    $options[] = array(
        'name' => __( '首页-要显示的分类ID列表', 'tt' ),
        'desc' => __( '分类ID数字之间用英文逗号分隔, 如果留空将展示除特别指定不显示的分类外的所有其他分类', 'tt' ),
        'id' => 'tt_cms_home_show_cats',
        'std' => '',
        'type' => 'text'
    );


    // 首页-不显示的分类ID列表
    $options[] = array(
        'name' => __( '首页-不显示的分类ID列表', 'tt' ),
        'desc' => __( '这些分类将不显示在CMS首页(如果在上一选项特别指定显示则仍然显示)', 'tt' ),
        'id' => 'tt_cms_home_hide_cats',
        'std' => '',
        'type' => 'text'
    );
  
    // - 首页分类版块最多显示文章数量
    $options[] = array(
        'name' => __( '首页分类版块最多显示文章数量', 'tt' ),
        'desc' => __( '请根据实际情况调节，确保美观', 'tt' ),
        'id' => 'tt_cms_home_show_cats_num',
        'std' => 6,
        'class' => 'mini',
        'type' => 'text'
    );

    // - 旧版首页-各分类选用模板
    // $category_keys = array_keys($options_categories);
    foreach ($options_categories as $id => $name) {
        $options[] = array(
            'name' => sprintf(__('首页分类模板样式 - %s(ID: %d)', 'tt'), $name, $id),
            'desc' => __('样式图片请查看主题assets/img/cms文件夹', 'tt'),
            'id' => sprintf('tt_cms_home_cat_style_%d', $id),
            'std' => 'Style_0',
            'type' => 'select',
            'options' => array(
                'Style_0' => __('半宽精简样式', 'tt'),
                'Style_6' => __('半宽精简无图样式', 'tt'),
                'Style_7' => __('全宽块状样式', 'tt'),
                'Style_1' => __('全宽样式1', 'tt'),
                'Style_2' => __('全宽样式2', 'tt'),
                'Style_3' => __('全宽样式3', 'tt'),
                'Style_4' => __('全宽样式4', 'tt'),
                'Style_5' => __('全宽样式5', 'tt')
            )
        );
    }

    // 分类页模板
    $options[] = array(
        'name' => __('分类页面使用列表风格', 'tt'),
        'desc' => __('分类页面默认使用卡片+边栏布局，若要使用列表风格布局，请在此勾选对应分类', 'tt'),
        'id' => 'tt_alt_template_cats',
        'std' => array(),
        'type' => 'multicheck',
        'options' => $options_categories
    );

    // - 分类右侧显示侧边栏
    $options[] = array(
        'name' => __( '分类列表右侧显示侧边栏', 'tt' ),
        'desc' => __( '在分类文章列表右侧显示侧边栏', 'tt' ),
        'id' => 'post_template_cats_is_sidebar',
        'std' => true,
        'type' => 'checkbox'
    );

    // 主题选项 - 内容设置
    $options[] = array(
        'name' => __( 'Content', 'tt' ),
        'type' => 'heading'
    );

    // - 首页排除分类
    $options[] = array(
        'name' => __('Home Hide Categories', 'tt'),
        'desc' => __('Choose categories those are not displayed in homepage', 'tt'),
        'id' => 'tt_home_undisplay_cats',
        'std' => array(),
        'type' => 'multicheck',
        'options' => $options_categories
    );

    // - 首页幻灯开关
    $options[] = array(
        'name' => __( '首页顶部展示幻灯', 'tt' ),
        'desc' => __( '展示幻灯（幻灯总开关）', 'tt' ),
        'id' => 'tt_enable_home_slides',
        'std' => false,
        'type' => 'checkbox'
    );
  
    // - 选择幻灯样式
    $options[] = array(
        'name' => __( '选择幻灯样式', 'tt' ),
        'desc' => __( '选择幻灯样式', 'tt' ),
        'id' => 'tt_k_custom_slide',
        'std' => 'max_big',
        'type' => 'select',
        'options' => array(
            'min' => __('小幻灯（右侧显示热门文章）', 'tt'),
            'big' => __('半宽幻灯', 'tt'),
            'max_big' => __('全屏幻灯', 'tt'),
        )
    );
  
   // 幻灯图片分辨率倍数
    $options[] = array(
        'name' => __('幻灯图片分辨率倍数', 'tt'),
        'desc' => __('如果幻灯片图片分辨率较小, 请增大此数值以使用更高分辨率的图片', 'tt'),
        'id' => 'tt_home_slides_ratio',
        'std' => '1',
        'class' => 'mini',
        'type' => 'text',
    );
    
    // 幻灯片高度
    $options[] = array(
        'name' => __( '自定义幻灯高度', 'tt' ),
        'desc' => __( '设置幻灯片高度，单位px（真全屏幻灯模式下的PC页面生效,修改后请修改自定义样式版本后缀以生效）', 'tt' ),
        'id' => 'tt_k_custom_slider_height',
        'std' => '500',
        'class' => 'mini',
       'type' => 'text'
    );
    
    // - 全宽幻灯开关
  //  $options[] = array(
   //     'name' => __( '全宽幻灯', 'tt' ),
  //     'desc' => __( '展示全宽幻灯，不显示热门文章', 'tt' ),
   //     'id' => 'tt_enable_home_full_width_slides',
  //      'std' => false,
  //     'type' => 'checkbox'
  //  );
  
    // - 首页置顶文章开关
    $options[] = array(
        'name' => __( '首页展示置顶文章', 'tt' ),
        'desc' => __( '展示置顶文章', 'tt' ),
        'id' => 'tt_enable_sticky_cats',
        'std' => true,
        'type' => 'checkbox'
    );
  
    // - 首页置顶文章标题
    $options[] = array(
        'name' => __( '首页置顶文章标题', 'tt' ),
        'desc' => __( '首页置顶文章标题', 'tt' ),
        'id' => 'tt_sticky_cats_title',
        'std' => '置顶文章',
        'type' => 'text'
    );
  
     // - 首页置顶描述
    $options[] = array(
        'name' => __( '首页置顶描述', 'tt' ),
        'desc' => __( '首页置顶描述', 'tt' ),
        'id' => 'tt_sticky_cats_description',
        'std' => '关注前沿设计风格，紧跟行业趋势，精选优质好资源！',
        'type' => 'text'
    );

    // - 幻灯文章ID列表
    $options[] = array(
        'name' => __( 'Slide Post IDs', 'tt' ),
        'desc' => __( 'The post IDs for home slides, separate with comma', 'tt' ),
        'id' => 'tt_home_slides',
        'std' => '',
        'type' => 'text'
    );

    // - 热门文章来源
    $options[] = array(
        'name' => __('Home Popular Posts Algorithm', 'tt'),
        'desc' => __('Choose the method of retrieving popular posts for homepage', 'tt'),
        'id' => 'tt_home_popular_algorithm',
        'std' => 'latest_reviewed',
        'type' => 'select',
        'options' => array(
            'most_viewed' => __('Most Viewed', 'tt'),
            'most_reviewed' => __('Most Reviewed', 'tt'),
            'latest_reviewed' => __('Latest Reviewed', 'tt')
        )
    );

    // - 商品推荐
//    $options[] = array(
//        'name' => __( 'Home Products Recommendation', 'tt' ),
//        'desc' => __( 'Enable products recommendation module for homepage', 'tt' ),
//        'id' => 'tt_home_products_recommendation',
//        'std' => false,
//        'type' => $theme_pro ? 'checkbox' : 'disabled'
//    );

    // - 文章评论数
    $options[] = array(
        'name' => __( 'Post Comments Count', 'tt' ),
        'desc' => __( 'The num of comments per page for a post to display, leave empty or set 0 to show all comments', 'tt' ),
        'id' => 'tt_comments_per_page',
        'std' => 20,
        'class' => 'mini',
        'type' => 'text'
    );

    // - 摘要阅读更多占位字符
    $options[] = array(
        'name' => __( 'Excerpt Read More Text', 'tt' ),
        'desc' => __( 'The placeholder string at end of excerpt for indicating reading more', 'tt' ),
        'id' => 'tt_read_more',
        'std' => ' ···',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 摘要长度
    $options[] = array(
        'name' => __( 'Excerpt Length', 'tt' ),
        'desc' => '',
        'id' => 'tt_excerpt_length',
        'std' => 100,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 外链转内链
    $options[] = array(
        'name' => __( 'Disable External Links', 'tt' ),
        'desc' => __( 'Convert external links in post content, excerpt or comments to internal links', 'tt' ),
        'id' => 'tt_disable_external_links',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 外链白名单
    $options[] = array(
        'name' => __( 'External Link Whitelist', 'tt' ),
        'desc' => __( 'External links which will not be converted', 'tt' ),
        'id' => 'tt_external_link_whitelist',
        'std' => '',
        'row' => 5,
        'type' => 'textarea'
    );


    // - 可投稿分类
    $default_allow_cats = array();
    foreach ($category_keys as $category_key) {
        $default_allow_cats[$category_key] = true;
    }
    $options[] = array(
        'name' => __('可投稿分类', 'tt'),
        'desc' => __('选择允许用户投稿的分类, 至少选择一个', 'tt'),
        'id' => 'tt_contribute_cats',
        'std' => $default_allow_cats,
        'type' => 'multicheck',
        'options' => $options_categories
    );

    // - 投稿最少字数
    $options[] = array(
        'name' => __( '投稿最少字数', 'tt' ),
        'desc' => '',
        'id' => 'tt_contribute_post_words_min',
        'std' => 100,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 开启首页顶部公告显示
    $options[] = array(
        'name' => __( '首页Banner底部显示公告', 'tt' ),
        'desc' => __( '在首页Banner底部滚动显示站点公告,开启必须新建公告文章，否则会出现缺失空白布局', 'tt' ),
        'id' => 'tt_enable_homepage_bulletins',
        'std' => true,
        'type' => 'checkbox'
    );


    // - 公告链接的链接前缀
    $options[] = array(
        'name' => __( 'Bulletins Archive Link Slug', 'tt' ),
        'desc' => __( 'The special prefix in bulletin archive link', 'tt' ),
        'id' => 'tt_bulletin_archives_slug',
        'std' => 'bulletin',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 公告链接模式
    $options[] = array(
        'name' => __( 'Bulletin Permalink Mode', 'tt' ),
        'desc' => __( 'The link mode for the rewrite bulletin permalink', 'tt' ),
        'id' => 'tt_bulletin_link_mode',
        'std' => 'post_id',
        'type' => 'select',
        'class' => 'mini',
        'options' => array(
            'post_id' => __( 'Post ID', 'tt' ),
            'post_name' => __( 'Post Name', 'tt' )
        )
    );


    // - 公告的有效期天数
    $options[] = array(
        'name' => __( 'Bulletin Effect Days', 'tt' ),
        'desc' => __( 'The effect days of a bulletin, expired bulletin will never be show', 'tt' ),
        'id' => 'tt_bulletin_effect_days',
        'std' => 10,
        'class' => 'mini',
        'type' => 'text'
    );

    // - 商品推荐
   $options[] = array(
       'name' => __( '首页商品展示模块', 'tt' ),
       'desc' => __( '显示热门首页商品', 'tt' ),
       'id' => 'tt_home_products_recommendation',
       'std' => false,
       'type' => 'checkbox'
   );
  
   $options[] = array(
        'name' => __( '模块顶部主标题', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_products_title',
        'std' => '主题 & 插件资源',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( '模块顶部标题描述', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_products_desc',
        'std' => '关注前沿设计风格，紧跟行业趋势，精选优质好资源！',
        'type' => 'text'
    );
  
   $options[] = array(
        'name' => __( '商品展示最新数目', 'tt' ),
        'desc' => __( '显示几个商品推荐', 'tt' ),
        'id' => 'tt_home_products_num',
        'std' => '4',
        'type' => 'text'
    );
  
    // - 首页最新文章
    $options[] = array(
        'name' => __( '首页最新文章', 'tt' ),
        'desc' => __( '默认开启，必选项', 'tt' ),
        'id' => 'tt_home_postlist_is',
        'std' => true,
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( '模块顶部主标题', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_postlist_title',
        'std' => '最新文章 & 资讯',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( '模块顶部标题描述', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_postlist_desc',
        'std' => '分享一切美好的事物、资讯、教程！',
        'type' => 'text'
    );
  
     // - 底部按钮条
    $options[] = array(
        'name' => __( '网站底部按钮条', 'tt' ),
        'desc' => __( '开启', 'tt' ),
        'id' => 'home_footer_btn_is',
        'std' => true,
        'type' => 'checkbox'
    );

    $options[] = array(
            'id' => 'home_footer_title',
            'desc' => '主标题文字',
            'std' => '欢迎使用 BBCat WordPress主题',
            'type' => 'text');
  
    $options[] = array(
        'id' => 'home_footer_img',
        'desc' => '左侧图标',
        'std' => THEME_ASSET . '/img/logo-dark.png',
        'type' => 'upload'
    );
  
    $options[] = array(
        'id' => 'home_footer_desc',
        'desc' => '描述文字',
        'std' => '为大客户提供的所有资源打包计划，包括当前已经发布的资源和以后持续更新内容在内只要购买这个资源包就可以免费下载和使用全站资源!',
        'type' => 'text');

    $options[] = array(
            'id' => 'home_footer_btn_name',
            'desc' => '按钮名称',
            'std' => '立即了解详情',
            'type' => 'text');

    $options[] = array(
        'id' => 'home_footer_btn_href',
        'desc' => '按钮链接',
        'std' => '/shop',
        'type' => 'text');
  
  // 主题选项 - 服务设置
    $options[] = array(
        'name' => __( '服务', 'tt' ),
        'type' => 'heading'
    );
    
   // - 首页服务内容模块
    $options[] = array(
        'name' => __( '首页服务内容模块', 'tt' ),
        'desc' => __( '默认开启', 'tt' ),
        'id' => 'tt_home_features',
        'std' => false,
        'type' => 'checkbox'
    );
  // - 首页服务内容模块数量
    $options[] = array(
        'name' => __( '首页服务内容模块数量', 'tt' ),
        'desc' => __( '建议输入3的倍数，最大9个', 'tt' ),
        'id' => 'tt_home_features_num',
        'std' => 3,
        'type' => 'select',
        'options' => array(
            '3' => __('3个', 'tt'),
            '6' => __('6个', 'tt'),
            '9' => __('9个', 'tt')
        )
    );
   $server_num = of_get_option('tt_home_features_num');
    for ($i=1; $i <= $server_num; $i++) {    
    $options[] = array(
        'name' => '服务'.$i,
        'id' => 'feature_title_'.$i,
        'desc' => '服务标题文字',
        'std' => '主题定制',
        'type' => 'text');

    $options[] = array(
        'id' => 'feature_desc_'.$i,
        'desc' => '服务描述文字',
        'std' => '你们想要什么，我就提供什么，没有找不到的资源',
        'type' => 'text');

    $options[] = array(
        'id' => 'feature_href_'.$i,
        'desc' => '链接',
        'std' => '#',
        'type' => 'text');
    }

	// 主题设置 - 边栏设置
	$options[] = array(
		'name' => __( 'Sidebar', 'tt' ),
		'type' => 'heading'
	);


    // - 所有边栏
    $all_sidebars = array(
        'sidebar_common'    =>    __('Common Sidebar', 'tt'),
        'sidebar_home'      =>    __('Home Sidebar', 'tt'),
        'sidebar_single'    =>    __('Single Sidebar', 'tt'),
        //'sidebar_archive'   =>    __('Archive Sidebar', 'tt'),
        //'sidebar_category'  =>    __('Category Sidebar', 'tt'),
        'sidebar_search'    =>    __('Search Sidebar', 'tt'),
        //'sidebar_404'       =>    __('404 Sidebar', 'tt'),
        'sidebar_page'      =>    __('Page Sidebar', 'tt'),
        'sidebar_download'  =>    __('Download Page Sidebar', 'tt')
    );
	// - 待注册的边栏
    $options[] = array(
        'name' => __('Register Sidebars', 'tt'),
        'desc' => __('Check the sidebars to register', 'tt'),
        'id'   => 'tt_register_sidebars',
        'std'  => array('sidebar_common' => true),
        'type' => 'multicheck',
        'options' => $all_sidebars
    );

    $register_status = of_get_option('tt_register_sidebars', array('sidebar_common' => true));
    if(!is_array($register_status)) {
        $register_status = array('sidebar_common' => true);
    }elseif(!isset($register_status['sidebar_common'])){
        $register_status['sidebar_common'] = true;
    }

    $available_sidebars = array();
    foreach ($register_status as $key => $value){
        if($value) $available_sidebars[$key] = $all_sidebars[$key];
    }
    $available_sidebars['sidebar_common'] = __('Common Sidebar', 'tt'); // 默认边栏始终可选

    $options[] = array(
        'name' => __('Home Sidebar', 'tt'),
        'desc' => __('Select a sidebar for homepage', 'tt'),
        'id'   => 'tt_home_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

    $options[] = array(
        'name' => __('Single Sidebar', 'tt'),
        'desc' => __('Select a sidebar for single post page', 'tt'),
        'id'   => 'tt_single_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

    $options[] = array(
        'name' => __('Archive Sidebar', 'tt'),
        'desc' => __('Select a sidebar for archive page', 'tt'),
        'id'   => 'tt_archive_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

//    $options[] = array(
//        'name' => __('Category Sidebar', 'tt'),
//        'desc' => __('Select a sidebar for category page', 'tt'),
//        'id'   => 'tt_category_sidebar',
//        'std'  => array('sidebar_common' => true),
//        'type' => 'select',
//        'class' => 'mini',
//        'options' => $available_sidebars
//    );

    $options[] = array(
        'name' => __('Search Sidebar', 'tt'),
        'desc' => __('Select a sidebar for search page', 'tt'),
        'id'   => 'tt_search_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

//    $options[] = array(
//        'name' => __('404 Sidebar', 'tt'),
//        'desc' => __('Select a sidebar for 404 page', 'tt'),
//        'id'   => 'tt_404_sidebar',
//        'std'  => array('sidebar_common' => true),
//        'type' => 'select',
//        'class' => 'mini',
//        'options' => $available_sidebars
//    );

    $options[] = array(
        'name' => __('Page Sidebar', 'tt'),
        'desc' => __('Select a sidebar for page', 'tt'),
        'id'   => 'tt_page_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

    $options[] = array(
        'name' => __('Download Page Sidebar', 'tt'),
        'desc' => __('Select a sidebar for download page', 'tt'),
        'id'   => 'tt_download_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );


	// 主题设置 - 社会化设置(包含管理员社会化链接等)
	$options[] = array(
		'name' => __( 'Social', 'tt' ),
		'type' => 'heading'
	);


    // - 站点服务QQ
    $options[] = array(
        'name' => __( 'Site QQ', 'tt' ),
        'desc' => __( 'The QQ which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_qq',
        'std' => '1733913392',
        'type' => 'text'
    );


    // - 站点服务QQ群
    $options[] = array(
        'name' => __( 'Site QQ Group ID', 'tt' ),
        'desc' => __( 'The ID key of QQ group which is dedicated for the site, visit `http://shang.qq.com` for detail', 'tt' ),
        'id' => 'tt_site_qq_group',
        'std' => '28ca5a1e2a74048dc1814982678d383b9d48589bf56593a164f6ba4fa8c4d79f',
        'type' => 'text'
    );


    // - 站点服务微博
    $options[] = array(
        'name' => __( 'Site Weibo', 'tt' ),
        'desc' => __( 'The name of Weibo account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_weibo',
        'std' => 'rogenart',
        'type' => 'text'
    );


    // - 站点服务Facebook
    $options[] = array(
        'name' => __( 'Site Facebook', 'tt' ),
        'desc' => __( 'The name of Facebook account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_facebook',
        'std' => 'RogenRu',
        'type' => 'text'
    );


    // - 站点服务Twitter
    $options[] = array(
        'name' => __( 'Site Twitter', 'tt' ),
        'desc' => __( 'The name of Twitter account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_twitter',
        'std' => 'wojiaoluyuan',
        'type' => 'text'
    );


    // - 站点服务微信
    $options[] = array(
        'name' => __( 'Site Weixin', 'tt' ),
        'desc' => __( 'The qrcode image of Weixin account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_weixin_qr',
        'std' => THEME_ASSET . '/img/qr/weixin.png',
        'type' => 'upload'
    );


    // - 开启QQ登录
    $options[] = array(
        'name' => __( 'QQ Login', 'tt' ),
        'desc' => __( 'QQ login ', 'tt' ),
        'id' => 'tt_enable_qq_login',
        'std' => false,
        'type' => 'checkbox'
    );


	// - QQ开放平台应用ID
    $options[] = array(
        'name' => __( 'QQ Open ID', 'tt' ),
        'desc' => __( 'Your QQ open application ID', 'tt' ),
        'id' => 'tt_qq_openid',
        'std' => '',
        'type' => 'text'
    );


    // - QQ开放平台应用KEY
    $options[] = array(
        'name' => __( 'QQ Open Key', 'tt' ),
        'desc' => __( 'Your QQ open application key', 'tt' ),
        'id' => 'tt_qq_openkey',
        'std' => '',
        'type' => 'text'
    );


    // - 开启微博登录
    $options[] = array(
        'name' => __( 'Weibo Login', 'tt' ),
        'desc' => __( 'Weibo login access', 'tt' ),
        'id' => 'tt_enable_weibo_login',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 微博开放平台Key
    $options[] = array(
        'name' => __( 'Weibo Open Key', 'tt' ),
        'desc' => __( 'Your weibo open application key', 'tt' ),
        'id' => 'tt_weibo_openkey',
        'std' => '',
        'type' => 'text'
    );


    // - 微博开放平台Secret
    $options[] = array(
        'name' => __( 'Weibo Open Secret', 'tt' ),
        'desc' => __( 'Your weibo open application secret', 'tt' ),
        'id' => 'tt_weibo_opensecret',
        'std' => '',
        'type' => 'text'
    );


    // - 开启微信登录
    $options[] = array(
        'name' => __( 'Weixin Login', 'tt' ),
        'desc' => __( 'Weixin login access', 'tt' ),
        'id' => 'tt_enable_weixin_login',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 微信开放平台Key
    $options[] = array(
        'name' => __( 'Weixin Open Key', 'tt' ),
        'desc' => __( 'Your weixin open application key', 'tt' ),
        'id' => 'tt_weixin_openkey',
        'std' => '',
        'type' => 'text'
    );


    // - 微信开放平台Secret
    $options[] = array(
        'name' => __( 'Weixin Open Secret', 'tt' ),
        'desc' => __( 'Your weixin open application secret', 'tt' ),
        'id' => 'tt_weixin_opensecret',
        'std' => '',
        'type' => 'text'
    );

    // - 开放平台接入新用户角色
    $options[] = array(
        'name' => __('Open User Default Role', 'tt'),
        'desc' => __('Choose the role and capabilities for the new connected user from open', 'tt'),
        'id' => 'tt_open_role',
        'std' => 'contributor',
        'type' => 'select',
        'options' => array(
            'editor' => __('Editor', 'tt'),
            'author' => __('Author', 'tt'),
            'contributor' => __('Contributor', 'tt'),
            'subscriber' => __('Subscriber', 'tt'),
        )
    );



	// 主题设置 - 广告设置
	$options[] = array(
		'name' => __( 'Ad', 'tt' ),
		'type' => 'heading'
	);


    // - 开启导航栏下方大横幅广告
    $options[] = array(
        'name' => __( '开启导航栏下方大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_nav_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 导航栏下方大横幅广告
    $options[] = array(
        'name' => __( '导航栏下方横幅广告', 'tt' ),
        'desc' => __( '多个页面可用', 'tt' ),
        'id' => 'tt_nav_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/960x90.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启幻灯下方大横幅广告
    $options[] = array(
        'name' => __( '开启幻灯下方大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_slide_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 幻灯下方大横幅广告
    $options[] = array(
        'name' => __( '幻灯下方大横幅广告', 'tt' ),
        'desc' => __( '仅首页幻灯开启时可用, 标准尺寸960*90', 'tt' ),
        'id' => 'tt_slide_bottom_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/960x90.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启置顶分类下方大横幅广告
    $options[] = array(
        'name' => __( '开启置顶分类下方大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_fc_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 置顶分类下方大横幅广告
    $options[] = array(
        'name' => __( '置顶分类下方大横幅广告', 'tt' ),
        'desc' => __( '仅首页置顶分类开启时可用, 标准尺寸960*90', 'tt' ),
        'id' => 'tt_fc_bottom_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/960x90.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启底部大横幅广告
    $options[] = array(
        'name' => __( '开启底部大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 底部大横幅广告
    $options[] = array(
        'name' => __( '底部大横幅广告', 'tt' ),
        'desc' => __( '多个页面可用, 标准尺寸960*90', 'tt' ),
        'id' => 'tt_bottom_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/960x90.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启文章文字上方广告
    $options[] = array(
        'name' => __( '开启文章文字上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_content_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 文章文字上方广告
    $options[] = array(
        'name' => __( '文章文字上方广告', 'tt' ),
        'desc' => __( '标准尺寸640*60', 'tt' ),
        'id' => 'tt_post_content_top_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/640x60.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启文章文字下方广告
    $options[] = array(
        'name' => __( '开启文章文字下方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_content_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 文章文字下方广告
    $options[] = array(
        'name' => __( '文章文字下方广告', 'tt' ),
        'desc' => __( '标准尺寸640*60', 'tt' ),
        'id' => 'tt_post_content_bottom_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/640x60.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启相关文章上方广告
    $options[] = array(
        'name' => __( '开启相关文章上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_relates_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 相关文章上方广告
    $options[] = array(
        'name' => __( '相关文章上方广告', 'tt' ),
        'desc' => __( '标准尺寸760*90', 'tt' ),
        'id' => 'tt_post_relates_top_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/760x90.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启评论框上方广告
    $options[] = array(
        'name' => __( '开启评论框上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_comment_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 评论框上方广告
    $options[] = array(
        'name' => __( '评论框上方广告', 'tt' ),
        'desc' => __( '标准尺寸760*90', 'tt' ),
        'id' => 'tt_post_comment_top_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/760x90.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启下载页面内容区上方广告
    $options[] = array(
        'name' => __( '开启下载页面内容区上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_dl_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 下载页面内容区上方广告1
    $options[] = array(
        'name' => __( '下载页面内容区上方广告1', 'tt' ),
        'desc' => __( '双矩形广告位-左, 标准尺寸350*300', 'tt' ),
        'id' => 'tt_dl_top_banner_1',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/350x300.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );

    // - 下载页面内容区上方广告2
    $options[] = array(
        'name' => __( '下载页面内容区上方广告2', 'tt' ),
        'desc' => __( '双矩形广告位-右, 标准尺寸350*300', 'tt' ),
        'id' => 'tt_dl_top_banner_2',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/350x300.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启下载页面内容区下方广告
    $options[] = array(
        'name' => __( '开启下载页面内容区下方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_dl_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 底部大横幅广告
    $options[] = array(
        'name' => __( '下载页面内容区下方广告', 'tt' ),
        'desc' => __( '仅适用于下载页面内容区下方, 标准尺寸760*90', 'tt' ),
        'id' => 'tt_dl_bottom_banner',
        'std' => '<a href="https://bbcatga.herokuapp.com" target="_blank"><img src="' . THEME_ASSET . '/img/banner/760x90.jpg"></a>',
        'raw' => true,
        'type' => 'textarea'
    );


    // 主题设置 - 积分系统设置
    $options[] = array(
        'name' => __('Credit', 'tt'),
        'type' => 'heading'
    );

    // - 积分价格
    $options[] = array(
        'name' => __( '积分价格(元/100积分)', 'tt' ),
        'desc' => __('注意: 积分充值最小单位为100, 此价格为100个积分的价格'),
        'id' => 'tt_hundred_credit_price',
        'std' => 1,
        'class' => 'mini',
        'type' => 'text'
    );
  
     // - 积分充值最低限额
    $options[] = array(
        'name' => __( '积分充值最低限额(元)', 'tt' ),
        'desc' => __('积分充值最低限额, 建议整数'),
        'id' => 'tt_hundred_min_credit_price',
        'std' => 1,
        'class' => 'mini',
        'type' => 'text'
    );
   
    // - 内嵌资源积分返还比例
    $options[] = array(
        'name' => __( '内嵌资源积分返还比例(单位%)', 'tt' ),
        'desc' => __('内嵌资源积分返还给作者的比例，100为全返，即买家花10积分购买作者就获得10积分'),
        'id' => 'tt_bought_resource_rewards_ratio',
        'std' => 100,
        'class' => 'mini',
        'type' => 'text'
    );

    // - 每日签到积分奖励
    $options[] = array(
        'name' => __( '每日签到积分奖励', 'tt' ),
        'desc' => '',
        'id' => 'tt_daily_sign_credits',
        'std' => '10',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 注册奖励积分
    $options[] = array(
        'name' => __( '注册奖励积分', 'tt' ),
        'desc' => __( '新用户注册时默认赠送的积分数量', 'tt' ),
        'id' => 'tt_reg_credit',
        'std' => '50',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 投稿奖励积分
    $options[] = array(
        'name' => __( '投稿奖励积分', 'tt' ),
        'desc' => __( '用户向本站投稿文章通过时奖励的积分', 'tt' ),
        'id' => 'tt_rec_post_credit',
        'std' => '50',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 评论奖励积分
    $options[] = array(
        'name' => __( '评论奖励积分', 'tt' ),
        'desc' => __( '用户在站内发表评论一次奖励的积分', 'tt' ),
        'id' => 'tt_rec_comment_credit',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 投稿积分奖励次数限制
    $options[] = array(
        'name' => __( '投稿积分奖励次数限制', 'tt' ),
        'desc' => __( '每日通过投稿最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_post_num',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 评论积分奖励次数限制
    $options[] = array(
        'name' => __( '评论积分奖励次数限制', 'tt' ),
        'desc' => __( '每日通过评论最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_comment_num',
        'std' => '10',
        'class' => 'mini',
        'type' => 'text'
    );


    // 主题设置 - 会员系统设置
	$options[] = array(
		'name' => __( 'Membership', 'tt' ),
		'type' => 'heading'
	);

    // - 月费会员价格
    $options[] = array(
        'name' => __( '月费会员价格', 'tt' ),
        'desc' => '',
        'id' => 'tt_monthly_vip_price',
        'std' => 8,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 年费会员价格
    $options[] = array(
        'name' => __( '年费会员价格', 'tt' ),
        'desc' => '',
        'id' => 'tt_annual_vip_price',
        'std' => 80,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 永久会员价格
    $options[] = array(
        'name' => __( '永久会员价格', 'tt' ),
        'desc' => '',
        'id' => 'tt_permanent_vip_price',
        'std' => 159,
        'class' => 'mini',
        'type' => 'text'
    );

    // - 注册送会员
    $options[] = array(
        'name' => __( '注册送会员', 'tt' ),
        'desc' => __( '注册送会员，可选任意等级，用于拉动注册活跃度！', 'tt' ),
        'id' => 'tt_reg_member',
        'std' => 0,
        'type' => 'select',
        'class' => 'mini',
        'options' => array(
            '0' => __('不赠送', 'tt'),
            '1' => __('赠送月费会员', 'tt'),
            '2' => __('赠送年费会员', 'tt'),
            '3' => __('赠送永久会员', 'tt'),
        )
    );

    // - 月费会员默认折扣
    $options[] = array(
        'name' => __( '月费会员默认折扣 (%)', 'tt' ),
        'desc' => '',
        'id' => 'tt_monthly_vip_discount',
        'std' => 100,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 年费会员默认折扣
    $options[] = array(
        'name' => __( '年费会员默认折扣 (%)', 'tt' ),
        'desc' => '',
        'id' => 'tt_annual_vip_discount',
        'std' => 90,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 永久会员默认折扣
    $options[] = array(
        'name' => __( '永久会员默认折扣 (%)', 'tt' ),
        'desc' => '',
        'id' => 'tt_permanent_vip_discount',
        'std' => 80,
        'class' => 'mini',
        'type' => 'text'
    );
  
    // - 月费会员免费下载文章收费资源
    $options[] = array(
        'name' => __( '启用月费会员免费下载文章收费资源', 'tt' ),
        'desc' => __( '月费会员免费下载文章收费资源', 'tt' ),
        'id' => 'tt_enable_monthly_vip_free_down',
        'std' => false,
        'type' => 'checkbox'
    );
  
    // - 年费会员免费下载文章收费资源
    $options[] = array(
        'name' => __( '启用年费会员免费下载文章收费资源', 'tt' ),
        'desc' => __( '年费会员免费下载文章收费资源', 'tt' ),
        'id' => 'tt_enable_annual_vip_free_down',
        'std' => false,
        'type' => 'checkbox'
    );
  
    // - 永久会员免费下载文章收费资源
    $options[] = array(
        'name' => __( '启用永久会员免费下载文章收费资源', 'tt' ),
        'desc' => __( '永久会员免费下载文章收费资源', 'tt' ),
        'id' => 'tt_enable_permanent_vip_free_down',
        'std' => false,
        'type' => 'checkbox'
    );
  
    // - 会员每日下载优惠限制未付款订单数
    $options[] = array(
        'name' => __( '会员每日下载优惠限制未付款订单数', 'tt' ),
        'desc' => '限制未付款订单数量，防止创建大量未付款订单逃避下载优惠限制，设置0为无限制',
        'id' => 'tt_vip_down_count',
        'std' => 5,
        'class' => 'mini',
        'type' => 'text'
    );
  
    // - 月费会员每日下载优惠限制次数
    $options[] = array(
        'name' => __( '月费会员每日下载优惠限制次数', 'tt' ),
        'desc' => '可限制其每日会员价创建的下载资源订单，设置0为无限制',
        'id' => 'tt_monthly_vip_down_count',
        'std' => 5,
        'class' => 'mini',
        'type' => 'text'
    );
    
    // - 年费会员每日下载优惠限制次数
    $options[] = array(
        'name' => __( '年费会员每日下载优惠限制次数', 'tt' ),
        'desc' => '可限制其每日会员价创建的下载资源订单，设置0为无限制',
        'id' => 'tt_annual_vip_down_count',
        'std' => 5,
        'class' => 'mini',
        'type' => 'text'
    );
  
    // - 永久会员每日下载优惠限制次数
    $options[] = array(
        'name' => __( '永久会员每日下载优惠限制次数', 'tt' ),
        'desc' => '可限制其每日会员价创建的下载资源订单，设置0为无限制',
        'id' => 'tt_permanent_vip_down_count',
        'std' => 5,
        'class' => 'mini',
        'type' => 'text'
    );

	// 主题设置 - 商店设置
	$options[] = array(
		'name' => __( 'Shop', 'tt' ),
		'type' => 'heading'
	);


    // - 开启商品系统
    $options[] = array(
        'name' => __( 'Enable Shop', 'tt' ),
        'desc' => __( 'After enable this, users can create orders and buy something those the site provided', 'tt' ),
        'id' => 'tt_enable_shop',
        'std' => true,
        'type' => $theme_pro ? 'checkbox' : 'disabled'
    );


    // - 商品链接的链接前缀
    $options[] = array(
        'name' => __( 'Products Archive Link Slug', 'tt' ),
        'desc' => __( 'The special prefix in product archive link', 'tt' ),
        'id' => 'tt_product_archives_slug',
        'std' => 'shop',
        'class' => 'mini',
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 商品链接模式
    $options[] = array(
        'name' => __( 'Product Permalink Mode', 'tt' ),
        'desc' => __( 'The link mode for the rewrite product permalink', 'tt' ),
        'id' => 'tt_product_link_mode',
        'std' => 'post_id',
        'type' => $theme_pro ? 'select' : 'disabled',
        'class' => 'mini',
        'options' => array(
            'post_id' => __( 'Post ID', 'tt' ),
            'post_name' => __( 'Post Name', 'tt' )
        )
    );


    // - 商品首页关键词
    $options[] = array(
        'name' => __( 'Shop Home Keywords', 'tt' ),
        'desc' => __( 'The keywords of shop homepage, good for SEO', 'tt' ),
        'id' => 'tt_shop_keywords',
        'std' => __('Market', 'tt'),
        'type' => $theme_pro ? 'text' : 'disabled'
    );

    // - 商品首页横幅图片
    $options[] = array(
        'name' => __( 'Banner背景图', 'tt' ),
        'desc' => '',
        'id' => 'tt_shop_hero_bg',
        'std' => THEME_ASSET . '/img/super-hero-shop.jpg',
        'type' => 'upload'
    );
  
    // - 商品首页横幅大标题
    $options[] = array(
        'name' => __( 'Shop Home Banner Title', 'tt' ),
        'desc' => __( 'The main title displayed in the banner of shop homepage', 'tt' ),
        'id' => 'tt_shop_title',
        'std' => __('Shop Quality Products', 'tt'),
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 商品首页横幅小标题
    $options[] = array(
        'name' => __( 'Shop Home Banner Sub Title', 'tt' ),
        'desc' => __( 'The sub title displayed in the banner of shop homepage', 'tt' ),
        'id' => 'tt_shop_sub_title',
        'std' => __('学习教程资源', 'tt'),
        'type' => $theme_pro ? 'text' : 'disabled'
    );

     // - 支付方式
    $options[] = array(
        'name' => __( '支付方式', 'tt' ),
        'desc' => __( '目前支持支付宝原生即时到账、担保交易、双功能接口, 对于无法申请支付宝接口的提供个人开发的Alipay Supervisor免签约支付程序', 'tt' ),
        'id' => 'tt_pay_channel',
        'std' => 'alipay',
        'type' => 'select', //$theme_pro ? 'select' : 'disabled',
        'options' => array(
            'alipay' => __( 'Alipay', 'tt' ),  // 支付宝
            'apsv' => __( 'Alipay Supervisor免签约支付', 'tt' ), // Alipay Supervisor 扫码支付
			'youzan' => __( '有赞自主店铺服务', 'tt' )
        )
    );


    // - 支付宝收款帐户
    $options[] = array(
        'name' => __( '支付宝收款帐户邮箱', 'tt' ),
        'desc' => __( '支付宝收款帐户邮箱,要收款必填并务必保持正确', 'tt' ),
        'id' => 'tt_alipay_email',
        'std' => '',
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 站点服务支付宝
    $options[] = array(
        'name' => __( 'Site Alipay', 'tt' ),
        'desc' => __( 'The qrcode image of Alipay account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_alipay_qr',
        'std' => THEME_ASSET . '/img/qr/alipay.png',
        'type' => 'upload'
    );


    // - 支付宝商家身份ID
    $options[] = array(
        'name' => __( '支付宝商家身份ID', 'tt' ),
        'desc' => __( '合作身份者id，以2088开头的16位纯数字', 'tt' ),
        'id' => 'tt_alipay_partner',
        'std' => '',
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 支付宝商家身份key
    $options[] = array(
        'name' => __( '支付宝商家身份key', 'tt' ),
        'desc' => __( '支付宝商家身份安全检验码，以数字和字母组成的32位字符', 'tt' ),
        'id' => 'tt_alipay_key',
        'std' => '',
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 支付宝商家收款类型
    $options[] = array(
        'name' => __( '支付宝商家收款类型', 'tt' ),
        'desc' => __( '支付宝商家收款类型, 支持即时到账, 双功能和担保交易, 注意：切换类型后必须对应修改商家身份key', 'tt' ),
        'id' => 'tt_alipay_service',
        'std' => 'create_direct_pay_by_user',
        'type' => $theme_pro ? 'select' : 'disabled',
        'options' => array(
            'create_direct_pay_by_user' => __( '即时到账', 'tt' ),  // 即时到账
            'trade_create_by_buyer' => __( '双功能', 'tt' ), // 双功能
            'create_partner_trade_by_buyer'  => __('担保交易', 'tt') // 担保交易
        )
    );


    // - Alipay Supervisor APP ID
    $options[] = array(
        'name' => __( 'Alipay Supervisor APP ID', 'tt' ),
        'desc' => '请设置Alipay Supervisor免签约支付辅助APP ID（免签辅助配置文件或设置中必须和这里一致）',
        'id' => 'tt_apsv_appid',
        'std' => '',
        'type' => 'text' //$theme_pro ? 'text' : 'disabled'
    );


    // - Alipay Supervisor APP Key
    $options[] = array(
        'name' => __( 'Alipay Supervisor APP Key', 'tt' ),
        'desc' => '请设置Alipay Supervisor免签约支付辅助APP KEY（免签辅助配置文件或设置中必须和这里一致）',
        'id' => 'tt_apsv_appkey',
        'std' => '',
        'type' => 'text' //$theme_pro ? 'text' : 'disabled'
    );


    // - Alipay Supervisor Secret
    $options[] = array(
        'name' => __( 'Alipay Supervisor Secret', 'tt' ),
        'desc' => '请设置Alipay Supervisor免签约支付辅助pushStateSecret（免签辅助配置文件或设置中必须和这里一致）',
        'id' => 'tt_apsv_secret',
        'std' => '',
        'type' => 'text' //$theme_pro ? 'text' : 'disabled'
    );

    // - 有赞云client id
    $options[] = array(
        'name' => __( '有赞云Client ID', 'tt' ),
        'desc' => __( '有赞云应用client_id, 参考https://console.youzanyun.com/application/setting', 'tt' ),
        'id' => 'tt_youzan_client_id',
        'std' => '',
        'type' => 'text' //$theme_pro ? 'text' : 'disabled'
    );

    // - 有赞云client secret
    $options[] = array(
        'name' => __( '有赞云Client Secret', 'tt' ),
        'desc' => __( '有赞云应用client_secret, 参考https://console.youzanyun.com/application/setting', 'tt' ),
        'id' => 'tt_youzan_client_secret',
        'std' => '',
        'type' => 'text' //$theme_pro ? 'text' : 'disabled'
    );

    // - 有赞云应用绑定的店铺ID
    $options[] = array(
        'name' => __( '有赞云应用绑定的店铺ID', 'tt' ),
        'desc' => __( '有赞云应用绑定的店铺ID, 参考https://console.youzanyun.com/application/setting 基本信息部分', 'tt' ),
        'id' => 'tt_youzan_kdt_id',
        'std' => '',
        'type' => 'text' //$theme_pro ? 'text' : 'disabled'
    );

    // 使用有赞云推送中转服务
    //$options[] = array(
     //   'name' => __( '使用有赞云推送中转服务', 'tt' ),
     //   'desc' => __( '如果你需要使用外置的推送解析服务，请开启此选项，并向主题方咨询购买服务，主要解决问题是单独服务稳定性，以及有赞云推送不支持LetEncrypt证书的网址，只能中转推送', 'tt' ),
     //   'id' => 'tt_enable_youzan_helper',
     //   'std' => false,
     //   'type' => 'checkbox'
   // );

    // - 有赞云辅助推送校验secret
   // $options[] = array(
    //    'name' => __( '有赞云辅助推送校验secret', 'tt' ),
    //    'desc' => __( '有赞云辅助推送校验secret, 请保持该secret私有, 并与辅助中配置的SELF_SECRET一致', 'tt' ),
    //    'id' => 'tt_youzan_self_secret',
    //    'std' => '',
    //    'type' => 'text' //$theme_pro ? 'text' : 'disabled'
   // );

   // // - 有赞云辅助接口地址
   // $options[] = array(
    //    'name' => __( '有赞云辅助接口地址', 'tt' ),
     //   'desc' => __( '有赞云辅助接口地址, 用于生成专用的收款二维码', 'tt' ),
     //   'id' => 'tt_youzan_util_api',
    //    'std' => '',
    //    'type' => 'text' //$theme_pro ? 'text' : 'disabled'
   // );
    // - 站点微信收款二维码
    $options[] = array(
        'name' => __( 'Site Weixin Pay QR', 'tt' ),
        'desc' => __( 'The Weixin pay qrcode image for collection money', 'tt' ),
        'id' => 'tt_weixin_pay_qr',
        'std' => THEME_ASSET . '/img/qr/weixin_pay.png',
        'type' => 'upload'
    );


    // - 站点支付宝收款二维码
    $options[] = array(
        'name' => __( 'Site Alipay Pay QR', 'tt' ),
        'desc' => __( 'The Alipay pay qrcode image for collection money', 'tt' ),
        'id' => 'tt_alipay_pay_qr',
        'std' => THEME_ASSET . '/img/qr/alipay_pay.png',
        'type' => 'upload'
    );

    // - 自动关闭过期订单
    $options[] = array(
        'name' => __( '订单状态维护', 'tt' ),
        'desc' => __( '自动关闭多少天以上未支付订单, 设置为0则不启用自动状态维护', 'tt' ),
        'id' => 'tt_maintain_orders_deadline',
        'std' => '0',
        'class' => 'mini',
        'type' => 'text' //$theme_pro ? 'text' : 'disabled'
    );

	// 主题设置 - 辅助设置(包含短链接、SMTP工具等)
	$options[] = array(
		'name' => __( 'Auxiliary', 'tt' ),
		'type' => 'heading'
	);


	// - Memcache/redis/...内存对象缓存
    $options[] = array(
        'name' => __( 'Object Cache', 'tt' ),
        'desc' => __( 'Object cache support, accelerate your site', 'tt' ),
        'id' => 'tt_object_cache',
        'std' => 'none',
        'type' => 'select',
        'options' => array(
            'memcache' => __( 'Memcache', 'tt' ),  //TODO: add tutorial url
            'redis' => __( 'Redis', 'tt' ),
            'none'  => __('None', 'tt')
        )
    );


    if (of_get_option('tt_object_cache')=='memcache'):
    // - Memcache Host
    $options[] = array(
        'name' => __( 'Memcache Host', 'tt' ),
        'desc' => __( 'Memcache server host', 'tt' ),
        'id' => 'tt_memcache_host',
        'std' => '127.0.0.1',
        'type' => 'text'
    );


    // - Memcache Port
    $options[] = array(
        'name' => __( 'Memcache Port', 'tt' ),
        'desc' => __( 'Memcache server port', 'tt' ),
        'id' => 'tt_memcache_port',
        'std' => 11211,
        'class' => 'mini',
        'type' => 'text'
    );
    endif;


    if (of_get_option('tt_object_cache')=='redis'):
    // - Redis Host
    $options[] = array(
        'name' => __( 'Redis Host', 'tt' ),
        'desc' => __( 'Redis server host', 'tt' ),
        'id' => 'tt_redis_host',
        'std' => '127.0.0.1',
        'type' => 'text'
    );


    // - Redis Port
    $options[] = array(
        'name' => __( 'Redis Port', 'tt' ),
        'desc' => __( 'Redis server port', 'tt' ),
        'id' => 'tt_redis_port',
        'std' => 6379,
        'class' => 'mini',
        'type' => 'text'
    );
    endif;


    // - Separator
//    $options[] = array(
//        'name' => __( 'Mailer Separator', 'tt' ),
//        'class'=> 'option-separator',
//        'type' => 'info'
//    );

    // - SMTP/PHPMail
    $options[] = array(
        'name' => __( 'SMTP/PHPMailer', 'tt' ),
        'desc' => __( 'Use SMTP or PHPMail as default mailer', 'tt' ),
        'id' => 'tt_default_mailer',
        'std' => 'smtp',
        'type' => 'select',
        'options' => array(
            'php' => __('PHP', 'tt'),
            'smtp' => __('SMTP', 'tt')
        )
    );


    if (of_get_option('tt_default_mailer')==='smtp'):
    // - SMTP 主机
    $options[] = array(
        'name' => __( 'SMTP Host', 'tt' ),
        'desc' => __( 'Your SMTP service host', 'tt' ),
        'id' => 'tt_smtp_host',
        'std' => 'smtp.gmail.com',
        'placeholder' => 'e.g smtp.163.com',
        'type' => 'text'
    );


    // - SMTP 端口
    $options[] = array(
        'name' => __( 'SMTP Port', 'tt' ),
        'desc' => __( 'Your SMTP service port', 'tt' ),
        'id' => 'tt_smtp_port',
        'std' => 465,
        'class' => 'mini',
        'type' => 'text'
    );


    // - SMTP 安全
    $options[] = array(
        'name' => __( 'SMTP Secure', 'tt' ),
        'desc' => __( 'Your SMTP server secure protocol', 'tt' ),
        'id' => 'tt_smtp_secure',
        'std' => 'ssl',
        'type' => 'select',
        'options' => array(
            'auto' => __('Auto', 'tt'),
            'ssl' => __('SSL', 'tt'),
            'tls' => __('TLS', 'tt'),
            'none' => __('None', 'tt')
        )
    );


    // - SMTP 用户名
    $options[] = array(
        'name' => __( 'SMTP Username', 'tt' ),
        'desc' => __( 'Your SMTP username', 'tt' ),
        'id' => 'tt_smtp_username',
        'std' => 'lu321yuan@gmial.com',
        'type' => 'text'
    );


    // - SMTP 密码
    $options[] = array(
        'name' => __( 'SMTP Password', 'tt' ),
        'desc' => __( 'Your SMTP password', 'tt' ),
        'id' => 'tt_smtp_password',
        'std' => 'yxfcmghhkesakm',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 你的姓名
    $options[] = array(
        'name' => __( 'Your Name', 'tt' ),
        'desc' => __( 'Your display name as the sender', 'tt' ),
        'id' => 'tt_smtp_name',
        'std' => $blog_name,
        'class' => 'mini',
        'type' => 'text'
    );
    endif;


    if (of_get_option('tt_default_mailer')!=='smtp'):
    // - PHP Mail 发信人姓名
    $options[] = array(
        'name' => __( 'PHP Mail Sender Display Name', 'tt' ),
        'desc' => __( 'The Sender display name when using PHPMail send mail', 'tt' ),
        'id' => 'tt_mail_custom_sender',
        'std' => $blog_name,
        'class' => 'mini',
        'type' => 'text'
    );


    // - PHP Mail 发信人地址
    $options[] = array(
        'name' => __( 'PHP Mail Sender Address', 'tt' ),
        'desc' => __( 'You can use fake mail address when use PHPMail', 'tt' ),
        'id' => 'tt_mail_custom_address',
        'std' => '',
        'placeholder' => 'e.g no-reply@domain.com',
        'type' => 'text'
    );
    endif;


    // - 短链接前缀
    $options[] = array(
        'name' => __( 'Short Link Prefix', 'tt' ),
        'desc' => __( 'Use short link instead long link or even convert external link to internal link', 'tt' ),
        'id' => 'tt_short_link_prefix',
        'std' => 'go',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 短链接记录
    $options[] = array(
        'name' => __( 'Short Link Records', 'tt' ),
        'desc' => __( 'One line for one record, please conform to the sample', 'tt' ),
        'id' => 'tt_short_link_records',
        'std' => 'baidu | http://www.baidu.com' . PHP_EOL,
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启登录邮件提醒
    $options[] = array(
        'name' => __( 'Login Email Notification', 'tt' ),
        'desc' => __( 'Enable email notification when a successfully login event happened', 'tt' ),
        'id' => 'tt_login_success_notify',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 开启登录错误邮件提醒
    $options[] = array(
        'name' => __( 'Login Failure Email Notification', 'tt' ),
        'desc' => __( 'Enable email notification when a login failure event happened', 'tt' ),
        'id' => 'tt_login_failure_notify',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 启用订单相关邮件提醒
    $options[] = array(
        'name' => __( 'Order Related Notification', 'tt' ),
        'desc' => __( 'Enable order related notifications', 'tt' ),
        'id' => 'tt_order_events_notify',
        'std' => true,
        'type' => 'checkbox'
    );

    // - 启用评论相关邮件提醒
    $options[] = array(
        'name' => __( 'Comment Related Notification', 'tt' ),
        'desc' => __( '启用评论邮件提醒(如果你的邮件发送较慢，将影响评论提交速度)', 'tt' ),
        'id' => 'tt_comment_events_notify',
        'std' => true,
        'type' => 'checkbox'
    );
  
    // 腾讯防水墙验证码
   $options[] = array(
        'name' => __( '登录与注册验证码功能（腾讯防水墙提供）', 'tt' ),
        'desc' => __( '启用登录与注册验证码功能', 'tt' ),
        'id' => 'tt_tencent_captcha',
        'std' => false,
        'type' => 'checkbox'
    );
  
    if (of_get_option('tt_tencent_captcha',false)):
    // 腾讯防水墙验证码 App ID
    $options[] = array(
        'name' => __( '腾讯防水墙验证码 App ID', 'tt' ),
        'desc' => __( '腾讯防水墙验证码 App ID，访问https://007.qq.com注册，完全免费，注册添加应用后到快速接入中获取App ID', 'tt' ),
        'id' => 'tt_tencent_captcha_id',
        'std' => '',
        'class' => 'mini',
       'type' => 'text'
    );

    // 腾讯防水墙验证码 App Secret Key
    $options[] = array(
        'name' => __( '腾讯防水墙验证码 App Secret Key', 'tt' ),
        'desc' => __( '腾讯防水墙验证码 App Secret Key，访问https://007.qq.com注册，完全免费，注册添加应用后到快速接入中获取App Secret Key', 'tt' ),
        'id' => 'tt_tencent_captcha_sk',
        'std' => '',
        'class' => 'mini',
       'type' => 'text'
    );
    
    endif;
    // - 主题静态资源CDN路径
    $options[] = array(
        'name' => __('主题静态资源CDN路径', 'tt'),
        'desc' => __('主题程序的静态资源存放路径，网站升级https此处一定要先改为https，否则启用https网页错位', 'tt'),
        'id' => 'tt_cute_static_cdn_path',
        'std' => THEME_ASSET,
        'type' => 'text'
    );


    // 主题反馈
  //    $options[] = array(
   //       'name' => __( 'Feedback', 'tt' ),
   //       'type' => 'heading'
   //   );

   //   // 交流论坛
   //   $options[] = array(
   //       'name' => __( '交流论坛', 'tt' ),
   //       'desc' => sprintf(__( '<br><h2><a href="%s" target="_blank">Tint主题/WordPress交流论坛</a></h2>', 'tt'), 'https://elune.me'),
   //       'type' => 'info'
   //   );

  //    // 联系作者
  //    $options[] = array(
  //        'name' => __( 'Contact Author', 'tt' ),
    //      'desc' => sprintf(__( '<br><h2>Email: chinash2010@gmail.com</h2><br><h2>Wechat & Alipay & QQ(below)</h2><br><img src="%s"><img src="%s"><img src="%s"> ', 'tt' ), THEME_ASSET . '/img/qr/weixin.png', THEME_ASSET . '/img/qr/alipay.png', THEME_ASSET . '/img/qr/qq.png'),
   //       'type' => 'info'
  //    );

   //   // 相关作品
   //   $options[] = array(
   //       'name' => __( 'Related Works', 'tt' ),
    //      'desc' => sprintf(__( '<br><h2>Alipay Supervisor (<a href="%s" target="_blank">View Detail</a>)</h2><br><p>A toolkit for helping improve payment experience</p>', 'tt'), TT_SITE . '/shop/apsv.html'),
    //      'type' => 'info'
   //   );

    //  // 相关作品2
    //  $options[] = array(
    //      'name' => "",
    //      'desc' => sprintf(__( '<br><h2>Alipay Supervisor 桌面版 (<a href="%s" target="_blank">查看详情</a>)</h2><br><p>支付宝免签约工具桌面版客户端</p>', 'tt'), TT_SITE . '/shop/apsv-gui.html'),
     //     'type' => 'info'
    //  );


    // 其他 - 主题调试/更新
    //TODO: 版本升级 升级日志
    $options[] = array(
        'name' => __( 'Others', 'tt' ),
        'type' => 'heading'
    );


    // - 开启调试
    $options[] = array(
        'name' => __( 'Debug Mode', 'tt' ),
        'desc' => __( 'Enable debug will force display php errors, disable theme cache, enable some private links or functions, etc.', 'tt' ),
        'id' => 'tt_theme_debug',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 单独暂停缓存
    $options[] = array(
        'name' => __( 'Disable Cache', 'tt' ),
        'desc' => __( 'Stop cache, user always get the latest content', 'tt' ),
        'id' => 'tt_disable_cache',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 主题专用私有Token
    $options[] = array(
        'name' => __('Tint Token', 'tt'),
        'desc' => __('Private token for theme, maybe useful somewhere.', 'tt'),
        'id' => 'tt_private_token',
        'std' => Utils::generateRandomStr(5),
        'class' => 'mini',
        'type' => 'text'
    );


    // - 刷新固定链接链接
    $options[] = array(
        'name'  =>  __('Refresh Rewrite Rules', 'tt'),
        'desc'  =>  sprintf(__('Please Click to <a href="%1$s/m/refresh?token=%2$s" target="_blank">Refresh Rewrite Rules</a> if you have encounter some 404 errors', 'tt'), $blog_home, of_get_option('tt_private_token')),
        'type'  => 'info'
    );


    // - 登录API后缀
    $options[] = array(
        'name' => __( '登录API后缀', 'tt' ),
        'desc' => __( '请变更默认值降低密码爆破攻击风险', 'tt' ),
        'id' => 'tt_session_api',
        'std' => 'session',
        'type' => 'text'
    );


    // - QQ邮我链接ID
    $options[] = array(
        'name' => __( 'QQ Mailme ID', 'tt' ),
        'desc' => __( 'The id of qq mailme, visit `http://open.mail.qq.com` for detail', 'tt' ),
        'id' => 'tt_mailme_id',
        'std' => '',
        'type' => 'text'
    );


    // - QQ邮件列表ID
    $options[] = array(
        'name' => __( 'QQ Mail list ID', 'tt' ),
        'desc' => __( 'The id of qq mailme, visit `http://list.qq.com` for detail', 'tt' ),
        'id' => 'tt_maillist_id',
        'std' => '',
        'type' => 'text'
    );


    // - Head自定义代码
    $options[] = array(
        'name' => __( 'Head Custom Code', 'tt' ),
        'desc' => __( 'Custom code loaded on page head', 'tt' ),
        'id' => 'tt_head_code',
        'std' => '',
        'type' => 'textarea',
        'raw' => true
    );


    // - Foot自定义代码
    $options[] = array(
        'name' => __( 'Foot Custom Code', 'tt' ),
        'desc' => __( 'Custom code loaded on page foot', 'tt' ),
        'id' => 'tt_foot_code',
        'std' => '',
        'type' => 'textarea',
        'raw' => true
    );

    // - Foot IDC备案文字
    $options[] = array(
        'name' => __( 'Foot Beian Text', 'tt' ),
        'desc' => __( 'IDC reference No. for regulations of China', 'tt' ),
        'id' => 'tt_beian',
        'std' => '',
        'type' => 'text'
    );


    $options[] = array(
        'name' => __( 'Site Open Date', 'tt' ),
        'desc' => __('The date of when site opened, use `YYYY-mm-dd` format', 'tt'),
        'id' => 'tt_site_open_date',
        'std' => date('Y-m-d'),//(new DateTime())->format('Y-m-d'),
        //'class' => 'mini',
        'type' => 'text'
    );



    // - 页脚输出统计PHP查询信息
    $options[] = array(
        'name' => __( 'Footer Queries Info', 'tt' ),
        'desc' => __( 'Show WordPress queries statistic information', 'tt' ),
        'id' => 'tt_show_queries_num',
        'std' => false,
        'type' => 'checkbox'
    );

//K版扩展
   $options[] = array(
        'name' => __( 'K版扩展', 'tt' ),
       'type' => 'heading'
   );
  
    // K版个性化CSS
   $options[] = array(
        'name' => __( 'K版个性化CSS', 'tt' ),
        'desc' => __( '启用K版扩展相关CSS(启用下列功能必须开启CSS，否则页面错乱，此功能包含了所有额外功能CSS，包括七彩标签，导航栏定位，footer的样式等，必须开启，相关文件在\assets\css\custom.css)', 'tt' ),
        'id' => 'tt_enable_k_css',
        'std' => true,
        'type' => 'checkbox'
    );
  // K版个性化JS
   $options[] = array(
        'name' => __( 'K版个性化JS', 'tt' ),
        'desc' => __( '启用K版扩展相关JS（包括美化添加的所有js功能，务必开启，相关文件在\assets\js\custom.js及custom-m.js）', 'tt' ),
        'id' => 'tt_enable_k_bkpfdh',
        'std' => true,
        'type' => 'checkbox'
    );
      // 熊掌号功能
$options[] = array(
        'name' => __( '熊掌号功能', 'tt' ),
        'desc' => __( '启用熊掌号功能(此选项为熊掌号功能总开关)', 'tt' ),
        'id' => 'tt_enable_k_xzhid',
        'std' => false,
        'type' => 'checkbox'
    );
  // 熊掌号ID
    $options[] = array(
        'name' => __( '熊掌号ID', 'tt' ),
        'desc' => __( '请填写熊掌号ID，到http://ziyuan.baidu.com/xzh/home/index获取', 'tt' ),
        'id' => 'tt_k_id',
        'std' => '熊掌号ID',
        'class' => 'mini',
       'type' => 'text'
    );
        // 熊掌号文章页底部关注功能
$options[] = array(
        'name' => __( '熊掌号文章页底部关注功能', 'tt' ),
        'desc' => __( '启用熊掌号文章页底部关注功能', 'tt' ),
        'id' => 'tt_enable_k_xzhwzgz',
        'std' => false,
        'type' => 'checkbox'
    );
   // 熊掌号Json_LD数据
  $options[] = array(
        'name' => __( '熊掌号Json_LD数据', 'tt' ),
        'desc' => __( '启用熊掌号Json_LD数据', 'tt' ),
        'id' => 'tt_enable_k_xzhld',
        'std' => false,
        'type' => 'checkbox'
    );
     // 文章页底部打赏按钮
  $options[] = array(
        'name' => __( '文章页底部打赏按钮', 'tt' ),
        'desc' => __( '启用文章页底部打赏按钮', 'tt' ),
        'id' => 'tt_enable_k_postds',
        'std' => true,
        'type' => 'checkbox'
    );
    // 文章页底部生成封面按钮及其相关功能
  $options[] = array(
        'name' => __( '文章页底部生成封面功能（php7以上支持）', 'tt' ),
        'desc' => __( '启用文章页底部生成封面按钮及其相关功能', 'tt' ),
        'id' => 'tt_enable_k_postfm',
        'std' => false,
        'type' => 'checkbox'
    );
   // - 文章封面底部Logo
    $options[] = array(
        'name' => __( '文章封面底部Logo', 'tt' ),
        'desc' => __( '请上传文章封面logo，分辨率为181X40', 'tt' ),
        'id' => 'tt_postfm_logo',
        'std' => '',
        'type' => 'upload'
    );
  // - 文章页底部生成封面底部描述
    $options[] = array(
        'name' => __( '文章封面底部描述', 'tt' ),
        'desc' => __( '文章封面底部描述文字，留空将抓取网站描述', 'tt' ),
        'id' => 'tt_postfm_description',
        'std' => '',
        'type' => 'text'
    );
       // 作者小工具扩展信息
  $options[] = array(
        'name' => __( '作者小工具扩展信息', 'tt' ),
        'desc' => __( '启用作者小工具扩展信息', 'tt' ),
        'id' => 'tt_enable_k_authorkz',
        'std' => true,
        'type' => 'checkbox'
    );

             //开启游客评论
  $options[] = array(
        'name' => __( '开启游客评论', 'tt' ),
        'desc' => __( '启用游客评论', 'tt' ),
        'id' => 'tt_enable_k_ykpl',
        'std' => true,
        'type' => 'checkbox'
    );
       //开启文章内容自动标签链接
  $options[] = array(
        'name' => __( '开启文章内容自动标签链接', 'tt' ),
        'desc' => __( '启用文章内容自动标签链接（此开关生效时间较长，如未生效请多清理几次缓存）', 'tt' ),
        'id' => 'tt_enable_k_post_tag_link',
        'std' => true,
        'type' => 'checkbox'
    );
            //开启页脚新样式
  $options[] = array(
        'name' => __( '开启页脚新样式', 'tt' ),
        'desc' => __( '启用页脚新样式', 'tt' ),
        'id' => 'tt_enable_k_footernews',
        'std' => true,
        'type' => 'checkbox'
    );
  
        //开启全局链接新窗口打开
  $options[] = array(
        'name' => __( '开启全局链接新窗口打开', 'tt' ),
        'desc' => __( '启用全局链接新窗口打开', 'tt' ),
        'id' => 'tt_enable_k_blank',
        'std' => true,
        'type' => 'checkbox'
    );
  // - 注册用户名黑名单字符列表
    $options[] = array(
        'name' => __( '注册用户名黑名单字符列表(不区分大小写)', 'tt' ),
        'desc' => __( '注册用户名黑名单字符列表，每个字符用英文逗号隔开，用于禁止某些特定字符注册，如禁止包含admin的用户名注册', 'tt' ),
        'id' => 'tt_reg_blacklist',
        'std' => 'admin,test',
        'type' => 'textarea',
        'raw' => true
    );
  // - 评论数据黑名单
    $options[] = array(
        'name' => __( '评论数据黑名单(不区分大小写)', 'tt' ),
        'desc' => __( '评论数据黑名单，可以是昵称，邮箱，网址，每个字符串用英文逗号隔开', 'tt' ),
        'id' => 'tt_comment_blacklist_check',
        'std' => 'admin,admin@qq.com',
        'type' => 'textarea',
        'raw' => true
    );
          //邀请码注册功能
  $options[] = array(
        'name' => __( '是否启用邀请码注册功能', 'tt' ),
        'desc' => __( '启用邀请码注册功能', 'tt' ),
        'id' => 'tt_enable_k_invite',
        'std' => false,
        'type' => 'checkbox'
    );
       // 邀请码价格
  $options[] = array(
        'name' => __( '邀请码价格', 'tt' ),
        'desc' => __( '请设置邀请码价格（单位：元）', 'tt' ),
        'id' => 'tt_k_invite_price',
        'std' => '5',
        'class' => 'mini',
       'type' => 'text'
    );
         // 邀请码有效期
  $options[] = array(
        'name' => __( '邀请码有效期', 'tt' ),
        'desc' => __( '请设置邀请码有效期（单位：天）', 'tt' ),
        'id' => 'tt_k_invite_active_time',
        'std' => '7',
        'class' => 'mini',
       'type' => 'text'
    );
  
          //限制免费资源需要捐赠才能下载
  $options[] = array(
        'name' => __( '是否启用限制免费资源需要捐赠才能下载', 'tt' ),
        'desc' => __( '启用限制免费资源需要捐赠才能下载', 'tt' ),
        'id' => 'tt_enable_k_donate',
        'std' => false,
        'type' => 'checkbox'
    );
       // 捐赠下载价格
  $options[] = array(
        'name' => __( '捐赠下载价格', 'tt' ),
        'desc' => __( '捐赠下载价格（单位：元）', 'tt' ),
        'id' => 'tt_k_donate_price',
        'std' => '5',
        'class' => 'mini',
       'type' => 'text'
    );
         // 捐赠下载有效期
  $options[] = array(
        'name' => __( '捐赠下载有效期', 'tt' ),
        'desc' => __( '请设置捐赠下载有效期（单位：小时）', 'tt' ),
        'id' => 'tt_k_donate_active_time',
        'std' => '12',
        'class' => 'mini',
       'type' => 'text'
    );
  
        //是否开启注册邮箱验证
  $options[] = array(
        'name' => __( '是否开启注册邮箱验证', 'tt' ),
        'desc' => __( '启用注册邮箱验证', 'tt' ),
        'id' => 'tt_enable_k_reg_email',
        'std' => true,
        'type' => 'checkbox'
    );
  
            //开放平台注册邮箱验证
  $options[] = array(
        'name' => __( '是否启用开放平台注册的邮箱验证', 'tt' ),
        'desc' => __( '启用开放平台注册邮箱验证', 'tt' ),
        'id' => 'tt_enable_k_reg_ver',
        'std' => false,
        'type' => 'checkbox'
    );
  
              //开启文章页右侧作者小工具
  $options[] = array(
        'name' => __( '是否启用文章页右侧作者小工具', 'tt' ),
        'desc' => __( '开启文章页右侧作者小工具', 'tt' ),
        'id' => 'tt_enable_k_author_widget',
        'std' => true,
        'type' => 'checkbox'
    );
  
              //隐藏文章页评论模块
  $options[] = array(
        'name' => __( '隐藏文章页评论模块', 'tt' ),
        'desc' => __( '隐藏文章页评论模块', 'tt' ),
        'id' => 'tt_enable_k_post_respond',
        'std' => false,
        'type' => 'checkbox'
    );
  
              //禁用fancybox图片灯箱
  $options[] = array(
        'name' => __( '禁用fancybox图片灯箱', 'tt' ),
        'desc' => __( '禁用fancybox图片灯箱', 'tt' ),
        'id' => 'tt_enable_k_fancybox',
        'std' => false,
        'type' => 'checkbox'
    );
  
  //自定义幻灯
   $options[] = array(
        'name' => __( '自定义幻灯内容', 'tt' ),
       'type' => 'heading'
   );
   // 使用提示
   $options[] = array(
         'name' => __( '使用提示', 'tt' ),
          'desc' => '自定义幻灯内容在真全屏幻灯模式下与文章幻灯混合显示',
          'type' => 'info'
      );
  
      // - 自定义幻灯内容
    $options[] = array(
        'name' => __( '自定义幻灯内容', 'tt' ),
        'desc' => __( '由于主题设置框架的限制，暂只支持此方式编辑幻灯内容，请按格式填写，每一条内容用回车隔开（图片地址|链接地址|显示标题），此处内容只在真全屏幻灯模式下显示，留空则只显示文章幻灯', 'tt' ),
        'id' => 'tt_k_custom_slider_content',
        'std' => '图片地址|链接地址|显示标题',
        'raw' => true,
        'type' => 'textarea'
    );
   
     // - 商品链接的链接前缀
//    $options[] = array(
//        'name' => __( 'Products Archive Link Slug', 'tt' ),
//        'desc' => __( 'The special prefix in product archive link', 'tt' ),
//        'id' => 'tt_thread_archives_slug',
//        'std' => 'thread',
//        'class' => 'mini',
//        'type' => 'text'
//    );
     //微博图床
   $options[] = array(
        'name' => __( '微博图床', 'tt' ),
       'type' => 'heading'
   );
  
  // 使用提示
   $options[] = array(
         'name' => __( '使用提示', 'tt' ),
          'desc' => '此功能用于自动替换文章图片内容，并保存原地址到数据库，即使微博图床挂了也能还原',
          'type' => 'info'
      );
   
   // 微博图床功能
   $options[] = array(
        'name' => __( '微博图床功能', 'tt' ),
        'desc' => __( '启用微博图床功能', 'tt' ),
        'id' => 'tt_enable_k_weibo_image',
        'std' => false,
        'type' => 'checkbox'
    );
  
   if (of_get_option('tt_enable_k_weibo_image')):
   // 微博图床替换方式
   $options[] = array(
        'name' => __( '微博图床替换方式', 'tt' ),
        'desc' => __( '启用微博图床功能', 'tt' ),
        'id' => 'tt_k_weibo_image_type',
        'std' => 'ssl',
        'type' => 'select',
        'options' => array(
            'normal' => '常规（推荐，不修改文章内容，访问页面时实时替换）',
            'modify' => '修改（保存文章时修改内容，查看页面时直接显示外链。不推荐使用，使用时建议关闭自动修复功能）'
        )
    );
  
    // 微博图床自动修复功能
   $options[] = array(
        'name' => __( '微博图床自动修复功能', 'tt' ),
        'desc' => __( '启用微博图床自动修复功能（请在php设置超时限制中适当增加数值，建议数值86400，否者时间太短程序将无法运行完毕）', 'tt' ),
        'id' => 'tt_enable_k_auto_check_weibo_image',
        'std' => false,
        'type' => 'checkbox'
    );
    
   // 媒体库图床加速
   $options[] = array(
        'name' => __( '媒体库图床加速', 'tt' ),
        'desc' => __( '启用媒体库图床加速（直接调用已存在的微博图床图片）', 'tt' ),
        'id' => 'tt_enable_media_weibo_image',
        'std' => false,
        'type' => 'checkbox'
    );
  
     // 缩略图图床加速
   $options[] = array(
        'name' => __( '缩略图图床加速', 'tt' ),
        'desc' => __( '启用缩略图图床加速（第一次获取较慢）', 'tt' ),
        'id' => 'tt_enable_thumb_weibo_image',
        'std' => false,
        'type' => 'checkbox'
    );
  
    // - 重置文章图片
    $options[] = array(
        'name'  =>  '重置文章图片',
        'desc'  =>  sprintf('请点击<a href="%1$s/site/weibo_image_rest?post_id=all" target="_blank">重置文章图片</a>  （如果你不想用微博图床了，可以点此重置为原图片链接）', $blog_home),
        'type'  => 'info'
    );
  
    // 微博账号
    $options[] = array(
        'name' => __( '微博账号', 'tt' ),
        'desc' => __( '请填写微博账号，用于登录发图（可以是小号，不会发出微博，传图不会留下任何痕迹）', 'tt' ),
        'id' => 'tt_k_weibo_image_username',
        'std' => '',
        'class' => 'mini',
       'type' => 'text'
    );
    
    // 微博密码
    $options[] = array(
        'name' => __( '微博密码', 'tt' ),
        'desc' => __( '请填写微博密码，用于登录发图（可以是小号，不会发出微博，传图不会留下任何痕迹）', 'tt' ),
        'id' => 'tt_k_weibo_image_password',
        'std' => '',
        'class' => 'mini',
       'type' => 'text'
    );
    endif;
   //页脚配置
   $options[] = array(
        'name' => __( '页脚配置', 'tt' ),
       'type' => 'heading'
   );
  
   // - 页脚支付宝二维码
    $options[] = array(
        'name' => __( '页脚支付宝二维码', 'tt' ),
        'desc' => __( '请上传页脚支付宝二维码，长=宽', 'tt' ),
        'id' => 'tt_custom_footer_alipay',
        'std' => THEME_ASSET . '/img/qr/alipay.png',
        'type' => 'upload'
    );
    
  // - 页脚微信二维码
    $options[] = array(
        'name' => __( '页脚微信二维码', 'tt' ),
        'desc' => __( '请上传页脚微信二维码，长=宽', 'tt' ),
        'id' => 'tt_custom_footer_weixin',
        'std' => THEME_ASSET . '/img/qr/weixin.png',
        'type' => 'upload'
    );
  
   //推广系统
   $options[] = array(
        'name' => __( '推广系统', 'tt' ),
       'type' => 'heading'
   );
  
   // - 访问推广奖励积分
    $options[] = array(
        'name' => __( '访问推广奖励积分', 'tt' ),
        'desc' => __( '通过分享链接推广其他用户访问本站时奖励的积分数量', 'tt' ),
        'id' => 'tt_rec_view_credit',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 注册推广奖励积分
    $options[] = array(
        'name' => __( '注册推广奖励积分', 'tt' ),
        'desc' => __( '通过分享链接推广其他用户注册本站用户时奖励的积分数量', 'tt' ),
        'id' => 'tt_rec_reg_credit',
        'std' => '30',
        'class' => 'mini',
        'type' => 'text'
    );
  
   // - 访问推广次数限制
    $options[] = array(
        'name' => __( '访问推广次数限制', 'tt' ),
        'desc' => __( '每日通过访问推广最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_view_num',
        'std' => '50',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 注册推广次数限制
    $options[] = array(
        'name' => __( '注册推广次数限制', 'tt' ),
        'desc' => __( '每日通过注册推广最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_reg_num',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );
    
    // 推广返利提成功能
   $options[] = array(
        'name' => __( '推广返利提成功能', 'tt' ),
        'desc' => __( '启用推广返利提成功能（用户消费余额或积分，推广人将获得推广提成）', 'tt' ),
        'id' => 'tt_rec_rebate',
        'std' => true,
        'type' => 'checkbox'
    );
    
    // - 推广返利提成比例
    $options[] = array(
        'name' => __( '推广返利提成比例', 'tt' ),
        'desc' => __( '请输入提成比例（%），如输入30则推广人将获得实付金额的30%（返现金或积分根据支付方式自动识别）', 'tt' ),
        'id' => 'tt_rec_rebate_ratio',
        'std' => '30',
        'class' => 'mini',
        'type' => 'text'
    );
	///////////////////////////////////////////////////////////////////////////

//	// Test data
//	$test_array = array(
//		'one' => __( 'One', 'tt' ),
//		'two' => __( 'Two', 'tt' ),
//		'three' => __( 'Three', 'tt' ),
//		'four' => __( 'Four', 'tt' ),
//		'five' => __( 'Five', 'tt' )
//	);
//
//	// Multicheck Array
//	$multicheck_array = array(
//		'one' => __( 'French Toast', 'tt' ),
//		'two' => __( 'Pancake', 'tt' ),
//		'three' => __( 'Omelette', 'tt' ),
//		'four' => __( 'Crepe', 'tt' ),
//		'five' => __( 'Waffle', 'tt' )
//	);
//
//	// Multicheck Defaults
//	$multicheck_defaults = array(
//		'one' => '1',
//		'five' => '1'
//	);
//
//	// Background Defaults
//	$background_defaults = array(
//		'color' => '',
//		'image' => '',
//		'repeat' => 'repeat',
//		'position' => 'top center',
//		'attachment'=>'scroll' );
//
//	// Typography Defaults
//	$typography_defaults = array(
//		'size' => '15px',
//		'face' => 'georgia',
//		'style' => 'bold',
//		'color' => '#bada55' );
//
//	// Typography Options
//	$typography_options = array(
//		'sizes' => array( '6','12','14','16','20' ),
//		'faces' => array( 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ),
//		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
//		'color' => false
//	);
//
//	// Pull all the categories into an array
////	$options_categories = array();
////	$options_categories_obj = get_categories();
////	foreach ($options_categories_obj as $category) {
////		$options_categories[$category->cat_ID] = $category->cat_name;
////	}
//
//	// Pull all tags into an array
////	$options_tags = array();
////	$options_tags_obj = get_tags();
////	foreach ( $options_tags_obj as $tag ) {
////		$options_tags[$tag->term_id] = $tag->name;
////	}
//
//
//	// Pull all the pages into an array
////	$options_pages = array();
////	$options_pages_obj = get_pages( 'sort_column=post_parent,menu_order' );
////	$options_pages[''] = 'Select a page:';
////	foreach ($options_pages_obj as $page) {
////		$options_pages[$page->ID] = $page->post_title;
////	}
//
//
//
//	$options[] = array(
//		'name' => __( 'Input Text Mini', 'tt' ),
//		'desc' => __( 'A mini text input field.', 'tt' ),
//		'id' => 'example_text_mini',
//		'std' => 'Default',
//		'class' => 'mini',
//		'type' => 'text'
//	);
//
//	$options[] = array(
//		'name' => __( 'Input Text', 'tt' ),
//		'desc' => __( 'A text input field.', 'tt' ),
//		'id' => 'example_text',
//		'std' => 'Default Value',
//		'type' => 'text'
//	);
//
//	$options[] = array(
//		'name' => __( 'Input with Placeholder', 'tt' ),
//		'desc' => __( 'A text input field with an HTML5 placeholder.', 'tt' ),
//		'id' => 'example_placeholder',
//		'placeholder' => 'Placeholder',
//		'type' => 'text'
//	);
//
//	$options[] = array(
//		'name' => __( 'Textarea', 'tt' ),
//		'desc' => __( 'Textarea description.', 'tt' ),
//		'id' => 'example_textarea',
//		'std' => 'Default Text',
//		'type' => 'textarea'
//	);
//
//	$options[] = array(
//		'name' => __( 'Input Select Small', 'tt' ),
//		'desc' => __( 'Small Select Box.', 'tt' ),
//		'id' => 'example_select',
//		'std' => 'three',
//		'type' => 'select',
//		'class' => 'mini', //mini, tiny, small
//		'options' => $test_array
//	);
//
//	$options[] = array(
//		'name' => __( 'Input Select Wide', 'tt' ),
//		'desc' => __( 'A wider select box.', 'tt' ),
//		'id' => 'example_select_wide',
//		'std' => 'two',
//		'type' => 'select',
//		'options' => $test_array
//	);
//
//	if ( $options_categories ) {
//		$options[] = array(
//			'name' => __( 'Select a Category', 'tt' ),
//			'desc' => __( 'Passed an array of categories with cat_ID and cat_name', 'tt' ),
//			'id' => 'example_select_categories',
//			'type' => 'select',
//			'options' => $options_categories
//		);
//	}
//
//	if ( $options_tags ) {
//		$options[] = array(
//			'name' => __( 'Select a Tag', 'options_check' ),
//			'desc' => __( 'Passed an array of tags with term_id and term_name', 'options_check' ),
//			'id' => 'example_select_tags',
//			'type' => 'select',
//			'options' => $options_tags
//		);
//	}
//
//	$options[] = array(
//		'name' => __( 'Select a Page', 'tt' ),
//		'desc' => __( 'Passed an pages with ID and post_title', 'tt' ),
//		'id' => 'example_select_pages',
//		'type' => 'select',
//		'options' => $options_pages
//	);
//
//	$options[] = array(
//		'name' => __( 'Input Radio (one)', 'tt' ),
//		'desc' => __( 'Radio select with default options "one".', 'tt' ),
//		'id' => 'example_radio',
//		'std' => 'one',
//		'type' => 'radio',
//		'options' => $test_array
//	);
//
//	$options[] = array(
//		'name' => __( 'Example Info', 'tt' ),
//		'desc' => __( 'This is just some example information you can put in the panel.', 'tt' ),
//		'type' => 'info'
//	);
//
//	$options[] = array(
//		'name' => __( 'Input Checkbox', 'tt' ),
//		'desc' => __( 'Example checkbox, defaults to true.', 'tt' ),
//		'id' => 'example_checkbox',
//		'std' => '1',
//		'type' => 'checkbox'
//	);
//
//	$options[] = array(
//		'name' => __( 'Advanced Settings', 'tt' ),
//		'type' => 'heading'
//	);
//
//	$options[] = array(
//		'name' => __( 'Check to Show a Hidden Text Input', 'tt' ),
//		'desc' => __( 'Click here and see what happens.', 'tt' ),
//		'id' => 'example_showhidden',
//		'type' => 'checkbox'
//	);
//
//	$options[] = array(
//		'name' => __( 'Hidden Text Input', 'tt' ),
//		'desc' => __( 'This option is hidden unless activated by a checkbox click.', 'tt' ),
//		'id' => 'example_text_hidden',
//		'std' => 'Hello',
//		'class' => 'hidden',
//		'type' => 'text'
//	);
//
//	$options[] = array(
//		'name' => __( 'Uploader Test', 'tt' ),
//		'desc' => __( 'This creates a full size uploader that previews the image.', 'tt' ),
//		'id' => 'example_uploader',
//		'type' => 'upload'
//	);
//
//	$options[] = array(
//		'name' => "Example Image Selector",
//		'desc' => "Images for layout.",
//		'id' => "example_images",
//		'std' => "2c-l-fixed",
//		'type' => "images",
//		'options' => array(
//			'1col-fixed' => $imagepath . '1col.png',
//			'2c-l-fixed' => $imagepath . '2cl.png',
//			'2c-r-fixed' => $imagepath . '2cr.png'
//		)
//	);
//
//	$options[] = array(
//		'name' =>  __( 'Example Background', 'tt' ),
//		'desc' => __( 'Change the background CSS.', 'tt' ),
//		'id' => 'example_background',
//		'std' => $background_defaults,
//		'type' => 'background'
//	);
//
//	$options[] = array(
//		'name' => __( 'Multicheck', 'tt' ),
//		'desc' => __( 'Multicheck description.', 'tt' ),
//		'id' => 'example_multicheck',
//		'std' => $multicheck_defaults, // These items get checked by default
//		'type' => 'multicheck',
//		'options' => $multicheck_array
//	);
//
//	$options[] = array(
//		'name' => __( 'Colorpicker', 'tt' ),
//		'desc' => __( 'No color selected by default.', 'tt' ),
//		'id' => 'example_colorpicker',
//		'std' => '',
//		'type' => 'color'
//	);
//
//	$options[] = array( 'name' => __( 'Typography', 'tt' ),
//		'desc' => __( 'Example typography.', 'tt' ),
//		'id' => "example_typography",
//		'std' => $typography_defaults,
//		'type' => 'typography'
//	);
//
//	$options[] = array(
//		'name' => __( 'Custom Typography', 'tt' ),
//		'desc' => __( 'Custom typography options.', 'tt' ),
//		'id' => "custom_typography",
//		'std' => $typography_defaults,
//		'type' => 'typography',
//		'options' => $typography_options
//	);
//
//	$options[] = array(
//		'name' => __( 'Text Editor', 'tt' ),
//		'type' => 'heading'
//	);
//
//	/**
//	 * For $settings options see:
//	 * http://codex.wordpress.org/Function_Reference/wp_editor
//	 *
//	 * 'media_buttons' are not supported as there is no post to attach items to
//	 * 'textarea_name' is set by the 'id' you choose
//	 */
//
//	$wp_editor_settings = array(
//		'wpautop' => true, // Default
//		'textarea_rows' => 5,
//		'tinymce' => array( 'plugins' => 'wordpress,wplink' )
//	);
//
//	$options[] = array(
//		'name' => __( 'Default Text Editor', 'tt' ),
//		'desc' => sprintf( __( 'You can also pass settings to the editor.  Read more about wp_editor in <a href="%1$s" target="_blank">the WordPress codex</a>', 'tt' ), 'http://codex.wordpress.org/Function_Reference/wp_editor' ),
//		'id' => 'example_editor',
//		'type' => 'editor',
//		'settings' => $wp_editor_settings
//	);

	return $options;
}
