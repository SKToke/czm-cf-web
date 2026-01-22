<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Committee;
use Carbon\Carbon;


class CommitteeController extends AdminController
{
    use ModificationTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Committee';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Committee());
        $this->modifyGrid($grid);

        $grid->model()->orderBy('position', 'asc')->orderBy('updated_at', 'asc');

        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'))->width(500);
        $grid->column('position', __('Commiteee Position'));

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('name', 'Name');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('position', 'position');
            });

            $filter->scope('trashed', 'Deleted Committees')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'digital_marketer', 'board_secretary'])) {
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
        $show = new Show(Committee::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('position', __('Commiteee Position'));
        $show->field('created_at')->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Last Updated at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
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
        $form = new Form(new Committee());
        $this->modifyForm($form);

        $form->text('name', __('Name'))->required()->help('Maximum 70 characters')->rules('min:3|max:70');
        $form->textarea('description', __('Description'))
            ->rules([new \App\Rules\NoSpacesInField(false, 1000)])
            ->help('Maximum 1000 words');
        $form->number('position', __('Position'))->rules('required|numeric|min:0|max:999');

        return $form;
    }
}
