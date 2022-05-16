jQuery(document).ready(function ($) {
    /**
     * Common functions ---------------------------------------------------------------------
     */
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

    /**
     *  Province, district, ward ----------------------------------------------------------
     */
    if ($('.select-province').length > 0) {
        $('.select-province').select2();
    }

    $('.select-province').change(function () {
        let url = '/wp-json/ash/v1/provinces/districts';
        let data = {province_id: $(this).val()};
        let exportDataSelector = '.select-district';
        let otherSelector = '.select-wards';

        callAjax(url, data, exportDataSelector, otherSelector);
    });

    $('.select-district').change(function () {
        let url = '/wp-json/ash/v1/provinces/districts/wards';
        let data = {district_id: $(this).val()};
        let exportDataSelector = '.select-wards';

        callAjax(url, data, exportDataSelector);
    });

    if ($('.select-province').val()) {
        let url = '/wp-json/ash/v1/provinces/districts';
        let data = {province_id: $('.select-province').val()};
        let exportDataSelector = '.select-district';
        let otherSelector = '.select-wards';

        callAjax(url, data, exportDataSelector, otherSelector);

        if (getUrlParameter('huyen')) {
            url = '/wp-json/ash/v1/provinces/districts/wards';
            data = {district_id: getUrlParameter('huyen')};
            exportDataSelector = '.select-wards';

            callAjax(url, data, exportDataSelector);
        }
    }

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

                let option = new Option('Đang tải dữ liệu...', '');
                $(exportDataSelector).append(option);
            },
            success: function (response) {
                if (response && response.length > 0) {
                    $(exportDataSelector).find('option').remove();

                    let option;
                    if (!otherSelector) {
                        option = new Option('+ Chọn phường xã', '');
                    } else {
                        option = new Option('+ Chọn quận huyện', '');
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

                    $(exportDataSelector).select2();
                } else {
                    $(exportDataSelector).find('option').remove();
                }
            }
        });
    }


    /**
     * Project detail page ----------------------------------------------------------------
     */
    $('.subproject-form .select-subproject').change(function () {
        location.href = $(this).val();
    });

    $('.subproject-form .select-action').change(function () {
        location.href = $(this).val();
    });

    /**
     * Project directory --------------------------------------------------------------------
     */
    if ($('.select-project').length > 0) {
        $('.select-project').select2();
    }

    if ($('.select-subproject').length > 0) {
        $('.select-subproject').select2();
    }

    if ($('.select-action').length > 0) {
        $('.select-action').select2();
    }

    $('.select-project').change(function () {
        let url = '/wp-json/wp/v2/projects';
        let data = {parent: $(this).val()};
        $.ajax({
            type: "get",
            dataType: "json",
            url: url,
            data: data,
            context: this,
            beforeSend: function () {
                $('.select-subproject').find('option').remove();
                let option = new Option('Đang tải dữ liệu...', null);
                $('.select-subproject').append(option);
            },
            success: function (response) {
                if (response && response.length > 0) {
                    $('.select-subproject').find('option').remove();

                    let selectedSubPj = getUrlParameter('tieu_du_an');

                    let option = new Option('+ Chọn tiểu dự án', '', selectedSubPj ? false : true);
                    $('.select-subproject').append(option);

                    $.each(response, function (index, value) {

                        let newState = new Option(value.title.rendered, value.id, false, selectedSubPj == value.id);
                        $('.select-subproject').append(newState);

                    });

                    $('.select-subproject').select2();

                    if (getUrlParameter('tieu_du_an')) {
                        $('.select-subproject').trigger('change', getUrlParameter('tieu_du_an'));
                    }
                } else {
                    $('.select-subproject').find('option').remove();
                }
            }
        });
    });

    $('.select-subproject').change(function (e) {
        let url = '/wp-json/ash/v1/project_actions';
        console.log(e);
        let data = {project: $(this).val()};
        $.ajax({
            type: "get",
            dataType: "json",
            url: url,
            data: data,
            context: this,
            beforeSend: function () {
                $('.select-action').find('option').remove();
                let option = new Option('Đang tải dữ liệu...', null);
                $('.select-action').append(option);
            },
            success: function (response) {
                if (response && response.length > 0) {
                    $('.select-action').find('option').remove();

                    let selectedPjAction = getUrlParameter('hoat_dong');

                    let option = new Option('+ Chọn nội dung hoạt động', '', selectedPjAction ? false : true);
                    $('.select-action').append(option);
                    $.each(response, function (index, value) {

                        let newState = new Option(value.post_title, value.ID, false, selectedPjAction == value.ID);
                        $('.select-action').append(newState);

                    });

                    $('.select-action').select2();
                } else {
                    $('.select-action').find('option').remove();
                }
            }
        });
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
                        newItem.find('.address').html('Địa chỉ: ' + item.acf.address);
                        newItem.find('.enterprise-phone').html('Điện thoại: ' + item.acf.enterprise_phone);
                        newItem.find('.enterprise-hotline').html('Đường dây nóng: ' + item.acf.enterprise_hotline);
                        newItem.find('.enterprise-email').html('Email: ' + item.acf.enterprise_email);
                        newItem.find('.enterprise-website').html('Website: ' + item.acf.enterprise_website);
                        newItem.css('display', 'flex');
                        newItem.css('flex-wrap', 'wrap');
                        newItem.css('width', '100%');
                        $('#enterpriseList').append(newItem);
                    } catch (e) {
                        console.error(e.message)
                    }
                });
            }
        });
    });

    $('.select-role').change(function () {
        let url = '/wp-json/ash/v1/project/positions';
        let data = {position: $(this).val()};
        $.ajax({
            type: "get",
            dataType: "json",
            url: url,
            data: data,
            context: this,
            beforeSend: function () {
                $('.select-role').find('option').remove();
                let option = new Option('Đang tải dữ liệu...', null);
                $('.select-role').append(option);
            },
            success: function (response) {
                if (response && response.length > 0) {
                    $('.select-subproject').find('option').remove();

                    let option = new Option('+ Chọn tiểu dự án', '', true);
                    $('.select-subproject').append(option);
                    $.each(response, function (index, value) {

                        let newState = new Option(value.post_title, value.ID, false, false);
                        $('.select-subproject').append(newState);

                    });
                } else {
                    $('.select-subproject').find('option').remove();
                }
            }
        });
    });

    if (getUrlParameter('du_an')) {
        $('.select-project').trigger('change', getUrlParameter('du_an'));
    }

});