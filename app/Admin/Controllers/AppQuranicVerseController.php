<?php

namespace App\Admin\Controllers;

use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\AppQuranicVerse;

class AppQuranicVerseController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AppQuranicVerse';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AppQuranicVerse());
        $this->modifyGrid($grid);

        $grid->column('quranic_verse_text', __('Quranic Verse Text'))->width(800)->display(function () {
            $text = $this->quranic_verse_text;
            if (mb_strlen($text) > 90) {
                $text = mb_substr($text, 0, 90) . '...';
            }
            return $text;
        });

        $grid->disableFilter();
        $totalRecords = AppQuranicVerse::all()->count();
        if ($totalRecords && $totalRecords >= 1) {
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
        $show = new Show(AppQuranicVerse::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('quranic_verse_text', __('Quranic Verse Text'))->unescape();
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
        $form = new Form(new AppQuranicVerse());
        $this->modifyForm($form);

        $form->ckeditor('quranic_verse_text', __('Quranic Verse Text'))->required()
            ->rules([new \App\Rules\NoSpacesInField(true, 200)])
            ->help('Maximum 200 words');

        return $form;
    }
}
