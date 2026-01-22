import './library/jquery.countTo.js';
import './library/jquery.appear.js';

const timer = $('.timer');
if(timer.length) {
    timer.appear(function () {
        timer.countTo();
    })
}
