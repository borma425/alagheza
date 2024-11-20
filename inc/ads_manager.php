<?php

// إضافة صفحة الإدارة
add_action('admin_menu', function () {
    add_menu_page(
        'وحدات ادسنس',
        'وحدات ادسنس',
        'manage_options',
        'adsense-manager',
        'render_adsense_admin_page',
        'dashicons-image-flip-vertical',
        80
    );
});

// عرض صفحة الإدارة لإدارة أكواد AdSense
function render_adsense_admin_page() {
    // التعامل مع إرسال النموذج
    if (isset($_POST['adsense_codes'])) {
        $adsense_data = array_map(function ($code, $position) {
            return [
                'code' => stripslashes($code),
                'position' => intval($position),
            ];
        }, $_POST['adsense_codes'], $_POST['adsense_positions']);

        // حفظ البيانات في خيارات ووردبريس
        update_option('adsense_data', $adsense_data);
        echo '<div class="updated"><p>تم تحديث إعدادات AdSense!</p></div>';
    }

    // استرجاع بيانات AdSense المحفوظة
    $adsense_data = get_option('adsense_data', []);
    ?>
    <div class="wrap">
        <h1>إدارة AdSense</h1>
        <form method="POST">
            <div id="adsense-wrapper">
                <?php foreach ($adsense_data as $index => $data): ?>
                    <div class="adsense-item">
                        <label for="adsense_codes_<?php echo $index; ?>"><strong>رمز AdSense:</strong></label><br>
                        <textarea name="adsense_codes[]" id="adsense_codes_<?php echo $index; ?>" rows="5" style="width:100%;"><?php echo esc_textarea($data['code']); ?></textarea><br>
                        <label for="adsense_positions_<?php echo $index; ?>"><strong>إدراج بعد الفقرة:</strong></label><br>
                        <input type="number" name="adsense_positions[]" id="adsense_positions_<?php echo $index; ?>" value="<?php echo intval($data['position']); ?>" min="1"><br><br>
                        <button type="button" class="button remove-adsense">إزالة</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button add-adsense">إضافة رمز AdSense آخر</button><br><br>
            <button type="submit" class="button button-primary">حفظ الإعدادات</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('adsense-wrapper');
            document.querySelector('.add-adsense').addEventListener('click', function () {
                const index = wrapper.children.length;
                const template = `
                    <div class="adsense-item">
                        <label for="adsense_codes_${index}"><strong>رمز AdSense:</strong></label><br>
                        <textarea name="adsense_codes[]" id="adsense_codes_${index}" rows="5" style="width:100%;"></textarea><br>
                        <label for="adsense_positions_${index}"><strong>إدراج بعد الفقرة:</strong></label><br>
                        <input type="number" name="adsense_positions[]" id="adsense_positions_${index}" value="1" min="1"><br><br>
                        <button type="button" class="button remove-adsense">إزالة</button>
                    </div>
                `;
                wrapper.insertAdjacentHTML('beforeend', template);
                attachRemoveEvent();
            });

            function attachRemoveEvent() {
                document.querySelectorAll('.remove-adsense').forEach(button => {
                    button.addEventListener('click', function () {
                        this.closest('.adsense-item').remove();
                    });
                });
            }
            attachRemoveEvent();
        });
    </script>
    <style>
        .adsense-item {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .remove-adsense {
            margin-top: 10px;
        }
    </style>
    <?php
}
