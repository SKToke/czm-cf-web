<?php

namespace App\Admin\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAdmin\Admin\Form;

trait ModificationTrait
{
    public function modifyGrid($grid): void
    {
        $grid->batchActions(function ($batch) {
            $batch->disableEdit();
            $batch->disableDelete();
        });
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->actions(function ($actions) {
            if (in_array(SoftDeletes::class, class_uses($actions->row)) && $actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
        });
    }

    public function modifyForm($form): void
    {
        $form->footer(function ($footer) {
            $footer->disableReset();
            // $footer->disableSubmit();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });
        $form->tools(function (Form\Tools $tools) {
            $tools->add('<a href="javascript:history.back()" class="btn btn-sm btn-secondary" style="margin-left: 8px;"><i class="fa fa-trash"></i>Cancel</a>');
        });
    }
}
