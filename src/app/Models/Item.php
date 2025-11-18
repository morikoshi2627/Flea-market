<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Item extends Model
{
    use HasFactory;

    //$item->favoritedBy でその商品を「いいね」したユーザー一覧が取得
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')
            ->withTimestamps();
    }

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories', 'item_id', 'category_id');
    }
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class); }

    //　取引チャット 
    public function transactionMessages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // 評価
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image',
        'brand_id',
        'condition_id',
        'price',
        'status',
        'buyer_id',
    ];


}
