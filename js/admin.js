$(document).ready(function () {
    var designerDataTable = "";
    var edit_id = "";

    $.ajax({
        type: "POST",
        url: "admin_control.php",
        data: {
            submitType: "load_info",
        },
        dataType: "html",
        error: function (request) {
            console.log(request.responseText);
        },
        success: function (resp) {
            $("#designerTable").html(resp);
            designerDataTable = $('#designer-table').DataTable();
        }
    });

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
    });

    //Add designer form submit
    $("#add_designer").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('submitType', 'add_designer');

        $.ajax({
            type: "POST",
            url: "admin_control.php",
            data: formData,
            dataType: "html",
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                designerDataTable.destroy();
                $("#designerTable").html(resp);
                designerDataTable = $('#designer-table').DataTable();
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    //Delete designer submit
    $("#designerTable").on('click', '.deleteDesigner', function () {
        var data = {
            submitType: "delete_designer",
            id: this.getAttribute("data-delete")
        }

        $.ajax({
            type: "POST",
            url: "admin_control.php",
            data: data,
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                if ($(".tableRow").filter("[data-designer=" + resp + "]").next().hasClass("child") == true) {
                    $(".tableRow").filter("[data-designer=" + resp + "]").next().remove();
                    $(".tableRow").filter("[data-designer=" + resp + "]").remove();
                } else {
                    $(".tableRow").filter("[data-designer=" + resp + "]").remove();
                }
            }
        });
    });

    //Reset Available Hour when it is depleted
    $("#designerTable").on('click', '.resetHour', function () {
        var data = {
            submitType: "reset_hour",
            id: this.getAttribute("data-resethour")
        }

        $.ajax({
            type: "POST",
            url: "admin_control.php",
            data: data,
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                if ($("button[data-resethour=" + resp + "]").next().hasClass("hour-reset-notice") == true) {
                    $("button[data-resethour=" + resp + "]").next().remove();
                }
            }
        });
    });

    //Grab previous values for edit   
    $("#designerTable").on('click', '.editDesigner', function () {
        var data = {
            submitType: "edit_designer",
            id: this.getAttribute("data-edit")
        }

        $.ajax({
            type: "POST",
            url: "admin_control.php",
            data: data,
            dataType: "json",
            error: function (request) {
                console.log(request.responseText);
            },
            success: function (resp) {
                var wLink = JSON.parse(resp["designer_work"]);
                $('input[name=editFName]').val(resp["designer_Fname"]);
                $('input[name=editLName]').val(resp["designer_Lname"]);
                $('textarea[name=editBio]').val(resp["designer_bio"]);
                $('input[name=editHours]').val(resp["designer_hours"]);
                $('input[name=editPrice]').val(resp["designer_price"]);
                $("#prevProfilePic").html(resp["designer_pic"].split(/(\\|\/)/g).pop())
                $("#prevWorkPic").html("");

                edit_id = resp["id"];

                for (var i = 0; i < wLink.length; i++) {
                    $("#prevWorkPic").append(wLink[i].split(/(\\|\/)/g).pop() + "<br/>");
                }
            }
        });
    });

    //Edit designer form submit
    $("#edit_designer").submit(function (e) {
        e.preventDefault();
        if (edit_id != "") {
            var formData = new FormData(this);
            formData.append('submitType', 'edit_designer_submit');
            formData.append('designer_id', edit_id);
            edit_id = "";

            $.ajax({
                type: "POST",
                url: "admin_control.php",
                dataType: "html",
                data: formData,
                error: function (request) {
                    console.log(request.responseText);
                },
                success: function (resp) {
                    designerDataTable.destroy();
                    $("#designerTable").html(resp);
                    designerDataTable = $('#designer-table').DataTable();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }
    })
});