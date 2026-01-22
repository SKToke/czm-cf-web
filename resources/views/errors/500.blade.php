<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CZM | Not Found</title>
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css?family=Maven+Pro:400,900" rel="stylesheet">
    </head>
    <body>
        <x-header/>
        <x-navbar/>
        <div id="flash-messages"></div>
        <main>
            <div id="notfound">
                <div class="notfound">
                    <div class="notfound-404">
                        <h1>500</h1>
                    </div>
                    <div class="mb-40">
                        <h2 class="d-none d-sm-block">There have been some problem</h2>
                        <h2 class="d-sm-none d-block mb-40">There have been <br>some problem!</h2>
                        <p class="d-none d-sm-block">Server error has been encountered. Contact site administrator for more information.</p>
                        <small class="text-black d-sm-none d-block">Server error has been encountered. Contact site administrator for more information.</small>
                    </div>
                    <a href="{{ route('home') }}"
                       class="czm-primary-btn">
                        Back To Homepage
                    </a>
                </div>
            </div>
        </main>
        <x-footer/>
    </body>
</html>
<style>
    * {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }


    #notfound {
        position: relative;
        height: 50vh;
    }

    #notfound .notfound {
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    .notfound {
        max-width: 920px;
        width: 100%;
        line-height: 1.4;
        text-align: center;
        padding-left: 15px;
        padding-right: 15px;
    }

    .notfound .notfound-404 {
        position: absolute;
        height: 100px;
        top: 0;
        left: 50%;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        transform: translateX(-50%);
        z-index: -1;
    }

    .notfound .notfound-404 h1 {
        font-family: 'Maven Pro', sans-serif;
        color: #ececec;
        font-weight: 900;
        font-size: 276px;
        margin: 0px;
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    .notfound h2 {
        font-family: 'Maven Pro', sans-serif;
        font-size: 46px;
        color: #000;
        font-weight: 900;
        text-transform: uppercase;
        margin: 0px;
    }

    .notfound p {
        font-family: 'Maven Pro', sans-serif;
        font-size: 16px;
        color: #000;
        font-weight: 400;
        text-transform: uppercase;
        margin-top: 15px;
    }

    .notfound a {
        font-family: 'Maven Pro', sans-serif;
        font-size: 12px;
        text-decoration: none;
        text-transform: uppercase;
        background: #25bada;
        display: inline-block;
        padding: 12px 19px;
        border: 2px solid transparent;
        border-radius: 40px;
        color: #fff;
        font-weight: 400;
        -webkit-transition: 0.2s all;
        transition: 0.2s all;
    }

    .notfound a:hover {
        background-color: #fff;
        border-color: #25bada;
        color: #0ebfe5;
    }

    @media only screen and (max-width: 480px) {
        .notfound .notfound-404 h1 {
            font-size: 162px;
        }
        .notfound h2 {
            font-size: 26px;
        }
    }
</style>
