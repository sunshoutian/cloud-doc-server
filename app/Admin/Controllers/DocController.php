<?php

namespace App\Admin\Controllers;

use App\Models\Doc;

use App\Models\DocClass;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class DocController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('文档');
            $content->description('管理');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('编辑');
            $content->description('文档');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('添加');
            $content->description('文档');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Doc::class, function (Grid $grid) {

            //$grid->model()->orderBy("order","desc");
            $grid->id('ID')->sortable();
            $grid->order('排序')->sortable()->editable();
            $grid->column('cover','封面')->image(null,50,75);
            $grid->doc_class()->title("所属分类");
            $grid->column("title","文档名称")->editable();
            //$grid->column("desc","文档描述");
            $grid->column("source","文档来源")->editable();
            $grid->column("is_end","是否完结")->switch();
            $grid->column("is_hot","是否推荐")->switch();
            $grid->column("state","状态")->switch();
            $grid->created_at('创建时间');


            $grid->actions(function (Grid\Displayers\Actions $actions){
                $actions->disableEdit();
                $actions->disableDelete();

                //$actions->append("<a href='".admin_url("doc-menu?doc_id=".$actions->getKey())."' class='btn btn-xs'>文档目录</a>");
                $actions->append("<a target='_blank' href='".admin_url("book-edit?doc_id=".$actions->getKey())."' class='btn btn-xs'>文档编辑</a>");
                $actions->append("<a  href='".admin_url("doc/".$actions->getKey()."/edit")."' class='btn btn-xs'>编辑</a>");
            });

            $grid->filter(function (Grid\Filter $filter){
                $filter->disableIdFilter();
                $filter->like("title","名称");
                $filter->is("doc_class_id","分类")->select(DocClass::selectOptions());
                $filter->is("state","状态")->select([0=>"禁用",1=>"正常"]);
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Doc::class, function (Form $form) {

            $form->text("title","文档名称");
            $form->text("desc","文档描述");
            $form->select("doc_class_id","所属分类")->options(DocClass::selectOptions());
            $form->image("cover","文档封面")->help("封面规格 500x800");
            $form->image("h_cover","文档封面-横向")->help("封面规格 540x300");
            $form->hidden("user_id")->default("0");
            $form->text("source","文档来源");
            $form->switch("is_end","是否完结")->default(0);
            $form->switch("is_hot","是否推荐")->default(0);
            $form->switch("state","状态")->default(1);
            $form->number("order","排序")->default(1);
        });
    }
}
