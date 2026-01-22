<?php

namespace App\Admin\Controllers;

use App\Enums\TransactionTypeEnum;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class DonationController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Donations';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Donation());
        $this->modifyGrid($grid);
        $grid->model()->orderBy('updated_at', 'desc');
        $grid->column('transaction_id', __('Transaction Id'));
        $grid->column('amount', __('Amount (BDT)'));
        $grid->column('payment_via', __('Paid Via'));
        $grid->column('donor_id', __('Donor'))->display(function () {
            if($this->donor_id) {
                $donor = $this->donor()->first();
                return "{$donor->name} ({$donor->email})";
            }else {
                return 'Anonymous';
            }
        });


        $grid->column('transaction_status')->display(function ($status) {
            return TransactionTypeEnum::from($status)->getTitle();
        })->label([
            1 => 'primary',
            2 => 'success',
            3 => 'danger',
            4 => 'warning',
        ]);

        $grid->column('donation_type', __('Donation Type'))->display(function (){
            return $this->donation_type->getTitle();
        });
        $grid->column('campaign_id', __('Donation On'))->display(function() {
            if ($this->campaign) {
                return "<a href='" . route('admin.campaigns.show', $this->campaign->id) . "'>" . $this->campaign->title . "</a>";
            } else {
                return 'General Purpose';
            }
        })->style('max-width:250px;min-width:200px;white-space:normal;word-break:break-all;');
        $grid->column('updated_at', __('Donated at'))->display(function () {
            return Carbon::parse($this->updated_at)->format('d-M-Y (h:i a)');
        });


        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->equal('campaign.id', __('Campaign'))->select(Campaign::all()->pluck('title','id')->toArray());
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('amount', 'Amount (BDT)');
                $filter->like('transaction_status', 'Transaction status')->select(TransactionTypeEnum::toArray());
            });
        });

        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

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
        $show = new Show(Donation::findOrFail($id));
        $show->field('transaction_id', __('Transaction Id'));
        $show->field('amount', __('Amount (BDT)'));
        $show->field('payment_via', __('Paid Via'));
        $show->field('created_at', __('Donated at'))->as(function () {
            return Carbon::parse($this->created_at)->format('d-M-Y (h:i a)');
        });
        $show->field('transaction_status', __('Transaction Status'))->as(function ($type) {
            return TransactionTypeEnum::from($type)->getTitle();
        });
        $show->field('donor_id', __('Donor'))->as(function ($donor_id) {
            if($donor_id){
                $donor = $this->donor()->first();
                return "{$donor->name} ({$donor->email})";
            } else {
                return 'Anonymous';
            }
        });
        $show->field('donation_type', __('Donation Type'))->as(function (){
            return $this->donation_type->getTitle();
        });
        $show->field('campaign_id', __('Donation On'))->unescape()->as(function() {
            if ($this->campaign) {
                return "Campaign: <a href='" . route('admin.campaigns.show', $this->campaign->id) . "'>" . $this->campaign->title . "</a>";
            } else {
                return 'General Purpose';
            }
        });

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });;

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Donation());

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        return $form;
    }
}
