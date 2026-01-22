<?php

namespace App\Admin\Controllers;

use App\Models\JobApplication;

use Illuminate\Support\Facades\Storage;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class JobApplicationController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Job application';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new JobApplication());
        $this->modifyGrid($grid);

        $grid->model()->orderBy('id', 'desc');

        $grid->column('jobPost.company_name', __('Company Name'));
        $grid->column('applicant_name', __('Applicant name'));
        $grid->column('mobile_no', __('Mobile number'));
        $grid->column('email', __('Email'));
        $grid->column('comment', __('Comment'));
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('jobPost.company_name', 'Company Name');
                $filter->like('applicant_name', 'Applicant Name');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('mobile_no', 'Mobile Number');
                $filter->like('email', 'Email');
            });
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
        $show = new Show(JobApplication::findOrFail($id));
        $show->field('applicant_name', __('Applicant name'));
        $show->field('mobile_no', __('Mobile number'));
        $show->field('email', __('Email'));
        $show->field('comment', __('Comment'));
        $show->cv(__('cv'))->unescape()->as(function ($cv) {
            if ($cv) {
                if (Storage::disk('public')->exists($cv)) {
                    $url = Storage::disk('public')->url($cv);
                    return "<a href=\"{$url}\" target=\"_blank\">Download CV</a>";
                } else {
                    return "File not found.";
                }
            }
            return "No CV uploaded.";
        });
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */

}
