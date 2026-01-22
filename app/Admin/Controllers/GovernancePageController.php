<?php

namespace App\Admin\Controllers;

use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\GovernancePage;

class GovernancePageController extends AdminController
{
    use ModificationTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'GovernancePage';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new GovernancePage());
        $this->modifyGrid($grid);

        $grid->column('title', __('Title'));
        $grid->column('description')->width(600)->display(function () {
            $msg = $this->description;
            if (mb_strlen($msg) > 70) {
                $msg = mb_substr($msg, 0, 70) . '...';
            }
            return $msg;
        });
        $grid->column('updated_at', __('Updated at'))->display(function () {
            return Carbon::parse($this->updated_at)->format('Y-m-d h:i:s A');
        });

        $grid->filter(function($filter) {
            $filter->disableIdFilter();
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

        $totalRecords = GovernancePage::all()->count();
        if ($totalRecords && $totalRecords > 0) {
            $grid->disableCreateButton();
        }
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
        $show = new Show(GovernancePage::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'))->unescape();
        $show->field('created_at', __('Created at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Updated at'))->as(function ($time) {
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
        $form = new Form(new GovernancePage());
        $this->modifyForm($form);

        $form->text('title', __('Title'))->required();
        $form->ckeditor('description')->required();

        $form->tools(function (Form\Tools $tools) {
            if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                $tools->disableDelete();
            }
        });

        return $form;
    }
}
