<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Member;
use Carbon\Carbon;

class MemberController extends AdminController
{
    use ModificationTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Member';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Member());
        $this->modifyGrid($grid);

        $grid->column('name', __('Name'));
        $grid->column('image', __('Image'))->image(null, 100, 100);
        $grid->column('self_designation', __('Self designation'));
        $grid->column('email_address', __('Email Address'));


        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->like('name', 'Name');
            $filter->scope('trashed', 'Deleted Members')->onlyTrashed();
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
        $show = new Show(Member::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('image', __('Image'))->image();
        $show->field('self_designation', __('Self designation'));
        $show->field('description', __('Description'));
        $show->field('contact_number', __('Contact Number'));
        $show->field('email_address', __('Email Address'));
        $show->field('facebook_link', __('Facebook Link'))->link();
        $show->field('linkedin_link', __('LinkedIn Link'))->link();
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
        $form = new Form(new Member());
        $this->modifyForm($form);

        $form->text('name', __('Name'))->required()->help('Maximum 50 characters')->rules('min:3|max:50');
        $form->image('image', __('Image'))
            ->removable()
            ->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')
            ->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
        $form->text('self_designation', __('Self designation'))->help('Maximum 120 characters')->rules('max:120');
        $form->textarea('description', __('Description'))
            ->help('Maximum 2000 words')
            ->rules([new \App\Rules\NoSpacesInField(false, 2000)]);
        $form->phonenumber('contact_number','Contact Number')->options(['mask' => '999999999999999'])->help('Maximum 15 digits');
        $form->email('email_address', __('Email Address'));
        $form->url('facebook_link', __('Facebook Link'));
        $form->url('linkedin_link', __('LinkedIn Link'));
        return $form;
    }
}
