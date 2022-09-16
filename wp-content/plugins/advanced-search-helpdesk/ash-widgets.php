<?php
add_action('widgets_init', 'create_helpdesk_title_widget');
function create_helpdesk_title_widget()
{
    register_widget('Helpdesk_Title_Widget');
}

class Helpdesk_Title_Widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'helpdesk_title_widget', // id của widget
            'Helpdesk Title Widget', // tên của widget
            array(
                'description' => 'Hiển thì tiêu đề của hướng dẫn, bao gồm tên của dự án/tiểu dự án.' // mô tả
            )
        );
    }

    function update($new_instance, $old_instance)
    {
    }

    function widget($args, $instance)
    {
        $project = get_field('helpdesk_project', get_the_ID());
        $project_number = get_field('project_number', $project->ID);
        ?>
        <div class="elementor-element elementor-element-15ba1df title elementor-widget elementor-widget-theme-post-title elementor-page-title elementor-widget-heading"
             data-id="15ba1df" data-element_type="widget" data-widget_type="theme-post-title.default">
            <div class="elementor-widget-container">
                <h1 class="elementor-heading-title elementor-size-large">
                    <?= (!empty($project_number) ? ($project_number . ' - ') : '') . get_the_title(); ?>
                </h1>
            </div>
        </div>
        <?php
    }
}

add_action('widgets_init', 'create_helpdesk_related_widget');
function create_helpdesk_related_widget()
{
    register_widget('Helpdesk_Related_Widget');
}

class Helpdesk_Related_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'related_helpdesk_widget', // id của widget
            'Related Helpdesk Widget', // tên của widget
            array(
                'description' => 'Hiển thị danh sách các hướng dẫn liên quan.' // mô tả
            )
        );
    }

    function update($new_instance, $old_instance)
    {
    }

    function widget($args, $instance)
    {
        $project = get_field('helpdesk_project', get_the_ID());
        $project_number = get_field('project_number', $project->ID);

        $args = array(
            'post_type' => 'helpdesk',
            'posts_per_page' => -1,
            'post__not_in' => array(get_the_ID()),
        );

        $args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;
        if (!empty($project)) {
            $args['meta_key'] = 'helpdesk_project';
            $args['meta_value'] = $project->ID;
        } else {
            $args['meta_key'] = 'helpdesk_project';
            $args['meta_value'] = false;
        }

        $helpdesk_contents = new WP_Query($args);

        if (!empty($helpdesk_contents)) { ?>
            <h1 class="elementor-heading-title elementor-size-default"><?= __('Hướng dẫn các hoạt động khác ' . (!empty($project_number) ? ('của ' . $project_number . ':') : '')) ?></h1>
            <ul class="search-result-list">
                <?php foreach ($helpdesk_contents->posts as $content) { ?>
                    <li>
                        <a href="<?= get_the_permalink($content) ?>" title="<?= get_the_title($content); ?>">
                            <?= get_the_title($content); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php }
    }
}