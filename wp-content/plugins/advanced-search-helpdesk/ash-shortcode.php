<?php
// Shortcode for search form
function create_search_form_shortcode($args, $content)
{ ?>
    <form class="search-form" action="<?= !empty($args["action"]) ? $args["action"] : '' ?>" method="get">
        <div class="row mb-2 subject">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Quý vị là ai?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <?php echo do_shortcode('[helpdesk_advance_subject_radio_group]'); ?>
            </div>
        </div>

        <div class="row mb-2 address">
            <div class="col-12 col-md-4 col-lg-3 m-auto">
                <span class="search-form-label"><?= __('Địa bàn thực hiện ở khu vực nào?') ?></span>
            </div>
            <?php echo do_shortcode('[helpdesk_advance_address_selection]'); ?>
        </div>

        <div class="row mb-2 activity">
            <div class="col-12 col-md-4 col-lg-3 m-auto">
                <span class="search-form-label"><?= __('Quý vị quan tâm đến hoạt động nào?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9 m-auto">
                <?php echo do_shortcode('[helpdesk_advance_subject_selector_project_action]'); ?>
            </div>
        </div>

        <div class="row mb-2 helpdesk-category">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Vấn đề quý vị quan tâm là?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <?php echo do_shortcode('[helpdesk_advance_helpdesk_content_radio_group]'); ?>
            </div>
        </div>

        <hr>

        <div class="row mb-2 helpdesk-submit-search">
            <div class="col-12 m-auto text-center">
                <button type="submit" class="btn search-btn">Tìm kiếm</button>
            </div>
        </div>
    </form>
    <?php
}

//[helpdesk_advance_search_form action=/search-result]
add_shortcode('helpdesk_advance_search_form', 'create_search_form_shortcode');

// Shortcode for search result
function search_result_shortcode($args, $content)
{
    $args = array(
        'post_type' => 'helpdesk',
        'posts_per_page' => 10,
        'page' => 1
    );

    if (!empty($_GET['page'])) {
        $args['page'] = $_GET['page'];
    }

    $meta_query = array();

    if (!empty($_GET['subject_type'])) {
        $meta_query[] = array(
            'key' => 'helpdesk_subject_type',
            'value' => $_GET['subject_type'],
        );
    }

    if (!empty($_GET['phase'])) {
        $meta_query[] = array(
            'key' => 'helpdesk_phase',
            'value' => $_GET['phase'],
        );
    }

    if (!empty($_GET['action'])) {
        $meta_query[] = array(
            'key' => 'helpdesk_action',
            'value' => $_GET['action'],
        );
    }

    if (!empty($_GET['tinh']) && empty($_GET['huyen']) && empty($_GET['xa'])) {
        $meta_query[] = array(
            'key' => 'helpdesk_location',
            'value' => $_GET['tinh'],
            'compare' => 'IN',
        );
    }

    if (!empty($_GET['tinh']) && !empty($_GET['huyen']) && empty($_GET['xa'])) {
        $meta_query[] = array(
            'key' => 'helpdesk_location',
            'value' => $_GET['huyen'],
            'compare' => 'IN',
        );
    }

    if (!empty($_GET['tinh']) && !empty($_GET['huyen']) && !empty($_GET['xa'])) {
        $meta_query[] = array(
            'key' => 'helpdesk_location',
            'value' => $_GET['xa'],
            'compare' => 'IN',
        );
    }

    if (!empty($_GET['helpdesk_category'])) {
        $tax_query = array(array(
            'taxonomy' => 'helpdesk_category',
            'field' => 'term_id',
            'terms' => $_GET['helpdesk_category'],
        ));
    }

    if (sizeof($meta_query) > 0) {
        $meta_query['relation'] = 'AND';
        $args['meta_query'] = $meta_query;
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $helpdesk_contents = get_posts($args);
    ?>
    <ul class="search-result-list">
        <?php foreach ($helpdesk_contents as $content) { ?>
            <li>
                <a href="<?= get_the_permalink($content) ?>" title="<?= get_the_title($content); ?>">
                    <?= get_the_title($content); ?>
                </a>
            </li>
        <?php } ?>
    </ul>
    <?php
}

//[helpdesk_advance_search_result]
add_shortcode('helpdesk_advance_search_result', 'search_result_shortcode');

function selection_address_advance($args, $content)
{
    ?>
    <div class="col-12 col-md-4 col-lg-3 mt-auto mb-auto">
        <div class="form-group mb-0">
            <?php $provinces = get_posts(array('numberposts' => -1, 'post_type' => 'province', 'order_by' => 'title', 'order' => 'ASC')); ?>
            <select name="tinh" class="form-control form-control-sm select-province">
                <option value="">+ Tỉnh thành</option>
                <?php foreach ($provinces as $province) { ?>
                    <option value="<?= $province->ID ?>" <?= (isset($_GET['tinh']) && $_GET['tinh'] == $province->ID) ? 'selected' : ''; ?>><?= get_the_title($province) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="col-12 col-md-4 col-lg-3 mt-auto mb-auto">
        <div class="form-group mb-0">
            <select name="huyen" class="form-control form-control-sm select-district">
                <option value="">+ Quận huyện</option>
            </select>
        </div>
    </div>

    <div class="col-12 col-md-4 col-lg-3 mt-auto mb-auto">
        <div class="form-group mb-0">
            <select name="xa" class="form-control form-control-sm select-wards">
                <option value="">+ Xã phường</option>
            </select>
        </div>
    </div>
    <?php
}

//[helpdesk_advance_address_selection]
add_shortcode('helpdesk_advance_address_selection', 'selection_address_advance');

function subject_radio_group($args, $content)
{
    ?>
    <?php $subjects = get_posts(array('numberposts' => -1, 'post_type' => 'subject', 'order' => 'ASC')); ?>
    <?php foreach ($subjects as $subject): ?>
    <div class="form-check mb-2">
        <label class="form-check-label">
            <input type="radio" class="form-check-input" name="subject_type"
                   value="<?= $subject->ID; ?>">
            <p class="radio-labels"><?= get_the_title($subject) ?></p>
        </label>
    </div>
<?php endforeach; ?>
<?php }

//[helpdesk_advance_subject_radio_group]
add_shortcode('helpdesk_advance_subject_radio_group', 'subject_radio_group');

function project_actions_selector($args, $content)
{ ?>
    <div class="form-group mb-0">
        <?php $action_projects = get_posts(array('numberposts' => -1, 'post_type' => 'project_action')); ?>
        <select name="action" class="form-control form-control-sm select-action">
            <option value="">+ Chọn hoạt động</option>
            <?php foreach ($action_projects as $action_project) { ?>
                <option value="<?= $action_project->ID ?>"><?= get_the_title($action_project) ?></option>
            <?php } ?>
        </select>
    </div>
    <?php
}

//[helpdesk_advance_subject_selector_project_action]
add_shortcode('helpdesk_advance_subject_selector_project_action', 'project_actions_selector');

function helpdesk_content_radio_group($agrs, $content)
{
    ?>
    <?php $helpdesk_categories = get_terms(array('taxonomy' => 'helpdesk_category', 'hide_empty' => false)); ?>
    <?php foreach ($helpdesk_categories as $helpdesk_category) { ?>
    <div class="form-check mb-2">
        <label class="form-check-label">
            <input type="radio" class="form-check-input" name="helpdesk_category"
                   value="<?= $helpdesk_category->term_id; ?>">
            <p class="radio-labels"><?= $helpdesk_category->name; ?></p>
        </label>
    </div>
<?php }; ?>
    <?php
}

//[helpdesk_advance_helpdesk_content_radio_group]
add_shortcode('helpdesk_advance_helpdesk_content_radio_group', 'helpdesk_content_radio_group');

function create_search_project_directory($args, $content)
{ ?>
    <form class="search-directory-form" action="<?= !empty($args["action"]) ? $args["action"] : '' ?>" method="get">
        <div class="row mb-2 keyword">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Từ khóa tìm kiếm?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="directorySearchKey" name="search"
                           placeholder="<?= __('Nhập từ khóa tìm kiếm. Ví dụ: hỗ trợ trồng rừng, hỗ trợ nước sinh hoạt,...'); ?>"
                           value="<?= $_GET['search'] ?? '' ?>"/>
                </div>
            </div>
        </div>

        <div class="row mb-2 address">
            <div class="col-12 col-md-4 col-lg-3 m-auto">
                <span class="search-form-label"><?= __('Nơi thực hiện?') ?></span>
            </div>
            <?php echo do_shortcode('[helpdesk_advance_address_selection]'); ?>
        </div>

        <div class="row mb-2 subject">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Tôi cần thông tin liên hệ của?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <?php $subjects = get_posts(array('post_type' => 'subject', 'numberposts' => -1, 'order' => 'ASC')); ?>
                <?php foreach ($subjects as $subject) : ?>
                    <div class="form-check mb-2">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="doi_tuong" value="<?= $subject->ID; ?>"
                                <?= (isset($_GET['doi_tuong']) && $_GET['doi_tuong'] == $subject->ID) ? 'checked="checked"' : '' ?> />
                            <p class="radio-labels"><?= $subject->post_title; ?></p>
                        </label>
                    </div>
                <?php endforeach; ?>
                <div class="form-check mb-2">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="subject" value=""/>
                        <p class="radio-labels"><?= __('Tất cả'); ?></p>
                    </label>
                </div>
            </div>
        </div>

        <div class="row mb-2 directory-project">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Thuộc dự án?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group mb-0">
                    <?php $projects = get_posts(array('post_type' => 'project', 'numberposts' => -1)); ?>
                    <select name="du_an" class="form-control form-control-sm select-project">
                        <option value="">+ Chọn dự án</option>
                        <?php foreach ($projects as $project) : ?>
                            <option value="<?= $project->ID ?>" <?= (isset($_GET['du_an']) && $_GET['du_an'] == $project->ID) ? 'selected' : '' ?>><?= get_the_title($project) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-2 directory-action">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Nội dung của hoạt động là?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group mb-0">
                    <select name="hoat_dong" class="form-control form-control-sm select-action">
                        <option value="">+ Chọn nội dung hoạt động</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-2 directory-submit-search">
            <div class="col-12 m-auto text-center">
                <button type="submit" class="btn search-btn"><?= __('Tra cứu thông tin liên hệ'); ?></button>
            </div>
        </div>
    </form>
    <?php
}

//[helpdesk_advance_directory_search_form action=/tra-cuu-danh-ba]
add_shortcode('helpdesk_advance_directory_search_form', 'create_search_project_directory');

// Shortcode for search result
function directory_search_result_shortcode($args, $content)
{
    $args = array(
        'post_type' => 'enterprise',
        'numberposts' => 10,
        'page' => 1
    );

    if (!empty($_GET['page'])) {
        $args['page'] = $_GET['page'];
    }

    if (!empty($_GET['search'])) {
        $args['s'] = $_GET['search'];
    }

    $meta_query = array();

    if (!empty($_GET['tinh']) && empty($_GET['huyen']) && empty($_GET['xa'])) {
        $meta_query[] = array(
            'key' => 'location',
            'value' => $_GET['tinh'],
            'compare' => 'IN',
        );
    }

    if (!empty($_GET['tinh']) && !empty($_GET['huyen']) && empty($_GET['xa'])) {
        $meta_query[] = array(
            'key' => 'location',
            'value' => $_GET['huyen'],
            'compare' => 'IN',
        );
    }

    if (!empty($_GET['tinh']) && !empty($_GET['huyen']) && !empty($_GET['xa'])) {
        $meta_query[] = array(
            'key' => 'location',
            'value' => $_GET['xa'],
            'compare' => 'IN',
        );
    }

    if (!empty($_GET['doi_tuong'])) {
        $meta_query[] = array(
            'key' => 'project_directory_subject_type',
            'value' => $_GET['doi_tuong'],
        );
    }

    if (!empty($_GET['du_an'])) {
        $meta_query[] = array(
            'key' => 'enterprise_project',
            'value' => $_GET['du_an'],
        );
    }

    if (!empty($_GET['hoat_dong'])) {
        $meta_query[] = array(
            'key' => 'enterprise_action',
            'value' => $_GET['hoat_dong'],
        );
    }

    if (sizeof($meta_query) > 0) {
        $meta_query['relation'] = 'AND';
        $project_directory = get_posts(array('post_type' => 'project_directory', 'numberposts' => -1, 'meta_query' => $meta_query));
        $enterprise_ids = array();
        foreach ($project_directory as $directory) {
            $enterprise = get_field('enterprise_directory', $directory);
            if (isset($enterprise) && !in_array($enterprise->ID, $enterprise_ids)) {
                $enterprise_ids[] = $enterprise->ID;
            }
        }
    }

    if (sizeof($enterprise_ids) > 0) {
        $args['include'] = $enterprise_ids;
    }

    $enterprises = get_posts($args);
    ?>
    <div class="directory_search_result">
        <?php foreach ($enterprises as $item) { ?>
            <div class="row enterprise-item">
                <div class="col-12 col-md-4 col-lg-3">
                    <?php if (!empty(get_the_post_thumbnail_url($item->ID))) { ?>
                        <img class="logo"
                             src="<?= get_the_post_thumbnail_url($item->ID); ?>">
                    <?php } ?>
                </div>
                <div class="col-12 col-md-8 col-lg-9">
                    <p class="enterprise-title"><?= get_the_title($item->post_title) ?></p>
                    <p class="address"><?= __('Địa chỉ: ') . get_field('address', $item->ID) ?></p>
                    <p class="phone">
                        <span class="enterprise-phone"><?= __('Điện thoại: ') . get_field('enterprise_phone', $item->ID) ?></span>
                        <span class="enterprise-hotline"><?= __('Đường dây nóng: ') . get_field('enterprise_hotline', $item->ID) ?></span>
                    </p>
                    <p class="online-contact">
                        <span class="enterprise-phone"><?= __('Email: ') . get_field('enterprise_email', $item->ID) ?></span>
                        <span class="enterprise-hotline"><?= __('Website: ') . get_field('enterprise_website', $item->ID) ?></span>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php
}

//[helpdesk_advance_directory_search_result]
add_shortcode('helpdesk_advance_directory_search_result', 'directory_search_result_shortcode');


