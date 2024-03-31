<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Voucher;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\TagSeeder;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class ProductTest extends TestCase
{
    public function testOneToMany()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::find("1");
        $this->assertNotNull($product);
        $category = $product->category;
        $this->assertNotNull($category);
        $this->assertEquals("FOOD", $category->id);
    }

    public function testHasOneOfMany()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $category = Category::find("FOOD");
        $this->assertNotNull($category);

        $cheapestProduct = $category->cheapestProduct;
        $this->assertNotNull($cheapestProduct);
        $this->assertEquals("1", $cheapestProduct->id);

        $mostExpensiveProduct = $category->mostExpensiveProduct;
        $this->assertNotNull($mostExpensiveProduct);
        $this->assertEquals("2", $mostExpensiveProduct->id);
    }

    public function testOneToOnePolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, ImageSeeder::class]);
        $product = Product::find("1");
        $this->assertNotNull($product);

        $image = $product->image;
        $this->assertNotNull($image);

        $this->assertEquals("https://www.test.com/image/2.jpg", $image->url);
    }

    public function testOneToManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, CommentSeeder::class]);

        $product = Product::find("1");
        $this->assertNotNull($product);

        $comments = $product->comments;
        foreach ($comments as $comment) {
            $this->assertEquals("product", $comment->commentable_type);
            $this->assertEquals($product->id, $comment->commentable_id);
        }
    }

    public function testOneOfManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, CommentSeeder::class]);

        $product = Product::find("1");
        $this->assertNotNull($product);

        $latestComment = $product->latestComment;
        $this->assertNotNull($latestComment);

        $oldestComment = $product->oldestComment;
        $this->assertNotNull($oldestComment);
    }

    public function testManyToManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, TagSeeder::class]);

        $product = Product::find("1");
        $tags = $product->tags;
        $this->assertNotNull($tags);
        $this->assertCount(1, $tags);

        foreach ($tags as $tag) {
            $this->assertNotNull($tag->id);
            $this->assertNotNull($tag->name);

            $vouchers = $tag->vouchers;
            $this->assertNotNull($vouchers);
            $this->assertCount(1, $vouchers);
        }
    }

    public function testEloquentCollection()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        // 2 products (1,2)
        $products = Product::query()->get();

        // WHERE id IN (1,2)
        $products = $products->toQuery()->where("price", 200)->get();

        $this->assertNotNull($products);
        $this->assertEquals("2", $products[0]->id);
    }

    public function testSerialization()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $products = Product::query()->get();

        $this->assertCount(2, $products);

        $json = $products->toJson(JSON_PRETTY_PRINT);
        Log::info($json);
    }

    public function testSerializationRelation()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, ImageSeeder::class]);

        $products = Product::query()->get();
        $products->load(["category", "image"]);
        $this->assertCount(2, $products);

        $json = $products->toJson(JSON_PRETTY_PRINT);
        Log::info($json);
    }
}
