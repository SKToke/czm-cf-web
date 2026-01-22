<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Restore;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Category;
use \App\Models\Program;

class CategoryController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());
        $this->modifyGrid($grid);

        $grid->column('title', __('Category Title'));
        $grid->column('parent.title', __('Parent Category'));
        $grid->column('program.title', __('Associated Program'));

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->like('title', 'Title');

            $filter->scope('trashed', 'Deleted Categories')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if (request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'resource_mobilizer', 'board_secretary'])) {
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
        $show = new Show(Category::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Category Title'));
        $show->field('slug', __('Slug'));
        $show->field('program.title', __('Program'));
        $show->field('parent.title', __('Parent Category'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Last Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category);
        $this->modifyForm($form);

        $parentCategories = Category::whereNotNull('program_id')->pluck('title','id');
        $form->text('title', __('Category Title'))->required()->help('Maximum 15 characters')->rules('min:3|max:15');
        $form->select('parent_id', __('Parent Category'))->options($parentCategories);

        return $form;
    }
}
