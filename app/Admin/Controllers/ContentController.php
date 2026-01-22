<?php

namespace App\Admin\Controllers;

use App\Enums\ContentTypeEnum;
use App\Models\TaggedCategory;
use Illuminate\Support\HtmlString;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use App\Models\Content;
use App\Models\Category;

class ContentController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Content';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Content());
        $grid->model()->orderBy('updated_at', 'desc');

        $this->modifyGrid($grid);

        $grid->column('name');
        $grid->column('content_type')->display(function ($status) {
            return ContentTypeEnum::from($status)->getTitle();
        });
        $grid->column('categories')->display(function () {
            return $this->categories->pluck('title')->implode(', ');
        })->style('min-width:100px;max-width:150px;white-space:normal;word-break:break-all;');

        $grid->column('id', __('Content Sections'))->display(function ($id) {
            $url = route('admin.contentSections.create', ['content_id' => $id]);
            $totalSections = $this->contentsections->count();
            return "<span>Total: $totalSections</span> <a href='$url' class='btn btn-xs btn-primary'>Add new Content Section</a>";
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('name', 'Name');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('content_type', 'Content Type')->select(ContentTypeEnum::toArray());
            });
        });

        $grid->filter(function ($filter) {
            $filter->scope('trashed', 'Deleted Contents')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if ($actions->row->content_type == ContentTypeEnum::STORY ||
                    $actions->row->content_type == ContentTypeEnum::QURANIC_VERSE ||
                    $actions->row->content_type == ContentTypeEnum::SADAQAH ||
                    $actions->row->content_type == ContentTypeEnum::CASH_WAQF ||
                    $actions->row->content_type == ContentTypeEnum::QARD_AL_HASAN) {
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
        $content = Content::with('contentSections')->findOrFail($id);
        $show = new Show(Content::findOrFail($id));

        $show->field('name');
        $show->field('content_type', __('Content type'))->as(function ($type) {
            return $type->getTitle();
        });

        $show->field('slug');

        $show->html('content_sections')->as(function () use ($content) {
            $html = '';

            $sortedSections = $content->contentsections->sortBy('position');

            foreach ($sortedSections as $section) {
                $html .= view('partials.content_section', ['section' => $section])->render();
            }

            return new HtmlString($html);
        });
        $contentId = request()->route('content');
        $content = Content::findOrFail($contentId);
        if ($content->content_type == ContentTypeEnum::QURANIC_VERSE ||
            $content->content_type == ContentTypeEnum::STORY ||
            $content->content_type == ContentTypeEnum::SADAQAH ||
            $content->content_type == ContentTypeEnum::CASH_WAQF ) {
            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableDelete();
                });
        }

        return $show;
    }


    protected function form()
    {
        $form = new Form(new Content());
        $this->modifyForm($form);

        $form->text('name', 'Name')->required()->help('Maximum 240 characters')->rules('min:3|max:240');

        if ($form->isCreating()) {
            $form->select('content_type')
                ->options([
                    ContentTypeEnum::BLOG->value => ContentTypeEnum::BLOG->name,
                    ContentTypeEnum::NEWS->value => ContentTypeEnum::NEWS->name
                ])
                ->required();
            $form->multipleSelect('categories')->options(Category::all()->pluck('title', 'id'))->placeholder('Select categories');
        }

        if ($form->isEditing()) {
            $contentId = request()->route('content');
            $content = Content::findOrFail($contentId);

            if ($content->content_type == ContentTypeEnum::BLOG || $content->content_type == ContentTypeEnum::NEWS) {

                $form->select('content_type')
                    ->options([
                        ContentTypeEnum::BLOG->value => ContentTypeEnum::BLOG->name,
                        ContentTypeEnum::NEWS->value => ContentTypeEnum::NEWS->name
                    ])
                    ->required();
                $selectedCategories = $content->categories ? $content->categories->pluck('id')->toArray() : [];
                $form->multipleSelect('select_categories')->options(Category::all()->pluck('title', 'id'))->default($selectedCategories);
            }
        }

        $form->saving(function (Form $form) {
            $fieldName = 'select_categories';
            $form->builder()->fields()->each(function ($field, $id) use ($form, $fieldName) {
                if ($field->column() === $fieldName) {
                    $form->builder()->fields()->forget($id);
                }
            });
        });
        $form->saved(function (Form $form) {
            $content = $form->model();

            if ($form->isCreating()) {
                $formCategories = request()->get('categories');
                $this->manageCategories($formCategories, $content->id);
            } elseif ($form->isEditing()) {
                $content->taggedCategories()->delete();
                $formCategories = request()->get('select_categories');
                if ($formCategories==null){
                    $formCategories=[];
                }
                $this->manageCategories($formCategories, $content->id);
            }
        });
        $contentId = request()->route('content');
        if ($contentId) {
            $content = Content::findOrFail($contentId);
            if ($content->content_type == ContentTypeEnum::QURANIC_VERSE ||$content->content_type == ContentTypeEnum::STORY||$content->content_type == ContentTypeEnum::SADAQAH ||$content->content_type == ContentTypeEnum::CASH_WAQF ) {
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableDelete();
                });
            }
        }

        return $form;
    }

    protected function manageCategories(array $formCategories, int $contentId)
    {
        if ($formCategories != null) {
            foreach ($formCategories as $categoryId) {
                if ($categoryId != null) {
                    $taggedCategory = new TaggedCategory([
                        'category_id' => $categoryId,
                        'parentable_type' => Content::class,
                        'parentable_id' => $contentId,
                    ]);

                    $taggedCategory->save();
                }
            }
        }
    }
}
//Admin::js('js/mutationObserver.js');
