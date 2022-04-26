jQuery(document).ready(function ($) {
    $('.select-province').change(function () {
        let url = '/wp-json/ash/v1/provinces/districts';
        let data = {province: $(this).val()};
        let exportDataSelector = '.select-district';
        let otherSelector = '.select-wards';

        callAjax(url, data, exportDataSelector, otherSelector);
    });

    $('.select-district').change(function () {
        let url = '/wp-json/ash/v1/provinces/districts/wards';
        let data = {district: $(this).val()};
        let exportDataSelector = '.select-wards';

        callAjax(url, data, exportDataSelector);
    });

    function callAjax(url, data, exportDataSelector, otherSelector = null) {
        $.ajax({
            type: "get",
            dataType: "json",
            url: url,
            data: data,
            context: this,
            beforeSend: function () {
                $(exportDataSelector).find('option').remove();
                if (otherSelector) {
                    $(otherSelector).find('option').remove();
                }

                let option = new Option('Đang tải dữ liệu...', null);
                $(exportDataSelector).append(option);
            },
            success: function (response) {
                if (response && response.length > 0) {
                    $(exportDataSelector).find('option').remove();

                    let option;
                    if (!otherSelector) {
                        option = new Option('+ Chọn phường xã', null);
                    } else {
                        option = new Option('+ Chọn quận huyện', null);
                    }
                    $(exportDataSelector).append(option);

                    $.each(response, function (index, value) {
                        let newState = new Option(value.post_title, value.ID);
                        $(exportDataSelector).append(newState);
                    });
                } else {
                    $(exportDataSelector).find('option').remove();
                }
            }
        });
    }

    $('.select-subproject').change(function () {
       location.href = $(this).val();
    });
});