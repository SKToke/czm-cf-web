<?php

namespace App\Admin\Controllers;

use App\Models\Attachment;
use App\Rules\NonRemovableSelectField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\Campaign;
use \App\Models\CampaignUpdate;
use Illuminate\Support\MessageBag;
use Carbon\Carbon;

class CampaignUpdateController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CampaignUpdate';
    protected $removedKeys = [];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CampaignUpdate());
        $this->modifyGrid($grid);

        $grid->column('campaign.title', __('Campaign'));
        $grid->column('title', __('Message Title'));
        $grid->column('message', __('Message Body'))->display(function () {
            $msg = $this->getCustomMessage();
            if (mb_strlen($msg) > 70) {
                $msg = mb_substr($msg, 0, 70) . '...';
            }
            return $msg;
        });
        $grid->column('updated_at', __('Last Updated at'))->display(function () {
            return Carbon::parse($this->updated_at)->format('Y-m-d h:i:s A');
        });


        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Message Title');
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('campaign.id', __('Campaign'))->select(Campaign::all()->pluck('title','id')->toArray());
            });
        });

        $grid->model()->orderBy('id', 'desc');
        $grid->actions(function ($actions) {
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
        $show = new Show(CampaignUpdate::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Message Title'));
        $show->field('campaign.title', __('Campaign'));
        $show->field('message', __('Message Body'))->unescape()->as(function () {
            return $this->getCustomMessage();
        });
        $show->field('disbursed_amount', __('Disbursed amount'));
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
        $show->field('updated_at', __('Last Updated at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });

        $show->panel()
            ->tools(function ($tools) {
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
        $form = new Form(new CampaignUpdate());
        $this->modifyForm($form);

        $campaignId = Request()->query('campaign_id');

        if ($campaignId) {
            $form->select('campaign_id', __('Campaign'))
                ->addElementClass('custom-required')
                ->options(Campaign::all()->pluck('title', 'id'))
                ->rules([new NonRemovableSelectField('Campaign')])
                ->default($campaignId)->readonly();
        } else {
            $form->select('campaign_id', __('Campaign'))
                ->addElementClass('custom-required')
                ->options(Campaign::all()->pluck('title', 'id'))
                ->rules([new NonRemovableSelectField('Campaign')]);
        }

        $form->text('title', __('Message Title'))->required()->help('Maximum 80 characters')->rules('min:3|max:80');
        $form->ckeditor('message', __('Message Body'))->rules([new \App\Rules\NoSpacesInField(false, 100)])
            ->help('Maximum 100 words');
        $form->decimal('disbursed_amount')
            ->placeholder('Enter disbursed amount');
        $form->morphMany('attachments', 'File Attachments', function (Form\NestedForm $form) {
            $form->text('title', __('Title'))
                ->required()
                ->help('Required. Max length: 120 characters.');
            $form->file('file', __('Upload File'))
                ->required()
                ->removable(false)
                ->help('Supported file: pdf,doc,jpg,jpeg,png,webp & Max file size: 10MB.');
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

            if ($form->campaign_id == null) {
                $error = new MessageBag([
                    'title'   => 'Null value error',
                    'message' => 'Campaign field can not be null',
                ]);
                return back()->with(compact('error'));
            }

            if ($form->message == null && $form->disbursed_amount == null) {
                $error = new MessageBag([
                    'title'   => 'Null value error',
                    'message' => 'Either message-body or disbursed-amount must be present',
                ]);
                return back()->with(compact('error'));
            }

            if ($form->disbursed_amount != null) {
                $campaign = Campaign::find($form->campaign_id);
                $validAmount = $campaign->allocated_amount - $campaign->getTotalDisbursedAmount();
                $newDisbursedAmount = $campaign->getTotalDisbursedAmount() + $form->disbursed_amount;

                if ($form->isEditing()) {
                    $campaignUpdate = CampaignUpdate::findOrFail(request()->route('campaign_update'));
                    if ($campaignUpdate->disbursed_amount) {
                        $validAmount = $campaign->allocated_amount - $campaign->getTotalDisbursedAmount() + $campaignUpdate->disbursed_amount;
                        $newDisbursedAmount = $campaign->getTotalDisbursedAmount() - $campaignUpdate->disbursed_amount + $form->disbursed_amount;
                    }
                }
                if ($form->disbursed_amount <= 0 || $newDisbursedAmount > $campaign->allocated_amount) {
                    $error = new MessageBag([
                        'title'   => 'Invalid value error',
                        'message' => 'As Disbursed amount must be greater than zero and total disbursed amount must be less than campaign allocated amount,
                                       you can add disbursed amount here at most ' . $validAmount . 'TK',
                    ]);
                    return back()->with(compact('error'));
                }
            }
        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        $form->saved(function (Form $form) {
            //remove attachments those need to be removed
            foreach($this->removedKeys as $attachmentKey) {
                $attachment = Attachment::find($attachmentKey);
                if ($attachment) {
                    $attachment->delete();
                }
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
