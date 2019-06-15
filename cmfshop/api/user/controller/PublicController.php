<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace api\user\controller;

use think\Db;
use think\Validate;
use cmf\controller\RestBaseController;

class PublicController extends RestBaseController
{
    // 用户注册
    public function register()
    {
        $validate = new Validate([
            'username'          => 'require',
            'password'          => 'require',
            'verification_code' => 'require'
        ]);

        $validate->message([
            'username.require'          => '请输入手机号,邮箱!',
            'password.require'          => '请输入您的密码!',
            'verification_code.require' => '请输入数字验证码!'
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $user = [];

        $findUserWhere = [];

        if (Validate::is($data['username'], 'email')) {
            $user['user_email']          = $data['username'];
            $findUserWhere['user_email'] = $data['username'];
        } else if (cmf_check_mobile($data['username'])) {
            $user['mobile']          = $data['username'];
            $findUserWhere['mobile'] = $data['username'];
        } else {
            $this->error("请输入正确的手机或者邮箱格式!");
        }

        $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
        if (!empty($errMsg)) {
            $this->error($errMsg);
        }

        $findUserCount = Db::name("user")->where($findUserWhere)->count();

        if ($findUserCount > 0) {
            $this->error("此账号已存在!");
        }

        $user['create_time'] = time();
        $user['user_status'] = 1;
        $user['user_type']   = 2;
        $user['user_pass']   = cmf_password($data['password']);

        $result = Db::name("user")->insert($user);


        if (empty($result)) {
            $this->error("注册失败,请重试!");
        }

        $this->success("注册并激活成功,请登录!");

    }

    // 用户登录 TODO 增加最后登录信息记录,如 ip
    public function login()
    {
        $validate = new Validate([
            'username' => 'require',
            'password' => 'require'
        ]);
        $validate->message([
            'username.require' => '请输入手机号,邮箱或用户名!',
            'password.require' => '请输入您的密码!'
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $findUserWhere = [];

        if (Validate::is($data['username'], 'email')) {
            $findUserWhere['user_email'] = $data['username'];
        } else if (cmf_check_mobile($data['username'])) {
            $findUserWhere['mobile'] = $data['username'];
        } else {
            $findUserWhere['user_login'] = $data['username'];
        }

        $findUser = Db::name("user")->where($findUserWhere)->find();

        if (empty($findUser)) {
            $this->error("用户不存在!");
        } else {

            switch ($findUser['user_status']) {
                case 0:
                    $this->error('您已被拉黑!');
                case 2:
                    $this->error('账户还没有验证成功!');
            }

            if (!cmf_compare_password($data['password'], $findUser['user_pass'])) {
                $this->error("密码不正确!");
            }
        }

        $allowedDeviceTypes = $this->allowedDeviceTypes;

        if (empty($data['device_type']) || !in_array($data['device_type'], $allowedDeviceTypes)) {
            $this->error("请求错误,未知设备!");
        }

        $userTokenQuery = Db::name("user_token")
            ->where('user_id', $findUser['id'])
            ->where('device_type', $data['device_type']);
        $findUserToken  = $userTokenQuery->find();
        $currentTime    = time();
        $expireTime     = $currentTime + 24 * 3600 * 180;
        $token          = md5(uniqid()) . md5(uniqid());
        if (empty($findUserToken)) {
            $result = $userTokenQuery->insert([
                'token'       => $token,
                'user_id'     => $findUser['id'],
                'expire_time' => $expireTime,
                'create_time' => $currentTime,
                'device_type' => $data['device_type']
            ]);
        } else {
            $result = $userTokenQuery
                ->where('user_id', $findUser['id'])
                ->where('device_type', $data['device_type'])
                ->update([
                    'token'       => $token,
                    'expire_time' => $expireTime,
                    'create_time' => $currentTime
                ]);
        }


        if (empty($result)) {
            $this->error("登录失败!");
        }

        $this->success("登录成功!", ['token' => $token, 'user' => $findUser]);
    }

    // 用户退出
    public function logout()
    {
        $userId = $this->getUserId();
        Db::name('user_token')->where([
            'token'       => $this->token,
            'user_id'     => $userId,
            'device_type' => $this->deviceType
        ])->update(['token' => '']);

        $this->success("退出成功!");
    }

    // 用户密码重置
    public function passwordReset()
    {
        $validate = new Validate([
            'username'          => 'require',
            'password'          => 'require',
            'verification_code' => 'require'
        ]);

        $validate->message([
            'username.require'          => '请输入手机号,邮箱!',
            'password.require'          => '请输入您的密码!',
            'verification_code.require' => '请输入数字验证码!'
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $userWhere = [];
        if (Validate::is($data['username'], 'email')) {
            $userWhere['user_email'] = $data['username'];
        } else if (cmf_check_mobile($data['username'])) {
            $userWhere['mobile'] = $data['username'];
        } else {
            $this->error("请输入正确的手机或者邮箱格式!");
        }

        $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
        if (!empty($errMsg)) {
            $this->error($errMsg);
        }

        $userPass = cmf_password($data['password']);
        Db::name("user")->where($userWhere)->update(['user_pass' => $userPass]);

        $this->success("密码重置成功,请使用新密码登录!");

    }

    //获取用户收货地址
    public function getAddress(){
        $userId = $this->getUserId();
        $address = Db::name('address')->where('uid', $userId)->select();
        if(!$address){
            $this->error("无收货地址!");
        }
        $this->success("收货地址获取成功",$address);
    }

    //添加收货地址
    public function addAddress(){
        $userId = $this->getUserId();
        //将当前用户所有收货地址取消默认状态
        Db::name('address')->where('uid', $userId)->setField('is_default', 0);
        //新增
        $data['uid'] = $userId;
        $data['consignee'] = input('consignee', '');
        $data['address'] = input('address', '');
        $data['mobile'] = input('mobile', '');
        $result = Db::name('address')->insert($data);
        if($result){
            $this->success("收货地址获取成功");
        }else{
            $this->error("收货地址添加失败!");
        }
    }

    //删除收货地址
    public function deleteAddress(){
        $userId = $this->getUserId();
        $id = input('id', 0); //地址ID
        Db::name('address')->where('id', $id)->delete();
        //判断当前用户如果没有默认收货地址，则将最新一条地址设为默认
        $count = Db::name('address')->where('uid',$userId)->where('is_default', 1)->count();
        if(!$count){
            //设置最新一条地址为默认
            $count = Db::name('address')->where('uid',$userId)->order('id desc')->limit(1)->setField('is_default', 1);
        }
        $this->success("地址删除成功");
    }

    //设默认地址
    public function setDefault(){
        $userId = $this->getUserId();
        $id = input('id', 0); //地址ID
        //除当前地址外都设为非默认
        Db::name('address')->where('uid',$userId)->where('id', 'neq', $id)->setField('is_default', 0);
        //再将当前地址设为默认
        Db::name('address')->where('id', $id)->setField('is_default', 1);
        $this->success("默认地址设置成功");
    }

    //获取某个用户具体某个收货地址
    public function getAddressById(){
        $id = input('id', 0); //地址ID
        $address = Db::name('address')->where('id', $id)->find();
        if(!$address){
            $this->error("无收货地址!");
        }
        $this->success("收货地址获取成功",$address);
    }

    // 编辑收货地址
    public function editAddress() {
        $id = input('id', 0);
        $data['consignee'] = input('consignee', '');
        $data['address'] = input('address', '');
        $data['mobile'] = input('mobile', '');
        $result = Db::name('address')->where('id', $id)->update($data);
        if($result){
            $this->success("收货地址编辑成功");
        }else{
            $this->error("收货地址编辑失败!");
        }
    }

    //判断有无默认收货地址
    public function haveAddress(){
        $userId = $this->getUserId();
        $count = Db::name('address')->where('uid', $userId)->where('is_default', 1)->count();
        if($count){
            $this->success("有默认收货地址");
        }else{
            $this->error("无默认收货地址无默认收货地址!");
        }
    }

    //获取某个订单信息
    public function getOrderDetail(){
        $userId = $this->getUserId();
        $oid = input('oid', 0);
        $order = Db::name('order')->where('id',$oid)->where('uid',$userId)->find();
        if($order){
            //修改订单状态
            switch ($order['order_status']) {
                case 1:
                    $order['order_status'] = '待付款';
                    break;
                case 2:
                    $order['order_status'] = '已完成';
                    break;
                case 3:
                    $order['order_status'] = '待发货';
                    break;
                case 4:
                    $order['order_status'] = '待收货';
                    break;
                case 5:
                    $order['order_status'] = '已取消';
                    break;

                default:
                    # code...
                    break;
            }
            //查询该订单下所有商品信息
            $goods['order'] = Db::name('order_goods')->where('oid', $oid)->select();
            //根据GID获取商品图片标题等信息
            foreach ($goods['order'] as $key => $value) {
                $goods['detail'][$key] = array_merge(Db::name('goods_post')->where('id',$goods['order'][$key]['gid'])->field('post_title,more')->find(), $value);
                $more = json_decode($goods['detail'][$key]['more'], true);
                if (!empty($more['thumbnail'])) {
                    $more['thumbnail'] = cmf_get_image_url($more['thumbnail']);
                }
                $goods['detail'][$key]['thumbnail'] = $more['thumbnail'];
            }
            $order['goods'] = $goods['detail'];
            $this->success("订单详情获取成功",$order);
        }else{
            $this->error("订单不存在!");
        }
    }

    // 订单列表
    public function getOrderList(){
        $userId = $this->getUserId();
        $status = input('status', 'ALL');
        $page = input('page', 1);
        $map = [];
        switch ($status) {
            case 'WAITPAY':
                $map['order_status'] = 1;
                break;
            case 'WAITSEND':
                $map['order_status'] = 2;
                break;
            case 'WAITRECEIVE':
                $map['order_status'] = 4;
                break;
            case 'FINISH':
                $map['order_status'] = 2;
                break;
            default:
                break;
        }
        $map['uid'] = $userId;
        $config = ['page'=>$page, 'list_rows'=>5];
        $order['order'] = Db::name('order')->where($map)->order('id desc')->paginate(null,false,$config);
        foreach ($order['order'] as $key => $value) {
            //获取该订单中商品金额最大的一件商品ID
            $gid = Db::name('order_goods')->where('oid',$value['id'])->order('price desc')->limit(1)->value('gid');
            $order['goods'][$key] = array_merge(Db::name('goods_post')->where('id',$gid)->field('post_title,more')->find(), $value);
            $more = json_decode($order['goods'][$key]['more'], true);
            if (!empty($more['thumbnail'])) {
                $more['thumbnail'] = cmf_get_image_url($more['thumbnail']);
            }
            $order['goods'][$key]['thumbnail'] = $more['thumbnail'];

        }
        $this->success("订单列表加载成功",$order);
    }

    //取消订单
    public function cancelOrder(){
        $userId = $this->getUserId();
        $oid = input('oid', 0);
        Db::name('order')->where('id',$oid)->where('uid',$userId)->setField('order_status', 5);
        $this->success("订单取消成功");
    }

    //确认收货
    public function confirmOrder(){
        $userId = $this->getUserId();
        $oid = input('oid', 0);
        Db::name('order')->where('id',$oid)->where('uid',$userId)->setField('order_status', 2);
        $this->success("确认收货成功");
    }
}
