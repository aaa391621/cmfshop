<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\goods\controller;

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;
use app\goods\model\GoodsCategoryModel;
use think\Db;
use app\admin\model\ThemeModel;


class AdminCategoryController extends AdminBaseController
{
    /**
     * 商品分类列表
     * @adminMenu(
     *     'name'   => '分类管理',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('goods_admin_category_index_view');

        if (!empty($content)) {
            return $content;
        }

        $goodsCategoryModel = new GoodsCategoryModel();
        $keyword             = $this->request->param('keyword');

        if (empty($keyword)) {
            $categoryTree = $goodsCategoryModel->adminCategoryTableTree();
            $this->assign('category_tree', $categoryTree);
        } else {
            $categories = $goodsCategoryModel->where('name', 'like', "%{$keyword}%")
                ->where('delete_time', 0)->select();
            $this->assign('categories', $categories);
        }

        $this->assign('keyword', $keyword);

        return $this->fetch();
    }

    /**
     * 添加商品分类
     * @adminMenu(
     *     'name'   => '添加商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品分类',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $content = hook_one('goods_admin_category_add_view');

        if (!empty($content)) {
            return $content;
        }

        $parentId            = $this->request->param('parent', 0, 'intval');
        $goodsCategoryModel = new GoodsCategoryModel();
        $categoriesTree      = $goodsCategoryModel->adminCategoryTree($parentId);

        $themeModel        = new ThemeModel();
        $listThemeFiles    = $themeModel->getActionThemeFiles('goods/List/index');
        $articleThemeFiles = $themeModel->getActionThemeFiles('goods/Article/index');

        $this->assign('list_theme_files', $listThemeFiles);
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }

    /**
     * 添加商品分类提交
     * @adminMenu(
     *     'name'   => '添加商品分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品分类提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $goodsCategoryModel = new GoodsCategoryModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'GoodsCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $goodsCategoryModel->addCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('AdminCategory/index'));

    }

    /**
     * 编辑商品分类
     * @adminMenu(
     *     'name'   => '编辑商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品分类',
     *     'param'  => ''
     * )
     */
    public function edit()
    {

        $content = hook_one('goods_admin_category_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $category = GoodsCategoryModel::get($id)->toArray();

            $goodsCategoryModel = new GoodsCategoryModel();
            $categoriesTree      = $goodsCategoryModel->adminCategoryTree($category['parent_id'], $id);

            $themeModel        = new ThemeModel();
            $listThemeFiles    = $themeModel->getActionThemeFiles('goods/List/index');
            $articleThemeFiles = $themeModel->getActionThemeFiles('goods/Article/index');

            $routeModel = new RouteModel();
            $alias      = $routeModel->getUrl('goods/List/index', ['id' => $id]);

            $category['alias'] = $alias;
            $this->assign($category);
            $this->assign('list_theme_files', $listThemeFiles);
            $this->assign('article_theme_files', $articleThemeFiles);
            $this->assign('categories_tree', $categoriesTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑商品分类提交
     * @adminMenu(
     *     'name'   => '编辑商品分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品分类提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'GoodsCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $goodsCategoryModel = new GoodsCategoryModel();

        $result = $goodsCategoryModel->editCategory($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /**
     * 商品分类选择对话框
     * @adminMenu(
     *     'name'   => '商品分类选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类选择对话框',
     *     'param'  => ''
     * )
     */
    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $goodsCategoryModel = new GoodsCategoryModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
</tr>
tpl;

        $categoryTree = $goodsCategoryModel->adminCategoryTableTree($selectedIds, $tpl);

        $where      = ['delete_time' => 0];
        $categories = $goodsCategoryModel->where($where)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 商品分类排序
     * @adminMenu(
     *     'name'   => '商品分类排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('goods_category'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 商品分类显示隐藏
     * @adminMenu(
     *     'name'   => '商品分类显示隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类显示隐藏',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $data                = $this->request->param();
        $goodsCategoryModel = new GoodsCategoryModel();

        if (isset($data['ids']) && !empty($data["display"])) {
            $ids = $this->request->param('ids/a');
            $goodsCategoryModel->where(['id' => ['in', $ids]])->update(['status' => 1]);
            $this->success("更新成功！");
        }

        if (isset($data['ids']) && !empty($data["hide"])) {
            $ids = $this->request->param('ids/a');
            $goodsCategoryModel->where(['id' => ['in', $ids]])->update(['status' => 0]);
            $this->success("更新成功！");
        }

    }

    /**
     * 删除商品分类
     * @adminMenu(
     *     'name'   => '删除商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品分类',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $goodsCategoryModel = new GoodsCategoryModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findCategory = $goodsCategoryModel->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
//判断此分类有无子分类（不算被删除的子分类）
        $categoryChildrenCount = $goodsCategoryModel->where(['parent_id' => $id,'delete_time' => 0])->count();

        if ($categoryChildrenCount > 0) {
            $this->error('此分类有子类无法删除!');
        }

        $categoryPostCount = Db::name('goods_category_post')->where('category_id', $id)->count();

        if ($categoryPostCount > 0) {
            $this->error('此分类有商品无法删除!');
        }

        $data   = [
            'object_id'   => $findCategory['id'],
            'create_time' => time(),
            'table_name'  => 'goods_category',
            'name'        => $findCategory['name']
        ];
        $result = $goodsCategoryModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
