<section id="main">
    <div class="container">
        <div class="row">
            <div class="page-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"> <?= trans("home"); ?></a></li>
                    <?php if (!empty($categoryArray['parentCategory'])): ?>
                        <li class="breadcrumb-item"><a href="<?= generateCategoryUrl(null, $categoryArray['parentCategory']->slug); ?>"><?= esc($categoryArray['parentCategory']->name); ?></a></li>
                    <?php endif;
                    if (!empty($categoryArray['subcategory'])): ?>
                        <li class="breadcrumb-item"><a href="<?= generateCategoryUrl($categoryArray['parentCategory']->slug, $categoryArray['subcategory']->slug); ?>"><?= esc($categoryArray['subcategory']->name); ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active"><?= esc($post->title); ?></li>
                </ol>
            </div>
            <div class="col-sm-12 col-md-8">
                <div class="content">
                    <div class="post-content">
                        <div class="post-title"><h1 class="title"><?= esc($post->title); ?></h1></div>
                        <?php if (!empty($post->summary)): ?>
                            <div class="post-summary"><h2><?= $post->summary; ?></h2></div>
                        <?php endif; ?>
                        <div class="post-meta">
                            <?php if (!empty($category) && !empty($category->parent_id)):
                                $parent = getCategory($category->parent_id);
                                if (!empty($parent)):?>
                                    <a href="<?= generateCategoryUrl($parent->slug, $category->slug); ?>" class="font-weight-normal"><i class="icon-folder"></i>&nbsp;&nbsp;<?= esc($category->name); ?></a>
                                <?php endif;
                            else: ?>
                                <a href="<?= generateCategoryUrl(null, $category->slug); ?>" class="font-weight-normal">
                                    <i class="icon-folder"></i>&nbsp;&nbsp;<?= esc($category->name); ?>
                                </a>
                            <?php endif; ?>
                            <span><i class="icon-clock"></i>&nbsp;&nbsp;<?= dateFormatDefault($post->created_at); ?></span>
                            <?php if ($generalSettings->comment_system == 1) : ?>
                                <span><i class="icon-comment"></i>&nbsp;&nbsp;<?= getPostCommentCount($post->id); ?> </span>
                            <?php endif; ?>
                            <?php if ($generalSettings->show_pageviews == 1) : ?>
                                <span><i class="icon-eye"></i>&nbsp;&nbsp;<?= $post->hit; ?></span>
                            <?php endif;
                            if ($post->post_type == 'petition') :
                                if ($post->petition_activate == 1) :?>
                                    <button class="btn btn-md btn-custom" data-toggle="modal" data-target="#myModal"><?= trans("sign_this"); ?></button>
                                <?php else: ?>
                                    <button class="btn btn-md btn-custom" data-toggle="modal" data-target="#myModal" style="background-color: grey;" disabled><?= trans("sign_this"); ?></button>
                                    <span style="margin-left: 10px;color: blue;font-size: 14px;">Campaign Completed</span>
                                <?php endif;?>
                            <?php endif;
                            if (authCheck()) : ?>
                                <form action="<?= base_url('add-remove-reading-list-post'); ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="post_id" value="<?= $post->id; ?>">
                                    <?php if ($is_reading_list == false) : ?>
                                        <button type="submit" class="add-to-reading-list pull-right"><i class="icon-plus-circle"></i>&nbsp;<?= trans("add_reading_list"); ?></button>
                                    <?php else: ?>
                                        <button type="submit" class="delete-from-reading-list  pull-right"><i class="icon-negative-circle"></i>&nbsp;<?= trans("delete_reading_list"); ?></button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <a href="<?= langBaseUrl('login'); ?>" class="add-to-reading-list pull-right">
                                    <i class="icon-plus-circle"></i>&nbsp;<?= trans("add_reading_list"); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if ($post->post_type == 'petition') :?>
                        <h3>Petition Delivered To</h3>
                        <?php endif; ?>
                        <div class="post-recipient">
                            <?php
                                foreach($postRecipients as $postRecipient) {?>
                                <div class="row">
                                <div class="col-xs-6" style="margin-bottom: 10px;">
                                    <div><?php echo $postRecipient->recipient_name;?></div>
                                    
                                </div>
                                <div class="col-xs-6" style="margin-bottom: 10px;">
                                    <div><?php echo $postRecipient->recipient_email;?></div>
                                    
                                </div>
                                </div>
                                
                            <?php }?>
                        </div>
                        <?php if (!empty($post->video_embed_code)): ?>
                            <div class="post-video" style="margin-top: 50px;">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="<?= $post->video_embed_code; ?>" allowfullscreen></iframe>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="post-image" style="margin-top: 50px;">
                                <?php if (!empty($additionalImages)):
                                    echo view("post/_post_details_slider", ["adSpace" => "post_top"]);
                                else:
                                    if (!empty($post->image_url)):?>
                                        <img src="<?= $post->image_url; ?>" class="img-responsive center-image" alt="<?= esc($post->title); ?>"/>
                                    <?php else:
                                        if (!empty($post->image_big)): ?>
                                            <img src="<?= getPostImage($post, 'big'); ?>" class="img-responsive center-image" alt="<?= esc($post->title); ?>"/>
                                        <?php endif;
                                    endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?= view("partials/_ad_spaces", ["adSpace" => "post_top"]); ?>
                        
                        <div class="post-text text-style">
                            <?= $post->content;
                            if (!empty($post->optional_url)) : ?>
                                <div class="optional-url-cnt">
                                    <a href="<?= esc($post->optional_url); ?>" class="btn btn-md btn-custom" target="_blank" rel="nofollow">
                                        <?= esc($settings->optional_url_button_name); ?>&nbsp;&nbsp;&nbsp;<i class="icon-long-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($post->post_url) && !empty($post->show_post_url)) : ?>
                                <div class="optional-url-cnt">
                                    <a href="<?= $post->post_url; ?>" class="btn btn-md btn-custom" target="_blank" rel="nofollow">
                                        <?= (!empty($feed->read_more_button_text)) ? esc($feed->read_more_button_text) : esc($settings->optional_url_button_name); ?>&nbsp;&nbsp;&nbsp;<i class="icon-long-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php $files = getPostFiles($post->id);
                            if (!empty($files)):?>
                                <div class="post-files">
                                    <h2 class="title"><?= trans("files"); ?></h2>
                                    <?php foreach ($files as $file): ?>
                                        <form action="<?= base_url('download-file'); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="name" value="<?= $file->file_name; ?>">
                                            <div class="file">
                                                <button type="submit"><i class="icon-file"></i><?= $file->file_name; ?></button>
                                            </div>
                                        </form>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="post-tags">
                            <?php if (!empty($postTags)): ?>
                                <h3 class="tags-title"><?= trans("tags"); ?></h3>
                                <ul class="tag-list">
                                    <?php foreach ($postTags as $tag) : ?>
                                        <li><a href="<?= langBaseUrl('tag/' . esc($tag->tag_slug)); ?>"><?= esc($tag->tag); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <?php if ($post->post_type == 'petition') :
                            if ($post->petition_activate == 1) :?>
                                <button class="btn btn-md btn-custom pull-right" data-toggle="modal" data-target="#myModal"><?= trans("sign_this"); ?></button>
                            <?php else: ?>
                                <button class="btn btn-md btn-custom pull-right" data-toggle="modal" data-target="#myModal" style="background-color: grey;" disabled><?= trans("sign_this"); ?></button>
                                <span style="margin-left: 10px;color: blue;font-size: 14px;">Campaign Completed</span>
                            <?php endif;?>
                        <?php endif;?>
                        
                        <div class="post-share">
                            <a href="javascript:void(0)" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?= langBaseUrl(esc($post->title_slug)); ?>', 'Share This Post', 'width=640,height=450');return false" class="btn-share share facebook">
                                <i class="icon-facebook"></i><span class="hidden-sm">Facebook</span>
                            </a>
                            <a href="javascript:void(0)" onclick="window.open('https://twitter.com/share?url=<?= langBaseUrl(esc($post->title_slug)); ?>&amp;text=<?= urlencode($post->title); ?>', 'Share This Post', 'width=640,height=450');return false" class="btn-share share twitter">
                                <i class="icon-twitter"></i><span class="hidden-sm">Twitter</span>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?= esc($post->title); ?> - <?= langBaseUrl(esc($post->title_slug)); ?>" target="_blank" class="btn-share share whatsapp">
                                <i class="icon-whatsapp"></i><span class="hidden-sm">Whatsapp</span>
                            </a>
                            <a href="javascript:void(0)" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url=<?= langBaseUrl(esc($post->title_slug)); ?>', 'Share This Post', 'width=640,height=450');return false" class="btn-share share linkedin">
                                <i class="icon-linkedin"></i><span class="hidden-sm">Linkedin</span>
                            </a>
                            <a href="javascript:void(0)" onclick="window.open('http://pinterest.com/pin/create/button/?url=<?= langBaseUrl(esc($post->title_slug)); ?>&amp;media=<?= getPostImage($post, 'mid') ?>', 'Share This Post', 'width=640,height=450');return false" class="btn-share share pinterest">
                                <i class="icon-pinterest"></i><span class="hidden-sm">Pinterest</span>
                            </a>
                        </div>

                        <?php if ($generalSettings->emoji_reactions == 1): ?>
                            <div class="col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="reactions noselect">
                                        <h4 class="title-reactions"><?= trans("whats_your_reaction"); ?></h4>
                                        <div id="reactions_result">
                                            <?= view('partials/_emoji_reactions'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="bn-bottom-post">
                                    <?= view("partials/_ad_spaces", ["adSpace" => "post_bottom"]); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?= view('post/_post_about_author', ['postUser' => $postUser]); ?>

                    <?php if (!empty($relatedPosts)): ?>
                        <div class="related-posts">
                            <div class="related-post-title">
                                <h4 class="title"><?= trans("related_posts"); ?></h4>
                            </div>
                            <div class="row related-posts-row">
                                <ul class="post-list">
                                    <?php foreach ($relatedPosts as $item): ?>
                                        <li class="col-sm-4 col-xs-12 related-posts-col">
                                            <a href="<?= langBaseUrl(esc($item->title_slug)); ?>">
                                                <?= view("post/_post_image", ['postItem' => $item, 'type' => 'imageSlider']); ?>
                                            </a>
                                            <h3 class="title">
                                                <a href="<?= langBaseUrl(esc($item->title_slug)); ?>"><?= esc(limitCharacter($item->title, 70, '...')); ?></a>
                                            </h3>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="comment-section">
                                <?php if ($generalSettings->comment_system == 1 || !empty(trim($generalSettings->facebook_comment ?? ''))): ?>
                                    <ul class="nav nav-tabs">
                                        <?php if ($generalSettings->comment_system == 1): ?>
                                            <li class="active"><a data-toggle="tab" href="#comments"><?= trans("comments"); ?></a></li>
                                        <?php endif;
                                        if ($generalSettings->comment_system == 1 && !empty(trim($generalSettings->facebook_comment ?? ''))): ?>
                                            <li><a data-toggle="tab" href="#facebook_comments"><?= trans("facebook_comments"); ?></a></li>
                                        <?php endif; ?>
                                    </ul>
                                    <div class="tab-content">
                                        <?php if ($generalSettings->comment_system == 1): ?>
                                            <div id="comments" class="tab-pane fade in active">
                                                <?= view('post/_add_comment'); ?>
                                                <div id="comment-result">
                                                    <?= view('post/_comments'); ?>
                                                </div>
                                            </div>
                                        <?php endif;
                                        if ($generalSettings->comment_system == 1 && !empty(trim($generalSettings->facebook_comment ?? ''))): ?>
                                            <div id="facebook_comments" class="tab-pane <?= ($generalSettings->comment_system != 1) ? 'active' : 'fade'; ?>">
                                                <div class="fb-comments" data-href="<?= current_url(); ?>" data-width="100%" data-numposts="5"
                                                     data-colorscheme="<?= $darkMode == 1 ? 'dark' : 'light'; ?>"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <?= view('partials/_sidebar'); ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty(trim($generalSettings->facebook_comment ?? ''))):
   echo $generalSettings->facebook_comment;
endif; ?>

<?php if (!empty($post->feed_id)): ?>
    <style>
        .post-text img {
            display: none !important;
        }

        .post-content .post-summary {
            display: none !important;
        }
    </style>
<?php endif; ?>


<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans('add_signature'); ?></h4>
            </div>
            <form>

                <input type="hidden" id="mail_to" value="<?=$postRecipientEmails?>">
                <input type="hidden" id="mail_bcc" value="backstories.ioa@gmail.com">
                <input type="hidden" id="mail_title" value="<?= esc($post->title); ?>">
                <input type="hidden" id="mail_content" value="<?= htmlentities($post->content);?>">

                <?= csrf_field(); ?>
                <input type="hidden" name="user_id" id="modal_user_id" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label"><?php echo trans("sign_name"); ?></label>
                        <input type="text" id="mail_sign_name" class="form-control" required>

                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo trans("sign_contact_number"); ?></label>
                        <input type="text" id="mail_contact_number" class="form-control" required>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="" id="mail_link" target="_blank" class="btn btn-success"><?= trans('sign_send'); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var main_image = '<?php if($post->image_shortened_url) echo $_SERVER['SERVER_NAME'].'/att-i/'.$post->image_shortened_url;?>' + '\n';
    var additional_images = JSON.parse('<?=json_encode($additionalImages);?>');
    var additional_images_content = '';
    
    additional_images.map((item) => {
        additional_images_content = additional_images_content + "<?=$_SERVER['SERVER_NAME'];?>" +'/att-i/' + item['shortened_url'] + '\n';
    });

    var files = JSON.parse('<?=json_encode($files);?>');
    var files_content = '';
    files.map((item) => {
        files_content = files_content + "<?=$_SERVER['SERVER_NAME'];?>" +'/att/' + item['shortened_url'] + '\n';
    });
    
    var mail_attachments = '\n\n' + 'Attachments: ' + '\n' + main_image + additional_images_content + files_content;
    console.log(mail_attachments);
    $(document).ready(function () {
        $('#mail_link').on('click', function() {

            $.ajax({
                url: "<?=base_url('add-sign')?>",
                method: 'get',
                data: {
                    post_id: "<?=$post->id?>",
                    name: $('#mail_sign_name').val(),
                    contact: $('#mail_contact_number').val()
                },
                success: function(res) {
                    // alert(res);
                }
            });

            var mail = 'mailto:'
            var mail_to =  $('#mail_to').val();
            var mail_bcc = '?bcc=' + $('#mail_bcc').val();
            var mail_title = '&subject=' + $('#mail_title').val();
            var mail_content = $('#mail_content').val();
        

            var mail_sign_name = '\n\n' + $('#mail_sign_name').val();
            var mail_contact_number = '\n' + $('#mail_contact_number').val();
            mail_content = mail_content + mail_attachments + mail_sign_name + mail_contact_number;
            mail_content = mail_content.replaceAll('<br />', '\n');
            mail_content = mail_content.replaceAll('&nbsp;', ' ');
            mail_content = mail_content.replace(/<[^>]+>/g, '');

            mail_content = encodeURIComponent(mail_content);
            $('#mail_link').attr('href', mail + mail_to + mail_bcc + mail_title + '&body=' + mail_content);
        });
    });

    
</script>
