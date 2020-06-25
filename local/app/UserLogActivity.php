<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class UserLogActivity extends Model {

    protected $table = 'user_activity';
    protected $guarded = [''];
    public $timestamps = false;
}
