<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsleter extends Model
{
    protected $table = 'newsleters'; 
    protected $fillable = ['email'];  
    public $timestamps = true;
}
