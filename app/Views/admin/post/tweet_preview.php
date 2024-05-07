<script>var txt_select_image = "<?= trans("select_image"); ?>";</script>
<?php 

if (!empty($post) && !empty($post->image_mid)){
	$postImgUrl = base_url($post->image_mid);
}
if (empty($post->image_mid)) {
	$postImgUrl = $post->image_url;
}

?>

<div class="row">
    <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12 form-header">
                    <h1 class="form-title"><?= 'Tweet Post'; ?></h1>
                    <a href="<?= adminUrl('posts'); ?>" class="btn btn-success btn-add-new pull-right">
                        <i class="fa fa-bars"></i>
                        <?= trans('posts'); ?>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    
                    <div class="form-post">
                        <div class="form-post-left">
                        
                        	<div class="tweet_block">
                            	<div class="tweet_header">
                                	<div class="tweet_handler">
                                    	<?php 
										if(isset($twitter_content->profile_image_url)){
											?>
											<img class="tweet_p_image" src="<?= $twitter_content->profile_image_url; ?>" />
											<?php
										}
										?>
                                        <span class="tweet_names">
											<?php echo $twitter_content->name;?>
                                            <small>@<?php echo $twitter_content->screen_name;?></small>
                                        </span>
                                        <div class="character-counter"></div>
                                    </div>
                                </div>
                                <div class="tweet_content">
                                	<div class="tweet_text"><?php echo trim(nl2br($tweets[0]));?> 1/<?php echo count($tweets);?></div>
                                    <div class="tweet_images">
                                    	<?php
										if (!empty($postImgUrl)){
											?>
											<img src="<?= $postImgUrl; ?>" />
											<?php 
										}
										?>
                                    </div>
                                </div>
                                <div class="tweet_date">
                                	<?php echo date("h:i A . M d, Y");?>
                                </div>
                                <div class="tweet_btns">
                                	<img class="tweet_btns" src="<?php echo base_url("assets/img");?>/tweet_btn.png" />
                                </div>
                                
                                <?php 
								$i=0;
								if($tweets){
									foreach($tweets as $tweet){
										if($i>0){
											?>
											<div class="tweet_block_reply">
												<div class="tweet_header">
													<div class="tweet_handler">
														<?php 
                                                        if($twitter_content->profile_image_url){
															?>
															<img class="tweet_p_image" src="<?php echo $twitter_content->profile_image_url;?>" />
															<?php
														}
														?>
														<span class="tweet_names">
															<?php echo $twitter_content->name;?>
															<small>@<?php echo $twitter_content->screen_name;?></small>
														</span>
													</div>
												</div>
												<div class="tweet_content">
													<div class="tweet_text"><?php echo trim(nl2br($tweet)); echo " ".($i+1)."/".count($tweets);?></div>
													<div class="tweet_images">
														<div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="post-select-image-container" style="max-width: 100%;" data-image-section="<?php echo $i;?>">
                                                                    <a class="btn-select-image" data-toggle="modal" data-target="#image_file_manager" data-image-type="tweets" data-image-section="<?php echo $i;?>">
                                                                        <div class="btn-select-image-inner">
                                                                            <i class="fa fa-image"></i>
                                                                            <button class="btn"><?= trans("select_image"); ?></button>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                                <input type="hidden" name="tweet_images[]" id="tweet_images<?php echo $i;?>">
                                                            </div>
                                                        </div>
													</div>
												</div>
												<div class="reply_btns">
													<img class="reply_btns" src="<?php echo base_url("assets/img");?>/reply_btn.png" />
												</div>
											</div>      
											<?php
										}
										$i++;
									}
								}
								?>
                                
                            </div>
                        
                            
                        </div>

                        <div class="form-post-right">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <div class="left">
                                                <h3 class="box-title">Tweet</h3>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="form-group">
                                               <img src="<?php echo base_url("assets/img");?>/progress.gif" class="progress"/>
                                               <?php 
												if($post->tweeted==2){
													?>
                                                    <p style="color:green;text-align: right;"><strong>Tweeted</strong></p>
                                                    <p style="color:green;text-align: right;"><strong><?php echo date('d/m/Y h:i:s A', strtotime($twts[0]->tweet_date));?></strong></p>
                                                    <p style="color:green;text-align: right;"><strong>
                                                    <a href="https://twitter.com/<?php echo $twitter_content->screen_name;?>/status/<?php echo $twts[0]->tweet_id?>" target="_blank">View Tweet</a>
                                                    </strong></p>
                                                    <?php
												} else {
													?>
                                                    <a href="<?php echo adminUrl("posts/tweet/")."?id=".$post->id?>" class="btn btn-primary pull-right tweet_now">Edit Tweet</a>
                                                    <?php
												}
											   ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
    </div>
</div>

<?= csrf_field(); ?>

<style>

@import 'https://fonts.googleapis.com/css?family=Roboto:400,700&subset=cyrillic';

.tweet_block {
    padding: 15px;
    background: #fff;
    border-radius: 4px;
    float: left;
    display: block;
    width: 100%;
}
.tweet_handler, .tweet_header, .tweet_content, .tweet_date, .tweet_btns {
    width: 100%;
    float: left;
}
.tweet_images img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 20px;
}
img.tweet_p_image {
    border-radius: 50%;
    display: inline;
    float: left;
    margin-right: 20px;
}
span.tweet_names {
    font-size: 16px;
    display: block;
    float: left;
    color: #333;
}
span.tweet_names small {
    display: block;
    color: #33333391;
}
.tweet_text {
    padding: 20px;
    font-size: 15px;
    width: 100%;
    display: block;
}
img.tweet_btns {
    max-width: 100%;
}
img.reply_btns {
    max-width: 100%;
}
.tweet_block_reply .tweet_text {
    padding: 5px;
}
.tweet_date {
    padding-top: 15px;
    padding-bottom: 10px;
}
img.progress {
    display: none;
    float: left;
    clear: both;
    position: relative;
    top: 7px;
}
.tweet_images .post-select-image-container .btn-select-image {
    width: 100%;
    height: 300px;
}
.tweet_images .post-select-image-container {
    position: relative;
    width: 100%;
    height: 300px;
    max-width: 100%;
    overflow: hidden;
    margin-bottom: 15px;
    border-radius: 2px;
}
.tweet_images .btn-danger {
    color: #fff;
    background-color: #DE524A;
    border-color: #DE524A;
}
.tweet_images .btn-delete-selected-file-image {
    position: absolute;
    top: 0;
    padding: 0;
    right: 0;
    border-radius: 2px;
    width: 22px;
    height: 22px;
    line-height: 20px;
    text-align: center;
}

@media (max-width: 1200px){
	.form-post-left {
		clear: both;
	}
	.form-post-right {
		clear: both;
	}
}

@media (min-width: 1200px){
	.form-post-left {
		display: table-cell;
		padding-right: 150px;
		vertical-align: top;
		padding-bottom: 100px;
	}
}

.post-select-image-container {
    display: none;
}

.tweet_text span {
    background: #ffa0a0;
}
.character-counter {
    float: right;
}
</style>



<?= view('admin/file-manager/_load_file_manager', ['loadImages' => true, 'loadFiles' => true]); ?>