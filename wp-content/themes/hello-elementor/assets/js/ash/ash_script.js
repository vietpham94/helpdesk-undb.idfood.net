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

    // Autofocus to search input when select2 open
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    /**
     *  Province, district, ward ----------------------------------------------------------
     */
    if ($('.select-province').length > 0) {
        $('.select-province').select2();
    }

    if ($('.select-district').length > 0) {
        $('.select-district').select2();
    }

    if ($('.select-wards').length > 0) {
        $('.select-wards').select2();
    }

    if ($('.select-position').length > 0) {
        $('.select-position').select2();
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

    if ($('.subproject-form .select-subproject').val() && $('.subproject-form .select-subproject').data('start')) {
        location.href = $('.subproject-form .select-subproject').val();
    }

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
        if ($('.select-subproject').length > 0) {
            getListSubproject($(this).val());
            getListActionByProjectSubproject($(this).val());
        }
    });

    function getListSubproject(projectId) {
        if (!projectId) {
            return;
        }

        let url = '/wp-json/wp/v2/projects';
        let data = {
            parent: projectId,
            'filter[orderby]': 'project_number',
            'order': 'asc'
        };
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
                        // let optionTitle = (value.acf && value.acf.project_number) ? value.acf.project_number + ' - ' + value.title.rendered : value.title.rendered;
                        let optionTitle = value.title.rendered;
                        let newState = new Option(optionTitle, value.id, false, selectedSubPj == value.id);
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
    }

    $('.select-subproject').change(function (e) {
        getListActionByProjectSubproject($(this).val());
    });

    function getListActionByProjectSubproject(projectId) {
        if (!projectId) {
            return;
        }

        let url = '/wp-json/ash/v1/project_actions';
        let data = {project: projectId};
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
    }

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
                $('#enterpriseList').append('<div class="loader faq-loading"></div>');
            },
            success: function (response) {
                $('#enterpriseList').find('.loader').remove();

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

    /**
     * FAQ-----------------------------------------------------------------------------------
     */
    if ($('.faq-search-form').length > 0) {
        getSearchFaq();
    }

    if ($('.faq-select-action').length > 0) {
        $('.faq-select-action').select2();
    }

    $('.select-project').change(function () {
        if ($('.faq-select-action').length > 0) {
            getListActionByProject($(this).val());
        }
    });

    function getListActionByProject(projectId) {
        if (!projectId) {
            return;
        }

        let url = '/wp-json/ash/v1/project_actions';
        let data = {'project': projectId};

        $.ajax({
            type: "get",
            dataType: "json",
            url: url,
            data: data,
            context: this,
            beforeSend: function () {
                $('.faq-select-action').find('option').remove();
                let option = new Option('Đang tải dữ liệu...', '');
                $('.faq-select-action').append(option);
            },
            success: function (response) {
                if (response && response.length > 0) {
                    $('.faq-select-action').find('option').remove();

                    let option = new Option('+ Chọn hoạt động', '');
                    $('.faq-select-action').append(option);

                    $.each(response, function (index, value) {
                        let newAction = new Option(value.post_title, value.ID, false, false);
                        $('.faq-select-action').append(newAction);
                    });

                    $('.faq-select-action').select2();
                } else {
                    $('.faq-select-action').find('option').remove();
                }
            }
        });
    }

    $('.faq-search-form').submit(function () {
        $('.faq-page').val(1);
        getSearchFaq();
        return false;
    });

    function getSearchFaq(isLoadMore = false) {
        let url = '/wp-json/ash/v1/faq';
        let data = $('.faq-search-form').serialize();

        $.ajax({
            type: "get",
            dataType: "json",
            url: url,
            data: data,
            context: this,
            beforeSend: function () {
                if (!isLoadMore) {
                    $('#accordionFAQ').find('.faq-card').remove();
                }
                $('.faq-load-more').prop('disabled', false);
                $('.faq-loading').show();
                $('.no-faq-search-result').hide();
            },
            success: function (response) {
                $('.faq-loading').hide();
                if (response && response.length > 0) {
                    let faqTemplate = $('.faq-card-template');
                    $.each(response, function (index, value) {
                        if (value.total_found != null) {
                            if (response.length > 1) {
                                $('.total-found').text('(Có tất cả ' + value.total_found + ' kết quả được tìm thấy)');
                                $('.total-found').show();
                            } else {
                                $('.total-found').hide();
                                if (!isLoadMore) {
                                    $('#accordionFAQ').find('.faq-card').remove();
                                }
                                $('.no-faq-search-result').show();
                                $('.faq-load-more').prop('disabled', true);
                            }
                        } else {
                            try {
                                let newItem = faqTemplate.clone();
                                newItem.removeClass('faq-card-template');
                                newItem.addClass('faq-card');
                                newItem.find('.card-header').attr('id', 'heading-' + value.ID);
                                newItem.find('.card-header a').html(value.post_title);
                                newItem.find('.card-header a').attr('data-target', '#collapse-' + value.ID);
                                newItem.find('.card-header a').attr('aria-controls', 'collapse-' + value.ID);
                                newItem.find('.answer-content').html(value.post_content);
                                newItem.find('.answer').attr('id', 'collapse-' + value.ID);
                                newItem.find('.answer').attr('aria-labelledby', 'heading-' + value.ID);
                                if (value.acf && value.acf.attached) {
                                    $.each(value.acf.attached, function (i, attached) {
                                        let attachedItem = '<li><a href="' + attached.url + '" download>' + attached.filename + '</a></li>';
                                        newItem.find('ul.attached-list').append(attachedItem);
                                    });
                                }
                                newItem.show();
                                $('#accordionFAQ').append(newItem);
                            } catch (e) {
                                console.error(e.message)
                            }
                        }
                    });

                    if (response.length < 10) {
                        $('.faq-load-more').prop('disabled', true);
                    }
                } else {
                    if (!isLoadMore) {
                        $('#accordionFAQ').find('.faq-card').remove();
                    }
                    $('.no-faq-search-result').show();
                    $('.faq-load-more').prop('disabled', true);
                }
            }
        });
    }

    $('.faq-load-more').click(function () {
        let page = $('.faq-page').val();
        $('.faq-page').val(+page + 1);
        getSearchFaq(true);
    });

    /**
     * huong-dan-thuc-hien-chuong-trinh -------------------------------------------------------
     */
    if ($('.helpdesk-search-result').length > 0) {
        getListHelpdesk();
    }

    $('.search-form').submit(function () {
        if ($('.helpdesk-search-result').length > 0) {
            $('.page').val(1);
            getListHelpdesk();
            return false;
        }
    });

    $('.helpdesk-search-result .load-more').click(function () {
        let page = $('.page').val();
        $('.page').val(page ? +page + 1 : 2);
        getListHelpdesk(true);
    });

    function getListHelpdesk(isLoadMore = false) {
        if ($('.search-form').length == 0) {
            return;
        }

        let url = '/wp-json/ash/v1/helpdesk-contents';
        let data = $('.search-form').serialize();

        data = data.replace('tinh', 'province');
        data = data.replace('huyen', 'district');
        data = data.replace('xa', 'ward');

        $.ajax({
            type: "get",
            dataType: "json",
            url: url,
            data: data,
            context: this,
            beforeSend: function () {
                if (!isLoadMore) {
                    $('.helpdesk-list').find('.helpdesk-item').remove();
                }
                $('.helpdesk-search-result .load-more').prop('disabled', false);
                $('.helpdesk-search-result .loader').show();
                $('.helpdesk-search-result .no-search-result').hide();
            },
            success: function (response) {
                $('.helpdesk-search-result .loader').hide();
                if (response && response.length > 0) {
                    let helpdeskTemplate = $('.helpdesk-item-template');
                    $.each(response, function (index, value) {
                        try {
                            if (value.total_found != null) {
                                if (response.length > 1) {
                                    $('.total-found').text('(Có tất cả ' + value.total_found + ' kết quả được tìm thấy)');
                                    $('.total-found').show();
                                } else {
                                    $('.total-found').hide();
                                    if (!isLoadMore) {
                                        $('.helpdesk-list').find('.helpdesk-item').remove();
                                    }
                                    $('.helpdesk-search-result .no-search-result').show();
                                    $('.helpdesk-search-result .load-more').prop('disabled', true);
                                }
                            } else {
                                let newItem = helpdeskTemplate.clone();
                                newItem.removeClass('helpdesk-item-template');
                                newItem.addClass('helpdesk-item');
                                newItem.find('.helpdesk-title a').html(value.post_title);
                                newItem.find('.helpdesk-title a').attr('href', value.url);
                                newItem.find('.helpdesk-excerpt').html(value.post_excerpt);
                                newItem.show();
                                $('.helpdesk-list').append(newItem);
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    });

                    if (response.length < 10) {
                        $('.helpdesk-search-result .load-more').prop('disabled', true);
                    }
                } else {
                    if (!isLoadMore) {
                        $('.helpdesk-list').find('.helpdesk-item').remove();
                    }
                    $('.helpdesk-search-result .no-search-result').show();
                    $('.helpdesk-search-result .load-more').prop('disabled', true);
                }
            }
        });
    }
});