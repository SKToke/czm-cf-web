<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>File Too Large</title>
    </head>
    <body>
        <div class="container">
            <div class="alert alert-danger mt-5">
                <h2>File Too Large</h2>
                <p>The file you tried to upload is too large. Maximum size exceeded.</p>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">Go Back to Form</a>
            </div>
        </div>
    </body>
</html>
