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
                                                        <div class="character-counter"></div>
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
                                                    <?php
												} else {
													?>
													<button name="tweet_now" class="btn btn-primary pull-right tweet_now">Tweet Now</button>
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

<script type="application/javascript">
	$(document).ready(function(e) {
		
		var message = new Array();
		message.push("");
		
		function split_to_chunks(to,text){
		
			var total_length = (280 - to.length);
			var text_arr = text.split(" ");
			var i=0;
			
			var twts = text_arr.map(function(word){
				
				if(i==0){
					var tot_length = total_length;
				} else {
					var tot_length = (274 - to.length);
				}
				if ( (message[i] + word + ' ').length <= tot_length ){
					if (text_arr[text_arr.length-1] == word){
						message[i] = message[i]+word;
					} else {
						message[i] = message[i]+word+' ';
					}
				} else {
					i++;
					if (text_arr[text_arr.length-1] == word){
						message[i] = word;
					} else {
						message[i] = word+' ';
					}
				}
				//return message[i];
			});
			//console.log(message);
			return message;
			
		}
		
		/*var tweetIs = `<?php //echo $tweetIs;?>`;
		var twts = split_to_chunks($(".tweet_names small").eq(0).text(),tweetIs);
		console.log(twts);*/
		
		$(".tweet_text").each(function(index) {
		   var max_length = 280;
		   var character_entered = $(this).text().length + $(".tweet_names small").eq(0).text().length;
		   var character_remaining = max_length - character_entered;
		   $(this).parent().parent().find(".tweet_header").eq(0).find(".character-counter").html(character_remaining);
		});
		
		function count_remaining_character(editable) {
			var max_length = 280;
			var character_entered = $(editable).text().length + $(".tweet_names small").eq(0).text().length;
			var character_remaining = max_length - character_entered;
			
			$(editable).parent().parent().find(".tweet_header").eq(0).find(".character-counter").html(character_remaining);
		
			if(max_length < character_entered)
			{
				$(editable).parent().parent().find(".tweet_header").eq(0).find(".character-counter").css('color','#FF0000');
				var inputText = $(editable).text();
				var allowedText = inputText.substring(0,max_length);
				var extraText = inputText.substring(max_length);
		
				if(editable.childElementCount > 0) {
					var innerText = $(editable).text();
					if(innerText.length >0) {
						var lastIndex = innerText.lastIndexOf(extraText);
						if(lastIndex >= 0) {
							var allowedText = innerText.substring(0,lastIndex);
							$(editable).html(allowedText+"<span>"+extraText+"</span>");
						}
					}
				} else {
					$(editable).html(allowedText+"<span>"+extraText+"</span>");
				}
				placeCaretAtEnd(editable[0]);
			}
			else
			{
			   $(editable).parent().parent().find(".tweet_header").eq(0).find(".character-counter").css('color','#A0A0A0');
			}
		}
		
		function placeCaretAtEnd(el) {
			el.focus();
			var range = document.createRange();
			range.selectNodeContents(el);
			range.collapse(false);
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		}
		
		
		document.addEventListener('click', ({ target }) => {
		  if (!target.closest('.tweet_block_reply') && !target.closest('.modal-file-manager')) {
			$(".tweet_images .post-select-image-container").fadeOut(0);
					$(".tweet_images .post-select-image-container").each(function(index) {
						if($(this).find(".post-select-image-container").length>0){
							$(this).fadeIn(0);
							$(this).find(".post-select-image-container").fadeIn(0);
						}
					});
		  }
		});
				
        $(document).on("click", ".tweet_text", function(){
			//$(".tweet_text").prop("contenteditable", false);
			$(".tweet_images .post-select-image-container").fadeOut(0);
			$(".tweet_images .post-select-image-container").each(function(index) {
                if($(this).find(".post-select-image-container").length>0){
					$(this).fadeIn(0);
					$(this).find(".post-select-image-container").fadeIn(0);
				}
            });
			$(this).prop("contenteditable", true);
			console.log($(this).next(".tweet_images .post-select-image-container").length)
			if($(this).next(".tweet_images").find(".post-select-image-container").length>0){
				$(this).next(".tweet_images").find(".post-select-image-container").fadeIn(0);
			}
		});
		/*$(document).on("keyup", ".tweet_text", function(){
			//console.log($(this).text().length);
			if($(this).text().length + $(".tweet_names small").eq(0).text().length)>=280){
				$(this).css("color", "red");
			} else {
				$(this).css("color", "#55606e");
			}
		});*/
		$(document).on("keyup", ".tweet_text", function(){
			//console.log($(this).text().length);
			count_remaining_character($(this));
		});
		
		
		$(document).on("click", ".tweet_now", function(){
			//get post id and get all text changes made on this page and post in sequence
			var tweets = new Array();
			$(".tweet_text").each(function(index, element) {
               tweets.push($(this).text().trim());
            });
			$(".tweet_now").remove();
			var tweet_images = new Array();
			$('[name="tweet_images[]"]').each(function(index, element) {
               tweet_images.push(element.value);
            });
			console.log(tweet_images);
			
			//post to admin for tweeting
			$(".progress").fadeIn(0);
			setTimeout(function(){
				$.ajax({
					url: "<?php echo adminUrl("posts/post-tweet");?>",
					data: {
						'tweets': tweets,
						'tweet_images': tweet_images,
						'post': '<?php echo $post->id;?>',
						'user_id': '<?php echo user()->id;?>',
						'app_csrf_token': $('[name="app_csrf_token"]').val(),
					},
					type: 'post',
					success: function (response)
					{
						$(".progress").fadeOut(0);
						$(".progress").replaceWith('<p style="color:green;text-align: right;"><strong>Tweet Successful</strong></p>');
					}
				});
			}, 2000);
			
		});
		
    });
</script>

<?= view('admin/file-manager/_load_file_manager', ['loadImages' => true, 'loadFiles' => true]); ?>