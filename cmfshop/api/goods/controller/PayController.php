<?php
namespace api\goods\controller;
use think\Controller;
use think\Db;
use cmf\controller\RestBaseController;

class PayController extends RestBaseController
{
    //微信支付回调验证
    public function notify() {
        //获取微信返回的数据结果
        $postData = file_get_contents("php://input");
        //将结果转换成数组
        $getData = $this->xmlstr_to_array($postData);

        if($getData['total_fee'] && ($getData['result_code'] == 'SUCCESS')){
            $order_sn_submit = trim($getData['out_trade_no']);
            // 以下是生成log测试
            // file_put_contents("../log.txt", $order_sn_submit, FILE_APPEND);
            //更新数据表订单状态
            Db::name('order')->where('order_sn_submit', $order_sn_submit)->update(['order_status'=>3]);
            echo "success";
        }else{
            echo "error";
        }
    }

    public function xmlstr_to_array($xmlstr){
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }

}
