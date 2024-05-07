<?php namespace App\Models;

use CodeIgniter\Model;

class PostAdminModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('posts');
		$this->builderTwitter = $this->db->table('tweet');
        $this->fileModel = new FileModel();
    }
	
	//break tweet
	public function split_to_chunks($to,$text){
		
		$text = str_replace("<br />", "<br/>", $text);
		
		$text = strip_tags($text, "<br>");
		
		$total_length = (280 - strlen($to)); //280
		$text_arr = explode(" ",$text);
		$i=0;
		$message[0]="";
		foreach ($text_arr as $word){
			if($i==0){
				$tot_length = $total_length;
			} else {
				$tot_length = (274 - strlen($to));
			}
			if ( strlen($message[$i] . $word . ' ') <= $tot_length ){
				if ($text_arr[count($text_arr)-1] == $word){
					$message[$i] .= $word;
				} else {
					$message[$i] .= $word . ' ';
				}
			} else {
				$i++;
				if ($text_arr[count($text_arr)-1] == $word){
					$message[$i] = $word;
				} else {
					$message[$i] = $word . ' ';
				}
			}
		}
		return $message;
	}
	
	//get tweets
	public function getTweets($post_id){
		return $this->builderTwitter->where('tweet.post_id', $post_id)->orderBy('tweet.id', 'ASC')->get()->getResult();
	}
	
	//insert tweet
	public function insertTweet($tweet_text, $tweet_image, $screen_name, $parent=0)
    {
		$tweetImage = "";
		if ($tweet_image) {
            $fileModel = new FileModel();
            $image = $fileModel->getImage($tweet_image);
            if (!empty($image)) {
                $tweetImage = $image->image_big;
            }
        }
		
        $data = [
            'post_id' => inputPost('post'),
            'tweet_text' => $tweet_text,
			'tweet_image' => $tweetImage,
            'user_id' => inputPost('user_id'),
			'tweet_id' => '',
            'tweet_parent' => $parent,
            'tweet_date' => date('Y-m-d H:i:s'),
            'tweet_by' => $screen_name,
            'status' => 0
        ];
		
		//$data['user_id'] = user()->id;
        if ($this->builderTwitter->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }
	
	//update post
	public function updatePost($id, $status)
    {
        $data = [
            'tweeted' => $status
        ];
		
		return $this->builder->where('id', cleanNumber($id))->update($data);
    }
	
	//update tweet
	public function updateTweet($id, $status, $tweet_id)
    {
        $data = [
            'status' => $status,
			'tweet_id' => $tweet_id
        ];
		return $this->builderTwitter->where('id', cleanNumber($id))->update($data);
    }
	
	//tweeted on twitter
	public function tweetDone()
    {
        $data = [
            'post_id' => inputPost('post_id'),
            'tweet_text' => inputPost('tweet_text'),
            'tweet_id' => '',
            'tweet_parent' => inputPost('tweet_parent'),
            'tweet_date' => date('Y-m-d H:i:s'),
            'tweet_by' => inputPost('tweet_by'),
            'status' => 0
        ];
		
		$data['user_id'] = user()->id;
		
        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }
	
    //add post
    public function addPost()
    {
        $data = $this->setData();
        $subcategoryId = inputPost('subcategory_id');
        if (!empty($subcategoryId)) {
            $data["category_id"] = $subcategoryId;
        }
        $data['user_id'] = user()->id;
        $data['visibility'] = 1;
        if ($this->generalSettings->approve_posts_before_publishing == 1 && !isAdmin()) {
            $data['visibility'] = 0;
        }
        $data['status'] = inputPost('status');
        $data['feed_id'] = 0;
        $data["created_at"] = date('Y-m-d H:i:s');
        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    //update post
    public function editPost($id)
    {
        $data = $this->setData();
        $data['user_id'] = inputPost('user_id');
        $subcategoryId = inputPost('subcategory_id');
        if (!empty($subcategoryId)) {
            $data["category_id"] = $subcategoryId;
        }
        $data["created_at"] = inputPost('date_published');
        $publish = inputPost('publish');
        if (!empty($publish) && $publish == 1) {
            $data["status"] = 1;
        }
        if (isAdmin()) {
            $data['visibility'] = inputPost('visibility');
        } else {
            $data['visibility'] = 1;
            if ($this->generalSettings->approve_posts_before_publishing == 1) {
                $data['visibility'] = 0;
            }
        }
        return $this->builder->where('id', cleanNumber($id))->update($data);
    }

    //set post data
    public function setData()
    {
        $data = [
            'post_type' => inputPost('post_type'),
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'title_slug' => inputPost('title_slug'),
            'summary' => inputPost('summary'),
            'keywords' => inputPost('keywords'),
            'category_id' => inputPost('category_id'),
            'content' => inputPost('content'),
            'optional_url' => inputPost('optional_url'),
            'need_auth' => inputPost('need_auth'),
            'is_slider' => inputPost('is_slider'),
            'is_picked' => inputPost('is_picked'),
            'signature' => inputPost('signature')
        ];

        if (!isset($data['is_slider'])) {
            $data['is_slider'] = 0;
        }
        if (!isset($data['is_picked'])) {
            $data['is_picked'] = 0;
        }
        if (!isset($data['need_auth'])) {
            $data['need_auth'] = 0;
        }
        if (!empty($data["title_slug"])) {
            $data["title_slug"] = removeSpecialCharacters($data["title_slug"], true);
        } else {
            $data["title_slug"] = strSlug($data["title"]);
        }
        if (empty(inputPost('image_url'))) {
            //add post image
            $fileModel = new FileModel();
            $image = $fileModel->getImage(inputPost('post_image_id'));
            if (!empty($image)) {
                $data["image_url"] = "";
                $data["image_big"] = $image->image_big;
                $data["image_mid"] = $image->image_mid;
                $data["image_small"] = $image->image_small;
                $data["image_slider"] = $image->image_slider;
                $data["image_mime"] = $image->image_mime;
                require_once APPPATH . 'Libraries/GenerateCode.php';
                $generateCode = new \GenerateCode();
                $data["image_shortened_url"] = $generateCode->generate_code();
            }
        } else {
            $data["image_url"] = inputPost('image_url');
            $data["image_big"] = "";
            $data["image_mid"] = "";
            $data["image_small"] = "";
            $data["image_slider"] = "";
            $data["image_mime"] = "";
        }
        if ($data['post_type'] == "video") {
            $data['video_url'] = inputPost('video_url');
            $data["video_embed_code"] = inputPost('video_embed_code');
        } else {
            $data['video_url'] = '';
            $data["video_embed_code"] = '';
        }
        return $data;
    }

    //update slug
    public function updateSlug($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $slug = $post->title_slug;
            $newSlug = $post->title_slug;
            //check page
            $pageModel = new PageModel();
            $page = $pageModel->getPageBySlug($slug);
            if (!empty($page)) {
                $newSlug = $slug . "-" . $post->id;
            }
            if ($this->isSlugUnique($slug, $id) == true) {
                $newSlug = $slug . "-" . $post->id;
            }
            $data = [
                'title_slug' => $newSlug
            ];
            $this->builder->where('id', $post->id)->update($data);
        }
    }

    //check slug
    public function isSlugUnique($slug, $id)
    {
        if (!empty($this->builder->where('title_slug', $slug)->where('id !=', $id)->get()->getRow())) {
            return true;
        }
        return false;
    }

    //get post
    public function getPost($id)
    {
        return $this->builder->where('id', cleanNumber($id))->get()->getRow();
    }

    //get posts count
    public function getPostsCount()
    {
        if (isAuthor()) {
            $this->builder->where('posts.user_id', user()->id);
        }
        return $this->builder->where('posts.visibility', 1)->where('posts.status', 1)->countAllResults();
    }

    //get pending posts count
    public function getPendingPostsCount()
    {
        if (isAuthor()) {
            $this->builder->where('posts.user_id', user()->id);
        }
        return $this->builder->where('posts.visibility', 0)->where('posts.status', 1)->countAllResults();
    }

    //get drafts count
    public function getDraftsCount()
    {
        if (isAuthor()) {
            $this->builder->where('posts.user_id', user()->id);
        }
        return $this->builder->where('posts.status', 0)->countAllResults();
    }

    //filter by values
    public function filterPosts()
    {
        $data = [
            'lang_id' => inputGet('lang_id'),
            'post_type' => inputGet('post_type'),
            'author' => inputGet('author'),
            'category' => inputGet('category'),
            'subcategory' => inputGet('subcategory'),
            'q' => inputGet('q'),
            'noimage' => inputGet('noimage'),
            'links' => inputGet('links'),
            'nodesc' => inputGet('nodesc'),
            'dupdesc' => inputGet('dupdesc'),
            'duptitle' => inputGet('duptitle'),
            'nodates' => inputGet('nodates'),
            'black' => inputGet('black'),
            'white' => inputGet('white'),
            'rssfeedurls' => inputGet('rssfeedurls')
        ];

        $categoryId = null;
        if (!empty($data['category'])) {
            $categoryId = $data['category'];
        }
        if (!empty($data['subcategory'])) {
            $categoryId = $data['subcategory'];
        }
        if (!empty($categoryId)) {
            $categoryModel = new CategoryModel();
            $categoryIds = getCategoryTreeIdsArray($categoryId);
            $this->builder->whereIn('posts.category_id', $categoryIds, false);
        }

        $data['q'] = trim($data['q'] ?? '');
        $data['user_id'] = "";
        //check if author
        if (!hasPermission('manage_all_posts')) {
            $data['user_id'] = user()->id;
        } else {
            if (!empty($data['author'])) {
                $data['user_id'] = $data['author'];
            }
        }

        if (!empty($data['lang_id'])) {
            $this->builder->where('posts.lang_id', $data['lang_id']);
        }

        
        if (!empty($data['rssfeedurls'])) {
            $this->builder->where('posts.feed_id', $data['rssfeedurls']);
        }

        if (!empty($data['noimage'])) {
            $this->builder->where('posts.image_url', null);
        }
        
        if (!empty($data['links'])) {
            $this->builder->like('posts.post_url', "http://");
        }
        
        if (!empty($data['nodesc'])) {
            $this->builder->where('posts.summary', null);
        }
        
        if (!empty($data['dupdesc'])) {
            //  $this->builder->group('posts.s', null);
        }
        
        if (!empty($data['nodates'])) {
            $this->builder->where('posts.dates', null);
        }
        
        if (!empty($data['duptitle'])) {
            $this->builder->where('posts.title', null);
        }

        if (!empty($data['black'])) {
            $blackAry = explode("," , $data['black']);
            foreach ($blackAry as $index => $value) {
                if(strlen($value)>2){
                    $this->builder->notLike('posts.title', $value);
                    $this->builder->notLike('posts.summary', $value);
                }
                # code...
            }
        }

        
        if (!empty($data['white'])) {
            $blackAry = explode("," , $data['white']);
            foreach ($blackAry as $index => $value) {
                if(strlen($value)>2){
                    $this->builder->like('posts.title', $value);
                    $this->builder->like('posts.summary', $value);
                }
                # code...
            }

        }


        if (!empty($data['post_type'])) {
            if ($data['post_type'] == 'video') {
                $this->builder->where('posts.post_type', 'video');
            } else {
                $this->builder->where('posts.post_type', 'post');
            }
        }
        if (!empty($data['q'])) {
            $this->builder->like('posts.title', $data['q']);
        }
        if (!empty($data['user_id'])) {
            $this->builder->where('posts.user_id', $data['user_id']);
        }
    }

    //filter by list
    public function filterPostsList($list)
    {
        if (!empty($list)) {
            if ($list == "slider_posts") {
                $this->builder->where('posts.is_slider', 1);
            }
            if ($list == "our_picks") {
                $this->builder->where('posts.is_picked', 1);
            }
        }
    }

    //get paginated posts
    public function getPostsPaginated($perPage, $offset, $list)
    {
        $this->filterPosts();
        $this->filterPostsList($list);
        return $this->builder->where('posts.visibility', 1)->where('posts.status', 1)->orderBy('posts.created_at', 'DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get paginated posts count
    public function getPostsPaginatedCount($list)
    {
        $this->filterPosts();
        $this->filterPostsList($list);
        return $this->builder->where('posts.visibility', 1)->where('posts.status', 1)->countAllResults();
    }

    //get paginated pending posts
    public function getPendingPostsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('posts.visibility', 0)->where('posts.status', 1)->orderBy('posts.created_at', 'DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get paginated pending posts count
    public function getPendingPostsPaginatedCount()
    {
        $this->filterPosts();
        return $this->builder->where('posts.visibility', 0)->where('posts.status', 1)->countAllResults();
    }

    //get paginated drafts
    public function getDraftsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('posts.status != ', 1)->orderBy('posts.created_at', 'DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get paginated drafts count
    public function getDraftsPaginatedCount()
    {
        $this->filterPosts();
        return $this->builder->where('posts.status != ', 1)->countAllResults();
    }

    //get post count by category
    public function getPostCountByCategory($categoryId)
    {
        return $this->builder->where('category_id', cleanNumber($categoryId))->where('visibility', 1)->where('status', 1)->countAllResults();
    }

    // approve petition
    public function postPetitionApprove($id) {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $result = "";
            if ($post->petition_approve == 1) {
                $data = ['petition_approve' => 0, 'visibility' => 0];
                $result = "removed";
            } else {
                $data = ['petition_approve' => 1, 'visibility' => 1];
                $result = "added";
            }
            $this->builder->where('id', $post->id)->update($data);
            return $result;
        }
    }

    // deactivate petition
    public function postPetitionActivate($id) {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $result = "";
            if ($post->petition_activate == 1) {
                $data = ['petition_activate' => 0];
                $result = "removed";
            } else {
                $data = ['petition_activate' => 1];
                $result = "added";
            }
            $this->builder->where('id', $post->id)->update($data);
            return $result;
        }
    }

    //add or remove post from slider
    public function postAddRemoveSlider($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $result = "";
            if ($post->is_slider == 1) {
                $data = ['is_slider' => 0];
                $result = "removed";
            } else {
                $data = ['is_slider' => 1];
                $result = "added";
            }
            $this->builder->where('id', $post->id)->update($data);
            return $result;
        }
    }

    //approve post
    public function approvePost($id)
    {
        $data = ['visibility' => 1];
        return $this->builder->where('id', cleanNumber($id))->update($data);
    }

    //publish post
    public function publishPost($id)
    {
        $data = ['created_at' => date("Y-m-d H:i:s")];
        return $this->builder->where('id', cleanNumber($id))->update($data);
    }

    //publish draft
    public function publishDraft($id)
    {
        $data = ['status' => 1];
        return $this->builder->where('id', cleanNumber($id))->update($data);
    }

    //add or remove post from picked
    public function postAddRemovePicked($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $result = "";
            if ($post->is_picked == 1) {
                //remove from picked
                $data = ['is_picked' => 0];
                $result = "removed";
            } else {
                //add to picked
                $data = ['is_picked' => 1];
                $result = "added";
            }
            $this->builder->where('id', $post->id)->update($data);
            return $result;
        }
    }

    //save home slider post order
    public function saveHomeSliderPostOrder($id, $order)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $data = [
                'slider_order' => $order
            ];
            $this->builder->where('id', $post->id)->update($data);
        }
    }

    //delete post
    public function deletePost($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            //delete tags
            $tagModel = new TagModel();
            $tagModel->deletePostTags($post->id);
            //delete comments
            $this->db->table('comments')->where('post_id', $post->id)->delete();
            //delete post files
            $this->db->table('post_files')->where('post_id', $post->id)->delete();
            //delete post images
            $this->db->table('post_images')->where('post_id', $post->id)->delete();
            //delete reactions
            $this->db->table('reactions')->where('post_id', $post->id)->delete();
            //delete from reading list
            $this->db->table('reading_lists')->where('post_id', $post->id)->delete();
            //delete post
            return $this->builder->where('id', $post->id)->delete();
        }
        return false;
    }

    //delete multi post
    public function deleteMultiPosts($postIds)
    {
        if (!empty($postIds)) {
            foreach ($postIds as $id) {
                $this->deletePost($id);
            }
        }
    }

    //draft post multi post
    public function publishMultiDraft($postIds)
    {
        if (!empty($postIds)) {
            foreach ($postIds as $id) {
                $this->publishDraft($id);
            }
        }
    }

    //delete old posts
    public function deleteOldPosts()
    {
        if ($this->generalSettings->auto_post_deletion == 1) {
            $days = cleanNumber($this->generalSettings->auto_post_deletion_days);
            if ($days > 1) {
                $sql = "SELECT id FROM posts WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY) AND feed_id != ''";
                if ($this->generalSettings->auto_post_deletion_delete_all == 1) {
                    $sql = "SELECT id FROM posts WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
                }
                $posts = $this->db->query($sql, array($days))->getResult();
                if (!empty($posts)) {
                    foreach ($posts as $post) {
                        $this->deletePost($post->id);
                    }
                }
            }
        }
    }

    /**
     * RECIPIENTS
     */
    
    //add post recipients
    public function addPostRecipients($postId)
    {
        $recipientNames = inputPost('recipient_name[]');
        $recipientEmails = inputPost('recipient_email[]');

        foreach($recipientNames as $key => $recipientName) {
            $item = [
                'post_id' => $postId,
                'recipient_name' => $recipientName,
                'recipient_email' => $recipientEmails[$key]
            ];
            $this->db->table('recipients')->insert($item);
        }
    }

    //remove post recipients
    public function removePostRecipients($postId)
    {
        $this->db->table('recipients')->where('post_id', cleanNumber($postId))->delete();
    }

    /**
     * --------------------------------------------------------------------
     * FILES
     * --------------------------------------------------------------------
     */

    //add post additional images
    public function addPostAdditionalImages($postId)
    {
        $imageIds = inputPost('additional_post_image_id');
        if (!empty($imageIds)) {
            foreach ($imageIds as $imageId) {
                $image = $this->fileModel->getImage($imageId);
                if (!empty($image)) {
                    require_once APPPATH . 'Libraries/GenerateCode.php';
                    $generateCode = new \GenerateCode();
                    $item = [
                        'post_id' => $postId,
                        'image_path' => $image->image_big,
                        'shortened_url' => $generateCode->generate_code()
                    ];
                    if (!empty($item["image_path"])) {
                        $this->db->table('post_images')->insert($item);
                    }
                }
            }
        }
    }

    //add post files
    public function addPostFiles($postId)
    {
        $fileIds = inputPost('post_selected_file_id');
        if (!empty($fileIds)) {
            foreach ($fileIds as $fileId) {
                $file = $this->fileModel->getFile($fileId);
                if (!empty($file)) {
                    $item = [
                        'post_id' => $postId,
                        'file_id' => $fileId
                    ];
                    $this->db->table('post_files')->insert($item);
                }
            }
        }
    }

    //get post additional image
    public function getPostAdditionalImage($fileId)
    {
        return $this->db->table('post_images')->where('id', cleanNumber($fileId))->get()->getRow();
    }

    //delete post main image
    public function deletePostMainImage($postId)
    {
        $post = $this->getPost($postId);
        if (!empty($post)) {
            $data = [
                'image_big' => "",
                'image_slider' => "",
                'image_mid' => "",
                'image_small' => "",
                'image_url' => "",
            ];
            $this->builder->where('id', $post->id)->update($data);
        }
    }

    //delete additional image
    public function deletePostAdditionalImage($fileId)
    {
        $image = $this->getPostAdditionalImage($fileId);
        if (!empty($image)) {
            $this->db->table('post_images')->where('id', cleanNumber($fileId))->delete();
        }
    }

    //get post file
    public function getPostFile($fileId)
    {
        return $this->db->table('post_files')->where('id', cleanNumber($fileId))->get()->getRow();
    }

    //delete post file
    public function deletePostFile($id)
    {
        $file = $this->getPostFile($id);
        if (!empty($file)) {
            $this->db->table('post_files')->where('id', $file->id)->delete();
        }
    }

}
