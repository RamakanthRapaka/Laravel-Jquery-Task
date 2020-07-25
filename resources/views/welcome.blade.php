<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Items Management Page</title>
        <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    </head>

    <body>
        <!--        <div id="jquery-script-menu">
                    <div class="jquery-script-center">
        
                        <div class="jquery-script-clear"></div>
                    </div>
                </div>-->
        <div class="container">
            <h1 style="margin:50px auto 50px auto; text-align:center">Items Management Page</h1>

            <div class="alert alert-success alert-dismissible fade out">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            </div>

            <div class="row form-group">
                <form>
                    <div class="col-xs-3">
                        <input type="text" class="form-control" id="item_name" name="item_name" value="" placeholder="Enter Item Name and Click Add">
                    </div>
                    <div class="col-xs-2">
                        <input type="button" name="add_item" id="add_item" value="Add" class="btn btn-primary btn-block">
                    </div>
                </form>
                <div class="col-xs-7">
                    <h3 style="margin:0px auto 25px auto; text-align:center">Selected Items :</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-5">
                    <select name="from" id="undo_redo" class="form-control" size="13" multiple="multiple">

                    </select>
                </div>

                <div class="col-xs-2" style="margin:70px auto 25px auto; text-align:center">
                    <button type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                    <button type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                </div>

                <div class="col-xs-5">
                    <select name="to" id="undo_redo_to" class="form-control" size="13" multiple="multiple"></select>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="{{ asset('assets/js/multiselect.js') }}"></script>
        <script type="text/javascript">
var api_url = 'http://items.test/api/v1';
$(document).ready(function () {
    $('#undo_redo').multiselect();

    $.ajax({
        url: api_url + '/itmeslist',
        method: 'POST',
        success: function (data) {
            if (data.status_code == '200') {
                $('#undo_redo').empty();
                $.each(data.data, function (k, v) {
                    if (v.category == 1) {
                        var markup = "<option value=" + v.id + ">" + v.item_name + "</option>";
                        $("#undo_redo").append(markup);
                    }

                    if (v.category == 2) {
                        var markups = "<option value=" + v.id + ">" + v.item_name + "</option>";
                        $("#undo_redo_to").append(markups);
                    }
                });
            }

            if (data.status_code == '422') {
                var errors = data.data;
                $.each(errors, function (key, val) {
                    $("#item_name #" + key + "_err").html(val);
                });

            }
        }
    });
});

$("#add_item").click(function () {
    $.ajax({
        url: api_url + '/saveorupdateitem',
        method: 'POST',
        data: {
            item_name: $("#item_name").val(),
        },
        success: function (data) {
            if (data.status_code == '201') {
                $('#item_name').val('');
                $('#undo_redo').empty();
                $.each(data.data, function (k, v) {
                    if (v.category == 1) {
                        var markup = "<option value=" + v.id + ">" + v.item_name + "</option>";
                        $("#undo_redo").append(markup);
                    }
                    $(this).prop('selected', false);
                });

                var validationerror = "<strong>Success! </strong>" + data.message;
                $(".alert").attr('class', 'alert alert-success alert-dismissible fade in');
                $(".alert").append(validationerror);
                $(".alert").fadeTo(2000, 500).slideUp(500, function () {
                    $(".alert").slideUp(500);
                });
            }

            if (data.status_code == '422') {
                var errors = data.data;
                $.each(errors, function (key, val) {
                    $(".alert").empty();
                    $("#item_name #" + key + "_err").html(val);

                    var validationerror = "<strong>Error! </strong>" + val;
                    $(".alert").attr('class', 'alert alert-danger alert-dismissible fade in');
                    $(".alert").append(validationerror);
                    $(".alert").fadeTo(2000, 500).slideUp(500, function () {
                        $(".alert").slideUp(500);
                    });

                });

            }
        }
    });
});
        </script>
    </body>
</html>
