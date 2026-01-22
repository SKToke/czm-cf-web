<?php

namespace App\Admin\Controllers;

use App\Enums\CampaignStatusEnum;
use App\Enums\CampaignTypeEnum;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Program;
use App\Models\TaggedCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Campaign;
use Illuminate\Support\MessageBag;
use \App\Rules\NonRemovableSelectField;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class CampaignController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Campaign';
    protected $removedKeys = [];


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Campaign());
        $this->modifyGrid($grid);

        $grid->column('campaign_id', __('Campaign ID'));
        $grid->column('title', __('Campaign Title'))
            ->style('max-width:200px;min-width:150px;white-space:normal;word-break:break-all;');
        $grid->column('program.title', __('Program'));

        $grid->column('categories')->display(function () {
            return $this->categories->pluck('title')->implode(', ');
        })->style('min-width:100px;max-width:150px;white-space:normal;word-break:break-all;');

        $grid->column('fund_count')->display(function () {
            $html = '<p class="mt-3">' . $this->getFundCount() . '(' . number_format($this->getFundPercentage(), 3) . '%)' . '</p>';
            return $html;
        });
        $grid->column('last_donation_time')->display(function () {
            return $this->getLastDonationDate() ? Carbon::parse($this->getLastDonationDate())->format('Y-m-d h:i:s A'):null;
        });
        $grid->column('urgency_status', __('Urgent'))->bool();
        $grid->column('remaining_days')->display(function () {
            $endDateTime = Carbon::parse($this->donation_end_time);
            $diff = Carbon::now()->diff($endDateTime);
            if ($diff->invert) {
                return '0';
            }
            $formattedString = '';

            if ($diff->y > 0) {
                $formattedString .= $diff->y . ' years ';
            }
            if ($diff->m > 0) {
                $formattedString .= $diff->m . ' months ';
            }
            if ($diff->d > 0) {
                $formattedString .= $diff->d . ' days ';
            }
            if ($diff->h > 0) {
                $formattedString .= $diff->h . ' hr ';
            }
            if ($diff->i > 0) {
                $formattedString .= $diff->i . ' min ';
            }

            return trim($formattedString);
        });
        $grid->column('allocated_amount', __('Required Amount'))->style('max-width:70px;min-width:75px;white-space:normal;word-break:break-all;');
        $grid->column('campaign_status')->display(function ($status) {
            return CampaignStatusEnum::from($status)->getTitle();
        })->style('max-width:75px;min-width:80px;white-space:normal;word-break:break-all;');
        $grid->column('Total Supporters')->display(function() {
            return $this->getTotalSupporters();
        });
        $grid->column('slug', __('Campaign Update'))->display(function () {
            if ($this->trashed()) {
                return '';
            }
            $url = route('admin.campaign-updates.create', ['campaign_id' => $this->id]);
            return "<a href='$url' class='btn btn-xs btn-primary'>Add Campaign Update</a>";
        });
        $grid->column('id', __('Notification'))->display(function ($id) {
            if ($this->trashed()) {
                return '';
            }
            $url = route('admin.notifications.create', ['campaign_id' => $id]);
            return "<a href='$url' class='btn btn-xs btn-primary'>Send Notification</a>";
        });

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->scope('trashed', 'Deleted Campaigns')->onlyTrashed();

            $filter->setCols(4, 6);

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');

                $filter->equal('program.id', __('Program'))->select(Program::all()->pluck('title','id')->toArray());

                $filter->where(function ($query) {
                    $query->whereHas('categories', function ($query) {
                        $query->where('categories.id', $this->input);
                    });
                }, 'category', 'category')->select(Category::all()->pluck('title','id')->toArray());

                $filter->where(function ($query) {
                    $endDate = now()->addDays($this->input);
                    $query->where('donation_end_time', '<=', $endDate);
                }, 'Remaining Days')->integer();
            });

            $filter->column(1/2, function ($filter) {
                $filter->between('allocated_amount', 'Required Amount');

                $filter->equal('campaign_status')->radio(CampaignStatusEnum::toArray());

                $filter->where(function ($query) {
                    $query->where('urgency_status', True);
                }, 'Is Urgent')->checkbox([
                    1    => 'Yes'

                ]);
            });
        });

        $grid->actions(function ($actions) {
            if($actions->row->getFundCount() > 0) {
                $actions->disableDelete();
            }
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'digital_marketer', 'campaign_manager', 'board_secretary'])) {
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
        $show = new Show(Campaign::findOrFail($id));

        $show->field('id');
        $show->field('campaign_id', __('Campaign ID'));
        $show->field('slug');
        $show->field('title');
        $show->field('thumbnail_image', __('Thumbnail Image'))->image();
        $show->field('image_paths', __('Images'))->image();
        $show->field('campaign_type', __('Campaign type'))->as(function ($type) {
            return $type->getTitle();
        });
        $show->field('campaign_status', __('Campaign status'))->as(function ($status) {
            return $status->getTitle();
        });
        $show->field('program.title', __('Program'));
        $show->field('categories')->as(function ($categories) {
            return $categories->pluck('title')->implode(', ');
        });
        $show->field('description')->unescape();
        $show->field('urgency_status', __('Urgent'))->using([0 => 'False', 1 => 'True']);
        $show->field('allocated_amount', __('Required Amount'));
        $show->field('donation_start_time')->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('donation_end_time')->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('fund_progress')->unescape()->as(function () {
            $html = '<p>' . $this->getFundCount() . 'Tk (' . number_format($this->getFundPercentage(), 3) . '%)' . '</p>';
            return $html;
        });
        $show->field('total_supporters')->as(function () {
            return $this->getTotalSupporters();
        });
        $show->field('last_donation_time')->as(function () {
            return $this->getLastDonationDate() ? Carbon::parse($this->getLastDonationDate())->format('Y-m-d h:i:s A'):null;
        });
        $show->field('share_count');
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
            return Carbon::parse($this->updated_at)->format('Y-m-d h:i:s A');
        });
        $show->field('campaignUpdates', __('Campaign Updates'))->unescape()->as(function () {
            return $this->getFormattedCampaignUpdates();
        });
        $show->field('number_of_recipients');

        $campaign = Campaign::findOrFail($id);
        $show->panel()
            ->tools(function ($tools) use ($campaign) {
                if($campaign && $campaign->getFundCount() > 0) {
                    $tools->disableDelete();
                }
                if (!Admin::user()->inRoles(['administrator', 'admin', 'digital_marketer', 'campaign_manager', 'board_secretary'])) {
                    $tools->disableDelete();
                }
            });

        return $show;
    }

    protected function manageCategories(array $formCategories, int $campaignId)
    {
        if ($formCategories != null) {
            foreach ($formCategories as $categoryId) {
                if ($categoryId != null) {
                    $taggedCategory = new TaggedCategory([
                        'category_id' => $categoryId,
                        'parentable_type' => Campaign::class,
                        'parentable_id' => $campaignId,
                    ]);

                    $taggedCategory->save();
                }
            }
        }
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Campaign());
        $this->modifyForm($form);

        $form->text('title')->help('Maximum 70 characters')
            ->creationRules(['required', 'min:3', 'max:70', "unique:campaigns"])
            ->updateRules(['required', 'min:3', 'max:70', "unique:campaigns,title,{{id}}"]);
        $form->ckeditor('description')
            ->rules([new \App\Rules\NoSpacesInField(false, 500)])
            ->help('Maximum 500 words');
        $form->select('campaign_type', __('Campaign type'))
            ->addElementClass('custom-required')
            ->options(CampaignTypeEnum::toArray())
            ->rules([new NonRemovableSelectField()]);
        $form->select('campaign_status', __('Campaign status'))
            ->addElementClass('custom-required-two')
            ->options(CampaignStatusEnum::toArray())
            ->rules([new NonRemovableSelectField()]);
        $form->select('program_id', __('Program'))
            ->addElementClass('custom-required-three')
            ->options(Program::all()->pluck('title', 'id'))
            ->rules([new NonRemovableSelectField('Program')]);

        if ($form->isCreating()) {
            $form->hidden('campaign_id')->default(1000);
            $form->multipleSelect('categories')
                ->options(Category::all()->pluck('title','id'))
                ->placeholder('Select categories');
        }

        if ($form->isEditing()) {
            $selectedCategories = [];
            $campaign = Campaign::findOrFail(request()->route('campaign'));
            if ($campaign && $campaign->categories) {
                $selectedCategories = $campaign->categories->pluck('id')->toArray();
            }
            $form->multipleSelect('select_categories')
                ->options(Category::all()->pluck('title','id'))
                ->default($selectedCategories);
        }

        $form->switch('urgency_status', __('Urgent'));
        $form->decimal('allocated_amount', __('Required Amount'))
            ->placeholder('Enter Required Amount')
            ->required()
            ->help('Must be greater than 0')
            ->rules(function ($form) {
                return 'gt:0';
            });
        $form->datetime('donation_start_time')->default(date('Y-m-d h:i:s'))->required();
        $form->datetime('donation_end_time')->default(date('Y-m-d h:i:s'))
            ->required()->rules(function ($form) {
                return 'after:donation_start_time';
            });
        $form->number('number_of_recipients', __('Number of Recipients'));
        $form->image('thumbnail_image', __('Thumbnail Image'))
            ->removable()
            ->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
        $form->multipleImage('image_paths', 'Images')
            ->addElementClass('custom-multiple-images')
            ->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
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

            $fieldName = 'select_categories';
            $form->builder()->fields()->each(function ($field, $id) use ($form, $fieldName) {
                if ($field->column() === $fieldName) {
                    $form->builder()->fields()->forget($id);
                }
            });
        });

        $form->saved(function (Form $form) {
            $campaign = $form->model();

            if ($form->isCreating()) {
                $campaign->campaign_id = $campaign->campaign_id + $campaign->id;
                $campaign->share_count = 0;
                $campaign->save();
                $formCategories = request()->get('categories');
                $this->manageCategories($formCategories, $campaign->id);
            } elseif ($form->isEditing()) {
                $campaign->taggedCategories()->delete();
                $formCategories = request()->get('select_categories');
                $this->manageCategories($formCategories, $campaign->id);
            }

            //remove attachments those need to be removed
            foreach($this->removedKeys as $attachmentKey) {
                $attachment = Attachment::find($attachmentKey);
                if ($attachment) {
                    $attachment->delete();
                }
            }
        });

        if ($form->isEditing() ) {
            $campaign = Campaign::findOrFail(request()->route('campaign'));

            if(($campaign && $campaign->getFundCount() > 0) || (!Admin::user()->inRoles(['administrator', 'admin', 'digital_marketer', 'campaign_manager', 'board_secretary']))) {
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableDelete();
                });
            }
        }

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
