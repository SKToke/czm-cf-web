<?php

namespace App\Admin\Controllers;

use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\CzmSupportCounter;

class CzmSupportCounterController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CzmSupportCounter';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CzmSupportCounter());
        $this->modifyGrid($grid);

        $grid->column('counter_label', __('Counter label'));
        $grid->column('counter_value', __('Counter value'));

        $grid->disableFilter();
        $totalRecords = CzmSupportCounter::all()->count();
        if ($totalRecords && $totalRecords >= 3) {
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
        $show = new Show(CzmSupportCounter::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('counter_label', __('Counter label'));
        $show->field('counter_value', __('Counter value'));
        $show->field('created_at', __('Created at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Updated at'))->as(function ($time) {
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
        $form = new Form(new CzmSupportCounter());
        $this->modifyForm($form);

        $form->text('counter_label', __('Counter label'))->required();
        $form->decimal('counter_value', __('Counter value'))->rules('required|numeric|min:0|max:99999999');

        return $form;
    }
}
