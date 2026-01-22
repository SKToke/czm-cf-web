<?php

namespace App\Admin\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\User;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class DonorController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Donors';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Donor());
        $this->modifyGrid($grid);

        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));
        $grid->column('donor_type', __('Donor Type'))->display(function (){
            return $this->donor_type->getTitle();
        });
        $grid->column('amount', __('Total Donated (BDT)'))->display(function () {
            return $this->totalDonation();
        });

        $grid->column('user_id', __('Registered User'))->display(function () {
           return $this->user ? 'Yes' : 'No';
        });

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('name', 'Name');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('email', 'Email');
                $filter->like('phone', 'Phone');
            });
        });

        $grid->disableCreateButton();
        $grid->disableActions();

        $grid->model()->orderBy('id', 'desc');
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
        $show = new Show(Donor::findOrFail($id));

        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('phone', __('Phone'));

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
        return false;
    }
}
