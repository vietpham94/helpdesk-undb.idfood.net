<?php
// Shortcode for search form
function create_search_form_shortcode($args, $content)
{ ?>
    <form class="search-form" action="<?= !empty($args["action"]) ? $args["action"] : '' ?>" method="get">
        <div class="row mb-2 subject">
            <div class="col-12 col-md-12 col-lg-3 pb-md-2">
                <span class="search-form-label"><?= __('Quý vị là ai?') ?></span>
            </div>
            <div class="col-12 col-md-12 col-lg-9">
                <?php echo do_shortcode('[helpdesk_advance_subject_radio_group]'); ?>
            </div>
        </div>

        <div class="row mb-2 address">
            <div class="col-12 col-md-12 col-lg-3 m-auto pb-md-2">
                <span class="search-form-label"><?= __('Địa bàn thực hiện ở khu vực nào?') ?></span>
            </div>
            <?php echo do_shortcode('[helpdesk_advance_address_selection]'); ?>
        </div>

        <div class="row mb-2 activity">
            <div class="col-12 col-md-12 col-lg-3 m-auto pb-md-2">
                <span class="search-form-label"><?= __('Quý vị quan tâm đến hoạt động nào?') ?></span>
            </div>
            <div class="col-12 col-md-12 col-lg-9 m-auto pb-md-2">
                <?php echo do_shortcode('[helpdesk_advance_subject_selector_project_action]'); ?>
            </div>
        </div>

        <div class="row mb-2 helpdesk-category">
            <div class="col-12 col-md-12 col-lg-3 pb-md-1 pt-md-1">
                <span class="search-form-label"><?= __('Vấn đề quý vị quan tâm là?') ?></span>
            </div>
            <div class="col-12 col-md-12 col-lg-9">
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
    <?php $helpdesk_categories = get_terms(array('taxonomy' => 'helpdesk_category', 'hide_empty' => false, 'order' => 'DESC')); ?>
    <div class="form-check mb-2">
        <label class="form-check-label">
            <input type="radio" class="form-check-input" name="helpdesk_category" value="" checked="checked">
            <p class="radio-labels"><?= __('Tất cả'); ?></p>
        </label>
    </div>
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
                        <input type="radio" class="form-check-input" name="doi_tuong"
                               value="0" <?= (empty($_GET['doi_tuong']) || $_GET['doi_tuong'] == 0) ? 'checked="checked"' : '' ?>/>
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
                    <?php $projects = get_posts(array('post_type' => 'project', 'numberposts' => -1, 'post_parent' => 0, 'order' => 'asc')); ?>
                    <select name="du_an" class="form-control form-control-sm select-project">
                        <option value="">+ Chọn dự án</option>
                        <?php foreach ($projects as $project) : ?>
                            <option value="<?= $project->ID ?>" <?= (isset($_GET['du_an']) && $_GET['du_an'] == $project->ID) ? 'selected' : '' ?>><?= get_the_title($project) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-2 directory-project">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Thuộc tiểu dự án?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group mb-0">
                    <select name="tieu_du_an" class="form-control form-control-sm select-subproject">
                        <option value="">+ Chọn tiểu dự án</option>
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

        <div class="row mb-2 directory-role">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Vai trò?') ?></span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group mb-0">
                    <?php $position = get_posts(array('numberposts' => -1, 'post_type' => 'position')); ?>
                    <select name="vai_tro" class="form-control form-control-sm select-position">
                        <option value="">+ Chọn vai trò</option>
                        <?php foreach ($position as $position) { ?>
                            <option value="<?= $position->ID ?>" <?= (isset($_GET['vai_tro']) && $_GET['vai_tro'] == $position->ID) ? 'selected' : '' ?>><?= get_the_title($position) ?></option>
                        <?php } ?>
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
        $location = $_GET['tinh'];
    }

    if (!empty($_GET['tinh']) && !empty($_GET['huyen']) && empty($_GET['xa'])) {
        $location = $_GET['huyen'];
    }

    if (!empty($_GET['tinh']) && !empty($_GET['huyen']) && !empty($_GET['xa'])) {
        $location = $_GET['xa'];
    }

    if (!empty($_GET['doi_tuong'])) {
        $meta_query[] = array(
            'key' => 'project_directory_subject_type',
            'value' => $_GET['doi_tuong'],
        );
    }

    if (!empty($_GET['du_an']) && empty($_GET['tieu_du_an'])) {
        $meta_query[] = array(
            'key' => 'enterprise_project',
            'value' => $_GET['du_an'],
        );
    }
    if (!empty($_GET['tieu_du_an'])) {
        $meta_query[] = array(
            'key' => 'enterprise_project',
            'value' => $_GET['tieu_du_an'],
        );
    }

    if (!empty($_GET['hoat_dong'])) {
        $meta_query[] = array(
            'key' => 'enterprise_action',
            'value' => $_GET['hoat_dong'],
        );
    }

    if (!empty($_GET['vai_tro'])) {
        $meta_query[] = array(
            'key' => 'role',
            'value' => $_GET['vai_tro'],
        );
    }

    $project_directory = array();

    if (sizeof($meta_query) > 1) {
        $meta_query['relation'] = 'AND';
        $project_directory = get_posts(array('post_type' => 'project_directory', 'numberposts' => -1, 'meta_query' => $meta_query));
    }

    if (sizeof($meta_query) == 1) {
        $project_directory = get_posts(array(
            'post_type' => 'project_directory',
            'numberposts' => -1,
            'meta_key' => $meta_query[0]['key'],
            'meta_value' => $meta_query[0]['value'],
        ));
    }

    $enterprise_ids = array();
    foreach ($project_directory as $directory) {
        if (!empty($location)) {
            $project_directory_locations = get_field('location', $directory->ID);

            $exist_location = array_filter($project_directory_locations, function ($obj) use ($location) {
                return ($obj->ID == $location);
            });

            if (!empty($exist_location)) {
                $enterprise = get_field('enterprise_directory', $directory);
                if (isset($enterprise) && !in_array($enterprise->ID, $enterprise_ids)) {
                    $enterprise_ids[] = $enterprise->ID;
                };
            }
        } else {
            $enterprise = get_field('enterprise_directory', $directory);
            if (isset($enterprise) && !in_array($enterprise->ID, $enterprise_ids)) {
                $enterprise_ids[] = $enterprise->ID;
            }
        }
    }

    $enterprises = array();
    if (sizeof($enterprise_ids) > 0) {
        $args['include'] = $enterprise_ids;
        $enterprises = get_posts($args);
    }

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
                    <p class="enterprise-title"><?= $item->post_title ?></p>
                    <p class="address"><?= __('<b>Địa chỉ:</b> ') . get_field('address', $item->ID) ?></p>
                    <p class="phone">
                        <span class="enterprise-phone"><?= __('<b>Điện thoại:</b> ') . get_field('enterprise_phone', $item->ID) ?></span>
                        <span class="enterprise-hotline"><?= __('<b>Đường dây nóng:</b> ') . get_field('enterprise_hotline', $item->ID) ?></span>
                    </p>
                    <p class="online-contact">
                        <span class="enterprise-phone"><?= __('<b>Email:</b> ') . get_field('enterprise_email', $item->ID) ?></span>
                        <span class="enterprise-hotline"><?= __('<b>Website:</b> ') . get_field('enterprise_website', $item->ID) ?></span>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php
}

function get_positions($project_directory, $enterprise_id)
{
    $positions = array();
    foreach ($project_directory as $item) {
        $enterprise_item = get_field('enterprise_directory', $item->ID);
        if (isset($enterprise_item) && $enterprise_id == $enterprise_item->ID) {
            $position_item = get_field('role', $item->ID);
            if (isset($position_item)) {
                $positions[] = $position_item;
            }
        }
    }
    return $positions;
}

function get_enterprises($project_directory, $enterprise_id)
{
    $enterprises = array();
    foreach ($project_directory as $item) {
        $enterprise_items = get_field('enterprise_directory', $item->ID);
        if (isset($enterprise_items) && $enterprise_id == $enterprise_items->ID) {
            $enterprise = get_field('enterprise_project', $item->ID);
            if (isset($enterprise)) {
                $enterprises[] = $enterprise;
            }
        }
    }
    return $enterprises;
}

//[helpdesk_advance_directory_search_result]
add_shortcode('helpdesk_advance_directory_search_result', 'directory_search_result_shortcode');

function suggestions_form_shortcode($args, $content)
{ ?>
    <?php if (isset($_POST['submit'])): ?>
    <div class="alert alert-success" role="alert">
        <?= __('Cám ơn đánh giá, góp ý của bạn cho chương trình !') ?>
    </div>
<?php endif; ?>
    <form class="search-form" action="<?= !empty($args["action"]) ? $args["action"] : '' ?>" method="POST">
        <div class="row mb-2 info-title">
            <h2>1. Thông tin cá nhân</h2>
        </div>
        <div class="row mb-2 full-name">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Họ và tên') ?>*</span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="name" name="ten" required/>
                </div>
            </div>
        </div>
        <div class="row mb-2 street">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Địa chỉ') ?>*</span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="street" name="dia_chi" required/>
                </div>
            </div>
        </div>
        <div class="row mb-2 address">
            <div class="col-12 col-md-4 col-lg-3 m-auto"></div>
            <?php echo do_shortcode('[helpdesk_advance_address_selection]'); ?>
        </div>
        <div class="row mb-2 contact-user">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Điện thoại') ?>*</span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="phone" name="phone" required/>
                </div>
                <span class="search-form-label"><?= __('Email') ?>*</span>
                <div class="form-group">
                    <input type="email" class="form-control form-control-sm" id="email" name="email" required/>
                </div>
            </div>
        </div>
        <div class="row mb-2 info-title">
            <h2>2. Nội dung Góp ý - Đánh giá - Phản ánh</h2>
        </div>
        <div class="row mb-2 subject">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Góp ý, đánh giá, phản ánh với vai trò là') ?>*</span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <?php echo do_shortcode('[helpdesk_advance_subject_radio_group]'); ?>
            </div>
        </div>
        <div class="row mb-2 place-work">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Nơi làm việc') ?>*</span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="work" name="work" required/>
                </div>
            </div>
        </div>
        <div class="row mb-2 activity">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Về việc') ?>*</span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <?php echo do_shortcode('[helpdesk_advance_subject_selector_project_action]'); ?>
            </div>
        </div>
        <div class="row mb-2 suggestions-content">
            <div class="col-12 col-md-4 col-lg-3">
                <span class="search-form-label"><?= __('Nội dung góp ý, đánh giá, phản ánh') ?>*</span>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <div class="form-group">
                    <textarea rows="8" class="form-control form-control-sm" id="content" name="content"
                              placeholder="<?= __('Nhập nội dung góp ý, đánh giá, phản ánh cụ thể'); ?>"
                              required></textarea>
                </div>
            </div>
        </div>
        <div class="row mb-2 helpdesk-submit-search">
            <div class="col-12 m-auto text-center">
                <input type="submit" class="btn search-btn" value="Gửi thông tin góp ý - Đánh giá - Phản ánh"
                       name="submit"/>
            </div>
        </div>
    </form>
    <?php
    if (isset($_POST['submit'])) {
        $suggestions_data = array(
            'post_title' => $_POST['ten'],
            'post_type' => 'suggestion',
            'post_content' => $_POST['content'],
            'post_status' => 'publish'
        );
        $suggestions_id = wp_insert_post($suggestions_data);
        if ($suggestions_id) {
            $dia_chi = $_POST['dia_chi'];
            if (!empty($_POST['xa'])) {
                $xa = get_post($_POST['xa']);
                if (isset($xa)) {
                    $dia_chi .= ',' . $xa->post_title;
                }
            }
            if (!empty($_POST['huyen'])) {
                $huyen = get_post($_POST['huyen']);
                if (isset($huyen)) {
                    $dia_chi .= ',' . $huyen->post_title;
                }
            }
            if (!empty($_POST['tinh'])) {
                $tinh = get_post($_POST['tinh']);
                if (isset($tinh)) {
                    $dia_chi .= ',' . $tinh->post_title;
                }
            }
            if (!empty($_POST['subject_type'])) {
                $vai_tro = get_post($_POST['subject_type']);

            }

            update_field('name', $_POST['ten'], $suggestions_id);
            update_field('address', $dia_chi, $suggestions_id);
            update_field('phone', $_POST['phone'], $suggestions_id);
            update_field('email', $_POST['email'], $suggestions_id);
            update_field('suggestion_position', !empty($vai_tro) ? $vai_tro->post_title : '', $suggestions_id);
            update_field('work_place', $_POST['work'], $suggestions_id);
            update_field('suggestion_action', $_POST['action'], $suggestions_id);
            update_field('suggestion_content', $_POST['content'], $suggestions_id);
        }
    }
}

//[helpdesk_suggestions_form]
add_shortcode('helpdesk_suggestions_form', 'suggestions_form_shortcode');

