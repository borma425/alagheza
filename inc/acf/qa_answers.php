<?php


function add_qa_meta_box() {
      add_meta_box(
          'qa_meta_box',             // ID of the meta box
          'أسئلة & أجوبة',     // Title of the meta box
          'display_qa_meta_box',     // Callback function to display the meta box
          'post',                    // Post type where this box will appear
          'normal',                  // Position
          'high'                     // Priority
      );
  }
  add_action('add_meta_boxes', 'add_qa_meta_box');

  
  function display_qa_meta_box($post) {
    $qa_data = get_post_meta($post->ID, '_qa_meta_key', true);
    $qa_data = is_array($qa_data) ? $qa_data : [];
echo '<style>/* Meta Box Container */
.qa-meta-box {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    margin-top: 15px;
}

/* Styling for Question & Answer Fields */
.qa-pair {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 15px;
    position: relative;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.qa-pair .qa-question, .qa-pair .qa-answer {
    margin-bottom: 10px;
}

.qa-input, .qa-textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.qa-input:focus, .qa-textarea:focus {
    border-color: #007cba;
    outline: none;
}

/* Add button styles */
.add-qa-btn {
    display: inline-block;
    background-color: #007cba;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
}

.add-qa-btn:hover {
    background-color: #005a9e;
}

/* Remove button styles */
.remove-qa {
    background-color: #dc3545;
    color: #fff;
    border: none;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 4px;
    cursor: pointer;
    position: absolute;
    top: -15px;
    right: 10px;
}

.remove-qa:hover {
    background-color: #c82333;
}

/* Responsive */
@media (max-width: 768px) {
    .qa-pair {
        flex-direction: column;
    }
}
</style>';
    wp_nonce_field(basename(__FILE__), 'qa_meta_nonce');
    $qa_title = get_post_meta($post->ID, 'qa_title', true);

    ?>
    <div class="qa-meta-box">

        <div id="qa-fields-container">

        <div>
<label style="font-size:medium;">العنوان الرئيسي</label>
    <input type="text" name="qa_title" value="<?php echo esc_attr( $qa_title ); ?>" class="qa-input" placeholder="العنوان الرئيسي"/>

</div><br><br>


            <?php if(!empty($qa_data)) : ?>
                <?php foreach ($qa_data as $index => $qa): ?>
                    <div class="qa-pair">
                        <div class="qa-question">
                            <label>السؤال</label>
                            <input type="text" name="qa_questions[]" value="<?php echo esc_attr($qa['question']); ?>" class="qa-input"/>
                        </div>
                        <div class="qa-answer">
                            <label>الإجابة</label>
                            <textarea name="qa_answers[]" class="qa-textarea"><?php echo esc_textarea($qa['answer']); ?></textarea>
                        </div>
                        <button type="button" class="remove-qa">الحذف</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>

                <div class="qa-pair">

                    <div class="qa-question">
                        <label>السؤال</label>
                        <input type="text" name="qa_questions[]" class="qa-input" />
                    </div>
                    <div class="qa-answer">
                        <label>الإجابة</label>
                        <textarea name="qa_answers[]" class="qa-textarea"></textarea>
                    </div>
                    <button type="button" class="remove-qa">الحذف</button>
                </div>
            <?php endif; ?>
        </div>

        <button type="button" id="add-qa-button" class="add-qa-btn">اضافة المزيد</button>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#add-qa-button').on('click', function() {
                $('#qa-fields-container').append(`
                    <div class="qa-pair">
                        <div class="qa-question">
                            <label>السؤال</label>
                            <input type="text" name="qa_questions[]" class="qa-input" />
                        </div>
                        <div class="qa-answer">
                            <label>الإجابة</label>
                            <textarea name="qa_answers[]" class="qa-textarea"></textarea>
                        </div>
                        <button type="button" class="remove-qa">الحذف</button>
                    </div>
                `);
            });

            $('#qa-fields-container').on('click', '.remove-qa', function() {
                $(this).closest('.qa-pair').remove();
            });
        });
    </script>

    <?php
}


  
  function save_qa_meta_data($post_id) {

  
      // Check auto-save
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return $post_id;
      }
  
      // Check user permissions
      if (!current_user_can('edit_post', $post_id)) {
          return $post_id;
      }
  


      
      if (isset($_POST['qa_title'])) {
        update_post_meta($post_id, 'qa_title', $_POST['qa_title']);
    }




      // Save questions and answers
      if (isset($_POST['qa_questions']) && isset($_POST['qa_answers'])) {
          $qa_data = [];
          $questions = $_POST['qa_questions'];
          $answers = $_POST['qa_answers'];
  
          for ($i = 0; $i < count($questions); $i++) {
              if (!empty($questions[$i]) && !empty($answers[$i])) {
                  $qa_data[] = [
                      'question' => sanitize_text_field($questions[$i]),
                      'answer'   => sanitize_textarea_field($answers[$i])
                  ];
              }
          }
  
          update_post_meta($post_id, '_qa_meta_key', $qa_data);
      } else {
          delete_post_meta($post_id, '_qa_meta_key');
      }
  }
  add_action('save_post', 'save_qa_meta_data');
  