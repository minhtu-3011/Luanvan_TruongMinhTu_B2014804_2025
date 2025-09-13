(function ($) {
    "use strict";
    var HT = {};

    // Lắng nghe sự kiện thay đổi location
    HT.getLocation = () => {
        $(document).on("change", ".location", function () {
            let _this = $(this);

            let option = {
                data: {
                    location_id: _this.val(),
                },
                target: _this.attr("data-target"),
            };

            HT.sendDataToGetLocation(option);
        });
    };

    // Gửi request để lấy dữ liệu location
    HT.sendDataToGetLocation = (option, callback = null) => {
        $.ajax({
            url: "/ajax/location/getLocation",
            type: "GET",
            data: option,
            dataType: "json",
            success: function (res) {
                $("." + option.target).html(res.html);

                // Gọi callback nếu có (ví dụ: set district sau khi load xong)
                if (typeof callback === "function") callback();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Lỗi: " + textStatus + " - " + errorThrown);
            },
        });
    };

    // Hàm load dữ liệu khi edit
    HT.initEditLocation = () => {
        if (province_id) {
            // Set province
            $(".province").val(province_id).trigger("change");

            // Load districts
            HT.sendDataToGetLocation(
                { data: { location_id: province_id }, target: "districts" },
                function () {
                    if (district_id) {
                        $(".districts").val(district_id).trigger("change");

                        // Load wards
                        HT.sendDataToGetLocation(
                            {
                                data: { location_id: district_id },
                                target: "wards",
                            },
                            function () {
                                if (ward_id) {
                                    $(".wards").val(ward_id);
                                }
                            }
                        );
                    }
                }
            );
        }
    };

    $(document).ready(function () {
        HT.getLocation();
        HT.initEditLocation();
    });
})(jQuery);
