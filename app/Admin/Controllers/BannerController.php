<?php

namespace App\Admin\Controllers;

use App\Models\Banner;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;


class BannerController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Banners';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Banner());
        $this->modifyGrid($grid);

        $grid->column('key');
        $grid->column('image', __('Image'))->image('', 100, 'auto');
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('key', 'Key');
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Banner::findOrFail($id));

        $show->field('key');

        $show->field('image', __('Image'))->image('', 100, 'auto');
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Banner());
        $this->modifyForm($form);

        $bannerNames = Banner::pluck('key', 'key')->toArray();
        $form->select('key', __('Name'))->options($bannerNames)->readonly();

        $form->image('image', __('image'))->removable()->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
