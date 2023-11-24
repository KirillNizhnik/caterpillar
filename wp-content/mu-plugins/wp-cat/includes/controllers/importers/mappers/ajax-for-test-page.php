<?php
class AjaxForTestPage{


    public function __construct()
    {
        add_action('wp_ajax_test_assign', array($this, 'ajaxTestAssign'));
        add_action('wp_ajax_nopriv_test_assign', array($this, 'ajaxTestAssign'));
        add_action('wp_ajax_delete-posts', array($this, 'ajaxDeletePosts'));
        add_action('wp_ajax_nopriv_delete-posts', array($this, 'ajaxDeletePosts'));
    }



    private function delete_all_posts($post_type) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }

    public function ajaxDeletePosts(){
        $postType = $_POST['postType'];
        $this->delete_all_posts($postType);
        wp_send_json_success();
        wp_die();
    }


    public function ajaxTestAssign(){
        $model = $_POST['model'];
        $taxonomy = $_POST['taxonomy'];
        $additionalInfo = $_POST['additionalInfo'];
        $family = null;
        if($taxonomy === 'used-family'){
            $assigner = new \Cat\Controllers\Importers\Mappers\FamilyAssignerByRule(
                $model,
                'used_family_custom_textarea',
                $taxonomy,
                false,
                $additionalInfo
            );
            $family = $assigner->checkFamilyByRule();
        }
        elseif ($taxonomy === 'family'){
            $assigner = new \Cat\Controllers\Importers\Mappers\FamilyAssignerByRule(
                $model,
                'family_custom_textarea',
                $taxonomy,
                $additionalInfo,
                false
            );
            $family = $assigner->checkFamilyByRule();
        }
        if($family === false){
            wp_send_json_success('SKIP');
        }
        elseif ($family === true){
            wp_send_json_success('NOT FIND');
        }
        else{
            wp_send_json_success($family->name);
        }
        wp_die();
    }





}new AjaxForTestPage();