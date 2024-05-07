<?php namespace App\Models;

use CodeIgniter\Model;

class SignModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('signs');
    }

    //add sign
    public function addSign()
    {
        $data = [
            'post_id' => inputGet('post_id'),
            'name' => inputGet('name'),
            'contact' => inputGet('contact'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->builder->insert($data);
    }

}
