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
namespace app\goods\service;

use app\goods\model\GoodsPostModel;

class PostService
{

    public function adminArticleList($filter)
    {
        return $this->adminPostList($filter);
    }
    public function adminOrderList($filter)
    {
        return $this->OrderList($filter);
    }

    public function adminAddressList($filter)
    {
        return $this->AddressList($filter);
    }
    public function adminCollectList($filter)
    {
        return $this->CollectList($filter);
    }
    public function adminCartList($filter)
    {
        return $this->CartList($filter);
    }
    public function adminPageList($filter)
    {
        return $this->adminPostList($filter, true);
    }

    public function adminPostList($filter, $isPage = false)
    {

        $where = [
            'a.create_time' => ['>=', 0],
            'a.delete_time' => 0
        ];

        $join = [
            ['__USER__ u', 'a.user_id = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            $where['b.category_id'] = ['eq', $category];
            array_push($join, [
                '__GOODS_CATEGORY_POST__ b', 'a.id = b.post_id'
            ]);
            $field = 'a.*,b.id AS post_category_id,b.list_order,b.category_id,u.user_login,u.user_nickname,u.user_email';
        }

        $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
        $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.published_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['a.published_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['a.published_time'] = ['<= time', $endTime];
            }
        }

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.post_title'] = ['like', "%$keyword%"];
        }

        if ($isPage) {
            $where['a.post_type'] = 2;
        } else {
            $where['a.post_type'] = 1;
        }

        $goodsPostModel = new GoodsPostModel();
        $articles        = $goodsPostModel->alias('a')->field($field)
            ->join($join)
            ->where($where)
            ->order('update_time', 'DESC')
            ->paginate(10);

        return $articles;

    }

    public function OrderList($filter)
    {

        $where = [
            'a.submit_time' => ['>=', 0],
        ];

        $join = [
            ['__USER__ u', 'a.uid = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';


        $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
        $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.submit_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['a.submit_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['a.submit_time'] = ['<= time', $endTime];
            }
        }
        $status = empty($filter['status']) ? '' : $filter['status'];
        if (!empty($status)) {
            $where['a.order_status'] = $status;
        }

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.order_sn'] = ['like', "%$keyword%"];
        }


        $goodsPostModel = new \app\goods\model\OrderModel();
        $order        = $goodsPostModel->alias('a')->field($field)
            ->join($join)
            ->where($where)
            ->order('id', 'DESC')
            ->paginate(10);

        return $order;

    }

    public function AddressList($filter)
    {

        $join = [
            ['__USER__ u', 'a.uid = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.address'] = ['like', "%$keyword%"];
            $goodsPostModel = new \app\goods\model\AddressModel();
            $order        = $goodsPostModel->alias('a')->field($field)
                ->join($join)
                ->where($where)
                ->order('uid', 'DESC')
                ->paginate(10);
            return $order;
        }else{
            $goodsPostModel = new \app\goods\model\AddressModel();
            $order        = $goodsPostModel->alias('a')->field($field)
                ->join($join)
                ->order('id', 'DESC')
                ->paginate(10);
            return $order;
        }

    }
    public function CollectList($filter)
    {

        $join = [
            ['__USER__ u', 'a.user_id = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.title'] = ['like', "%$keyword%"];
            $goodsPostModel = new \app\user\model\UserFavoriteModel();
            $order        = $goodsPostModel->alias('a')->field($field)
                ->join($join)
                ->where($where)
                ->order('user_id', 'DESC')
                ->paginate(10);
            return $order;
        }else{
            $goodsPostModel = new \app\user\model\UserFavoriteModel();
            $order        = $goodsPostModel->alias('a')->field($field)
                ->join($join)
                ->order('user_id', 'DESC')
                ->paginate(10);
            return $order;
        }

    }
    public function CartList($filter)
    {

        $join = [
            ['__USER__ u', 'a.uid = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.name'] = ['like', "%$keyword%"];
            $goodsPostModel = new \app\goods\model\CartModel();
            $order        = $goodsPostModel->alias('a')->field($field)
                ->join($join)
                ->where($where)
                ->order('uid', 'DESC')
                ->paginate(10);
            return $order;
        }else{
            $goodsPostModel = new \app\goods\model\CartModel();
            $order        = $goodsPostModel->alias('a')->field($field)
                ->join($join)
                ->order('id', 'DESC')
                ->paginate(10);
            return $order;
        }

    }

    public function publishedArticle($postId, $categoryId = 0)
    {
        $goodsPostModel = new GoodsPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'      => 1,
                'post.published_time' => [['< time', time()], ['> time', 0]],
                'post.post_status'    => 1,
                'post.delete_time'    => 0,
                'post.id'             => $postId
            ];

            $article = $goodsPostModel->alias('post')->field('post.*')
                ->where($where)
                ->find();
        } else {
            $where = [
                'post.post_type'       => 1,
                'post.published_time'  => [['< time', time()], ['> time', 0]],
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => $postId
            ];

            $join    = [
                ['__GOODS_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $article = $goodsPostModel->alias('post')->field('post.*')
                ->join($join)
                ->where($where)
                ->find();
        }


        return $article;
    }

    //上一篇商品
    public function publishedPrevArticle($postId, $categoryId = 0)
    {
        $goodsPostModel = new GoodsPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'      => 1,
                'post.published_time' => [['< time', time()], ['> time', 0]],
                'post.post_status'    => 1,
                'post.delete_time'    => 0,
                'post.id '            => ['<', $postId]
            ];

            $article = $goodsPostModel->alias('post')->field('post.*')
                ->where($where)
                ->order('id', 'DESC')
                ->find();

        } else {
            $where = [
                'post.post_type'       => 1,
                'post.published_time'  => [['< time', time()], ['> time', 0]],
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => ['<', $postId]
            ];

            $join    = [
                ['__GOODS_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $article = $goodsPostModel->alias('post')->field('post.*')
                ->join($join)
                ->where($where)
                ->order('id', 'DESC')
                ->find();
        }


        return $article;
    }

    //下一篇商品
    public function publishedNextArticle($postId, $categoryId = 0)
    {
        $goodsPostModel = new GoodsPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'      => 1,
                'post.published_time' => [['< time', time()], ['> time', 0]],
                'post.post_status'    => 1,
                'post.delete_time'    => 0,
                'post.id'             => ['>', $postId]
            ];

            $article = $goodsPostModel->alias('post')->field('post.*')
                ->where($where)
                ->order('id', 'ASC')
                ->find();
        } else {
            $where = [
                'post.post_type'       => 1,
                'post.published_time'  => [['< time', time()], ['> time', 0]],
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => ['>', $postId]
            ];

            $join    = [
                ['__GOODS_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $article = $goodsPostModel->alias('post')->field('post.*')
                ->join($join)
                ->where($where)
                ->order('id', 'ASC')
                ->find();
        }


        return $article;
    }

    public function publishedPage($pageId)
    {

        $where = [
            'post_type'      => 2,
            'published_time' => [['< time', time()], ['> time', 0]],
            'post_status'    => 1,
            'delete_time'    => 0,
            'id'             => $pageId
        ];

        $goodsPostModel = new GoodsPostModel();
        $page            = $goodsPostModel
            ->where($where)
            ->find();

        return $page;
    }

}