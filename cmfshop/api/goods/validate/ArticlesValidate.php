<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace api\goods\validate;

use think\Validate;

class ArticlesValidate extends Validate
{
    protected $rule = [
        'post_title'        =>  'require',
        'post_price'        =>  'require',
	    'post_content'      =>  'require',
	    'categories'        =>  'require'
    ];
    protected $message = [
        'post_title.require'    =>  '商品标题不能为空',
        'post_price.require'    =>  '商品价格不能为空',
	    'post_content.require'  =>  '内容不能为空',
	    'categories.require'    =>  '商品分类不能为空'
    ];

    protected $scene = [
        'article'  => [ 'post_title' , 'post_content' , 'categories','post_price' ],
        'page' => ['post_title']
    ];
}