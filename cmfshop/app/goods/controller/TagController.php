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
use app\goods\model\GoodsTagModel;

class TagController extends HomeBaseController
{
    public function index()
    {
        $id             = $this->request->param('id');

        $goodsTagModel = new GoodsTagModel();

        if(is_numeric($id)){
            $tag = $goodsTagModel->where('id', $id)->where('status', 1)->find();
        }else{
            $tag = $goodsTagModel->where('name', $id)->where('status', 1)->find();
        }


        if (empty($tag)) {
            abort(404, '标签不存在!');
        }

        $this->assign('tag', $tag);

        return $this->fetch('/tag');
    }

}
