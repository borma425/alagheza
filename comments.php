						<form  method="POST" action="<?php echo esc_url(get_option('siteurl')); ?>/wp-comments-post.php"  id="commentform">
                        <input type="hidden" name="action" value="k_comment">
		<input type="hidden" name="post_id" value="143071">
        <input type="hidden" id="url" name="url" required="" value="admin">
                        
                        <label for="review">المراجعة:</label>
							<textarea   aria-labelledby="review-label" id="review" name="comment"  rows="5" required=""></textarea>

							<label   for="name">الاسم:</label>
							<input  aria-labelledby="name-label" type="text" name="author" id="author" required="">

							<label for="email">البريد الإلكتروني:</label>
							<input   aria-labelledby="email-label"  type="email" id="email" name="email" required="">

							<button class="btn btn-primary" type="submit" name="submit" id="submit">
            <?php comment_id_fields(); ?>

								إرسال المراجعة
							</button>
    <?php do_action('comment_form', $post->ID); ?>

						</form>
						<br>

