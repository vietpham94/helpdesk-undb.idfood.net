<?php
$children_project = get_posts(array(
    'post_type' => 'project',
    'posts_per_page' => -1,
    'post_parent' => get_the_ID(),
));

$project_actions = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'project_action',
    'meta_key' => 'project',
    'meta_value' => get_the_ID()
));

$helpdesk_contents = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'helpdesk',
    'meta_key' => 'helpdesk_project',
    'meta_value' => get_the_ID()
));

$project_action_ids = array();

?>
<?php get_header(); ?>
    <section class="container project-title">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?= get_the_title(); ?></h2>
            </div>
        </div>
        <form class="subproject-form" action="" method="get">
            <div class="row subproject-form">
                <?php if (sizeof($children_project) > 0) { ?>
                    <div class="col-12 col-md-4 col-lg-3">
                        <?= __('Chọn tiểu dự án cần tra cứu'); ?>
                    </div>

                    <div class="col-12 col-md-8 col-lg-9">
                        <div class="form-group mb-0">
                            <select name="subproject" class="form-control form-control-sm select-subproject">
                                <option value="">+ Chọn tiểu dự án</option>
                                <?php foreach ($children_project as $child_project) { ?>
                                    <option value="<?= get_the_permalink($child_project); ?>"><?= get_the_title($child_project) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>

                <?php if (sizeof($project_actions) > 0) { ?>
                    <div class="col-12 col-md-4 col-lg-3">
                        <?= __('Chọn hoạt động cần tra cứu'); ?>
                    </div>
                    <div class="col-12 col-md-8 col-lg-9">
                        <div class="form-group mb-0">
                            <select name="action" class="form-control form-control-sm select-action">
                                <option value="">+ Chọn hoạt động</option>
                                <?php foreach ($project_actions as $project_action) { ?>
                                    <?php if (!in_array($project_action->ID, $project_action_ids)) {
                                        $project_action_ids[] = $project_action->ID;
                                    } ?>
                                    <option value="<?= $project_action->ID ?>"><?= get_the_title($project_action) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>

                <?php if (sizeof($children_project) > 0 || sizeof($project_actions) > 0) { ?>
                    <div class="col-12">
                        <button type="submit"><?= __('Tra Cứu'); ?></button>
                    </div>
                <?php } ?>
            </div>
        </form>
    </section>

    <section class="container about-project">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?php __('Tổng quan'); ?></h2>
            </div>
        </div>
        <div class="row">
            <?php $project_metadata = get_field_objects(get_the_ID()); ?>
            <div class="col-12 col-md-8 col-lg-7">
                <p class="project-target">
                    <label><?= $project_metadata['project_target']['label']; ?></label>
                    <?= $project_metadata['project_target']['value']; ?>
                </p>

                <p class="project-target">
                    <label><?= $project_metadata['project_subject']['label']; ?></label>
                    <?= $project_metadata['project_subject']['value']; ?>
                </p>

                <p class="project-target">
                    <label><?= __('Nội dung'); ?></label>
                    <?= get_the_content(); ?>
                </p>

                <p class="project-target">
                    <label><?= $project_metadata['project_organizational']['label']; ?></label>
                    <?= $project_metadata['project_organizational']['value']; ?>
                </p>

                <p class="project-target">
                    <label><?= $project_metadata['project_construction']['label']; ?></label>
                    <?= $project_metadata['project_construction']['value']; ?>
                </p>
            </div>
            <div class="col-12 col-md-4 col-lg-5">
                <img class="project-source-of-capital-img"
                     src="<?= $project_metadata['project_source_of_capital']['value']; ?>"
                     alt="<?= $project_metadata['project_source_of_capital']['label']; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <button type="button"><?= __('Thông tin chi tiết dự án'); ?></button>
            </div>
        </div>
    </section>

    <section class="container helpdesk-contents">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?php __('Hướng dẫn thực hiện'); ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php $helpdesk_categories = get_terms(array('taxonomy' => 'helpdesk_category', 'hide_empty' => true)); ?>
                <?php foreach ($helpdesk_categories as $helpdesk_category) { ?>
                    <p class="helpdesk-category"><?= $helpdesk_category->name; ?></p>
                    <ul class="helpdesk-list">
                        <?php foreach ($helpdesk_contents as $content) { ?>
                            <?php $hd_cat = get_the_terms($content->ID, 'helpdesk_category'); ?>
                            <?php if (in_array($helpdesk_category, $hd_cat)) { ?>
                                <li><a href="<?= get_the_permalink($content) ?>"><?= $content->post_title; ?></a></li>
                            <?php } ?>
                        <?php }; ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="container project-directory">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?php __('Thông tin liên hệ'); ?></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <p class="title"><?= __('Quý vị cần thông tin liên hệ ở khu vực nào?'); ?></p>
            </div>
            <?php echo do_shortcode('[helpdesk_advance_address_selection]'); ?>
        </div>

        <div class="row">
            <div class="col-12">
                <?php
                $project_directories = get_posts(array(
                    'numberposts' => 20,
                    'post_type' => 'project_directory',
                    'meta_key' => 'enterprise_action',
                    'meta_value' => $project_action_ids,
                    'meta_compare' => 'IN'
                ));

                $enterprise_directory_items = array();
                $positions = array();
                foreach ($project_directories as $item) {
                    $ed = get_field('enterprise_directory', $item);
                    if (empty($enterprise_directory_items[$ed->ID])) {
                        $enterprise_directory_items[$ed->ID] = $ed;
                        if (!in_array(get_field('role', $item), $positions)) {
                            $positions[] = get_field('role', $item);
                        }
                    }
                }

                ?>
                <div class="row">
                    <?php foreach ($positions as $role) { ?>
                        <div class="col-12"></div>
                        <p class="position-tite"><?= $role; ?></p></div>
                        <?php foreach ($enterprise_directory_items as $item) { write_log($item->ID); write_log(get_fields($item->ID));?>
                                <?php if ($role == get_field('role', $item->ID)) : ?>
                                    <div class="col-12 col-md-4 col-lg-3">
                                        <?php if (!empty(get_field('logo', $item->ID))) { ?>
                                            <img class="logo" src="<?= get_field('logo', $item->ID); ?>">
                                        <?php } ?>
                                    </div>
                                    <div class="col-12 col-md-8 col-lg-9">
                                        <p class="enterprise-title"><?= get_the_title($item) ?></p>
                                        <p class="address"><?= __('Địa chỉ'); ?></p>
                                        <p class="phone">
                                            <span class="enterprise-phone"><?= __('Điện thoại: ') . get_field('enterprise_phone', $item) ?></span>
                                            <span class="enterprise-hotline"><?= __('Đường dây nóng: ') .  get_field('enterprise_hotline', $item) ?></span>
                                        </p>
                                        <p class="online-contact">
                                            <span class="enterprise-phone"><?= __('Email: ') . get_field('enterprise_email', $item) ?></span>
                                            <span class="enterprise-hotline"><?= __('Website: ') . get_field('enterprise_website', $item) ?></span>
                                        </p>
                                    </div>
                                <?php endif; ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>