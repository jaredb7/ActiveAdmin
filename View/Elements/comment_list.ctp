<?php
App::uses('AdminComment', 'ActiveAdmin.Model');
//If the controller name is set and we're not on the dashboard controller and not on the index or add action
if (isset($controller_name) && isset($resource_id)) {
    //Retrieve the list of comments for this controller and resource_id
    $admin_comment = new AdminComment();
    $comments = $admin_comment->findComments($controller_name, $resource_id);
    //Get comments count
    $comment_count = !empty($comments) ? count($comments) : 0;

    ?>
    <h3>Comments (<?php echo $comment_count ?>)</h3>

    <div class="panel_contents">
        <?php
        foreach ($comments as $id => $comment):
            $comment = $comment['AdminComment'];
            ?>
            <div class="active_admin_comment" id="active_admin_comment_<?php echo $comment['id'] ?>">

                <div class="active_admin_comment_meta">
                    <h4 class="active_admin_comment_author"><?php echo $comment['author'] ? : "Anonymous"; ?></h4>
                    <span><?php echo CakeTime::nice($comment['created']) ?></span>
                </div>

                <div class="active_admin_comment_body"><p> <?php echo $comment['body']; ?> </p></div>
                <div style="clear:both;"></div>
            </div>
        <?php endforeach;
        if (empty($comments)):?>
            <span class="empty">No comments yet.</span>
        <?php endif ?>
    </div>
<?php } ?>