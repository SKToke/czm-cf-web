<?php

namespace App\Admin\Controllers;

use App\Enums\CampaignSubscriptionTypeEnum;
use App\Models\Campaign;
use App\Models\Donor;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\CampaignSubscription;

class CampaignSubscriptionController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CampaignSubscription';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CampaignSubscription());
        $this->modifyGrid($grid);

        $grid->column('campaign.title', __('Campaign'));
        $grid->column('Subscriber')->display(function () {
            return $this->donor->name . '<br>' . '(' . $this->donor->email . ')';
        });
        $grid->column('subscription_type')->display(function ($type) {
            return CampaignSubscriptionTypeEnum::from($type)->getTitle();
        });
        $grid->column('subscribed_amount', __('Subscribed amount'));
        $grid->column('subscription_start_date', __('Subscription started'))->display(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $grid->column('last_donated')->display(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $grid->column('last_notified')->display(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $grid->column('due_amount', __('Due/Advanced'));

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->scope('subscribed_records')
                ->where('active', true);

            $filter->scope('unsubscribed_records')
                ->where('active', false);

            $filter->column(1/2, function ($filter) {
                $filter->equal('campaign.id', __('Campaign'))->select(Campaign::all()->pluck('title','id')->toArray());
                $filter->equal('donor.id', __('Donor'))->select(Donor::all()->pluck('name','id')->toArray());
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('subscription_type')->radio(CampaignSubscriptionTypeEnum::toArray());
            });
        });

        $grid->model()->orderBy('id', 'desc');
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
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
        $show = new Show(CampaignSubscription::findOrFail($id));

        $show->field('id');
        $show->field('campaign.title', __('Campaign'));
        $show->field('Subscriber')->unescape()->as(function () {
            return $this->donor->name . ' (' . $this->donor->email . ')';
        });
        $show->field('subscription_type')->as(function ($type) {
            return $type->getTitle();
        });
        $show->field('subscribed_amount');
        $show->field('last_donated')->as(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $show->field('last_notified')->as(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $show->field('subscription_start_date')->as(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $show->field('next_donation_date')->as(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $show->field('due_amount', __('Due/Advanced'));
        $show->field('active')->using([0 => 'False', 1 => 'True']);
        $show->field('created_at')->as(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
        });
        $show->field('updated_at')->as(function ($time) {
            return $time ? Carbon::parse($time)->format('Y-m-d h:i:s A') : null;
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
        return false;
    }
}
