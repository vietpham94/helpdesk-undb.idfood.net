<?php

namespace ASH;

class WC_REST_Advance_Search_Helpdesk_Controller
{
    protected $namespace;
    protected $custome_post_types = array(
        'province',                           // Tỉnh TP
        'district',                           // Quận Huyện
        'wards',                              // Phường xã
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
            '/provinces/districts/wards/village',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_village'),
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

        $meta_query = array('relation' => 'AND');

        if (!empty($request->get_param('subject_type'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_subject_type',
                'value' => $request->get_param('subject_type')
            );
        }

        if (!empty($request->get_param('phase'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_phase',
                'value' => $request->get_param('phase')
            );
        }

        if (!empty($request->get_param('action'))) {
            $meta_query[] = array(
                'key' => 'helpdesk_action',
                'value' => $request->get_param('action'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('project_organizational'))) {
            $meta_query[] = array(
                'key' => 'project_organizational',
                'value' => $request->get_param('project_organizational'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('project_subject'))) {
            $meta_query[] = array(
                'key' => 'project_subject',
                'value' => $request->get_param('project_subject'),
                'compare' => 'LIKE',
            );
        }

        if (!empty($request->get_param('project_target'))) {
            $meta_query[] = array(
                'key' => 'project_subject',
                'value' => $request->get_param('project_target'),
                'compare' => 'LIKE',
            );
        }
    }

    public function get_project_directories(\WP_REST_Request $request)
    {

    }

    public function get_districts(\WP_REST_Request $request)
    {
        if (empty($request->get_param('province'))) {
            return array();
        }

        $cities = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'district',
            'meta_key' => 'province_id',
            'meta_value' => $request->get_param('province')
        ));

        return $cities;
    }

    public function get_wards(\WP_REST_Request $request)
    {
        if (empty($request->get_param('district'))) {
            return array();
        }

        $cities = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'wards',
            'meta_key' => 'district',
            'meta_value' => $request->get_param('district')
        ));

        return $cities;
    }

    public function get_village(\WP_REST_Request $request)
    {
        if (empty($request->get_param('district'))) {
            return array();
        }

        $wards = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'village',
            'meta_key' => 'wards_id',
            'meta_value' => $request->get_param('wards')
        ));

        return $wards;
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
            'numberposts' => -1,
            'post_type' => 'project_action',
        );

        if (sizeof($meta_query) > 0) {
            $meta_query['relation'] = 'AND';
            $args['meta_query'] = $meta_query;
        }

        return get_posts($args);
    }
}

add_action('rest_api_init', function () {
    $latest_posts_controller = new WC_REST_Advance_Search_Helpdesk_Controller();
    $latest_posts_controller->register_routes();
});
