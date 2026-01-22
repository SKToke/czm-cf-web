<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\UserZakatCalculation;
use PDF;

class UserZakatCalculationController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'UserZakatCalculation';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserZakatCalculation());
        $this->modifyGrid($grid);

        $grid->column('registered_user', __('Registered user'))->bool();
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('zakat_type', __('Zakat type'));
        $grid->column('payable_zakat', __('Payable zakat'));
        $grid->column('nisab_standard', __('Nisab standard'));
        $grid->column('nisab_value', __('Nisab value'));
        $grid->column('total_assets', __('Total assets'));
        $grid->column('total_liabilities', __('Total liabilities'));
        $grid->column('net_zakatable_assets', __('Net zakatable assets'));
        $grid->column('paid_to_czm', __('Paid to czm'));
        $grid->column('date', __('Date'));
        $grid->column('archived', __('Archived'))->bool();
        $grid->column('exported', __('Exported'))->bool();

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->date('date');
                $filter->where(function ($query) {
                    $query->where('registered_user', True);
                }, 'Is Registered User')->checkbox([
                    1    => 'Yes'
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->where(function ($query) {
                    $query->where('archived', True);
                }, 'Is Archived By User')->checkbox([
                    1    => 'Yes'
                ]);
                $filter->where(function ($query) {
                    $query->where('exported', True);
                }, 'Is Exported By User')->checkbox([
                    1    => 'Yes'
                ]);
            });
        });

        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });

        return $grid;
    }

    public function generatePDF($id)
    {
        $userZakatCalculation = UserZakatCalculation::find($id);
        if ($userZakatCalculation) {
            $pdf = PDF::loadView('pdf.zakat', ['userZakatCalculation' => $userZakatCalculation]);
            return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="zakat-calculation.pdf"');
        }
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(UserZakatCalculation::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('date', __('Date'));
        $show->field('registered_user', __('Registered user'))->using([0 => 'False', 1 => 'True']);
        $show->field('name', __('Name'));
        $show->field('mobile', __('Mobile'));
        $show->field('email', __('Email'));
        $show->field('zakat_type', __('Zakat type'));
        $show->field('nisab_standard', __('Nisab standard'));
        $show->field('nisab_value', __('Nisab value'));
        $show->field('total_assets', __('Total assets'));
        $show->field('total_liabilities', __('Total liabilities'));
        $show->field('net_zakatable_assets', __('Net zakatable assets'));
        $show->field('payable_zakat', __('Payable zakat'));
        $show->field('paid_to_czm', __('Paid to czm'));
        $show->field('archived', __('Archived'))->using([0 => 'False', 1 => 'True']);
        $show->field('exported', __('Exported'))->using([0 => 'False', 1 => 'True']);
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->field('generate_pdf', __('Calculation Details'))->unescape()->as(function () use ($id) {
            return '<form action="' . route('admin.zakat-calculation.pdf', ['id' => $id]) . '" method="POST">' .
            csrf_field() .
            '<button type="submit" class="btn btn-primary">Export details as PDF</button>' .
           '</form>';
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
    protected function form()
    {
        $form = new Form(new UserZakatCalculation());

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
