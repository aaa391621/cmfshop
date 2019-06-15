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

use cmf\controller\AdminBaseController;
use app\goods\service\PostService;
use think\Db;

class OrderController extends AdminBaseController
{
    /**
     * 订单列表
     * @adminMenu(
     *     'name'   => '订单管理',
     *     'parent' => 'goods/Order/index',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => 'money',
     *     'remark' => '订单列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {

        $param = $this->request->param();

        $postService = new PostService();
        $data        = $postService->adminOrderList($param);

        $data->appends($param);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : '');
        $this->assign('order', $data->items());

        $this->assign('page', $data->render());

        //dump($data);
        return $this->fetch();
    }

    //查看订单商品
    public function orderGoods($id = 0){
        $lists['order_goods']= Db::name('order_goods')->where('oid', $id)->select();
        foreach ($lists['order_goods'] as $key => $value) {
            $lists['goods'][$key] = Db::name('goods_post')->where('id',$value['gid'])->find();
            $lists['goods'][$key]['num'] = $lists['order_goods'][$key]['num'];
        }
        //dump($lists['goods']);
        $this->assign('lists', $lists['goods']);
        return view();
    }

    //修改订单状态
    public function changeOrderStatus(){
        if(request()->isPost()) {
            $id = input('id', 0);
            $order_status = db('order')->where('id', $id)->value('order_status');
            $num = '5';
            $str = '';
            switch ($order_status) {
                case 1:
                    $num = 3;
                    $str = '待发货';
                    break;
                case 3:
                    $num = 4;
                    $str = '待收货';
                    break;
                case 4:
                    $num = 2;
                    $str = '已完成';
                    break;
                default:
                    break;
            }
            if($num!=1 && $num!=5){
                //设置订单状态值
                $result = db('order')->where('id',$id)->setField('order_status', $num);
                if($result!==false){
                    $data['status'] = 200;
                    $data['order_status'] = $str;
                    $data['msg'] = '设置成功';
                }else{
                    $data['status'] = 202;
                    $data['msg'] = '设置失败';
                }
            }else{
                $data['status'] = 202;
                $data['msg'] = '无需设置';
            }
            return json($data);
        }
    }


}
