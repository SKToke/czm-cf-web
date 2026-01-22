<?php

namespace App\Admin\Controllers;

use App\Enums\ReportTypeEnum;
use App\Models\Program;
use App\Models\Report;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class ReportsController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Reports';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Report());
        $this->modifyGrid($grid);


        $grid->column('report_type')->display(function ($status) {
            return ReportTypeEnum::from($status)->getTitle();
        });

        $grid->column('download', __('Download'))->display(function ($value, $column) {
            $programs = Program::all()->pluck('title');

            if($this->report_type==ReportTypeEnum::Amount_Paid_By_Month->value){
                return '<form action="' . route('monthly-payment-report', $this->id) . '" method="POST">'
                    . csrf_field()
                    . '<div class="row">'
                    . '<div class="col-10">'
                    . '</div>'
                    . '<div class="col-2">'
                    . '<div class="form-check form-switch mb-2">'
                    . '<input class="form-check-input" type="checkbox" name="downloadType" id="downloadType">'
                    . '<label class="form-check-label" for="downloadType">as PDF</label>'
                    . '</div>'
                    . '<button type="submit" class="btn btn-sm btn-primary">Download</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            }
            if($this->report_type==ReportTypeEnum::Amount_Paid_By_Project->value){
                $form = '<form action="' . route('monthly-payment-report-by-project', $this->id) . '" method="POST">'
                    . csrf_field()
                    . '<div class="row">'
                    . '<div class="col-10 mt-3">'
                    . '<select name="program" class="me-2">'
                    . '<option value="">Select Program</option>';
                foreach($programs as $program) {
                    $form = $form . '<option value=' .$program . '>' . $program . '</option>';
                }
                $form = $form . '</select>'
                    . '</div>'
                    . '<div class="col-2">'
                    . '<div class="form-check form-switch mb-2">'
                    . '<input class="form-check-input" type="checkbox" name="downloadType" id="downloadType">'
                    . '<label class="form-check-label" for="downloadType">as PDF</label>'
                    . '</div>'
                    . '<button type="submit" class="btn btn-sm btn-primary">Download</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';

                return $form;
            }
            if($this->report_type==ReportTypeEnum::Disbursement_Report->value){
                return '<form action="' . route('disbursement-report', $this->id) . '" method="POST">'
                    . csrf_field()
                    . '<div class="row">'
                    . '<div class="col-10">'
                    . '</div>'
                    . '<div class="col-2">'
                    . '<div class="form-check form-switch mb-2">'
                    . '<input class="form-check-input" type="checkbox" name="downloadType" id="downloadType">'
                    . '<label class="form-check-label" for="downloadType">as PDF</label>'
                    . '</div>'
                    . '<button type="submit" class="btn btn-sm btn-primary">Download</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            }
            if($this->report_type==ReportTypeEnum::Disbursement_Report_By_Project->value){
                $form = '<form action="' . route('disbursement-report-by-project', $this->id) . '" method="POST">'
                    . csrf_field()
                    . '<div class="row">'
                    . '<div class="col-10 mt-3">'
                    . '<select name="program" class="me-2">'
                    . '<option value="">Select Program</option>';

                foreach($programs as $program) {
                    $form = $form . '<option value=' .$program . '>' . $program . '</option>';
                }
                $form = $form . '</select>'
                    . '</div>'
                    . '<div class="col-2">'
                    . '<div class="form-check form-switch mb-2">'
                    . '<input class="form-check-input" type="checkbox" name="downloadType" id="downloadType">'
                    . '<label class="form-check-label" for="downloadType">as PDF</label>'
                    . '</div>'
                    . '<button type="submit" class="btn btn-sm btn-primary">Download</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';

                return $form;
            }

            $form = '<form action="' . route('report-download', $this->id) . '" method="POST">'
                . csrf_field()
                . '<div class="row">'
                . '<div class="col-8 mt-3">'
                . '<div class="row">'
                . '<div class="col-4">';

            if($this->report_type == ReportTypeEnum::Transaction->value || $this->report_type == ReportTypeEnum::Campaign->value){
                $form = $form
                    . '<select name="program" class="me-2">'
                    . '<option value="">Select Program</option>';
                foreach($programs as $program) {
                    $form = $form . '<option value=' .$program . '>' . $program . '</option>';
                }
                $form = $form . '</select>';
            }

            $form = $form
                . '</div>'
                . '<div class="col-8">'
                . '<label for="start_date">Start Date:</label>'
                . '<input type="date" id="start_date" name="start_date" style="margin-right: 10px;">'
                . '<label for="end_date">End Date:</label>'
                . '<input type="date" id="end_date" name="end_date">'
                . '</div>'
                . '</div>'
                . '</div>'
                . '<div class="col-2">'
                . '</div>'
                . '<div class="col-2">'
                . '<div class="form-check form-switch mb-2">'
                . '<input class="form-check-input" type="checkbox" name="downloadType" id="downloadType">'
                . '<label class="form-check-label" for="downloadType">as PDF</label>'
                . '</div>'
                . '<button type="submit" class="btn btn-sm btn-primary">Download</button>'
                . '</form>'
                . '</div>'
                . '</div>';
            return $form;
        });

        $grid->disableCreateButton();
        $grid->disableActions();

        $grid->disableFilter();

        return $grid;
    }


    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
}
