<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:kane < chengjin005@163.com>
// +----------------------------------------------------------------------
namespace app\goods\controller;

use app\goods\model\GoodsTagModel;
use cmf\controller\AdminBaseController;
use think\Db;

/**
 * Class AdminTagController 标签管理控制器
 * @package app\goods\controller
 */
class AdminTagController extends AdminBaseController
{
    /**
     * 商品标签管理
     * @adminMenu(
     *     'name'   => '商品标签',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品标签',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('goods_admin_tag_index_view');

        if (!empty($content)) {
            return $content;
        }

        $goodsTagModel = new GoodsTagModel();
        $tags           = $goodsTagModel->paginate();

        $this->assign("arrStatus", $goodsTagModel::$STATUS);
        $this->assign("tags", $tags);
        $this->assign('page', $tags->render());
        return $this->fetch();
    }

    /**
     * 添加商品标签
     * @adminMenu(
     *     'name'   => '添加商品标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品标签',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $goodsTagModel = new GoodsTagModel();
        $this->assign("arrStatus", $goodsTagModel::$STATUS);
        return $this->fetch();
    }

    /**
     * 添加商品标签提交
     * @adminMenu(
     *     'name'   => '添加商品标签提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品标签提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {

        $arrData = $this->request->param();

        $goodsTagModel = new GoodsTagModel();
        $goodsTagModel->isUpdate(false)->allowField(true)->save($arrData);

        $this->success(lang("SAVE_SUCCESS"));

    }

    /**
     * 更新商品标签状态
     * @adminMenu(
     *     'name'   => '更新标签状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '更新标签状态',
     *     'param'  => ''
     * )
     */
    public function upStatus()
    {
        $intId     = $this->request->param("id");
        $intStatus = $this->request->param("status");
        $intStatus = $intStatus ? 1 : 0;
        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }

        $goodsTagModel = new GoodsTagModel();
        $goodsTagModel->isUpdate(true)->save(["status" => $intStatus], ["id" => $intId]);

        $this->success(lang("SAVE_SUCCESS"));

    }

    /**
     * 删除商品标签
     * @adminMenu(
     *     'name'   => '删除商品标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品标签',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }
        $goodsTagModel = new GoodsTagModel();

        $goodsTagModel->where(['id' => $intId])->delete();
        Db::name('goods_tag_post')->where('tag_id', $intId)->delete();
        $this->success(lang("DELETE_SUCCESS"));
    }
}
