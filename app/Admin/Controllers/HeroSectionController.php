<?php

namespace App\Admin\Controllers;

use App\Models\HeroSection;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use OpenAdmin\Admin\Auth\Permission;
use OpenAdmin\Admin\Layout\Content;

class HeroSectionController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Slider';

    public function edit($id, Content $content): Content
    {
        Permission::check('heroSection.update');
        return parent::edit($id, $content);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new HeroSection());
        $this->modifyGrid($grid);

        $grid->column('photo', __('Slider Photo'))->image();
        $grid->column('description', __('Description'));
        $grid->column('created_at', __('Created At'))->display(function () {
            return Carbon::parse($this->created_at)->format('Y-m-d h:i:s A');
        });
        $grid->column('active', __('Active'))->bool();

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->like('description', 'Description');

            $filter->scope('trashed', 'Deleted Records')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                    $actions->disableDelete();
                }
            }
        });

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
        $show = new Show(HeroSection::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('description', __('Description'));
        $show->field('photo', __('Slider Photo'))->image();
        $show->field('link', __('Hyperlink'))->link();
        $show->field('active', __('Active'))->using([1 => 'Yes', 0 => 'No']);
        $show->field('created_at', __('Created at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Last Updated at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });

        $show->panel()
            ->tools(function ($tools) {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                    $tools->disableDelete();
                }
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
        $form = new Form(new HeroSection());
        $this->modifyForm($form);

        $form->image('photo', __('Slider Photo'))->required()->removable()
            ->rules('max:10240')
            ->rules('mimes:jpg,jpeg,png,webp')
            ->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
        $form->text('description', __('Description'))->help('Maximum 100 characters')->rules('min:0|max:100');
        $form->url('link', __('Hyperlink'));
        $form->switch('active', __('Active'))->default(1);

        $form->tools(function (Form\Tools $tools) {
            if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                $tools->disableDelete();
            }
        });

        return $form;
    }
}
