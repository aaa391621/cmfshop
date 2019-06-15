<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\goods\model;

use think\Model;

class GoodsTagPostModel extends Model
{
    /**
     * 获取指定id相关的商品id数组
     * @param $post_id  商品id
     * @return array    相关的商品id
     */
    function getRelationPostIds($post_id)
    {
        $tagIds  = $this->where('post_id', $post_id)
            ->column('tag_id');
        $postIds = $this->whereIn('tag_id', $tagIds)
            ->column('post_id');
        return array_unique($postIds);
    }
}