<?php
class AudiateWidget {
    private $options;

    /**
     * @var string
     */
    private static $fieldKey = 'audiate_widget';

    public function __construct()
    {
        $this->options = get_option('audiate_option_name');

        add_action('add_meta_boxes', array($this, 'create_audiate_checkbox'));
        add_action('save_post', array($this, 'save_audiate_checkbox'));

        $this->add_widget();
    }

    public function create_audiate_checkbox()
    {
        if ($this->options['show_on_all_posts_1'] !== 'yes') {
            add_meta_box(
                'has_audiate_checkbox',
                'Audiate Widget',
                array($this, 'show_audiate_checkbox_in_post_edit'),
                'post',
                'side',
                'high'
            );
        }

        if ($this->options['show_on_all_pages_3'] !== 'yes') {
            add_meta_box(
                'has_audiate_checkbox',
                'Audiate Widget',
                array($this, 'show_audiate_checkbox_in_post_edit'),
                'page',
                'side',
                'high'
            );
        }
    }

    public function show_audiate_checkbox_in_post_edit()
    {
        global $post;

        $key = AudiateWidget::$fieldKey;
        $fieldValue = get_post_meta($post->ID, $key, true);
        ?>
        <input name="audiate_checkbox_nonce"  type="hidden" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>">
        <p>
            <label for="<?php echo $key ?>">
                <input
                    name="<?php echo $key ?>"
                    id="<?php echo $key ?>"
                    value="on"
                    type="checkbox"
                    <?php echo isset($fieldValue) && $fieldValue === 'on' ? 'checked' : ""; ?>
                > Show Widget on Page
            </label>
        </p>
        <?php
    }

    public function save_audiate_checkbox($postId)
    {
        // verify nonce
        if (!wp_verify_nonce($_POST['audiate_checkbox_nonce'], basename(__FILE__))) {
            return $postId;
        }

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $postId;
        }

        // check permissions
        if ('page' === $_POST['post_type']) {
            if (!current_user_can('edit_page', $postId)) {
                return $postId;
            } elseif (!current_user_can('edit_post', $postId)) {
                return $postId;
            }
        }

        $fieldValue = sanitize_text_field( $_POST[AudiateWidget::$fieldKey] );

        if ($fieldValue == 'on') {
            update_post_meta($postId, AudiateWidget::$fieldKey, $fieldValue);
        } else {
            delete_post_meta($postId, AudiateWidget::$fieldKey);
        }

        return $postId;
    }

    public function add_widget()
    {
        if(isset($this->options['widget_code_2'])) {

            add_filter('the_content', array($this, 'add_code_to_post_content'), 2000, 2 );
            add_shortcode('audiate-widget', 'getShortCode');
        }
    }

    public function get_code($isTitle = false) {
        if(!$this->options['widget_code_2']) {
           return '';
        }

        $widget =  '<div class="audiate-widget">' . $this->options['widget_code_2'] . '</div>';

        if($isTitle) {
            $widget = str_replace(array("\n", "\r"), "", str_replace("</script>", "</scr'+'ipt>", $widget));
            $widget = "<script>
             (function() {
                let article = document.querySelector('h1.entry-title');
                if(!article) {
                  return;
                }
                const tag = '" .$widget . "';                
                const scriptRegex = /<script .*<\/script>/gi
                const scriptPart = scriptRegex.exec(tag)[0].replace(/\<script|\<\/script>/gi, '').split(' ')
                const restTag = tag.replace(scriptRegex, '')
                    
                const script = document.createElement('script')
                  
                scriptPart.filter(Boolean).forEach(part => {

                  const [k, v] = part.split('=')
                  
                  if (!v){
                    return
                  }
                
                  script.setAttribute(k, v.replace(/^\"/, '').replace(/\">?$/, ''))
                })
                  
                document.body.appendChild(script)                 
                article.insertAdjacentHTML( 'afterend',  restTag);
             })();
            </script>";
        }
        return $widget;
    }

    /*
    * Add the widget to posts before the post content
    */
    public function add_code_to_post_content($content) {
        global $post;
        $allowedPostTypes = array('post', 'page');

        if (is_singular($allowedPostTypes) && in_the_loop() && is_main_query()) {
            if(!isset($this->options['position_4']) || $this->options['position_4'] === 'before_content') {
                $widget = $this->get_code();
            } else if ($this->options['position_4'] === 'after_title') {
                $widget = $this->get_code(true);
            }

            if(
                    get_post_type() === 'post' && $this->options['show_on_all_posts_1'] === 'yes' ||
                    get_post_type() === 'page' && $this->options['show_on_all_pages_3'] === 'yes'
            ) {
                    return $widget . $content;
            }

            $showOnPost = get_post_meta($post->ID, self::$fieldKey, true);

            if (isset($showOnPost) && $showOnPost === 'on') {
                return $widget . $content;
            }

            return $content;
        }
        return $content;
    }

}