<?php

namespace Tests\Feature;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function testCreateComment()
    {
        $comment = new Comment();
        $comment->email = "test@test.com";
        $comment->title = "Sample Title";
        $comment->comment = "Sample Comment";
        $comment->commentable_id = "1";
        $comment->commentable_type = "product";
        $comment->save();

        $this->assertNotNull($comment->id);
    }

    public function testDefaultAttributes()
    {
        $comment = new Comment();
        $comment->email = "test@test.com";
        $comment->commentable_id = "1";
        $comment->commentable_type = "product";
        $comment->save();

        $this->assertNotNull($comment->id);
        $this->assertNotNull($comment->title);
        $this->assertNotNull($comment->comment);
    }
}
