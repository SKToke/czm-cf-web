<?php

namespace App\Admin\Controllers;

use App\Enums\NotifiableUserTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Attachment;
use App\Models\Campaign;
use App\Models\Donor;
use App\Models\User;
use App\Models\UserNotification;
use App\Rules\NonRemovableSelectField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Enums\NotificationTypeEnum;

class NotificationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    use ModificationTrait;

    protected $title = 'Notifications';
    protected $removedKeys = [];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Notification());
        $this->modifyGrid($grid);

        $grid->model()->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc');

        $grid->column('notification_title', __('Title'))->style('max-width:80px;min-width:80px;white-space:normal;word-break:break-all;');

        $grid->column('type')->display(function ($type) {
            return NotificationTypeEnum::from($type)->getTitle();
        })->style('max-width:75px;min-width:80px;white-space:normal;word-break:break-all;');

        $grid->column('notification_description', __('Description'))->display(function ($value) {
            $msg = htmlspecialchars_decode($value);
            if (mb_strlen($msg) > 70) {
                $msg = mb_substr($msg, 0, 70) . '...';
            }
            return $msg;
        });

        $grid->column('send_mail', __('Notify by Mail'))->bool();

        $grid->column('attachment_count', __('Total Attached Files'))->display(function () {
            return $this->attachments()->count();
        });

        $grid->column('users', __('Number of Recipients'))->display(function () {
            return $this->users()->count();
        });

        $grid->column('campaign.title', __('Associated Campaign'))->style('max-width:120px;min-width:100px;white-space:normal;word-break:break-all;');

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('type', 'Notification Type')->select(NotificationTypeEnum::toArray());
            });
            $filter->scope('trashed', 'Deleted Notifications')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                    $actions->disableDelete();
                }
            }
            $actions->disableEdit();
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
        $show = new Show(Notification::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'))->as(function ($type) {
            return NotificationTypeEnum::from($type)->getTitle();
        });
        $show->field('notification_title', __('Title'));
        $show->field('notification_description')->unescape();
        $show->field('send_mail', __('Notify by Mail'))->using([0 => 'No', 1 => 'Yes']);
        $show->field('mail_subject');
        $show->field('mail_body')->unescape();
        $show->field('attachments', __('Attachments'))->unescape()->as(function () {
            $attachments = $this->attachments;

            if ($attachments->count() > 0) {
                $html = '<ul>';
                foreach ($attachments as $attachment) {
                    $filePath = Storage::disk('admin')->url($attachment->file);
                    $html .= "<li><a href='{$filePath}' target='_blank'>{$attachment->title}</a></li>";
                }
                $html .= '</ul>';
                return $html;
            }

            return 'No attachments.';
        });
        $show->field('created_at')->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('user_type', __('Recipient Type'))->as(function ($userType) {
            return $userType->getTitle();
        });
        $show->field('users', __('Recipients'))->as(function ($user) {
            return $user->pluck('email');
        })->label();

        $show->panel()
            ->tools(function ($tools) {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                    $tools->disableDelete();
                }
                $tools->disableEdit();
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
        $form = new Form(new Notification());
        $this->modifyForm($form);

        $campaignId = Request()->query('campaign_id');

        if ($campaignId) {
            $form->select('type', __('Type'))->options(NotificationTypeEnum::toArray())->rules('required')
                ->help('Required.');

            $form->select('campaign_id', __('Campaign'))->options(Campaign::all()->pluck('title', 'id'))->default($campaignId)->readonly();
        } else {
            $form->select('type', __('Type'))->options(NotificationTypeEnum::toArray())->default(1)->rules('required')
                ->help('Required.')->readonly();
        }

        $form->text('notification_title', __('Title'))
            ->rules('required|max:120')
            ->help('Required. Max length: 120 characters.');

        $form->ckeditor('notification_description', __('Description'))
            ->rules('max:1200')
            ->help('Max length: 1200 characters.');

        $form->select('user_type', __('Recipient Type'))
            ->addElementClass('custom-required')
            ->rules([new NonRemovableSelectField('Recipient Type')])
            ->options(NotifiableUserTypeEnum::toArray())->default(1)
            ->help('Required.')->when(1, function (Form $form) {
                $form->listbox('users', __('Select Users'))->options(User::all()->pluck('email', 'id'))->height(300);
            });

        $form->radio('send_mail',__('Notify by Mail'))->options([
                1 =>'Yes',
                0 =>'No',
            ])->when(1, function (Form $form) {
            $form->text('mail_subject', __('Mail Subject'))
                ->rules('max:120')
                ->help('Max length: 120 characters.');

            $form->ckeditor('mail_body', __('Mail Body'));
            $form->morphMany('attachments', 'File Attachments', function (Form\NestedForm $form) {
                $form->text('title', __('Title'))
                    ->required()
                    ->help('Required. Max length: 120 characters.');
                $form->file('file', __('Upload File'))
                    ->required()
                    ->removable(false)
                    ->help('Supported file: pdf,doc,jpg,jpeg,png,webp & Max file size: 10MB.');
            });
        });

        $form->saving(function (Form $form) {
            $attachments = collect($form->attachments);

            $result = $this->validateAttachments($attachments);
            $validAttachments = $result['validAttachments'];
            $errors = $result['errors'];
            $this->removedKeys = $result['removedKeys'];

            $form->attachments = $validAttachments;

            if (!empty($errors)) {
                $error = new MessageBag([
                    'title'   => 'Invalid File error',
                    'message' => 'All Attachment Files must be of type: pdf,doc,jpg,jpeg,png,webp & Max file size: 10MB.',
                ]);
                return back()->with(compact('error'));
            }

            if ($form->send_mail == 1 && ($form->mail_subject == null || $form->mail_body == null)) {
                $error = new MessageBag([
                    'title' => 'Null value error',
                    'message' => 'Mail Subject and Body must be present if you want to send an email.',
                ]);
                return back()->with(compact('error'));
            }
            $nonNullUserArray = $form->users;
            if ($nonNullUserArray) {
                $nonNullUserArray = array_filter($form->users, function($value) {
                    return $value !== null;
                });
            }

            if ($form->user_type == 1 && !is_null($nonNullUserArray) && count($nonNullUserArray)==0) {
                $error = new MessageBag([
                    'title' => 'Null value error',
                    'message' => 'Some users must be selected if Recipient Type is "Selected Users".',
                ]);
                return back()->with(compact('error'));
            }
        });

        $form->saved(function (Form $form) {
            //remove attachments those need to be removed
            foreach($this->removedKeys as $attachmentKey) {
                $attachment = Attachment::find($attachmentKey);
                if ($attachment) {
                    $attachment->delete();
                }
            }

            $notification = $form->model();
            if ($form->campaign_id) {
                $notification->campaign_id = $form->campaign_id;
            }

            $users = null;

            if ($form->user_type != null && $form->user_type == NotifiableUserTypeEnum::ALL_USERS->value) {
                $users = User::all();
            }
            else if ($form->user_type != null && $form->user_type == NotifiableUserTypeEnum::DONORS->value) {
                if ($form->campaign_id) {
                    $users = User::join('donors', 'users.id', '=', 'donors.user_id')
                        ->join('donations', 'donors.id', '=', 'donations.donor_id')
                        ->where('donations.campaign_id', '=', $form->campaign_id)
                        ->where('donations.transaction_status', '=', TransactionTypeEnum::Complete->value)
                        ->select('users.*')
                        ->distinct()
                        ->get();
                } else {
                    $users = User::join('donors', 'users.id', '=', 'donors.user_id')
                        ->select('users.*')
                        ->distinct()
                        ->get();
                }
            }
            else if ($form->user_type != null && $form->user_type == NotifiableUserTypeEnum::NEWSLETTER_SUBSCRIBERS->value) {
                $users = User::join('newsletter_subscriptions', 'users.email', '=', 'newsletter_subscriptions.email')
                    ->select('users.*')
                    ->distinct()
                    ->get();
            }
            else if ($form->user_type != null && $form->user_type == NotifiableUserTypeEnum::CAMPAIGN_SUBSCRIBERS->value) {
                if ($form->campaign_id) {
                    $users = User::join('donors', 'users.id', '=', 'donors.user_id')
                        ->join('campaign_subscriptions', 'donors.id', '=', 'campaign_subscriptions.donor_id')
                        ->where('campaign_subscriptions.campaign_id', '=', $form->campaign_id)
                        ->where('campaign_subscriptions.active', '=', true)
                        ->distinct()
                        ->select('users.*')
                        ->get();
                } else {
                    $users = User::join('donors', 'users.id', '=', 'donors.user_id')
                        ->join('campaign_subscriptions', 'donors.id', '=', 'campaign_subscriptions.donor_id')
                        ->where('campaign_subscriptions.active', '=', true)
                        ->distinct()
                        ->select('users.*')
                        ->get();
                }
            }

            if ($users) {
                foreach($users as $user) {
                    UserNotification::create([
                        'user_id' => $user->id,
                        'notification_id' => $notification->id,
                    ]);
                }
            }

            $notification->save();
        });

        $form->tools(function (Form\Tools $tools) {
            if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary'])) {
                $tools->disableDelete();
            }
        });

        return $form;
    }

    private function validateAttachments(\Illuminate\Support\Collection $attachments, int $maxSize = 10485760)
    {
        $validAttachments = [];
        $errors = [];
        $removedKeys = [];

        foreach ($attachments as $key => $attachment) {
            if (isset($attachment['_remove_']) && $attachment['_remove_'] == 1) {
                $removedKeys[] = $key;
                continue;
            }

            if (!isset($attachment['file']) && $attachment['_remove_'] == 0) {
                $validAttachments[$key] = $attachment;
            }

            if (isset($attachment['file']) && $attachment['file'] instanceof UploadedFile) {
                $file = $attachment['file'];
                $validator = Validator::make([$key => $file], [
                    $key => [new \App\Rules\ValidateAttachmentField($maxSize)]
                ]);

                if ($validator->fails()) {
                    $errors[$key] = $validator->errors()->first($key);
                } else {
                    $validAttachments[$key] = $attachment;
                }
            }
        }

        return [
            'validAttachments' => $validAttachments,
            'errors' => $errors,
            'removedKeys' => $removedKeys
        ];
    }
}
