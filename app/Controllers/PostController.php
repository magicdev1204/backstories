<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\PostAdminModel;
use App\Models\CategoryModel;
use App\Models\PostFile1Model;
use App\Models\RecipientModel;
use App\Models\TagModel;
use App\Models\RssModel;

use Abraham\TwitterOAuth\TwitterOAuth;

class PostController extends BaseAdminController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->postModel = new PostModel();
        $this->postAdminModel = new PostAdminModel();
    }

    /**
     * Add Post
     */
    public function addPost()
    {
        checkPermission('add_post');
        $data['title'] = trans("add_post");
        $model = new CategoryModel();
        $data['parentCategories'] = $model->getParentCategoriesByLang($this->activeLang->id);
        $data['adminSettings'] = getAdminSettings();
        $data['postType'] = "post";

        echo view('admin/includes/_header', $data);
        echo view('admin/post/add_post', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Post Post
     */
    public function addPostPost()
    {
        checkPermission('add_post');
        $backUrl = adminUrl('add-post');
        if (inputPost('post_type') == 'video') {
            $backUrl = adminUrl('add-video');
        }

        if(inputPost('post_type') == 'petition') {
            $backUrl = adminUrl('add-petition');
        }
        
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        $val->setRule('summary', trans("summary"), 'max_length[5000]');
        $val->setRule('category_id', trans("category"), 'required');
        $val->setRule('optional_url', trans("optional_url"), 'max_length[1000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to($backUrl)->withInput();
        } else {
            $postId = $this->postAdminModel->addPost();
            if (!empty($postId)) {

                if(inputPost('post_type') == 'petition') {
                    $this->postAdminModel->addPostRecipients($postId);
                }

                $this->postAdminModel->updateSlug($postId);
                $tagModel = new TagModel();
                $tagModel->addPostTags($postId);
                $this->postAdminModel->addPostAdditionalImages($postId);
                $this->postAdminModel->addPostFiles($postId);

                resetCacheDataOnChange();
                if(inputPost('post_type') == 'petition') {
                    $this->session->setFlashdata('success', trans("petition") . " " . trans("msg_suc_added"));
                    return redirect()->to($backUrl);
                }
                $this->session->setFlashdata('success', trans("post") . " " . trans("msg_suc_added"));
                return redirect()->to($backUrl);
            }
        }
        $this->session->setFlashdata('error', trans("msg_error"));
        return redirect()->to($backUrl)->withInput();
    }

    /**
     * Edit Post
     */
    public function editPost($id)
    {
        $this->checkRolePermission();
        $data['title'] = trans("update_post");
        $data['post'] = $this->postAdminModel->getPost($id);
        if (empty($data['post'])) {
            return redirect()->to(adminUrl('posts'));
        }
        $data['adminSettings'] = getAdminSettings();
        //check if author
        if (!isAdmin()) {
            if ($data['post']->user_id != user()->id) {
                return redirect()->to(adminUrl());
            }
        }
        //combine post tags
        $tags = "";
        $count = 0;
        $tagModel = new TagModel();
        $tagsArray = $tagModel->getPostTags($id);
        if (!empty($tagsArray)) {
            foreach ($tagsArray as $item) {
                if ($count > 0) {
                    $tags .= ",";
                }
                $tags .= $item->tag;
                $count++;
            }
        }

        $data['tags'] = $tags;
        $data['post_images'] = getPostAdditionalImages($id);
        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->getParentCategoriesByLang($data['post']->lang_id);
        $data['users'] = $this->authModel->getAuthors();
        $data['category_id'] = '';
        $data['subcategory_id'] = '';
        $categoryArray = getCategoryArray($data['post']->category_id);
        if (!empty($categoryArray['parentCategory'])) {
            $data['category_id'] = $categoryArray['parentCategory']->id;
        }
        if (!empty($categoryArray['subcategory'])) {
            $data['subcategory_id'] = $categoryArray['subcategory']->id;
        }
        $data['subcategories'] = $categoryModel->getAllSubcategoriesByParentId($data['category_id']);

        echo view('admin/includes/_header', $data);
        if ($data['post']->post_type == "video") {
            echo view('admin/post/edit_video', $data);
        } else {
            echo view('admin/post/edit_post', $data);
        }
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Post Post
     */
    public function editPostPost()
    {
        $this->checkRolePermission();
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        $val->setRule('summary', trans("summary"), 'max_length[5000]');
        $val->setRule('category_id', trans("category"), 'required');
        $val->setRule('optional_url', trans("optional_url"), 'max_length[1000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $postId = inputPost('id');
            $post = $this->postAdminModel->getPost($postId);
            if(empty($post)){
                return redirect()->to(adminUrl('posts'));
            }
            //check if author
            if (!isAdmin()) {
                if ($post->user_id != user()->id) {
                    return redirect()->to(adminUrl());
                }
            }
            if ($this->postAdminModel->editPost($postId)) {

                //
                if(inputPost('post_type') == 'petition') {
                    
                    $this->postAdminModel->removePostRecipients($postId);
                    $this->postAdminModel->addPostRecipients($postId);
                }

                //update slug
                $this->postAdminModel->updateSlug($postId);
                $tagModel = new TagModel();
                $tagModel->editPostTags($postId);
                $this->postAdminModel->addPostAdditionalImages($postId);
                $this->postAdminModel->addPostFiles($postId);

                resetCacheDataOnChange();
                if(inputPost('post_type') == 'petition') {
                    $this->session->set('msg_success', trans("petition") . " " . trans("msg_suc_updated"));
                    return redirect()->to(adminUrl('edit-petition') . '/' . $postId);
                }
                $this->session->set('msg_success', trans("post") . " " . trans("msg_suc_updated"));
                return redirect()->to(adminUrl('edit-post') . '/' . $postId);
            }
        }
        $this->session->set('error', trans("msg_error"));
        return redirect()->back()->withInput();
    }

    /**
     * Add Video
     */
    public function addVideo()
    {
        checkPermission('add_post');
        $data['title'] = trans("add_video");
        $model = new CategoryModel();
        $data['adminSettings'] = getAdminSettings();
        $data['parentCategories'] = $model->getParentCategoriesByLang($this->activeLang->id);
        $data['postType'] = "video";

        echo view('admin/includes/_header', $data);
        echo view('admin/post/add_video', $data);
        echo view('admin/includes/_footer');
    }

    /*
    * Posts
    */
	
    public function posts()
    {
        $this->checkRolePermission();
		
		require APPPATH."/Libraries/twitteroauth-main/vendor/autoload.php";
		
        $data['title'] = trans('posts');
        $data['authors'] = $this->authModel->getAuthors();
        $data['formAction'] = adminUrl("posts");
        $data['listType'] = "posts";
        $data['adminSettings'] = getAdminSettings();
        $numRows = $this->postAdminModel->getPostsPaginatedCount($data['listType']);
        $pager = paginate($this->getPostsPerPage(), $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated($this->getPostsPerPage(), $pager->offset, $data['listType']);
		
		$consumer_key = 'K7cvD1TI1SDdEPirhgwyHykR7';
		$consumer_secret = 'MXgeCC7wTFScW254ln9DaclMRkmZENqca8eoIGiLaRrDP42D9d';
		
		$connection = new TwitterOAuth($consumer_key, $consumer_secret);
		
		/*echo "<pre>";
		print_r($_SESSION);
		die;*/
		
		if(isset($_SESSION['oauth_token'])){
	
		  $oauth_token=$_SESSION['oauth_token'];
		  //unset($_SESSION['oauth_token']);
		  
		  if(isset($_GET['oauth_verifier'])){
		  	
			$params = array(
				"oauth_verifier" => $_GET['oauth_verifier'],
				"oauth_token" => $_GET['oauth_token']
			);
			
			$access_token = $connection->oauth("oauth/access_token", $params);
		  
			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
		  
		  	$_SESSION['oauth_token'] = $access_token['oauth_token'];       
		  	$_SESSION['oauth_token_secret'] = $access_token['oauth_token_secret'];
			$_SESSION['twitter_auth'] = 'yes';
			
			$data['twitter_content'] = $content;
			$_SESSION['twitter_content'] = $content;
			
			/*echo "<pre>1";
			print_r($data['twitter_content']);
			die;*/
			 
		  } else {
			  
		  	$connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
			
			$data['twitter_content'] = $content;
			if(!isset($data['twitter_content']->screen_name)){
				
				$connection = new TwitterOAuth($consumer_key, $consumer_secret);
				
				$temporary_credentials = $connection->oauth(
					'oauth/request_token', array(
					  "oauth_callback" => adminUrl('posts')
					)
				);
				
				$_SESSION['oauth_token'] = $temporary_credentials['oauth_token'];       
				$_SESSION['oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];
				
				$url = $connection->url(
				  "oauth/authorize", 
				  array(
					"oauth_token" => $temporary_credentials['oauth_token']
				  )
				);
				
				$data['twitter_auth_url'] = $url;
				
				/*echo "<pre>3";
				print_r($data['twitter_auth_url']);
				die;*/
			} else {
				$_SESSION['twitter_content'] = $content;
				
				/*echo "<pre>2";
				print_r($data['twitter_content']);
				die;*/
			
			}
			
		  }
		  
		} else {
		  
		  
		  $temporary_credentials = $connection->oauth(
			  'oauth/request_token', array(
				"oauth_callback" => adminUrl('posts')
			  )
		  );
		  
		  $_SESSION['oauth_token'] = $temporary_credentials['oauth_token'];       
		  $_SESSION['oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];
		  
		  $url = $connection->url(
			"oauth/authorize", 
			array(
			  "oauth_token" => $temporary_credentials['oauth_token']
			)
		  );
		  
		  $data['twitter_auth_url'] = $url;
		  
		}
		
		$data['postAdminModel'] = $this->postAdminModel;

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
		
    }
	
	public function tweet()
    {
		
		require APPPATH."/Libraries/twitteroauth-main/vendor/autoload.php";
		
		$id = $_GET['id'];
		$this->checkRolePermission();
        $data['title'] = trans("update_post");
        $data['post'] = $this->postAdminModel->getPost($id);
        if (empty($data['post'])) {
            return redirect()->to(adminUrl('posts'));
        }
        $data['adminSettings'] = getAdminSettings();
        //check if author
        if (!isAdmin()) {
            if ($data['post']->user_id != user()->id) {
                return redirect()->to(adminUrl());
            }
        }
		
		$data['twts'] = $this->postAdminModel->getTweets($data['post']->id);
		
		/*echo "<pre>";
		print_r($data['twts']);
		die;*/
		
        //combine post tags
        $tags = "";
        $count = 0;
        $tagModel = new TagModel();
        $tagsArray = $tagModel->getPostTags($id);
        if (!empty($tagsArray)) {
            foreach ($tagsArray as $item) {
                if ($count > 0) {
                    $tags .= ",";
                }
                $tags .= $item->tag;
                $count++;
            }
        }

        $data['tags'] = $tags;
        $data['post_images'] = getPostAdditionalImages($id);
        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->getParentCategoriesByLang($data['post']->lang_id);
        $data['users'] = $this->authModel->getAuthors();
        $data['category_id'] = '';
        $data['subcategory_id'] = '';
        $categoryArray = getCategoryArray($data['post']->category_id);
        if (!empty($categoryArray['parentCategory'])) {
            $data['category_id'] = $categoryArray['parentCategory']->id;
        }
        if (!empty($categoryArray['subcategory'])) {
            $data['subcategory_id'] = $categoryArray['subcategory']->id;
        }
        $data['subcategories'] = $categoryModel->getAllSubcategoriesByParentId($data['category_id']);
		
		
		//twitter auth
		
		$consumer_key = 'K7cvD1TI1SDdEPirhgwyHykR7';
		$consumer_secret = 'MXgeCC7wTFScW254ln9DaclMRkmZENqca8eoIGiLaRrDP42D9d';
		$connection = new TwitterOAuth($consumer_key, $consumer_secret);
		
		if(isset($_SESSION['oauth_token'])){
	
		  $oauth_token=$_SESSION['oauth_token'];
		  //unset($_SESSION['oauth_token']);
		  
		  if(isset($_GET['oauth_verifier'])){
		  	
			$params = array(
				"oauth_verifier" => $_GET['oauth_verifier'],
				"oauth_token" => $_GET['oauth_token']
			);
			
			$access_token = $connection->oauth("oauth/access_token", $params);
		  
			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
		  
		  	$_SESSION['oauth_token'] = $access_token['oauth_token'];       
		  	$_SESSION['oauth_token_secret'] = $access_token['oauth_token_secret'];
			
			$data['twitter_content'] = $content;
			
			/*echo "<pre>1";
			print_r($content);
			die;*/
			 
		  } else {
			  
		  	$connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
			
			$data['twitter_content'] = $content;
			
			/*echo "<pre>2";
			print_r($content);
			die;*/
			
		  }
		  
		  $data['tweets'] = $this->postAdminModel->split_to_chunks($data['twitter_content']->screen_name,$data['post']->content);
		  
		} else {
		  
		  $temporary_credentials = $connection->oauth(
			  'oauth/request_token', array(
				"oauth_callback" => adminUrl('posts')
			  )
		  );
		  
		  $_SESSION['oauth_token'] = $temporary_credentials['oauth_token'];       
		  $_SESSION['oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];
		  
		  $url = $connection->url(
			"oauth/authorize", 
			array(
			  "oauth_token" => $temporary_credentials['oauth_token']
			)
		  );
		  
		  $data['twitter_auth_url'] = $url;
		  
		  $data['tweets'] = $this->postAdminModel->split_to_chunks('' , $data['post']->content);
		  
		}
		
		$data['tweetIs'] = strip_tags($data['post']->content, "<br />");
		

        echo view('admin/includes/_header', $data);
        
		echo view('admin/post/tweet_post', $data);
		
        echo view('admin/includes/_footer');
	}
	
	public function tweetPreview()
    {
		
		require APPPATH."/Libraries/twitteroauth-main/vendor/autoload.php";
		
		$id = $_GET['id'];
		$this->checkRolePermission();
        $data['title'] = trans("update_post");
        $data['post'] = $this->postAdminModel->getPost($id);
        if (empty($data['post'])) {
            return redirect()->to(adminUrl('posts'));
        }
        $data['adminSettings'] = getAdminSettings();
        //check if author
        if (!isAdmin()) {
            if ($data['post']->user_id != user()->id) {
                return redirect()->to(adminUrl());
            }
        }
		
		$data['twts'] = $this->postAdminModel->getTweets($data['post']->id);
		
		/*echo "<pre>";
		print_r($data['twts']);
		die;*/
		
        //combine post tags
        $tags = "";
        $count = 0;
        $tagModel = new TagModel();
        $tagsArray = $tagModel->getPostTags($id);
        if (!empty($tagsArray)) {
            foreach ($tagsArray as $item) {
                if ($count > 0) {
                    $tags .= ",";
                }
                $tags .= $item->tag;
                $count++;
            }
        }

        $data['tags'] = $tags;
        $data['post_images'] = getPostAdditionalImages($id);
        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->getParentCategoriesByLang($data['post']->lang_id);
        $data['users'] = $this->authModel->getAuthors();
        $data['category_id'] = '';
        $data['subcategory_id'] = '';
        $categoryArray = getCategoryArray($data['post']->category_id);
        if (!empty($categoryArray['parentCategory'])) {
            $data['category_id'] = $categoryArray['parentCategory']->id;
        }
        if (!empty($categoryArray['subcategory'])) {
            $data['subcategory_id'] = $categoryArray['subcategory']->id;
        }
        $data['subcategories'] = $categoryModel->getAllSubcategoriesByParentId($data['category_id']);
		
		
		//twitter auth
		
		$consumer_key = 'K7cvD1TI1SDdEPirhgwyHykR7';
		$consumer_secret = 'MXgeCC7wTFScW254ln9DaclMRkmZENqca8eoIGiLaRrDP42D9d';
		$connection = new TwitterOAuth($consumer_key, $consumer_secret);
		
		if(isset($_SESSION['oauth_token'])){
	
		  $oauth_token=$_SESSION['oauth_token'];
		  //unset($_SESSION['oauth_token']);
		  
		  if(isset($_GET['oauth_verifier'])){
		  	
			$params = array(
				"oauth_verifier" => $_GET['oauth_verifier'],
				"oauth_token" => $_GET['oauth_token']
			);
			
			$access_token = $connection->oauth("oauth/access_token", $params);
		  
			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
		  
		  	$_SESSION['oauth_token'] = $access_token['oauth_token'];       
		  	$_SESSION['oauth_token_secret'] = $access_token['oauth_token_secret'];
			
			$data['twitter_content'] = $content;
			
			/*echo "<pre>1";
			print_r($content);
			die;*/
			 
		  } else {
			  
		  	$connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
			
			$data['twitter_content'] = $content;
			
			/*echo "<pre>2";
			print_r($content);
			die;*/
			
		  }
		  
		  $data['tweets'] = $this->postAdminModel->split_to_chunks($data['twitter_content']->screen_name,$data['post']->content);
		  
		} else {
		  
		  $temporary_credentials = $connection->oauth(
			  'oauth/request_token', array(
				"oauth_callback" => adminUrl('posts')
			  )
		  );
		  
		  $_SESSION['oauth_token'] = $temporary_credentials['oauth_token'];       
		  $_SESSION['oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];
		  
		  $url = $connection->url(
			"oauth/authorize", 
			array(
			  "oauth_token" => $temporary_credentials['oauth_token']
			)
		  );
		  
		  $data['twitter_auth_url'] = $url;
		  
		  $data['tweets'] = $this->postAdminModel->split_to_chunks('' , $data['post']->content);
		  
		}
		
		$data['tweetIs'] = strip_tags($data['post']->content, "<br />");
		

        echo view('admin/includes/_header', $data);
        
		echo view('admin/post/tweet_preview', $data);
		
        echo view('admin/includes/_footer');
	}
	
	public function postTweet()
    {
		
		/*echo "<pre>1";
		print_r($_POST);
		die;*/
		
		require APPPATH."/Libraries/twitteroauth-main/vendor/autoload.php";
		
		$consumer_key = 'K7cvD1TI1SDdEPirhgwyHykR7';
		$consumer_secret = 'MXgeCC7wTFScW254ln9DaclMRkmZENqca8eoIGiLaRrDP42D9d';
		$connection = new TwitterOAuth($consumer_key, $consumer_secret);
		
		if(isset($_SESSION['oauth_token'])){
	
		  $oauth_token=$_SESSION['oauth_token'];
		  //unset($_SESSION['oauth_token']);
		  
		  if(isset($_GET['oauth_verifier'])){
		  	
			$params = array(
				"oauth_verifier" => $_GET['oauth_verifier'],
				"oauth_token" => $_GET['oauth_token']
			);
			
			$access_token = $connection->oauth("oauth/access_token", $params);
		  
			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
		  
		  	$_SESSION['oauth_token'] = $access_token['oauth_token'];       
		  	$_SESSION['oauth_token_secret'] = $access_token['oauth_token_secret'];
			
			$twitter_content = $content;
			
			/*echo "<pre>1";
			print_r($content);
			die;*/
			 
		  } else {
			  
		  	$connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$content = $connection->get("account/verify_credentials");
			
			$twitter_content = $content;
			
			/*echo "<pre>2";
			print_r($content);
			die;*/
			
		  }
		  
		} else {
		  return 'false';
		  die;
		}
		
		try{
			
			
		
		if(inputPost('tweets')){
			$tweets = inputPost('tweets');
			$tweet_images = inputPost('tweet_images');
			
			/*echo "<pre>";
			print_r($tweets);
			print_r($tweet_images);
			die;*/
			
			$postId = $_POST['post'];
			$user_id = $_POST['user_id'];
			
			$post = $this->postAdminModel->getPost($postId);
			$postTweeted = $post->tweeted;
			if($postTweeted==0){
				$parent = 0;
				$i=0;
				foreach($tweets as $tweet){
					//insert all tweets
					$tweet_image = NULL;
					if($i>0){
						$tweet = "@".$twitter_content->screen_name." ".$tweet;
						$tweet_image = $tweet_images[$i-1];
					}
					$tweetId = $this->postAdminModel->insertTweet($tweet, $tweet_image, $twitter_content->screen_name, $parent);
					if($i==0){
						$parent = $tweetId;	
					}
					$i++;
				}
				
				//update the post table
				$this->postAdminModel->updatePost($postId, 1);
				$postTweeted = 1;
			}
			
			if($postTweeted==1){
				
				$twts = $this->postAdminModel->getTweets($postId);
				
				$postImgUrl = '';
				if (!empty($post) && !empty($post->image_mid)){
					$postImgUrl = base_url($post->image_mid);
				}
				if (empty($post->image_mid)) {
					$postImgUrl = $post->image_url;
				}
				
				$parent = 0;
				$i=0;
				if($twts){
					try{
					
						foreach($twts as $twt){
							//post tweet if not posted tweet
							if($i==0){
								if($twt->status!=1){
									
									if ($postImgUrl!=''){
										$tempFile = FCPATH.'/uploads/temp/'.basename($postImgUrl);
										file_put_contents($tempFile, file_get_contents($postImgUrl));
										
										$media1 = $connection->upload('media/upload', ['media' => $tempFile]);
										$parameters = [
											'status' => $twt->tweet_text,
											//'attachment_url' => $postImgUrl
											'media_ids' => implode(',', [$media1->media_id_string])
										];
									} else {
										$parameters = [
											'status' => $twt->tweet_text
										];
									}
									
									$result = $connection->post('statuses/update', $parameters);
									
									if($result) {
										$parent = $result->id_str;
										$this->postAdminModel->updateTweet($twt->id, 1, $parent);
										unlink($tempFile);
									}
								} else {
									$parent = $twt->tweet_id;
								}
								$i++;
							} else {
								
								if (!empty($twt->tweet_image)){
									$tweetImgUrl = base_url($twt->tweet_image);
									$tempFile = FCPATH.'/uploads/temp/'.basename($tweetImgUrl);
									file_put_contents($tempFile, file_get_contents($tweetImgUrl));
									
									$media1 = $connection->upload('media/upload', ['media' => $tempFile]);
									$parameters = [
										'status' => $twt->tweet_text,
										'media_ids' => implode(',', [$media1->media_id_string]),
										'in_reply_to_status_id' => $parent
									];
								} else {
									$parameters = [
										'status' => $twt->tweet_text,
										'in_reply_to_status_id' => $parent
									];
								}
								
								$result = $connection->post("statuses/update", $parameters);
								if($result) {
									$parent = $result->id_str;
									$this->postAdminModel->updateTweet($twt->id, 1, $parent);
									if (!empty($twt->tweet_image)){
										unlink($tempFile);
									}
								}
							}
						}
						
						$this->postAdminModel->updatePost($postId, 2);
						echo $postTweeted = 'Tweet Successful';
						die;
					
					} catch(Exception $e){
						/*echo "<pre>";
						print_r($e);
						die;*/
					}
				}
			
			} else if($post->tweeted==2) {
				echo "Already done";
				die;
			}
			
		}
		
		} catch(EXCEPTION $e){
			/*echo "<pre>";
			print_r($e);
			die;*/
		}
		
		die;
		
		
    }

    /**
     * Add Petition
     */
    public function addPetition()
    {
        checkPermission('add_post');
        $data['title'] = trans("add_petition");
        $model = new CategoryModel();
        $data['parentCategories'] = $model->getParentCategoriesByLang($this->activeLang->id);
        $data['adminSettings'] = getAdminSettings();
        $data['postType'] = "post";

        echo view('admin/includes/_header', $data);
        echo view('admin/post/add_petition', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Post
     */
    public function editPetition($id)
    {
        $this->checkRolePermission();
        $data['title'] = trans("update_petition");
        $data['post'] = $this->postAdminModel->getPost($id);
        if (empty($data['post'])) {
            return redirect()->to(adminUrl('petitions'));
        }
        $data['adminSettings'] = getAdminSettings();
        //check if author
        if (!isAdmin()) {
            if ($data['post']->user_id != user()->id) {
                return redirect()->to(adminUrl());
            }
        }
        //combine post tags
        $tags = "";
        $count = 0;
        $tagModel = new TagModel();
        $tagsArray = $tagModel->getPostTags($id);
        if (!empty($tagsArray)) {
            foreach ($tagsArray as $item) {
                if ($count > 0) {
                    $tags .= ",";
                }
                $tags .= $item->tag;
                $count++;
            }
        }

        $data['tags'] = $tags;
        $data['post_images'] = getPostAdditionalImages($id);
        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->getParentCategoriesByLang($data['post']->lang_id);
        $data['users'] = $this->authModel->getAuthors();
        $data['category_id'] = '';
        $data['subcategory_id'] = '';
        $categoryArray = getCategoryArray($data['post']->category_id);
        if (!empty($categoryArray['parentCategory'])) {
            $data['category_id'] = $categoryArray['parentCategory']->id;
        }
        if (!empty($categoryArray['subcategory'])) {
            $data['subcategory_id'] = $categoryArray['subcategory']->id;
        }
        $data['subcategories'] = $categoryModel->getAllSubcategoriesByParentId($data['category_id']);

        $data['recipients'] = "";
        $recipientModel = new RecipientModel();
        $data['recipients'] = $recipientModel->getRecipientsByPost($id);

        echo view('admin/includes/_header', $data);
        if($data['post']->post_type == "petition") {
            echo view('admin/post/edit_petition', $data);
        }
        else if ($data['post']->post_type == "video") {
            echo view('admin/post/edit_video', $data);
        } else {
            echo view('admin/post/edit_post', $data);
        }
        echo view('admin/includes/_footer');
    }

    /**
     * Petitions
     */
    public function petitions()
    {
        $this->checkRolePermission();
        $data['title'] = trans('petitions');
        $data['authors'] = $this->authModel->getAuthors();
        $data['formAction'] = adminUrl("petitions");
        $data['listType'] = "posts";
        $data['adminSettings'] = getAdminSettings();
        $numRows = $this->postAdminModel->getPostsPaginatedCount($data['listType']);
        $pager = paginate($this->getPostsPerPage(), $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated($this->getPostsPerPage(), $pager->offset, $data['listType']);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/petitions', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Slider Posts
     */
    public function sliderPosts()
    {
        checkPermission('manage_all_posts');
        $data['title'] = trans('slider_posts');
        $data['authors'] = $this->authModel->getAuthors();
        $data['formAction'] = adminUrl("slider-posts");
        $data['listType'] = "slider_posts";
        $data['adminSettings'] = getAdminSettings();
        $numRows = $this->postAdminModel->getPostsPaginatedCount($data['listType']);
        $pager = paginate($this->getPostsPerPage(), $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated($this->getPostsPerPage(), $pager->offset, $data['listType']);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Our Picks
     */
    public function ourPicks()
    {
        checkPermission('manage_all_posts');
        $data['title'] = trans('our_picks');
        $data['authors'] = $this->authModel->getAuthors();
        $data['formAction'] = adminUrl("our-picks");
        $data['listType'] = "our_picks";
        $data['adminSettings'] = getAdminSettings();
        $numRows = $this->postAdminModel->getPostsPaginatedCount($data['listType']);
        $pager = paginate($this->getPostsPerPage(), $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated($this->getPostsPerPage(), $pager->offset, $data['listType']);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Pending Posts
     */
    public function pendingPosts()
    {
        $this->checkRolePermission();
        $data['title'] = trans('pending_posts');
        $data['authors'] = $this->authModel->getAuthors();
        $data['formAction'] = adminUrl("pending-posts");
        $data['adminSettings'] = getAdminSettings();
        $numRows = $this->postAdminModel->getPendingPostsPaginatedCount();
        $pager = paginate($this->getPostsPerPage(), $numRows);
        $data['posts'] = $this->postAdminModel->getPendingPostsPaginated($this->getPostsPerPage(), $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/pending_posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Drafts
     */
    public function drafts() 
    {
        $this->checkRolePermission();
        $data['title'] = trans('drafts');
        $data['authors'] = $this->authModel->getAuthors();
        $data['formAction'] = adminUrl("drafts");
        $numRows = $this->postAdminModel->getDraftsPaginatedCount();
        $pager = paginate($this->getPostsPerPage(), $numRows);
        $data['posts'] = $this->postAdminModel->getDraftsPaginated($this->getPostsPerPage(), $pager->offset);
        $data['adminSettings'] = getAdminSettings();
        echo view('admin/includes/_header', $data);
        echo view('admin/post/drafts', $data);
        echo view('admin/includes/_footer');
    }
    public function drafts1() 
    {
        $this->checkRolePermission();
        $data['title'] = trans('feed_articles');
        $data['authors'] = $this->authModel->getAuthors();
        $data['formAction'] = adminUrl("articles");
        $numRows = $this->postAdminModel->getDraftsPaginatedCount();
        $pager = paginate($this->getPostsPerPage(), $numRows);
        $data['posts'] = $this->postAdminModel->getDraftsPaginated($this->getPostsPerPage(), $pager->offset);
        $data['adminSettings'] = getAdminSettings();
        echo view('admin/includes/_header', $data);
        echo view('admin/rss/articles', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Auto Post Deletion
     */
    public function autoPostDeletion()
    {
        checkPermission('manage_all_posts');
        $data['title'] = trans('auto_post_deletion');
        echo view('admin/includes/_header', $data);
        echo view('admin/post/auto_post_deletion', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Post Options Post
     */
    public function postOptionsPost()
    {
        $option = inputPost('option');
        $id = inputPost('id');
        $data["post"] = $this->postAdminModel->getPost($id);
        if (empty($data['post'])) {
            return redirect()->back();
        }

        //if option approve
        if($option == 'add-petition-approve') {
            checkPermission('manage_all_posts');
            $result = $this->postAdminModel->postPetitionApprove($id);
            if ($result == "removed") {
                $this->session->setFlashdata('success', trans("msg_reject"));
            }
            if ($result == "added") {
                $this->session->setFlashdata('success', trans("msg_approve"));
            }
        }
        //if option deactivate
        if($option == 'add-petition-activate') {
            checkPermission('manage_all_posts');
            $result = $this->postAdminModel->postPetitionActivate($id);
            if ($result == "removed") {
                $this->session->setFlashdata('success', trans("msg_deactivate"));
            }
            if ($result == "added") {
                $this->session->setFlashdata('success', trans("msg_activate"));
            }
        }

        //if option add remove from slider
        if ($option == 'add-remove-from-slider') {
            checkPermission('manage_all_posts');
            $result = $this->postAdminModel->postAddRemoveSlider($id);
            if ($result == "removed") {
                $this->session->setFlashdata('success', trans("msg_remove_slider"));
            }
            if ($result == "added") {
                $this->session->setFlashdata('success', trans("msg_add_slider"));
            }
        }
        //if option add remove from picked
        if ($option == 'add-remove-from-picked') {
            checkPermission('manage_all_posts');
            $result = $this->postAdminModel->postAddRemovePicked($id);
            if ($result == "removed") {
                $this->session->setFlashdata('success', trans("msg_remove_picked"));
            }
            if ($result == "added") {
                $this->session->setFlashdata('success', trans("msg_add_picked"));
            }
        }
        //if option approve
        if ($option == 'approve') {
            checkPermission('manage_all_posts');
            if (isAdmin()) {
                if ($this->postAdminModel->approvePost($id)) {
                    $this->session->setFlashdata('success', trans("msg_post_approved"));
                } else {
                    $this->session->setFlashdata('error', trans("msg_error"));
                }
            }
        }
        //if option publish
        if ($option == 'publish') {
            checkPermission('manage_all_posts');
            if ($this->postAdminModel->publishPost($id)) {
                $this->session->setFlashdata('success', trans("msg_published"));
            } else {
                $this->session->setFlashdata('error', trans("msg_error"));
            }
        }
        //if option publish draft
        if ($option == 'publish_draft') {
            $this->checkRolePermission();
            if ($this->postAdminModel->publishDraft($id)) {
                $this->session->setFlashdata('success', trans("msg_published"));
            } else {
                $this->session->setFlashdata('error', trans("msg_error"));
            }
        }

        resetCacheDataOnChange();
        return redirect()->back();
    }

    /**
     * Auto Post Deletion Post
     */
    public function autoPostDeletionPost()
    {
        checkPermission('manage_all_posts');
        if ($this->settingsModel->updateAutoPostDeletionSettings()) {
            $this->session->setFlashdata('success', trans("msg_updated"));
        } else {
            $this->session->setFlashdata('error', trans("msg_error"));
        }
        return redirect()->to(adminUrl('auto-post-deletion'));
    }

    /**
     * Delete Post
     */
    public function deletePost()
    {
        $this->checkRolePermission();
        $id = inputPost('id');
        if ($this->postAdminModel->deletePost($id)) {
            resetCacheDataOnChange();
            $this->session->setFlashdata('success', trans("post") . " " . trans("msg_suc_deleted"));
        } else {
            $this->session->setFlashdata('error', trans("msg_error"));
        }
    }

    /**
     * Delete Selected Posts
     */
    public function deleteSelectedPosts()
    {
        $this->checkRolePermission();
        $postIds = inputPost('post_ids');
        $this->postAdminModel->deleteMultiPosts($postIds);
        resetCacheDataOnChange();
    }

    /**
     * Publish Selected Draft
     */
    public function publishSelectedDrafts()
    {
        $this->checkRolePermission();
        $postIds = inputPost('post_ids');
        $this->postAdminModel->publishMultiDraft($postIds);
        resetCacheDataOnChange();
    }

    /**
     * Save Home Slider Post Order
     */
    public function homeSliderPostsOrderPost()
    {
        checkPermission('manage_all_posts');
        $postId = inputPost('id');
        $order = inputPost('slider_order');
        $this->postAdminModel->saveHomeSliderPostOrder($postId, $order);
        resetCacheDataOnChange();
        return redirect()->to(adminUrl('slider-posts'));
    }

    /**
     * Get Video from URL
     */
    public function getVideoFromURL()
    {
        include(APPPATH . 'Libraries/VideoUrlParser.php');
        $parser = new \VideoUrlParser();
        $url = inputPost('url');
        $data = array(
            'video_embed_code' => $parser->getEmbedCode($url),
            'video_thumbnail' => $parser->getThumbnail($url)
        );
        echo json_encode($data);
    }

    /**
     * Delete Post Main Image
     */
    public function deletePostMainImage()
    {
        $this->checkRolePermission();
        $postId = inputPost('post_id');
        $this->postAdminModel->deletePostMainImage($postId);
    }

    /**
     * Delete Additional Image
     */
    public function deletePostAdditionalImage()
    {
        $this->checkRolePermission();
        $fileId = inputPost('file_id');
        $this->postAdminModel->deletePostAdditionalImage($fileId);
    }

    /**
     * Delete Post File
     */
    public function deletePostFile()
    {
        $this->checkRolePermission();
        $fileId = inputPost('file_id');
        $this->postAdminModel->deletePostFile($fileId);
    }

    //get perpage
    public function getPostsPerPage()
    {
        if (!empty(inputGet('show', true))) {
            return cleanNumber(inputGet('show', true));
        }
        return 15;
    }

    //check role permission
    public function checkRolePermission()
    {
        if (!hasPermission('manage_all_posts') && !hasPermission('add_post')) {
            redirectToUrl(base_url());
            exit();
        }
    }
	
	public function twitterLogout(){
		$this->session->remove('oauth_token');
		$this->session->remove('oauth_token_secret');
		$this->session->remove('twitter_auth');
		$this->session->remove('twitter_content');
		redirectToUrl(adminUrl('posts'));
		exit();
	}
}
