<?php

namespace App\Admin\Controllers;

use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Admin\Actions\Restore;
use \App\Models\Program;
use \App\Models\Category;

class ProgramController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Program';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Program());
        $this->modifyGrid($grid);

        $grid->column('Title - Subtitle')->display(function () {
            $msg = $this->title . ($this->subtitle ? ' - ' . $this->subtitle : '');
            if (mb_strlen($msg) > 60) {
                $msg = mb_substr($msg, 0, 60) . '...';
            }
            return $msg;
        });

        $grid->column('slug')->display(function () {
            $msg = $this->slug;
            if (mb_strlen($msg) > 60) {
                $msg = mb_substr($msg, 0, 60) . '...';
            }
            return $msg;
        });
        $grid->column('slogan')->display(function () {
            $msg = $this->slogan;
            if (mb_strlen($msg) > 60) {
                $msg = mb_substr($msg, 0, 60) . '...';
            }
            return $msg;
        });
        $grid->column('program_logo')->image('', 100, 'auto');
        $grid->column('default', __('Show in Landing'))->bool();
        $grid->column('active', __('Active'))->bool();

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');
                $filter->equal('active', __('Active'))->radio([
                    0    => 'False',
                    1    => 'True',
                ]);
            });

            $filter->column(1/2, function ($filter) {
                $filter->equal('default', __('Show in Landing Page'))->radio([
                    0    => 'False',
                    1    => 'True',
                ]);
            });

            $filter->scope('trashed', 'Deleted Programs')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if (request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
            if(count($actions->row->campaigns) > 0) {
                $actions->disableDelete();
            }
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer'])) {
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
        $show = new Show(Program::findOrFail($id));

        $show->field('id');
        $show->field('title');
        $show->field('slug');
        $show->field('active', __('Active'))->using([0 => 'False', 1 => 'True']);
        $show->field('default', __('Show in Landing Page'))->using([0 => 'False', 1 => 'True']);
        $show->field('objective')->unescape();
        $show->field('activities_description', __('Activity'))->unescape();
        $show->field('strategy')->unescape();
        $show->field('subtitle');
        $show->field('slogan');
        $show->field('program_logo', __('Program Logo'))->image();
        $show->field('photos', __('Program Photos'))->image();
        $show->field('counter_1_label', __('Counter 1 label'));
        $show->field('counter_1_value', __('Counter 1 value'));
        $show->field('counter_2_label', __('Counter 2 label'));
        $show->field('counter_2_value', __('Counter 2 value'));
        $show->field('counter_3_label', __('Counter 3 label'));
        $show->field('counter_3_value', __('Counter 3 value'));
        $show->field('counter_4_label', __('Counter 4 label'));
        $show->field('counter_4_value', __('Counter 4 value'));
        $show->field('created_at', __('Created at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Last Updated at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('links', __('Program related links'))->unescape()->as(function ($time) {
            if (($this->links() && $this->links()->count() > 0)) {
                $linkSec = '<ol>';
                foreach ($this->links()->get() as $link) {
                    $linkSec = $linkSec . '<li>';
                    if ($link->title) {
                        $linkSec = $linkSec . '<span>' . $link->title . ': </span>';
                    }
                    if ($link->label) {
                        $linkSec = $linkSec . '<span>' . '<a href="' . $link->link . '">' . $link->label . '</a>' . '</span>';
                    } else {
                        $linkSec = $linkSec . '<span>' . '<a href="' . $link->link . '">' . $link->link . '</a>' . '</span>';
                    }
                    $linkSec = $linkSec . '</li>';
                }
                $linkSec = $linkSec . '</ol>';
                return $linkSec;
            }
        });

        $program = Program::findOrFail($id);
        if($program && count($program->campaigns) > 0) {
            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableDelete();
                });
        }

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Program());
        $this->modifyForm($form);

        $form->text('title', __('Title'))->required()->help('Maximum 15 characters')->rules('min:3|max:15');
        $form->text('subtitle')->help('Maximum 240 characters')->rules('max:240');
        $form->text('slogan')->help('Maximum 240 characters')->rules('max:240');
        $form->switch('default', __('Show in Landing Page'));
        $form->switch('active', __('Active'));
        $form->ckeditor('objective')
            ->rules([new \App\Rules\NoSpacesInField(false, 100)])
            ->help('Maximum 100 words');
        $form->ckeditor('strategy')
            ->rules([new \App\Rules\NoSpacesInField(false, 100)])
            ->help('Maximum 100 words');
        $form->ckeditor('activities_description', __('Activity'))
            ->rules([new \App\Rules\NoSpacesInField(false, 200)])
            ->help('Maximum 200 words');
        $form->image('program_logo', __('Program Logo'))->removable()->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
        $form->multipleImage('photos', __('Program Photos'))
            ->addElementClass('custom-multiple-images')
            ->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
        $form->text('counter_1_label', __('Counter 1 label'));
        $form->decimal('counter_1_value', __('Counter 1 value'))->rules('nullable|numeric|min:0|max:99999999');
        $form->text('counter_2_label', __('Counter 2 label'));
        $form->decimal('counter_2_value', __('Counter 2 value'))->rules('nullable|numeric|min:0|max:99999999');
        $form->text('counter_3_label', __('Counter 3 label'));
        $form->decimal('counter_3_value', __('Counter 3 value'))->rules('nullable|numeric|min:0|max:99999999');
        $form->text('counter_4_label', __('Counter 4 label'));
        $form->decimal('counter_4_value', __('Counter 4 value'))->rules('nullable|numeric|min:0|max:99999999');

        $form->hasMany('links', 'Program links', function (Form\NestedForm $nestedForm) {
            $nestedForm->text('title', __('Title'))
                ->rules('max:120')
                ->help('Max length: 120 characters.');
            $nestedForm->text('link_label');
            $nestedForm->text('link')->required();
        });

        $form->saved(function (Form $form) {
            if ($form->model()->exists && $form->isCreating()) {
                Category::create([
                    'title' => $form->model()->title,
                    'program_id' => $form->model()->id,
                ]);
            }
        });

        if ($form->isEditing() ) {
            $program = Program::findOrFail(request()->route('program'));

            if($program && count($program->campaigns) > 0) {
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableDelete();
                });
            }
        }

        return $form;
    }
}
