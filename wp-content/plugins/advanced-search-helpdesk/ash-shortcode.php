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

        <div class="row mb-2 helpdesk-category">
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
                    <option value="<?= $province->ID ?>"><?= get_the_title($province) ?></option>
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




