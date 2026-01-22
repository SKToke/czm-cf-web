<?php

namespace App\Admin\Controllers;

use App\Enums\ContactType;
use App\Models\Campaign;
use App\Models\ContactUsQuery;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class ContactUsController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Contact Us queries';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ContactUsQuery());
        $this->modifyGrid($grid);
        $grid->model()->orderBy('id', 'desc');

        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('mobile_no', __('Phone'));
        $grid->column('message', __('Message'))->width(600)->display(function () {
            $msg = $this->message;
            if (mb_strlen($msg) > 60) {
                $msg = mb_substr($msg, 0, 60) . '...';
            }
            return $msg;
        });

        $grid->column('contact_type')->display(function ($contactType) {
            return ContactType::from($contactType)->getTitle();
        });

        $grid->column('campaign.title', __('Related Campaign'))->display(function() {
            if ($this->campaign) {
                return "<a href='" . route('admin.campaigns.show', $this->campaign->id) . "'>" . $this->campaign->title . "</a>";
            } else {
                return null;
            }
        })->style('max-width:250px;min-width:200px;white-space:normal;word-break:break-all;');

        $grid->column('responded', __('Responded'))->display(function ($responded) {
            return $responded ? 'Responded' : 'Not Responded';
        })->label([
            1 => 'success',
            0 => 'danger',
        ]);

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('name', 'Name');
                $filter->equal('campaign.id', __('Campaign'))->select(Campaign::all()->pluck('title','id')->toArray());
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('email', 'Email');
                $filter->like('mobile_no', 'Phone');
            });
            $filter->scope('trashed', 'Deleted Records')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer', 'digital_marketer'])) {
                    $actions->disableDelete();
                }
            }
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
        $show = new Show(ContactUsQuery::findOrFail($id));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('mobile_no', __('Phone'));
        $show->field('contact_type')->as(function ($type) {
            return $type->getTitle();
        });
        $show->field('campaign_id', __('Related Campaign'))->unescape()->as(function() {
            if ($this->campaign) {
                return "<a href='" . route('campaign-details', ['slug' => $this->campaign->slug]) . "'>" . $this->campaign->title . "</a>";
            } else {
                return 'General Purpose';
            }
        });
        $show->field('responded', __('Responded'))->as(function ($responded) {
            return match ($responded) {
                0 => 'Not responded',
                1 => 'Responded',
            };
        });
        $show->field('message', __('Message'));
        $show->field('created_at', __('Created at'))->as(function ($time) {
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
        $form = new Form(new ContactUsQuery());
        $this->modifyForm($form);
        $form->radio('responded','Responded')->options(['0' => 'Not Responded', '1' => 'Responded'])->stacked();

        return $form;
    }
}
