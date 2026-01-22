<?php

/**
 * Open-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * OpenAdmin\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * OpenAdmin\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */
use OpenAdmin\Admin\Facades\Admin;

OpenAdmin\Admin\Form::forget(['editor']);

Admin::navbar(function (\OpenAdmin\Admin\Widgets\Navbar $navbar) {
    if (Admin::user()) {
        $adminEmail = Admin::user()->email;
        $navbar->right(<<<HTML
            <button type="button" class="btn btn-outline-dark me-2">$adminEmail</button>
        HTML);
    }
    $navbar->right(new \App\Admin\Extensions\Nav\Links());

});

Admin::style('
.in-active {
    pointer-events: none;
    opacity: 0.5;
}
.red-asterisk::before {
    content: "* ";
    color: red;
}
.card {
    .filter-box form .col-md-6 .card-body .fields-group .form-group.row {
        label.col-sm-2.form-label,
        label.col-sm-2.control-label{
            width: 24%;
        }
        label.col-sm-2.control-label{
            text-align: right;
        }
        div.col-sm-8 {
            width: 70%;
        }
    }
}
.chart-container {
    width: 100%;
    height: auto;
    display: flex;
    justify-content: center;
    align-items: center;
}
canvas {
    max-width: 100%;
    height: 400px;
}
');

Admin::script('
    var formGroups = document.querySelectorAll(".form-group");
    formGroups.forEach(function(group) {
        if (group.querySelector(".custom-required") || group.querySelector(".custom-required-two") || group.querySelector(".custom-required-three")) {
            var label = group.querySelector(".form-label");
            if (label) {
                label.classList.add("red-asterisk");
            }
        }
    });
    function disableButtonsForNewImages() {
        document.querySelectorAll(".files.new .icon-trash, .files.new .icon-download").forEach(function(button) {
            button.classList.add("in-active");
            button.addEventListener("click", function(event) {
                event.preventDefault();
            });
        });
    }

    disableButtonsForNewImages();

    document.querySelectorAll(".custom-multiple-images").forEach(function(input) {
        input.addEventListener("change", function () {
            setTimeout(disableButtonsForNewImages, 100);
        });
    });
');
Admin::js('js/ckeditor.js');
