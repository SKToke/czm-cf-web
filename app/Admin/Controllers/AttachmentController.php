<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Attachment;

class AttachmentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Attachment';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Attachment());

        $grid->column('id', __('Id'));
        $grid->column('parentable_type', __('Parentable type'));
        $grid->column('parentable_id', __('Parentable id'));
        $grid->column('title', __('Title'));
        $grid->column('download_count', __('Download count'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Attachment::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('parentable_type', __('Parentable type'));
        $show->field('parentable_id', __('Parentable id'));
        $show->field('title', __('Title'));
        $show->field('download_count', __('Download count'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Attachment());

        $form->text('parentable_type', __('Parentable type'));
        $form->number('parentable_id', __('Parentable id'));
        $form->text('title', __('Title'));
        $form->file('file', __('Upload File'))
            ->rules('required|max:51200')
            ->help('Required. Max file size: 50MB.');
        $form->number('download_count', __('Download count'));

        return $form;
    }
}
