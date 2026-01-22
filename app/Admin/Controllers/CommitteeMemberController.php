<?php

namespace App\Admin\Controllers;

use App\Models\Committee;
use App\Models\Member;
use App\Rules\NonRemovableSelectField;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\CommitteeMember;

class CommitteeMemberController extends AdminController
{
    use ModificationTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CommitteeMember';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CommitteeMember());
        $this->modifyGrid($grid);

        $grid->column('id', __('Id'));
        $grid->column('committee_id', __('Committee'))->display(function() {
            return $this->committee?->name;
        });
        $grid->column('member_id', __('Member'))->display(function() {
            return $this->member?->name;
        });
        $grid->column('designation', __('Designation'));

        $grid->model()->orderBy('position', 'asc')->orderBy('updated_at', 'asc');
        $grid->column('position', __('Position'));
        $grid->column('updated_at', __('Updated at'))->display(function () {
            return Carbon::parse($this->updated_at)->format('Y-m-d h:i:s A');
        });

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->equal('committee.id', __('Committee'))->select(Committee::all()->pluck('name','id')->toArray());
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('member.id', __('Member'))->select(Member::all()->pluck('name','id')->toArray());
            });
        });

        $grid->actions(function ($actions) {
            if (!Admin::user()->inRoles(['administrator', 'admin', 'digital_marketer', 'board_secretary'])) {
                $actions->disableDelete();
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
        $show = new Show(CommitteeMember::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('committee_id', __('Committee'))->as(function() {
            return $this->committee->name;
        });
        $show->field('member_id', __('Member'))->as(function() {
            return $this->member->name;
        });
        $show->field('designation', __('Designation'));
        $show->field('position', __('Position'));
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
        $form = new Form(new CommitteeMember());
        $this->modifyForm($form);

        $form->select('committee_id', __("Committee"))
            ->options(Committee::all()->pluck('name', 'id'))
            ->addElementClass('custom-required')
            ->rules([new NonRemovableSelectField('Committee')]);

        $form->select('member_id', __("Member"))
            ->options(Member::all()->pluck('name', 'id'))
            ->addElementClass('custom-required-two')
            ->rules([new NonRemovableSelectField('Member')]);

        $form->number('position', __('Position'))->rules('required|numeric|min:0|max:999');
        $form->text('designation', __('Designation'))->help('Maximum 120 characters')->rules('max:120');

        return $form;
    }
}
