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
use app\goods\model\GoodsPostModel;
use app\goods\service\PostService;
use app\goods\model\GoodsCategoryModel;
use think\Db;
use app\admin\model\ThemeModel;

class AdminArticleController extends AdminBaseController
{
    /**
     * 商品列表
     * @adminMenu(
     *     'name'   => '商品管理',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('goods_admin_article_index_view');

        if (!empty($content)) {
            return $content;
        }

        $param = $this->request->param();

        $categoryId = $this->request->param('category', 0, 'intval');

        $postService = new PostService();
        $data        = $postService->adminArticleList($param);

        $data->appends($param);

        $goodsCategoryModel = new GoodsCategoryModel();
        $categoryTree        = $goodsCategoryModel->adminCategoryTree($categoryId);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('articles', $data->items());
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());


        return $this->fetch();
    }

    /**
     * 添加商品
     * @adminMenu(
     *     'name'   => '添加商品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',zz
     *     'remark' => '添加商品',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $content = hook_one('goods_admin_article_add_view');

        if (!empty($content)) {
            return $content;
        }

        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('goods/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        return $this->fetch();
    }

    /**
     * 添加商品提交
     * @adminMenu(
     *     'name'   => '添加商品提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['post']['post_status'] = 1;
            $data['post']['is_top']      = 0;
            $data['post']['recommended'] = 0;

            $post = $data['post'];

            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $goodsPostModel = new GoodsPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }


            $goodsPostModel->adminAddArticle($data['post'], $data['post']['categories']);

            $data['post']['id'] = $goodsPostModel->id;
            $hookParam          = [
                'is_add'  => true,
                'article' => $data['post']
            ];
            hook('goods_admin_after_save_article', $hookParam);


            $this->success('添加成功!', url('AdminArticle/edit', ['id' => $goodsPostModel->id]));
        }

    }

    /**
     * 编辑商品
     * @adminMenu(
     *     'name'   => '编辑商品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $content = hook_one('goods_admin_article_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');

        $goodsPostModel = new GoodsPostModel();
        $post            = $goodsPostModel->where('id', $id)->find();
        $postCategories  = $post->categories()->alias('a')->column('a.name', 'a.id');
        $postCategoryIds = implode(',', array_keys($postCategories));

        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('goods/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('post', $post);
        $this->assign('post_categories', $postCategories);
        $this->assign('post_category_ids', $postCategoryIds);

        return $this->fetch();
    }

    /**
     * 编辑商品提交
     * @adminMenu(
     *     'name'   => '编辑商品提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data = $this->request->param();

            //需要抹除发布、置顶、推荐的修改。
            unset($data['post']['post_status']);
            unset($data['post']['is_top']);
            unset($data['post']['recommended']);

            $post   = $data['post'];
            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $goodsPostModel = new GoodsPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }

            $goodsPostModel->adminEditArticle($data['post'], $data['post']['categories']);

            $hookParam = [
                'is_add'  => false,
                'article' => $data['post']
            ];
            hook('goods_admin_after_save_article', $hookParam);

            $this->success('保存成功!');

        }
    }

    /**
     * 商品删除
     * @adminMenu(
     *     'name'   => '商品删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $param           = $this->request->param();
        $goodsPostModel = new GoodsPostModel();

        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result       = $goodsPostModel->where(['id' => $id])->find();
            $data         = [
                'object_id'   => $result['id'],
                'create_time' => time(),
                'table_name'  => 'goods_post',
                'name'        => $result['post_title'],
                'user_id'     => cmf_get_current_admin_id()
            ];
            $resultGoods = $goodsPostModel
                ->where(['id' => $id])
                ->update(['delete_time' => time()]);
            if ($resultGoods) {
                Db::name('goods_category_post')->where(['post_id' => $id])->update(['status' => 0]);
                Db::name('goods_tag_post')->where(['post_id' => $id])->update(['status' => 0]);

                Db::name('recycleBin')->insert($data);
            }
            $this->success("删除成功！", '');

        }

        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
            $recycle = $goodsPostModel->where(['id' => ['in', $ids]])->select();
            $result  = $goodsPostModel->where(['id' => ['in', $ids]])->update(['delete_time' => time()]);
            if ($result) {
                Db::name('goods_category_post')->where(['post_id' => ['in', $ids]])->update(['status' => 0]);
                Db::name('goods_tag_post')->where(['post_id' => ['in', $ids]])->update(['status' => 0]);
                foreach ($recycle as $value) {
                    $data = [
                        'object_id'   => $value['id'],
                        'create_time' => time(),
                        'table_name'  => 'goods_post',
                        'name'        => $value['post_title'],
                        'user_id'     => cmf_get_current_admin_id()
                    ];
                    Db::name('recycleBin')->insert($data);
                }
                $this->success("删除成功！", '');
            }
        }
    }

    /**
     * 商品发布
     * @adminMenu(
     *     'name'   => '商品发布',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品发布',
     *     'param'  => ''
     * )
     */
    public function publish()
    {
        $param           = $this->request->param();
        $goodsPostModel = new GoodsPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $goodsPostModel->where(['id' => ['in', $ids]])->update(['post_status' => 1, 'published_time' => time()]);

            $this->success("发布成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $goodsPostModel->where(['id' => ['in', $ids]])->update(['post_status' => 0]);

            $this->success("取消发布成功！", '');
        }

    }

    /**
     * 商品置顶
     * @adminMenu(
     *     'name'   => '商品置顶',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品置顶',
     *     'param'  => ''
     * )
     */
    public function top()
    {
        $param           = $this->request->param();
        $goodsPostModel = new GoodsPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $goodsPostModel->where(['id' => ['in', $ids]])->update(['is_top' => 1]);

            $this->success("置顶成功！", '');

        }

        if (isset($_POST['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $goodsPostModel->where(['id' => ['in', $ids]])->update(['is_top' => 0]);

            $this->success("取消置顶成功！", '');
        }
    }

    /**
     * 商品推荐
     * @adminMenu(
     *     'name'   => '商品推荐',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品推荐',
     *     'param'  => ''
     * )
     */
    public function recommend()
    {
        $param           = $this->request->param();
        $goodsPostModel = new GoodsPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $goodsPostModel->where(['id' => ['in', $ids]])->update(['recommended' => 1]);

            $this->success("推荐成功！", '');

        }
        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $goodsPostModel->where(['id' => ['in', $ids]])->update(['recommended' => 0]);

            $this->success("取消推荐成功！", '');

        }
    }

    /**
     * 商品排序
     * @adminMenu(
     *     'name'   => '商品排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('goods_category_post'));
        $this->success("排序更新成功！", '');
    }

    public function move()
    {

    }

    public function copy()
    {

    }


}
