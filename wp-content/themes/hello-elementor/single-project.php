<?php
global $post;

$children_project = get_posts(array(
    'post_type' => 'project',
    'posts_per_page' => -1,
    'post_parent' => get_the_ID(),
    'meta_key' => 'project_number',
    'orderby' => 'meta_value',
    'order' => 'ASC'
));

if ($post->post_parent != 0) {
    $children_project = get_posts(array(
        'post_type' => 'project',
        'posts_per_page' => -1,
        'post_parent' => $post->post_parent,
        'meta_key' => 'project_number',
        'orderby' => 'meta_value',
        'order' => 'ASC'
    ));
}

$project_actions = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'project_action',
    'meta_key' => 'project',
    'meta_value' => get_the_ID()
));

$helpdesk_contents = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'helpdesk',
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'helpdesk_project',
            'value' => get_the_ID(),
        ),
        array(
            'key' => 'helpdesk_project',
            'value' => '',
        )
    )
));

$project_action_ids = array();

?>
<?php get_header(); ?>
    <section class="container project-title">
        <div class="row">
            <div class="col-12">
                <h2 class="title">
                    <?php //get_field('project_number', get_the_ID()) ?>
                    <?= get_the_title(); ?>
                </h2>
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
                            <select name="subproject" class="form-control form-control-sm select-subproject" data-start="<?= $post->post_parent == 0 ?>">
                                <option value="">+ Chọn tiểu dự án</option>
                                <?php foreach ($children_project as $index => $child_project) { ?>
                                    <option value="<?= get_the_permalink($child_project); ?>"
                                        <?= $index == 0 ? 'selected' : '' ?>>
                                        <?= get_field('project_number', $child_project->ID) ?> -
                                        <?= get_the_title($child_project) ?>
                                    </option>
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
                                    <option value="<?= get_the_permalink($project_action); ?>"><?= get_the_title($project_action) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </form>
    </section>

    <section class="container about-project d-none">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?= __('Tổng quan'); ?></h2>
            </div>
        </div>
        <div class="row">
            <?php $project_metadata = get_field_objects(get_the_ID()); ?>
            <div class="col-12 col-md-8 col-lg-7">
                <p class="project-target">
                    <label><?= $project_metadata['project_target']['label']; ?></label>
                    <?= $project_metadata['project_target']['value']; ?>
                </p>

                <p class="project-subject">
                    <label><?= $project_metadata['project_subject']['label']; ?></label>
                    <?= $project_metadata['project_subject']['value']; ?>
                </p>

                <p class="project-contents">
                    <label><?= __('Nội dung'); ?></label>
                    <?= get_the_content(); ?>
                </p>

                <p class="project-organizational">
                    <label><?= $project_metadata['project_organizational']['label']; ?></label>
                <ul>
                    <?php foreach ($project_metadata['project_organizational']['value'] as $organizational) : ?>
                        <li>
                            <a href="<?= get_the_permalink($organizational); ?>"><?= get_the_title($organizational); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                </p>

                <p class="project-construction">
                    <label><?= $project_metadata['project_construction']['label']; ?></label>
                <ul>
                    <?php foreach ($project_metadata['project_construction']['value'] as $construction) : ?>
                        <li><a href="<?= get_the_permalink($construction); ?>"><?= get_the_title($construction); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                </p>
            </div>
            <?php if (!empty($project_metadata['project_source_of_capital']['value'])): ?>
                <div class="col-12 col-md-4 col-lg-5">
                    <img class="project-source-of-capital-img"
                         src="<?= $project_metadata['project_source_of_capital']['value']; ?>"
                         alt="<?= $project_metadata['project_source_of_capital']['label']; ?>">
                </div>
            <?php endif; ?>
        </div>
        <div class="row d-none">
            <div class="col-12 text-center">
                <button type="button"><?= __('Thông tin chi tiết dự án'); ?></button>
            </div>
        </div>
    </section>

    <section class="container helpdesk-contents">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?= __('Hướng dẫn thực hiện'); ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php $helpdesk_categories = get_terms(array('taxonomy' => 'helpdesk_category', 'hide_empty' => true)); ?>
                <?php if (sizeof($helpdesk_contents) > 0) : ?>
                    <?php foreach ($helpdesk_categories as $helpdesk_category) { ?>
                        <p class="helpdesk-category"><?= $helpdesk_category->name; ?></p>
                        <ul class="helpdesk-list">
                            <?php foreach ($helpdesk_contents as $content) { ?>
                                <?php $hd_cat = get_the_terms($content->ID, 'helpdesk_category'); ?>
                                <?php if (in_array($helpdesk_category, $hd_cat)) { ?>
                                    <li><a href="<?= get_the_permalink($content) ?>"><?= $content->post_title; ?></a>
                                    </li>
                                <?php } ?>
                            <?php }; ?>
                        </ul>
                    <?php } ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="container project-directory">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?= __('Tìm liên hệ'); ?></h2>
            </div>
        </div>

        <div class="row search">
            <div class="col-12">
                <p class="project-directory-title"><?= __('Quý vị cần thông tin liên hệ ở khu vực nào?'); ?></p>
            </div>
            <?php echo do_shortcode('[helpdesk_advance_address_selection]'); ?>
            <div class="col-12 col-md-12 col-lg-3 m-auto">
                <input type="hidden" class="project-id" value="<?= get_the_ID(); ?>">
                <button type="button" class="btn btn-filter-project-directory"><?= __('Tìm kiếm'); ?></button>
            </div>
        </div>

        <div class="row enterprise-list" id="enterpriseList">
            <?php
            $project_directories = get_posts(array(
                'numberposts' => -1,
                'post_type' => 'project_directory',
                'meta_key' => 'enterprise_action',
                'meta_value' => $project_action_ids,
                'meta_compare' => 'IN'
            ));

            $enterprise_directory_items = array();
            $positions = array();
            foreach ($project_directories as $item) {
                $ed = get_field('enterprise_directory', $item->ID);
                if (empty($enterprise_directory_items[$ed->ID])) {
                    $enterprise_directory_items[$ed->ID] = array(get_field('role', $item->ID)->post_title => $ed);
                    if (!in_array(get_field('role', $item->ID), $positions)) {
                        $positions[] = get_field('role', $item->ID);
                    }
                }
            }
            ?>

            <?php foreach ($positions as $role) { ?>
                <div class="col-12">
                    <p class="position-title"><?= $role->post_title ?></p>
                </div>
                <?php foreach ($enterprise_directory_items as $item) { ?>
                    <?php if (!empty($item[$role->post_title])) : ?>
                        <div class="col-12 col-md-4 col-lg-3">
                            <?php if (!empty(get_the_post_thumbnail_url($item[$role->post_title]->ID))) { ?>
                                <img class="logo"
                                     src="<?= get_the_post_thumbnail_url($item[$role->post_title]->ID); ?>">
                            <?php } ?>
                        </div>
                        <div class="col-12 col-md-8 col-lg-9">
                            <p class="enterprise-title"><?= get_the_title($item[$role->post_title]) ?></p>
                            <p class="address"><?= __('<b>Địa chỉ:</b> ') . get_field('address', $item[$role->post_title]) ?></p>
                            <p class="phone">
                                <span class="enterprise-phone"><?= __('<b>Điện thoại:</b> ') . get_field('enterprise_phone', $item[$role->post_title]) ?></span>
                                <span class="enterprise-hotline"><?= __('<b>Đường dây nóng:</b> ') . get_field('enterprise_hotline', $item[$role->post_title]) ?></span>
                            </p>
                            <p class="online-contact">
                                <span class="enterprise-phone"><?= __('<b>Email:</b> ') . get_field('enterprise_email', $item[$role->post_title]) ?></span>
                                <span class="enterprise-hotline"><?= __('<b>Website:</b> ') . get_field('enterprise_website', $item[$role->post_title]) ?></span>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php } ?>
            <?php } ?>

        </div>
        <div class="template-enterprise-item" style="display: none;">
            <div class="col-12 col-md-4 col-lg-3">
                <img class="logo" src="">
            </div>
            <div class="col-12 col-md-8 col-lg-9">
                <p class="enterprise-title"></p>
                <p class="address"></p>
                <p class="phone">
                    <span class="enterprise-phone"></span>
                    <span class="enterprise-hotline"></span>
                </p>
                <p class="online-contact">
                    <span class="enterprise-email"></span>
                    <span class="enterprise-website"></span>
                </p>
            </div>
        </div>
    </section>

    <section class="container faq">
        <div class="row">
            <div class="col-12">
                <h2 class="title"><?= __('Câu hỏi thường gặp'); ?></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php
                $faq_args = array(
                    'post_type' => 'faq',
                    'page' => 1,
                    'numberposts' => 10,
                    'meta_key' => 'project_id',
                    'meta_value' => get_the_ID(),
                );

                $faqs = get_posts($faq_args);
                ?>
                <div class="accordion" id="accordionFAQ">
                    <?php foreach ($faqs as $key => $faq) : ?>
                        <div class="card">
                            <div class="card-header" id="heading-<?= $key ?>">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                            data-target="#collapse-<?= $key ?>" aria-expanded="false"
                                            aria-controls="collapse-<?= $key ?>">
                                        <?= get_the_title($faq) ?>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapse-<?= $key ?>" class="collapse" aria-labelledby="heading-<?= $key ?>"
                                 data-parent="#accordionFAQ">
                                <div class="card-body">
                                    <?= get_the_content(null, false, $faq); ?>

                                    <?php $attached = get_field('attached', $faq->ID); ?>
                                    <?php if (!empty($attached)): ?>
                                        <p class="attached-title font-weight-bold"><?= __('Các tài liệu đính kèm') ?></p>
                                        <ul class="attached-list">
                                            <?php foreach ($attached as $fileItem): ?>
                                                <li>
                                                    <a href="<?= $fileItem['url']; ?>"
                                                       download><?= $fileItem['filename']; ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>