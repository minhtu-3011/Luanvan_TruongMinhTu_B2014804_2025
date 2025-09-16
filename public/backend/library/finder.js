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

    HT.multipleUploadImageCkeditor = () => {
        // $(document).on("click", ".multipleUploadImageCkeditor", function (e) {
        //     let object = $(this);
        //     HT.browseServerCkeditor(object, "Images");
        //     e.preventDefault();
        // });
        $(document).on("click", ".multipleUploadImageCkeditor", function (e) {
            e.preventDefault();
            let editorId = $(this).data("target"); // lấy id từ data-target
            HT.browseServerCkeditor(editorId, "Images");
        });
    };

    HT.uploadAlbum = () => {
        $(document).on("click", ".upload-picture", function (e) {
            HT.browseServerAlbum();
            e.preventDefault();
        });
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

    HT.browseServerCkeditor = (editorId, type = "Images") => {
        CKFinder.popup({
            resourceType: type,
            chooseFiles: true,
            onInit: function (finder) {
                finder.on("files:choose", function (evt) {
                    evt.data.files.forEach(function (file) {
                        var fileUrl = file.getUrl();

                        let html = `
                        <figure class="image-box">
                            <img src="${fileUrl}" alt="">
                            <figcaption>Nhập chú thích...</figcaption>
                        </figure>
                        <p>&nbsp;</p>
                    `;

                        CKEDITOR.instances[editorId].insertHtml(html);
                    });
                });

                finder.on("file:choose:resizedImage", function (evt) {
                    var fileUrl = evt.data.resizedUrl;

                    let html = `
                    <figure class="image-box">
                        <img src="${fileUrl}" alt="">
                        <figcaption>Nhập chú thích...</figcaption>
                    </figure>
                    <p>&nbsp;</p>
                `;

                    CKEDITOR.instances[editorId].insertHtml(html);
                });
            },
        });
    };

    HT.browseServerAlbum = () => {
        CKFinder.popup({
            resourceType: "Images",
            chooseFiles: true,
            onInit: function (finder) {
                finder.on("files:choose", function (evt) {
                    let html = "";
                    evt.data.files.forEach(function (file) {
                        let fileUrl = file.getUrl();
                        html += '<li class="ui-state-default">';
                        html += '  <div class="thumb">';
                        html += '    <span class="span image img-scaledown">';
                        html +=
                            '      <img src="' +
                            fileUrl +
                            '" alt="' +
                            fileUrl +
                            '">';
                        html +=
                            '      <input type="hidden" name="album[]" value="' +
                            fileUrl +
                            '">';
                        html += "    </span>";
                        html +=
                            '    <button class="delete-image"><i class="fa fa-trash"></i></button>';
                        html += "  </div>";
                        html += "</li>";
                    });

                    $(".click-to-upload").addClass("hidden");
                    $("#sortable").append(html);
                    $(".upload-list").removeClass("hidden");
                });
            },
        });
    };

    HT.deletePicture = () => {
        $(document).on("click", ".delete-image", function () {
            let _this = $(this);
            _this.parents(".ui-state-default").remove();

            if ($(".ui-state-default").length == 0) {
                $(".click-to-upload").removeClass("hidden");
                $(".upload-list").addClass("hidden");
            }
        });
    };

    $(document).ready(function () {
        HT.uploadImageToInput();
        HT.setupCkeditor();
        HT.uploadImageAvatar();
        HT.multipleUploadImageCkeditor();
        HT.uploadAlbum();
        HT.deletePicture();
    });
})(jQuery);
