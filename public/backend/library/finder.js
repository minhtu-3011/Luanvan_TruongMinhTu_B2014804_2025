(function ($) {
    "use strict";
    var HT = {};

    HT.setupCkeditor = () => {
        if ($(".ck-editor")) {
            $(".ck-editor").each(function () {
                let editor = $(this);
                let elementId = editor.attr("id");
                let elementHeight = editor.attr("data-height");
                HT.ckeditor4(elementId);
            });
        }
    };

    HT.ckeditor4 = (elementId, elementHeight) => {
        if (typeof elementHeight == "undefined") {
            elementHeight = 500;
        }
        CKEDITOR.replace(elementId, {
            height: elementHeight,
            removeButtons: "",
            entities: true,
            allowedContent: true,
            toolbarGroups: [
                { name: "clipboard", groups: ["clipboard", "undo"] },
                {
                    name: "editing",
                    groups: ["find", "selection", "spellchecker"],
                },
                { name: "links" },
                { name: "insert" },
                { name: "forms" },
                { name: "tools" },
                {
                    name: "document",
                    groups: ["mode", "document", "doctools"],
                },
                { name: "colors" },
                { name: "others" },
                "/",
                { name: "basicstyles", groups: ["basicstyles", "cleanup"] },
                {
                    name: "paragraph",
                    groups: ["list", "indent", "blocks", "align", "bidi"],
                },
                { name: "styles" },
            ],
        });
    };

    HT.uploadImageAvatar = () => {
        $(".image-target").click(function () {
            let input = $(this);
            let type = "Images";
            HT.browseServerAvatar(input, type);
        });
    };

    HT.browseServerAvatar = (object) => {
        let type = "Images"; // Mặc định là Images
        CKFinder.popup({
            resourceType: type,
            chooseFiles: true,
            onInit: function (finder) {
                finder.on("files:choose", function (evt) {
                    var file = evt.data.files.first();
                    if (file) {
                        var fileUrl = file.getUrl();
                        // Cập nhật ảnh hiển thị
                        object.find("img").attr("src", fileUrl);
                        // Cập nhật input ẩn (giả sử input nằm cùng form-row)
                        object.siblings('input[type="hidden"]').val(fileUrl);
                    }
                });

                finder.on("file:choose:resizedImage", function (evt) {
                    var fileUrl = evt.data.resizedUrl;
                    object.find("img").attr("src", fileUrl);
                    object.siblings('input[type="hidden"]').val(fileUrl);
                });
            },
        });
    };

    HT.uploadImageToInput = () => {
        $(".upload-image").click(function () {
            let input = $(this);
            let type = input.attr("data-type");
            HT.openCkFinder(input, type);
        });
    };

    HT.openCkFinder = (object, type) => {
        if (typeof type === "undefined") {
            type = "Images";
        }
        CKFinder.popup({
            resourceType: type,
            chooseFiles: true,
            onInit: function (finder) {
                finder.on("files:choose", function (evt) {
                    var file = evt.data.files.first();
                    object.val(file.getUrl()); // <-- Đây là URL ảnh
                });
                finder.on("file:choose:resizedImage", function (evt) {
                    console.log(evt.data.resizedUrl);
                });
            },
        });
    };

    $(document).ready(function () {
        HT.uploadImageToInput();
        HT.setupCkeditor();
        HT.uploadImageAvatar();
    });
})(jQuery);
