<?php namespace App\Models;

use CodeIgniter\Model;

class RecipientModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('recipients');
    }

    public function getRecipientsByPost($postId)
    {
        return $this->builder->where('post_id', $postId)->get()->getResult();
    }
}