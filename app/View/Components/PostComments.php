<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Post;

class PostComments extends Component
{
    public $post;
    public $comments;

    public function __construct(Post $post, $comments)
    {
        $this->post = $post;
        $this->comments = $comments;
    }

    public function render()
    {
        return view('components.post-comments');
    }
}
