<?php

class Questions extends \BaseModel {

    protected $table = 'questions';
    protected $guarded = array('id', '_method', '_token');
    protected $fillable = array('order', 'title', 'is_true', 'is_branding', 'question', 'answer');
    public static $rules = array('question' => 'required', 'answer' => 'required');
}