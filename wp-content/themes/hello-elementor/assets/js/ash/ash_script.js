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

                    let selectedDistrict = getUrlParameter('huyen');
                    let selectedWard = getUrlParameter('xa');

                    $.each(response, function (index, value) {
                        if (selectedDistrict == value.ID || selectedWard == value.ID) {
                            let newState = new Option(value.post_title, value.ID, false, true);
                            $(exportDataSelector).append(newState);
                        } else {
                            let newState = new Option(value.post_title, value.ID, false, false);
                            $(exportDataSelector).append(newState);
                        }
                    });
                } else {
                    $(exportDataSelector).find('option').remove();
                }
            }
        });
    }

    $('.subproject-form .select-subproject').change(function () {
        location.href = $(this).val();
    });

    $('.subproject-form .select-action').change(function () {
        location.href = $(this).val();
    });

    $('.btn-filter-project-directory').click(function () {
        let province = $('.project-directory .select-province').val();
        let district = $('.project-directory .select-district').val();
        let wards = $('.project-directory .select-wards').val();
        let projectId = $('.project-directory .project-id').val();
        let actionId = $('.project-directory .action-id').val();

        let project_directory_location = province;

        if (district && district != 'null') {
            project_directory_location = district;
        }

        if (wards && wards != 'null') {
            project_directory_location = wards;
        }

        let data = {
            project: projectId,
            action: actionId,
            location: project_directory_location
        }

        $.ajax({
            type: "get",
            dataType: "json",
            url: '/wp-json/ash/v1/enterprise',
            data: data,
            context: this,
            beforeSend: function () {
                $('#enterpriseList').empty();
            },
            success: function (response) {
                let template = $('.template-enterprise-item');
                if (response.length == 0) {
                    $('#enterpriseList').append('<div class="col-12 no-result">Không tìm thấy đơn vị nào!</div>');
                    return;
                }

                response.forEach(item => {
                    try {
                        let newItem = template.clone();
                        newItem.find('.logo').attr('src', item.logo);
                        newItem.find('.enterprise-title').html(item.post_title);
                        newItem.find('.address').html(item.acf.address.label + ': ' + item.acf.address.value);
                        newItem.find('.enterprise-phone').html(item.acf.enterprise_phone.label + ': ' + item.acf.enterprise_phone.value);
                        newItem.find('.enterprise-hotline').html(item.acf.enterprise_hotline.label + ': ' + item.acf.enterprise_hotline.value);
                        newItem.find('.enterprise-email').html(item.acf.enterprise_email.label + ': ' + item.acf.enterprise_email.value);
                        newItem.find('.enterprise-website').html(item.acf.enterprise_website.label + ': ' + item.acf.enterprise_website.value);
                        newItem.show();
                        console.log(newItem);
                        $('#enterpriseList').append(newItem);
                    } catch (e) {
                        console.error(e.message)
                    }
                });
            }
        });
    });

    let getUrlParameter = function getUrlParameter(sParam) {
        let sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return (sParameterName[1] === undefined || sParameterName[1] === 'null') ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };

    if ($('.select-province').val()) {
        let url = '/wp-json/ash/v1/provinces/districts';
        let data = {province: $('.select-province').val()};
        let exportDataSelector = '.select-district';
        let otherSelector = '.select-wards';

        callAjax(url, data, exportDataSelector, otherSelector);

        if (getUrlParameter('huyen')) {
            url = '/wp-json/ash/v1/provinces/districts/wards';
            data = {district: getUrlParameter('huyen')};
            exportDataSelector = '.select-wards';

            callAjax(url, data, exportDataSelector);
        }
    }
});