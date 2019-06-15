<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\goods\api;

use app\goods\model\GoodsCategoryModel;

class CategoryApi
{
    /**
     * 分类列表 用于模板设计
     * @param array $param
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function index($param = [])
    {
        $goodsCategoryModel = new GoodsCategoryModel();

        $where = ['delete_time' => 0];

        if (!empty($param['keyword'])) {
            $where['name'] = ['like', "%{$param['keyword']}%"];
        }

        //返回的数据必须是数据集或数组,item里必须包括id,name,如果想表示层级关系请加上 parent_id
        return $goodsCategoryModel->where($where)->select();
    }

    /**
     * 分类列表 用于导航选择
     * @return array
     */
    public function nav()
    {
        $goodsCategoryModel = new GoodsCategoryModel();

        $where = ['delete_time' => 0];

        $categories = $goodsCategoryModel->where($where)->select();

        $return = [
            //'name'  => '商品分类',
            'rule'  => [
                'action' => 'goods/List/index',
                'param'  => [
                    'id' => 'id'
                ]
            ],//url规则
            'items' => $categories //每个子项item里必须包括id,name,如果想表示层级关系请加上 parent_id
        ];

        return $return;
    }

}