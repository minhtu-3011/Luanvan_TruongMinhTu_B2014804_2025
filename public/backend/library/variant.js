(function ($) {
    "use strict";
    var HT = {};

    HT.switchery = () => {
        $(".js-switch").each(function () {
            var switchery = new Switchery(this, { color: "#1AB394" });
        });
    };

    HT.setupProductVariant = () => {
        if ($(".turnOnVariant").length) {
            $(document).on("click", ".turnOnVariant", function () {
                let _this = $(this);
                if (_this.siblings("input:checked").length == 0) {
                    $(".variant-wrapper").removeClass("hidden");
                    let price = $("input[name=price]").val();
                    let code = $("input[name=code]").val();
                    if (price == "" || code == "") {
                        alert(
                            "Bạn phải nhập vào Giá và Mã sản phẩm để sử dụng chức năng này"
                        );
                        return false;
                    }
                } else {
                    $(".variant-wrapper").addClass("hidden");
                }
            });
        }
    };

    HT.addVariant = () => {
        if ($(".add-variant").length) {
            $(document).on("click", ".add-variant", function () {
                let html = HT.renderVariantItem(attributeCatalogue);
                $(".variant-body").append(html);
                $(".variantTable thead").html("");
                $(".variantTable tbody").html("");
                HT.checkMaxAttributeGroup(attributeCatalogue);
                HT.disabledAttributeCatalogueChoose();
                $(".variant-body .choose-attribute").last().select2();
            });
        }
    };

    HT.renderVariantItem = (attributeCatalogue) => {
        let html = "";

        html += '<div class="row mb20 variant-item">';
        html += '<div class="col-lg-3">';
        html += '<div class="attribute-catalogue">';
        html +=
            '<select name="attributeCatalogue[]" id="" class="choose-attribute select2">';
        html += '<option value="0">Chọn Nhóm thuộc tính</option>';
        for (let i = 0; i < attributeCatalogue.length; i++) {
            html +=
                '<option value="' +
                attributeCatalogue[i].id +
                '">' +
                attributeCatalogue[i].name +
                "</option>";
        }

        html += "</select>";
        html += "</div>";
        html += "</div>";

        html += '<div class="col-lg-8">';
        html +=
            '<input type="text" name="" disabled class="fake-variant form-control">';
        html += "</div>";

        html += '<div class="col-lg-1">';
        html +=
            '<button type="button" class="remove-attribute btn btn-danger">';
        html +=
            '<svg data-icon="TrashSolidLarge" aria-hidden="true" focusable="false" width="15" height="16" viewBox="0 0 15 16" class="bem-Svg" style="display: block;">';
        html +=
            '<path fill="currentColor" d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H2v2h12V2z"></path>';
        html += "</svg>";
        html += "</button>";
        html += "</div>";
        html += "</div>";

        return html;
    };

    HT.chooseVariantGroup = () => {
        $(document).on("change", ".choose-attribute", function () {
            let _this = $(this);
            let attributeCatalogueId = _this.val();
            if (attributeCatalogueId != 0) {
                _this
                    .parents(".col-lg-3")
                    .siblings(".col-lg-8")
                    .html(HT.select2Variant(attributeCatalogueId));

                $(".selectVariant").each(function (key, index) {
                    HT.getSelect2($(this));
                });
            } else {
                _this
                    .parents(".col-lg-3")
                    .siblings(".col-lg-8")
                    .html(
                        '<input type="text" name="attribute[' +
                            attributeCatalogueId +
                            '][]" disabled="" class="fake-variant form-control">'
                    );
            }

            HT.disabledAttributeCatalogueChoose();
        });
    };

    HT.getSelect2 = (object) => {
        let option = {
            attributeCatalogueId: object.attr("data-catId"),
        };

        $(object).select2({
            minimumInputLength: 2,
            placeholder: "Nhập tối thiểu 2 kí tự để tìm kiếm",
            ajax: {
                url: "ajax/attribute/getAttribute",
                type: "GET",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        option: option,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (obj, i) {
                            return obj;
                        }),
                    };
                },
                cache: true,
            },
        });
    };

    HT.createProductVariant = () => {
        $(document).on("change", ".selectVariant", function () {
            let _this = $(this);
            HT.createVariant();
        });
    };

    HT.createVariant = () => {
        let attributes = [];
        let attributeTitle = [];
        let variants = [];
        $(".variant-item").each(function () {
            let _this = $(this);
            let attr = [];
            let attrVariant = [];
            let attributeCatalogueId = _this.find(".choose-attribute").val();
            let optionText = _this
                .find(".choose-attribute option:selected")
                .text();
            let attribute = $(".variant-" + attributeCatalogueId).select2(
                "data"
            );

            for (let i = 0; i < attribute.length; i++) {
                let item = {};
                let itemVariant = {};
                item[optionText] = attribute[i].text;
                itemVariant[attributeCatalogueId] = attribute[i].id;
                attr.push(item);
                attrVariant.push(itemVariant);
            }
            attributeTitle.push(optionText);
            attributes.push(attr);
            variants.push(attrVariant);
        });
        attributes = attributes.reduce((a, b) =>
            a.flatMap((d) => b.map((e) => ({ ...d, ...e })))
        );

        variants = variants.reduce((a, b) =>
            a.flatMap((d) => b.map((e) => ({ ...d, ...e })))
        );

        HT.createTableHeader(attributeTitle);

        let trClass = [];
        attributes.forEach((item, index) => {
            let $row = HT.createVariantRow(item, variants[index]);
            let classModified =
                "tr-variant-" +
                Object.values(variants[index]).join(", ").replace(/, /g, "-");
            trClass.push(classModified);
            if (!$("table.variantTable tbody tr").hasClass(classModified)) {
                $("table.variantTable tbody").append($row);
            }
        });

        $("table.variantTable tbody tr").each(function () {
            const $row = $(this);
            const rowClasses = $row.attr("class");
            if (rowClasses) {
                const rowClassArray = rowClasses.split(" ");
                let shouldRemove = false;
                rowClassArray.forEach((rowClass) => {
                    if (rowClass == "variant-row") {
                        return;
                    } else if (!trClass.includes(rowClass)) {
                        shouldRemove = true;
                    }
                });
                if (shouldRemove) {
                    $row.remove();
                }
            }
        });

        // let html = HT.renderTableHtml(attributes, attributeTitle, variants);
        // $("table.variantTable").html(html);
    };

    HT.createVariantRow = (attributeItem, variantItem) => {
        let attributeString = Object.values(attributeItem).join(", ");
        let attributeId = Object.values(variantItem).join(", ");
        let classModified = attributeId.replace(/, /g, "-");

        let $row = $("<tr>").addClass(
            "variant-row tr-variant-" + classModified
        );
        let $td;

        $td = $("<td>").append(
            $("<span>")
                .addClass("image")
                .append(
                    $("<img>")
                        .attr(
                            "src",
                            "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRXr5jCWfK1B-jMiMyRtfD6wNi3H6uMWD4vTQ&s"
                        )
                        .addClass("imageSrc img-cover")
                )
        );

        $row.append($td);

        Object.values(attributeItem).forEach((value) => {
            $td = $("<td>").text(value);
            $row.append($td);
        });

        let mainPrice = $("input[name=price]").val();
        let mainSku = $("input[name=code]").val();
        $td = $("<td>").addClass("hidden td-variant");
        let inputHiddenFields = [
            { name: "variant[quantity][]", class: "variant_quantity" },
            {
                name: "variant[sku][]",
                class: "variant_sku",
                value: mainSku + "-" + classModified,
            },
            {
                name: "variant[price][]",
                class: "variant_price",
                value: mainPrice,
            },
            { name: "variant[barcode][]", class: "variant_barcode" },
            { name: "variant[file_name][]", class: "variant_filename" },
            { name: "variant[file_url][]", class: "variant_fileurl" },
            { name: "variant[album][]", class: "variant_album" },
            { name: "productVariant[name][]", value: attributeString },
            { name: "productVariant[id][]", value: attributeId },
        ];

        $.each(inputHiddenFields, function (_, field) {
            let $input = $("<input>")
                .attr("type", "text")
                .attr("name", field.name)
                .addClass(field.class);

            if (field.value) {
                $input.val(field.value);
            }

            $td.append($input);
        });

        $row.append($("<td>").addClass("td-quantity").text("-"))
            .append($("<td>").addClass("td-price").text(mainPrice))
            .append(
                $("<td>")
                    .addClass("td-sku")
                    .text(mainSku + "-" + classModified)
            )
            .append($td);

        return $row;
    };

    HT.createTableHeader = (attributeTitle) => {
        let $thead = $("table.variantTable thead");
        let $row = $("<tr>");
        $row.append($("<td>").text("Hình Ảnh"));
        for (let i = 0; i < attributeTitle.length; i++) {
            $row.append($("<td>").text(attributeTitle[i]));
        }
        $row.append($("<td>").text("Số lượng"));
        $row.append($("<td>").text("Giá tiền"));
        $row.append($("<td>").text("SKU"));

        $thead.html($row);
        return $thead;
    };

    // HT.renderTableHtml = (attributes, attributeTitle, variants) => {
    //     let html = "";

    //     // thead
    //     html += "<thead>";
    //     html += "<tr>";
    //     html += "<td>Hình ảnh</td>";
    //     for (let i = 0; i < attributeTitle.length; i++) {
    //         html += "<td>" + attributeTitle[i] + "</td>";
    //     }
    //     html += "<td>Số lượng</td>";
    //     html += "<td>Giá tiền</td>";
    //     html += "<td>SKU</td>";
    //     html += "</tr>";
    //     html += "</thead>";

    //     // tbody
    //     html += "<tbody>";

    //     for (let j = 0; j < attributes.length; j++) {
    //         html += '<tr class="variant-row">';
    //         html += "<td>";
    //         html += '<span class="image ">';
    //         html +=
    //             '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRXr5jCWfK1B-jMiMyRtfD6wNi3H6uMWD4vTQ&s" alt="" class="imageSrc img-cover">';
    //         html += "</span>";
    //         html += "</td>";

    //         let attributeArray = [];
    //         let attributeIdArray = [];
    //         $.each(attributes[j], function (index, value) {
    //             html = html + "<td>" + value + "</td>";
    //             attributeArray.push(value);
    //         });

    //         $.each(variants[j], function (index, value) {
    //             attributeIdArray.push(value);
    //         });

    //         let attributeString = attributeArray.join(", ");
    //         let attributeId = attributeIdArray.join(", ");

    //         html = html + '<td class="td-quantity"></td>';
    //         html = html + '<td class="td-price"></td>';
    //         html = html + '<td class="td-sku"></td>';
    //         html = html + '<td class="hidden td-variant">';

    //         html =
    //             html +
    //             '<input type="text" name="variant[quantity][]" class="variant_quantity">';
    //         html =
    //             html +
    //             '<input type="text" name="variant[sku][]" class="variant_sku">';
    //         html =
    //             html +
    //             '<input type="text" name="variant[price][]" class="variant_price">';
    //         html =
    //             html +
    //             '<input type="text" name="variant[barcode][]" class="variant_barcode">';
    //         html =
    //             html +
    //             '<input type="text" name="variant[file_name][]" class="variant_filename">';
    //         html =
    //             html +
    //             '<input type="text" name="variant[file_url][]" class="variant_fileurl">';
    //         html =
    //             html +
    //             '<input type="text" name="variant[album][]" class="variant_album">';
    //         html =
    //             html +
    //             '<input type="text" name="attribute[name][]" value="' +
    //             attributeString +
    //             '">';
    //         html =
    //             html +
    //             '<input type="text" name="attribute[id][]" value="' +
    //             attributeId +
    //             '">';

    //         html = html + "</td>";

    //         html += "</tr>";
    //     }

    //     html += "</tbody>";

    //     return html;
    // };

    HT.disabledAttributeCatalogueChoose = () => {
        let id = [];
        $(".choose-attribute").each(function () {
            let _this = $(this);
            let selected = _this.find("option:selected").val();
            if (selected && selected != 0) {
                id.push(selected);
            }
        });

        // reset lại
        $(".choose-attribute").find("option").prop("disabled", false);

        // disable các option đã chọn
        for (let i = 0; i < id.length; i++) {
            $(".choose-attribute")
                .find("option[value=" + id[i] + "]")
                .not(":selected") // giữ option đã chọn vẫn cho hiển thị ở select đó
                .prop("disabled", true);
        }
    };

    HT.checkMaxAttributeGroup = (attributeCatalogue) => {
        let variantItem = $(".variant-item").length;
        if (variantItem >= attributeCatalogue.length) {
            $(".add-variant").remove();
        } else {
            $(".variant-foot").html(
                '<button type="button" class="add-variant">Thêm phiên bản mới</button>'
            );
        }
    };

    HT.removeAttribute = () => {
        $(document).on("click", ".remove-attribute", function () {
            let _this = $(this);
            _this.parents(".variant-item").remove();
            HT.checkMaxAttributeGroup(attributeCatalogue);
            HT.createVariant();
        });
    };

    HT.select2Variant = (attributeCatalogueId) => {
        let html =
            '<select class="selectVariant variant-' +
            attributeCatalogueId +
            ' form-control" name="attribute[' +
            attributeCatalogueId +
            '][]" multiple data-catid="' +
            attributeCatalogueId +
            '"></select>';
        return html;
    };

    HT.variantAlbum = () => {
        $(document).on("click", ".click-to-upload-variant", function (e) {
            HT.browseServerAlbum();
            e.preventDefault();
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
                            '      <input type="hidden" name="variantAlbum[]" value="' +
                            fileUrl +
                            '">';
                        html += "    </span>";
                        html +=
                            '    <button class="variant-delete-image"><i class="fa fa-trash"></i></button>';
                        html += "  </div>";
                        html += "</li>";
                    });

                    $(".click-to-upload-variant").addClass("hidden");
                    $("#sortable2").append(html);
                    $(".upload-variant-list").removeClass("hidden");
                });
            },
        });
    };

    HT.deleteVariantAlbum = () => {
        $(document).on("click", ".variant-delete-image", function (e) {
            e.preventDefault(); // ngăn reload hoặc redirect

            // Xoá <li> cha chứa ảnh
            $(this).closest("li").remove();

            // Nếu không còn ảnh nào thì show lại nút upload
            if ($("#sortable2 li").length === 0) {
                $(".click-to-upload-variant").removeClass("hidden");
                $(".upload-variant-list").addClass("hidden");
            }
        });
    };

    HT.switchChange = () => {
        $(document).on("change", ".js-switch", function () {
            let _this = $(this);
            let isChecked = _this.prop("checked"); // <-- chỉ get
            if (isChecked == true) {
                _this
                    .parents(".col-lg-2")
                    .siblings(".col-lg-10")
                    .find(".disabled")
                    .removeAttr("disabled");
            } else {
                _this
                    .parents(".col-lg-2")
                    .siblings(".col-lg-10")
                    .find(".disabled")
                    .attr("disabled", true);
            }

            console.log(isChecked);
        });
    };

    HT.updateVariant = () => {
        $(document).on("click", ".variant-row", function () {
            let _this = $(this);

            let variantData = {};
            _this
                .find(".td-variant input[type=text][class^='variant_']")
                .each(function () {
                    let className = $(this).attr("class");
                    variantData[className] = $(this).val();
                });

            let updateVariantBox = HT.updateVariantHtml(variantData);
            if ($(".updateVariantTr").length == 0) {
                _this.after(updateVariantBox);
                HT.switchery();
            }

            console.log(variantData);
        });
    };

    HT.variantAlbumList = (album) => {
        let html = "";

        if (album.length && album[0] != "") {
            for (let i = 0; i < album.length; i++) {
                html += "<li class='ui-state-default'>";
                html += "  <div class='thumb'>";
                html += "    <span class='span image img-scaledown'>";
                html +=
                    "      <img src='" + album[i] + "' alt='" + album[i] + "'>";
                html +=
                    "      <input type='hidden' name='variantAlbum[]' value='" +
                    album[i] +
                    "'>";
                html += "    </span>";
                html +=
                    "    <button class='variant-delete-image'><i class='fa fa-trash'></i></button>";
                html += "  </div>";
                html += "</li>";
            }
        }

        return html;
    };

    HT.updateVariantHtml = (variantData) => {
        let variantAlbum = variantData.variant_album.split(",");
        let variantAlbumItem = HT.variantAlbumList(variantAlbum);
        let html = "";
        html += "<tr class='updateVariantTr'>";
        html += '    <td colspan="6">';
        html += '        <div class="updateVariant ibox">';
        html += '            <div class="ibox-title">';
        html +=
            '                <div class="uk-flex uk-flex-middle uk-flex-space-between">';
        html += "                    <h5>Cập nhật thông tin phiên bản</h5>";
        html += '                    <div class="button-group">';
        html += '                        <div class="uk-flex uk-flex-middle">';
        html +=
            '                            <button type="button" class="cancleUpdate btn btn-danger mr10">Huỷ bỏ</button>';
        html +=
            '                            <button type="button" class="saveUpdateVariant btn btn-success">Lưu lại</button>';
        html += "                        </div>";
        html += "                    </div>";
        html += "                </div>";
        html += "            </div>";

        html += '            <div class="ibox-content">';
        html +=
            '                <div class="click-to-upload-variant ' +
            (variantAlbum.length > 0 && variantAlbum[0] !== ""
                ? "hidden"
                : "") +
            ' ">';
        html += '                    <div class="icon">';
        html +=
            '                        <a href="" class="upload-variant-picture">';
        html +=
            '                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80" role="img" aria-label="Image">';
        html +=
            '                                <image href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQs9gUXKwt2KErC_jWWlkZkGabxpeGchT-fyw&s"';
        html +=
            '                                       x="0" y="0" width="80" height="80" preserveAspectRatio="xMidYMid slice"/>';
        html += "                            </svg>";
        html += "                        </a>";
        html += "                    </div>";
        html +=
            '                    <div class="small-text">Sử dụng nút chọn hình hoặc click vào đây để thêm ảnh</div>';
        html += "                </div>";
        html +=
            '                <ul class="upload-variant-list ' +
            (variantAlbumItem.length ? "" : "hidden") +
            ' sortui ui-sortable clearfix" id="sortable2">' +
            variantAlbumItem +
            "</ul>";

        html += '                <div class="row mt10 uk-flex uk-flex-middle">';
        html +=
            '                    <div class="col-lg-2 uk-flex uk-flex-middle">';
        html +=
            '                        <label for="" class="mr10">Tồn kho</label>';
        html +=
            '                        <input type="checkbox" class="js-switch" ' +
            (variantData.variant_quantity !== "" ? "checked" : "") +
            ' data-target="variantQuantity">';
        html += "                    </div>";
        html += '                    <div class="col-lg-10">';
        html += '                        <div class="row">';
        html += '                            <div class="col-lg-3">';
        html +=
            '                                <label for="" class="control-label">Số lượng</label>';
        html +=
            '                                <input type="text" ' +
            (variantData.variant_quantity == "" ? "disabled" : "") +
            '  name="variant_quantity" value="' +
            variantData.variant_quantity +
            '" class="form-control ' +
            (variantData.variant_quantity == "" ? "disabled" : "") +
            '  int">';
        html += "                            </div>";
        html += '                            <div class="col-lg-3">';
        html +=
            '                                <label for="" class="control-label">SKU</label>';
        html +=
            '                                <input type="text" id="sku" name="variant_sku" value="' +
            variantData.variant_sku +
            '" class="form-control text-right">';
        html += "                            </div>";
        html += '                            <div class="col-lg-3">';
        html +=
            '                                <label for="" class="control-label">Giá</label>';
        html +=
            '                                <input type="text" id="sku" name="variant_price" value="' +
            HT.addCommas(variantData.variant_price) +
            '" class="form-control int">';
        html += "                            </div>";
        html += '                            <div class="col-lg-3">';
        html +=
            '                                <label for="" class="control-label">Barcode</label>';
        html +=
            '                                <input type="text" id="sku" name="variant_barcode" value="' +
            variantData.variant_barcode +
            '" class="form-control text-right">';
        html += "                            </div>";
        html += "                        </div>";
        html += "                    </div>";
        html += "                </div>";

        html += '                <div class="row mt10 uk-flex uk-flex-middle">';
        html +=
            '                    <div class="col-lg-2 uk-flex uk-flex-middle">';
        html +=
            '                        <label for="" class="mr10">QL file</label>';
        html +=
            '                        <input type="checkbox" class="js-switch" data-target="disabled" ' +
            (variantData.variant_filename !== "" ? "checked" : "") +
            ">";
        html += "                    </div>";
        html += '                    <div class="col-lg-10">';
        html += '                        <div class="row">';
        html += '                            <div class="col-lg-6">';
        html +=
            '                                <label for="" class="control-label">Tên file</label>';
        html +=
            '                                <input type="text" ' +
            (variantData.variant_filename == "" ? "disabled" : "") +
            ' name="variant_file_name" value="' +
            variantData.variant_filename +
            '" class="form-control ' +
            (variantData.variant_filename == "" ? "disabled" : "") +
            '">';
        html += "                            </div>";
        html += '                            <div class="col-lg-6">';
        html +=
            '                                <label for="" class="control-label">Đường dẫn</label>';
        html +=
            '                                <input type="text" ' +
            (variantData.variant_filename == "" ? "disabled" : "") +
            ' name="variant_file_url" value="' +
            variantData.variant_fileurl +
            '" class="form-control ' +
            (variantData.variant_filename == "" ? "disabled" : "") +
            '">';
        html += "                            </div>";
        html += "                        </div>";
        html += "                    </div>";
        html += "                </div>";

        html += "            </div>";
        html += "        </div>";
        html += "    </td>";
        html += "</tr>";

        return html;
    };

    HT.cancleVariantUpdate = () => {
        $(document).on("click", ".cancleUpdate", function () {
            HT.closeVariantUpdate();
        });
    };

    HT.closeVariantUpdate = () => {
        $(".updateVariantTr").remove();
    };

    HT.saveVariantUpdate = () => {
        $(document).on("click", ".saveUpdateVariant", function () {
            let variant = {
                quantity: $("input[name=variant_quantity]").val(),
                sku: $("input[name=variant_sku]").val(),
                price: $("input[name=variant_price]").val(),
                barcode: $("input[name=variant_barcode]").val(),
                filename: $("input[name=variant_file_name]").val(),
                fileurl: $("input[name=variant_file_url]").val(),
                album: $('input[name="variantAlbum[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
            };

            console.log(variant);

            $.each(variant, function (index, value) {
                $(".updateVariantTr")
                    .prev()
                    .find(".variant_" + index)
                    .val(value);
            });
            HT.previewVariantTr(variant);
            HT.closeVariantUpdate();
        });
    };
    HT.previewVariantTr = (variant) => {
        let option = {
            quantity: variant.quantity,
            price: variant.price,
            sku: variant.sku,
        };
        console.log(option);
        $.each(option, function (index, value) {
            $(".updateVariantTr")
                .prev()
                .find(".td-" + index)
                .html(value);
        });
        $(".updateVariantTr")
            .prev()
            .find(".imageSrc")
            .attr("src", variant.album[0]);
    };

    HT.addCommas = (nStr) => {
        nStr = String(nStr);
        nStr = nStr.replace(/\./gi, "");
        let str = "";
        for (let i = nStr.length; i > 0; i -= 3) {
            let a = i - 3 < 0 ? 0 : i - 3;
            str = nStr.slice(a, i) + "." + str;
        }
        str = str.slice(0, str.length - 1);
        return str;
    };

    HT.setupSelectMultiple = (callback) => {
        if ($(".selectVariant").length) {
            let count = $(".selectVariant").length;

            $(".selectVariant").each(function () {
                let _this = $(this);
                let attributeCatalogueId = _this.attr("data-catid");
                if (attribute != "") {
                    $.get(
                        "ajax/attribute/loadAttribute",
                        {
                            attribute: attribute,
                            attributeCatalogueId: attributeCatalogueId,
                        },
                        function (json) {
                            if (
                                json.items != "undefined" &&
                                json.items.length
                            ) {
                                for (let i = 0; i < json.items.length; i++) {
                                    var option = new Option(
                                        json.items[i].text,
                                        json.items[i].id,
                                        true,
                                        true
                                    );
                                    _this.append(option).trigger("change");
                                }
                            }

                            if (--count === 0 && callback) {
                                callback();
                            }
                        }
                    );
                }

                HT.getSelect2(_this);
            });
        }
    };

    HT.productVariant = () => {
        variant = JSON.parse(atob(variant));
        $(".variant-row").each(function (index, value) {
            let _this = $(this);

            let variantKey = Array.from(_this[0].classList)
                .find((cls) => cls.trim().startsWith("tr-variant-"))
                .split("variant-")[1]
                .trim();

            // console.log(variantKey);

            let dataIndex = variant.sku.findIndex((sku) =>
                sku.includes(variantKey)
            );

            if (dataIndex !== -1) {
                let inputHiddenFields = [
                    {
                        name: "variant[quantity][]",
                        class: "variant_quantity",
                        value: variant.quantity[dataIndex],
                    },
                    {
                        name: "variant[sku][]",
                        class: "variant_sku",
                        value: variant.sku[dataIndex],
                    },
                    {
                        name: "variant[price][]",
                        class: "variant_price",
                        value: variant.price[dataIndex],
                    },
                    {
                        name: "variant[barcode][]",
                        class: "variant_barcode",
                        value: variant.barcode[dataIndex],
                    },
                    {
                        name: "variant[file_name][]",
                        class: "variant_filename",
                        value: variant.file_name[dataIndex],
                    },
                    {
                        name: "variant[file_url][]",
                        class: "variant_fileurl",
                        value: variant.file_url[dataIndex],
                    },
                    {
                        name: "variant[album][]",
                        class: "variant_album",
                        value: variant.album[dataIndex],
                    },
                ];

                for (let i = 0; i < inputHiddenFields.length; i++) {
                    _this
                        .find("." + inputHiddenFields[i].class)
                        .val(
                            inputHiddenFields[i].value
                                ? inputHiddenFields[i].value
                                : 0
                        );
                }

                let album = variant.album[dataIndex];
                let variantImage = album
                    ? album.split(",")[0]
                    : "https://daks2k3a4ib2z.cloudfront.net/6343da4ea0e69336d8375527/6343da5f04a965c89988b149_1665391198377-image16-p-500.jpg";

                _this
                    .find(".td-quantity")
                    .html(HT.addCommas(variant.quantity[dataIndex]));
                _this
                    .find(".td-price")
                    .html(HT.addCommas(variant.price[dataIndex]));
                _this.find(".td-sku").html(variant.sku[dataIndex]);
                _this.find(".imageSrc").attr("src", variantImage);
            }
        });
    };

    $(document).ready(function () {
        HT.setupProductVariant();

        HT.addVariant();
        HT.disabledAttributeCatalogueChoose();
        HT.chooseVariantGroup();
        HT.removeAttribute();
        HT.createProductVariant();
        HT.variantAlbum();
        HT.deleteVariantAlbum();
        HT.switchChange();
        HT.updateVariant();
        HT.cancleVariantUpdate();
        HT.saveVariantUpdate();
        HT.setupSelectMultiple(() => {
            HT.productVariant();
        });
    });
})(jQuery);
