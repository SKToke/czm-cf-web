<?php

namespace App\Admin\Controllers;

use App\Models\JobPost;
use App\Models\NewsletterSubscription;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use Illuminate\Validation\ValidationException;

class JobPostController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Job post';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new JobPost());
        $grid->model()->orderBy('updated_at', 'desc');
        $this->modifyGrid($grid);

        $grid->column('title', __('Title'));
        $grid->column('company_name', __('Company Name'));
        $grid->column('job_nature', __('Job Nature'));
        $grid->column('opening_date', __('Opening date'));
        $grid->column('closing_date', __('Closing date'));
        $grid->column('location', __('Location'));
        $grid->column('job_application_count', __('Job application count'))->display(function () {
            return $this->jobApplications()->count();
        });
        $grid->column('logo', __('Logo'))->image('', 100, 'auto');

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');
                $filter->like('job_nature', 'Job Nature');
                $filter->like('location', 'Location');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('company_name', 'Company Name');
                $filter->like('opening_date', 'Opening Date');
                $filter->like('closing_date', 'Closing Date');
            });

            $filter->scope('trashed', 'Deleted Job Posts')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin'])) {
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
        $show = new Show(JobPost::findOrFail($id));
        $show->field('title', __('Name'));
        $show->field('description', __('Description'))->unescape();;
        $show->field('job_nature', __('Job nature'));
        $show->field('company_name', __('Company Name'));
        $show->field('location', __('Location'));
        $show->field('opening_date', __('Opening Date'));
        $show->field('closing_date', __('Closing Date'));
        $show->field('logo', __('Logo'))->image('', 100, 'auto');;

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new JobPost());
        $this->modifyForm($form);
        $form->text('title', 'Title')
            ->required()
            ->rules(['max:250', new \App\Rules\NoSpacesInField(true)])
            ->help('Maximum 250 characters.');
        $form->ckeditor('description', 'Description');
        $form->text('job_nature', 'Job Nature')->required()->rules('max:250');
        $form->text('company_name', 'Company Name')->required()->rules('max:250');
        $form->date('opening_date', 'Opening Date')->default(date('Y-m-d'))->required();
        $form->date('closing_date', 'Closing Date')->default(date('Y-m-d'))->required()->rules(function ($form) {
            return 'after:opening_date';
        });
        $form->text('location', 'Location')->rules('max:250');
        $form->image('logo', __('logo'))->removable()->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');

        return $form;
    }

}
