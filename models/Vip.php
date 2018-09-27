<?php
namespace models;

class Vip extends Model
{
    // 设置这个模型对应的表
    protected $table = 'vip';
    // 设置允许接收的字段
    protected $fillable = ['username','password'];
}