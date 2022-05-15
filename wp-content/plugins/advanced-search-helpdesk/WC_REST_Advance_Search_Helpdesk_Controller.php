<?php

namespace ASH;

class WC_REST_Advance_Search_Helpdesk_Controller
{
    protected $namespace;
    protected $custome_post_types = array(
        'province',                           // Tỉnh TP
        'district',                           // Quận Huyện
        'ward',                               // Phường xã
        'village',                            // Thông bản
        'project',                            // Dự án
        'subject',                            // Đối tượng
        'enterprise',                         // Tổ chức
        'document_category',                  // Loại tài liệu
        'documents',                          // Tài liệu
        'phase',                              // Giai đoạn dự án
        'project_action',                     // Hoạt động dự án
        'project_directory',                  // Danh bạ dự án
        'suggestion',                         // Đánh giá, góp ý
        'helpdesk',                           // Nội dung hướng dẫn
        'helpdesk_category'                   // Phân loại nội dung hướng dẫn (Taxonomy)
    );

    public function __construct()
    {
        $this->namespace = '/ash/v1';
    }

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/helpdesk-contents',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_helpdesk_contents'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/project-directories',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_project_directories'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/provinces/districts',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_districts'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/provinces/districts/wards',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_wards'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/provinces/districts/wards/villages',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_villages'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/project_actions',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_project_actions'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/enterprise',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_enterprise'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/project/children',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_project_children'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/project/actions',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_actions_by_project'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/suggestion',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'new_suggestion'),
            )
        );
    }

    public function get_helpdesk_contents(\WP_REST_Request $request)
    {
        $args = array(
            'post_type' => 'helpdesk',
            'posts_per_page' => 10,
            'page' => 1
        );

        if (!empty($request->get_param('page'))) {
            $args['page'] = $request->get_param('page');
        }

        $meta_query = array();

        if (!empty($request->get_param('project'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_project',
                'value' => $request->get_param('project')
            );
        }

        if (!empty($request->get_param('action'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_action',
                'value' => $request->get_param('action')
            );
        }

        if (!empty($request->get_param('phase'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_phase',
                'value' => $request->get_param('phase'),
            );
        }

        if (!empty($request->get_param('subject_type'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_subject_type',
                'value' => $request->get_param('subject_type'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('province')) && empty($request->get_param('district')) && empty($request->get_param('ward'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_location',
                'value' => $request->get_param('province'),
                'compare' => 'IN',
            );
        }

        if (!empty($request->get_param('province')) && !empty($request->get_param('district')) && empty($request->get_param('ward'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_location',
                'value' => $request->get_param('district'),
                'compare' => 'IN',
            );
        }

        if (!empty($request->get_param('province')) && !empty($request->get_param('district')) && !empty($request->get_param('ward'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_location',
                'value' => $request->get_param('wards'),
                'compare' => 'IN',
            );
        }

        if (sizeof($meta_query) > 0) {
            $meta_query['relation'] = 'AND';
            $args['meta_query'] = $meta_query;
        }

        if (!empty($request->get_param('helpdesk_category'))) {
            $tax_query = array(
                array(
                    'taxonomy' => 'helpdesk_category',
                    'field' => 'slug',
                    'terms' => $request->get_param('helpdesk_category'),
                ),
            );
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $helpdesk_contents = get_posts($args);
        $result = array();
        foreach ($helpdesk_contents as $content) {
            $item = (array)$content;
            $item['acf'] = get_fields($content->ID);
            $item['terms'] = wp_get_post_terms($content->ID, 'helpdesk_category');
            $result[] = $item;
        }
        return $result;
    }

    public function get_project_directories(\WP_REST_Request $request)
    {
        if (empty($request->get_param('project'))) {
            return array();
        }

        if (!empty($request->get_param('page'))) {
            $args['page'] = $request->get_param('page');
        }

        $args = array(
            'numberposts' => -1,
            'post_type' => 'project_directory',
            'meta_key' => 'project_directory_id',
            'meta_value' => $request->get_param('project')
        );

        return get_posts($args);
    }

    public function get_districts(\WP_REST_Request $request)
    {
        if (empty($request->get_param('province_id'))) {
            return array();
        }

        $districts = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'district',
            'meta_key' => 'province_id',
            'meta_value' => $request->get_param('province_id')
        ));

        $result = array();
        foreach ($districts as $district) {
            $item = (array)$district;
            $item['acf'] = get_fields($district->ID);
            if (empty($item['acf'])) {
                $province_id = get_post_meta($district->ID, 'province_id');
                $province = get_post($province_id[0]);
                $district_code = get_post_meta($district->ID, 'district_code');
                $item['acf'] = array(
                    'province_id' => $province,
                    'district_code' => ((is_array($district_code) && sizeof($district_code) > 0) ? $district_code[0] : '')
                );
            }
            $result[] = $item;
        }

        return $result;
    }

    public function get_wards(\WP_REST_Request $request)
    {
        if (empty($request->get_param('district_id'))) {
            return array();
        }

        $wards = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'ward',
            'meta_key' => 'district',
            'meta_value' => $request->get_param('district_id')
        ));

        $result = array();
        foreach ($wards as $ward) {
            $item = (array)$ward;
            $item['acf'] = get_fields($ward->ID);
            if (empty($item['acf'])) {
                $province_id = get_post_meta($ward->ID, 'province_id');
                $province = get_post($province_id[0]);

                $district_id = get_post_meta($ward->ID, 'district_id');
                $district = get_post($district_id[0]);

                $ward_code = get_post_meta($ward->ID, 'ward_code');

                $item['acf'] = array(
                    'province_id' => $province,
                    'district_id' => $district,
                    'ward_code' => ((is_array($ward_code) && sizeof($ward_code) > 0) ? $ward_code[0] : '')
                );
            }
            $result[] = $item;
        }

        return $result;
    }

    public function get_villages(\WP_REST_Request $request)
    {
        if (empty($request->get_param('ward_id'))) {
            return array();
        }

        $villages = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'village',
            'meta_key' => 'ward_id',
            'meta_value' => $request->get_param('ward_id')
        ));
        $result = array();
        foreach ($villages as $village) {
            $item = (array)$village;
            $item['acf'] = get_fields($village->ID);
            if (empty($item['acf'])) {
                $province_id = get_post_meta($village->ID, 'province_id');
                $province = get_post($province_id[0]);

                $district_id = get_post_meta($village->ID, 'district_id');
                $district = get_post($district_id[0]);

                $ward_id = get_post_meta($village->ID, 'ward_id');
                $ward = get_post($ward_id[0]);

                $village_code = get_post_meta($ward->ID, 'village_code');

                $item['acf'] = array(
                    'province_id' => $province,
                    'district_id' => $district,
                    'ward_id' => $ward,
                    'village_code' => ((is_array($village_code) && sizeof($village_code) > 0) ? $village_code[0] : '')
                );
            }
            $result[] = $item;
        }

        return $result;
    }

    public function get_project_actions(\WP_REST_Request $request)
    {
        $meta_query = array();

        if (!empty($request->get_param('project'))) {
            $meta_query[] = array(
                'key' => 'project',
                'value' => $request->get_param('project'),
            );
        }

        if (!empty($request->get_param('subject_type'))) {
            $meta_query[] = array(
                'key' => 'action_subject_type',
                'value' => $request->get_param('subject_type'),
            );
        }

        if (!empty($request->get_param('location'))) {
            $meta_query[] = array(
                'key' => 'action_location',
                'value' => $request->get_param('location'),
                'compare' => 'IN',
            );
        }

        if (!empty($request->get_param('action_target'))) {
            $meta_query[] = array(
                'key' => 'action_target',
                'value' => $request->get_param('action_target'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('action_subject'))) {
            $meta_query[] = array(
                'key' => 'action_subject',
                'value' => $request->get_param('action_target'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('action_organizational'))) {
            $meta_query[] = array(
                'key' => 'action_organizational',
                'value' => $request->get_param('action_organizational'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('action_construction'))) {
            $meta_query[] = array(
                'key' => 'action_construction',
                'value' => $request->get_param('action_construction'),
                'compare' => 'LIKE',
            );
        }

        $args = array(
            'post_type' => 'project_action',
            'paged' => 1,
            'numberposts' => 20
        );

        if (!empty($request->get_param('page'))) {
            $args['paged'] = $request->get_param('page');
        }

        if (!empty($request->get_param('per_page'))) {
            $args['numberposts'] = $request->get_param('per_page');
        }

        if (sizeof($meta_query) > 0) {
            $meta_query['relation'] = 'AND';
            $args['meta_query'] = $meta_query;
        }

        return get_posts($args);
    }

    public function get_enterprise(\WP_REST_Request $request)
    {
        $meta_query = array();

        if (!empty($request->get_param('project'))) {
            $meta_query[] = array(
                'key' => 'enterprise_project',
                'value' => $request->get_param('project'),
            );
        }

        if (!empty($request->get_param('action'))) {
            $meta_query[] = array(
                'key' => 'enterprise_action',
                'value' => $request->get_param('action'),
            );
        }

        if (!empty($request->get_param('location'))) {
            $location = $request->get_param('location');
        }

        if (!empty($request->get_param('position')) && $request->get_param('position') != 0) {
            $meta_query[] = array(
                'key' => 'position',
                'value' => $request->get_param('position'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('subject_type'))) {
            $meta_query[] = array(
                'key' => 'project_directory_subject_type',
                'value' => $_GET['doi_tuong'],
            );
        }

        $args = array(
            'numberposts' => !empty($request->get_param('numberposts')) ? $request->get_param('numberposts') : -1,
            'post_type' => 'project_directory',
            'paged' => !empty($request->get_param('page')) ? $request->get_param('page') : 1,
            's' => !empty($request->get_param('search')) ? $request->get_param('search') : '',
        );

        if (sizeof($meta_query) > 0) {
            $meta_query['relation'] = 'AND';
            $args['meta_query'] = $meta_query;
        }

        $project_directory_list = get_posts($args);
        $enterprise_list = array();
        foreach ($project_directory_list as $directory) {
            if (!empty($location)) {
                $project_directory_locations = get_field('location', $directory->ID);

                $exist_location = array_filter($project_directory_locations, function ($obj) use ($location) {
                    return ($obj->ID == $location);
                });
            }

            if (!empty($exist_location) || empty($location)) {
                $enterprise = get_field('enterprise_directory', $directory);
                $item = (array)$enterprise;
                $item['acf'] = get_fields($enterprise->ID);
                $item['position'] = get_field('role', $directory->ID);
                $item['logo'] = get_the_post_thumbnail_url($enterprise->ID);
                $enterprise_list[] = $item;
            }
        }
        return $enterprise_list;
    }

    public function get_project_children(\WP_REST_Request $request)
    {
        $args = array('post_type' => 'project', 'numberposts' => -1, 'order' => 'asc');

        if (!empty($request->get_param('project'))) {
            $args['post_parent'] = $request->get_param('project');
        } else {
            $args['post_parent'] = 0;
        }

        return get_posts($args);
    }

    public function get_actions_by_project(\WP_REST_Request $request)
    {
        $args = array('post_type' => 'project_action', 'numberposts' => -1, 'order' => 'asc');

        if (!empty($request->get_param('project'))) {
            $args['meta_key'] = 'project';
            $args['meta_value'] = $request->get_param('project');
        } else {
            return array();
        }

        return get_posts($args);
    }

    public function new_suggestion($data)
    {
        if (empty($data)) {
            return new WP_Error(
                'woocommerce_rest_cannot_view',
                'Vui lòng truền lên đầy đủ dữ liệu góp ý đánh giá',
                array(
                    'status' => 400,
                )
            );
        }

        if (empty($data['acf'])) {
            return new WP_Error(
                'woocommerce_rest_cannot_view',
                'Vui lòng truền lên đầy đủ dữ liệu góp ý đánh giá.',
                array(
                    'status' => 400,
                )
            );
        }

        $suggestion_data = array(
            'post_title' => $data['post_title'],
            'post_type' => 'suggestion',
            'post_status' => 'publish'
        );

        $suggestion_id = wp_insert_post($suggestion_data);

        if (!empty($suggestion_id)) {
            foreach ($data['acf'] as $key => $value) {
                update_field($key, $value, $suggestion_id);
            }
        }

        $result = get_post($suggestion_id);
        $suggestion = (array)$result;
        $suggestion['acf'] = get_fields($suggestion_id);

        return $suggestion;
    }
}

add_action('rest_api_init', function () {
    $latest_posts_controller = new WC_REST_Advance_Search_Helpdesk_Controller();
    $latest_posts_controller->register_routes();
});
