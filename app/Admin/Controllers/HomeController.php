<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Layout\Column;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->css_file(Admin::asset("open-admin/css/pages/dashboard.css"))
            ->css_file(url('/custom-admin/dashboard.css'))
            ->row(CustomDashboard::title())
            ->row(function (Row $row) {
                $row->column(2, function (Column $column) {
                    $column->append(CustomDashboard::users());
                });

                $row->column(2, function (Column $column) {
                    $column->append(CustomDashboard::donors());
                });

                $row->column(2, function (Column $column) {
                    $column->append(CustomDashboard::programs());
                });

                $row->column(2, function (Column $column) {
                    $column->append(CustomDashboard::campaigns());
                });

                $row->column(2, function (Column $column) {
                    $column->append(CustomDashboard::recurringSubscriptions());
                });

                $row->column(2, function (Column $column) {
                    $column->append(CustomDashboard::atRiskSubscriptions(3));
                });
            })
            ->row(CustomDashboard::todaysUpdates())
            ->row(CustomDashboard::extraVisitSection());
    }
}
