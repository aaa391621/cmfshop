<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\goods\model;

use api\common\model\CommonModel;
class GoodsTagModel extends CommonModel
{
    //可查询字段
    protected $visible = [
        'id','articles.id','recommended', 'post_count', 'name','articles'
    ];
    //模型关联方法
    protected $relationFilter = ['articles'];

    /**
     * 基础查询
     */
    protected function base($query)
    {
        $query->alias('post_tag')->where('post_tag.status', 1);
    }
    /**
     * 关联 商品表
     * @return $this
     */
    public function articles()
    {
        return $this->belongsToMany('GoodsPostModel','goods_tag_post','post_id','tag_id');
    }
}
