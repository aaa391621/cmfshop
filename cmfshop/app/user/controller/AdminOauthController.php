<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\goods\service\PostService;

class AdminOauthController extends AdminBaseController
{

    /**
     * 后台第三方用户列表
     * @adminMenu(
     *     'name'   => '第三方用户',
     *     'parent' => 'user/AdminIndex/default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '第三方用户',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('user_admin_oauth_index_view');

        if (!empty($content)) {
            return $content;
        }

        $oauthUserQuery = Db::name('third_party_user');

        $lists = $oauthUserQuery->field('a.*,u.user_nickname,u.sex,u.avatar')->alias('a')->join('__USER__ u', 'a.user_id = u.id')->where("status", 1)->order("create_time DESC")->paginate(10);
        // 获取分页显示
        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    /**
     * 后台删除第三方用户绑定
     * @adminMenu(
     *     'name'   => '删除第三方用户绑定',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除第三方用户绑定',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = input('param.id', 0, 'intval');
        if (empty($id)) {
            $this->error('非法数据！');
        }
        Db::name("third_party_user")->where("id", $id)->delete();
        $this->success("删除成功！", "admin_oauth/index");
    }


    // 收藏管理
    public function address()
    {

        $param = $this->request->param();

        $postService = new PostService();
        $data        = $postService->adminAddressList($param);

        $data->appends($param);

        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');

        $this->assign('address', $data->items());

        $this->assign('page', $data->render());

        //dump($data);
        return $this->fetch();
    }

    // 地址管理
    public function collect()
    {

        $param = $this->request->param();

        $postService = new PostService();
        $data        = $postService->adminCollectList($param);

        $data->appends($param);

        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');

        $this->assign('collect', $data->items());

        $this->assign('page', $data->render());

        //dump($data);
        return $this->fetch();
    }

    // 地址管理
    public function cart()
    {

        $param = $this->request->param();

        $postService = new PostService();
        $data        = $postService->adminCartList($param);

        $data->appends($param);

        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');

        $this->assign('cart', $data->items());

        $this->assign('page', $data->render());

        //dump($data);
        return $this->fetch();
    }


}