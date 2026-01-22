<?php

namespace App\Admin\Controllers;

use App\Models\ContentSection;
use Illuminate\Http\Request;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Category;
use \App\Models\Content;

class ContentSectionController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ContentSection';

    public function customDestroy(Request $request)
    {
        $contentSectionId = $request->contentSectionId;
        $contentSection = ContentSection::find($contentSectionId);
        $contentId = $contentSection->content_id;
        $contentSection->deleted_at = now();
        $contentSection->save();

        return redirect()->route('admin.contents.show', $contentId);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ContentSection());
        $this->modifyGrid($grid);

        $grid->column('title', __('Section Title'));
        $grid->column('description', __('Section description'));
        $grid->column('position', __('Section position'));

        $grid->disableActions();
        $grid->disableFilter();
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
        $show = new Show(ContentSection::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ContentSection());
        $this->modifyForm($form);

        $contentId = Request()->query('content_id');

        if ($contentId) {
            $form->select('content_id', __('Content'))->options(Content::all()->pluck('name', 'id'))->default($contentId)->readonly();
        }

        $form->text('title', __('Section Title'))->required()->help('Maximum 240 characters')->rules('min:3|max:240');
        $form->ckeditor('description', __('Section Description'))->required()->rules([new \App\Rules\NoSpacesInField(true)]);
        $form->number('position')->rules('required|numeric|min:0|max:999');
        $form->image('image',__('image'))->removable()->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');

        $form->saved(function (Form $form) {
            $contentSection = $form->model();
            if ($form->content_id) {
                $contentSection->content_id = $form->content_id;
            }
            $contentSection->save();
            return redirect()->route('admin.contents.show', $contentSection->content_id);
        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableView();
            $tools->disableDelete();
        });
        $form->disableViewCheck();

        return $form;
    }
}
