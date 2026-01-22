<?php

namespace App\Admin\Controllers;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use OpenAdmin\Admin\Auth\Permission;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Show;
use \App\Models\Notice;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NoticeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    use ModificationTrait;

    protected $title = 'Notice';
    protected $removedKeys = [];

    public function edit($id, Content $content): Content
    {
        Permission::check('notice.update');
        return parent::edit($id, $content);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Notice());
        $this->modifyGrid($grid);

        $grid->model()->orderBy('published_date', 'desc')->orderBy('updated_at', 'desc');

        $grid->column('title', __('Title'));
        $grid->column('description', __('Description'))->display(function () {
            $msg = $this->description;
            if (mb_strlen($msg) > 60) {
                $msg = mb_substr($msg, 0, 60) . '...';
            }
            return $msg;
        });
        $grid->column('published_date', __('Published date'));

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('published_date', 'Published Date')->date();
            });

            $filter->scope('trashed', 'Deleted Notices')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer'])) {
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
        $show = new Show(Notice::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description')->unescape();
        $show->field('published_date', __('Published date'));
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
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer'])) {
                    $tools->disableDelete();
                }
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
        $form = new Form(new Notice());
        $this->modifyForm($form);

        $form->text('title', __('Title'))
            ->rules('required|max:120')
            ->help('Required. Max length: 120 characters.');
        $form->ckeditor('description', __('Description'))
            ->rules('max:600')
            ->help('Max length: 600 characters.');
        $form->date('published_date', __('Published date'))
            ->default(date('Y-m-d'))
            ->rules(['required', 'date', 'before_or_equal:' . date('Y-m-d')])
            ->help('Required. Format: YYYY-MM-DD.');
        $form->morphMany('attachments', 'File Attachments', function (Form\NestedForm $form) {
            $form->text('title', __('Title'))
                ->required()
                ->help('Required. Max length: 120 characters.');
            $form->file('file', __('Upload File'))
                ->required()
                ->removable(false)
                ->help('Supported file: pdf,doc,jpg,jpeg,png,webp & Max file size: 10MB.');
        });

        $form->tools(function (Form\Tools $tools) {
            if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer'])) {
                $tools->disableDelete();
            }
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
