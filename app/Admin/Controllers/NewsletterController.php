<?php

namespace App\Admin\Controllers;

use App\Models\NewsletterSubscription;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class NewsletterController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Newsletter Subscriptions';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new NewsletterSubscription());
        $this->modifyGrid($grid);

        $grid->model()->orderBy('id', 'desc');
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));
        $grid->column('created_at', __('Subscribed at'))->display(function () {
            return Carbon::parse($this->created_at)->format('d-M-Y (h:i a)');
        });


        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('name', 'Name');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('email', 'Email');
                $filter->like('phone', 'Phone');
            });
            $filter->scope('trashed', 'Deleted Records')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            if ($actions->row->trashed()) {
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                    $actions->disableDelete();
                }
            }
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
        $show = new Show(NewsletterSubscription::findOrFail($id));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('phone', __('Phone'));
        $show->field('created_at', __('Subscribed at'))->as(function () {
            return Carbon::parse($this->created_at)->format('d-M-Y (h:i a)');
        });

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                    $tools->disableDelete();
                }
            });;

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */

    protected function form()
    {
        $form = new Form(new NewsletterSubscription());

        return $form;
    }
}
