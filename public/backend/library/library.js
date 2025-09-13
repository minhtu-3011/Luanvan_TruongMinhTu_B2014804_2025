(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr("content");

    HT.switchery = () => {
        $(".js-switch").each(function () {
            var switchery = new Switchery(this, { color: "#1AB394" });
        });
    };

    HT.changeStautus = () => {
        $(document).on("change", ".status", function () {
            let _this = $(this);
            let option = {
                value: _this.val(),
                modelId: _this.attr("data-modelId"),
                model: _this.attr("data-model"),
                field: _this.attr("data-field"),
                _token: _token,
            };

            $.ajax({
                url: "/ajax/dashboard/changeStatus",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                    let inputValue = option.value == 1 ? 0 : 1;
                    if (res.flag == true) {
                        _this.val(inputValue);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("Lỗi: " + textStatus + " - " + errorThrown);
                },
            });
        });
    };

    HT.checkAll = () => {
        if ($("#checkAll").length) {
            // Chọn tất cả
            $(document).on("click", "#checkAll", function () {
                let checkAllState = $(this).prop("checked");
                $(".checkboxItem")
                    .prop("checked", checkAllState)
                    .trigger("change");
            });

            // Check/uncheck từng item
            $(document).on("change", ".checkboxItem", function () {
                let _this = $(this);
                let row = _this.closest("tr");

                if (_this.is(":checked")) {
                    row.addClass("active-bg");
                } else {
                    row.removeClass("active-bg");
                }
            });
        }
    };

    HT.changeStatusAll = () => {
        if ($(".changeStatusAll").length) {
            $(document).on("click", ".changeStatusAll", function (e) {
                let _this = $(this);
                let id = [];
                $(".checkboxItem").each(function () {
                    let checkBox = $(this);
                    if (checkBox.prop("checked")) {
                        id.push(checkBox.val());
                    }
                });
                console.log(id);

                let option = {
                    value: _this.attr("data-value"),
                    model: _this.attr("data-model"),
                    field: _this.attr("data-field"),
                    id: id,
                    _token: _token,
                };

                $.ajax({
                    url: "/ajax/dashboard/changeStatusAll",
                    type: "POST",
                    data: option,
                    dataType: "json",
                    success: function (res) {
                        if (res.flag == true) {
                            if (res.flag == true) {
                                let cssActive1 =
                                    "background-color: rgb(26, 179, 148); border-color: rgb(26, 179, 148); box-shadow: rgb(26, 179, 148) 0px 0px 0px 16px inset; transition: border 0.4s, box-shadow 0.4s, background-color 1.2s;";
                                let cssActive2 =
                                    "left: 20px; background-color: rgb(255, 255, 255); transition: background-color 0.4s, left 0.2s;";
                                let cssUnActive =
                                    "box-shadow: rgb(223, 223, 223) 0px 0px 0px 0px inset; border-color: rgb(223, 223, 223); background-color: rgb(255, 255, 255); transition: border 0.4s, box-shadow 0.4s;";
                                let cssUnActive2 =
                                    'style="left: 0px; transition: background-color 0.4s, left 0.2s;"';
                                if (option.value == 1) {
                                    for (let i = 0; i < id.length; i++) {
                                        if (option.value == 1) {
                                            $(".js-switch-" + id[i])
                                                .find("span.switchery")
                                                .attr("style", cssActive1);

                                            $(".js-switch-" + id[i])
                                                .find("small")
                                                .attr("style", cssActive2);
                                        }
                                    }
                                } else if (option.value == 0) {
                                    for (let i = 0; i < id.length; i++) {
                                        if (option.value == 0) {
                                            $(".js-switch-" + id[i])
                                                .find("span.switchery")
                                                .attr("style", cssUnActive);

                                            $(".js-switch-" + id[i])
                                                .find("small")
                                                .attr("style", cssUnActive2);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("Lỗi: " + textStatus + " - " + errorThrown);
                    },
                });

                e.preventDefault();
            });
        }
    };

    $(document).ready(function () {
        HT.switchery();
        HT.changeStautus();
        HT.checkAll();
        HT.changeStatusAll();
    });
})(jQuery);

{
    /* <script>
    $(document).ready(function () {
        var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, { color: '#1AB394' });
    })
</script> */
}
