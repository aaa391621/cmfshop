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
namespace app\goods\controller;

use cmf\controller\HomeBaseController;
use app\goods\model\GoodsCategoryModel;

class ListController extends HomeBaseController
{
    public function index()
    {
        $id                  = $this->request->param('id', 0, 'intval');
        $goodsCategoryModel = new GoodsCategoryModel();

        $category = $goodsCategoryModel->where('id', $id)->where('status', 1)->find();
       
        $this->assign('category', $category);

        $listTpl = empty($category['list_tpl']) ? 'list' : $category['list_tpl'];

        return $this->fetch('/' . $listTpl);
    }

}
