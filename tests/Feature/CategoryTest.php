<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Scopes\IsActiveScope;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ReviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testInsert()
    {
        $category = new Category();
        $category->id = "GADGET";
        $category->name = "Gadget";
        $result = $category->save();
        assertTrue($result);
    }

    public function testInsertMany()
    {
        $categories = [];
        for ($i = 1; $i <= 10; $i++) {
            $categories[] = [
                "id" => "ID $i",
                "name" => "Name $i",
                "is_active" => true
            ];
        }

        // $result = Category::query()->insert($categories);
        $result = Category::insert($categories);
        assertTrue($result);

        // $total = Category::query()->count();
        $total = Category::count();
        assertEquals(10, $total);
    }

    public function testFind()
    {
        $this->seed(CategorySeeder::class);
        $category = Category::find("FOOD");
        assertNotNull($category);
        assertEquals("FOOD", $category->id);
        assertEquals("Food", $category->name);
        assertEquals("Food Category", $category->description);
    }

    public function testUpdate()
    {
        $this->seed(CategorySeeder::class);
        $category = Category::find("FOOD");
        $category->name = "Food Updated";
        $result = $category->update();
        assertTrue($result);
    }

    public function testSelect()
    {
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category->id = "ID $i";
            $category->name = "Name $i";
            $category->is_active = true;
            $category->save();
        }

        $categories = Category::whereNull("description")->get();
        assertCount(5, $categories);
        $categories->each(function ($category) {
            assertNull($category->description);
            // karena hasil dari perulangan ini merupakan object dari model maka bisa di manipulasi misalnya di update.
            $category->description = "Updated";
            $category->update();
        });
    }

    public function testUpdateMany()
    {
        $categories = [];
        for ($i = 1; $i <= 10; $i++) {
            $categories[] = [
                "id" => "ID $i",
                "name" => "Name $i",
                "is_active" => true
            ];
        }

        $result = Category::insert($categories);
        assertTrue($result);

        Category::whereNull("description")->update([
            "description" => "Updated"
        ]);

        $total = Category::where("description", "=", "Updated")->count();
        $this->assertEquals(10, $total);
    }

    public function testDelete()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::find("FOOD");
        $result = $category->delete();
        $this->assertTrue($result);

        $total = Category::count();
        $this->assertEquals(0, $total);
    }

    public function testDeleteMany()
    {
        $categories = [];
        for ($i = 1; $i <= 10; $i++) {
            $categories[] = [
                "id" => "ID $i",
                "name" => "Name $i",
                "is_active" => true
            ];
        }

        $result = Category::insert($categories);
        assertTrue($result);

        $total = Category::count();
        $this->assertEquals(10, $total);

        Category::whereNull("description")->delete();

        $total = Category::count();
        $this->assertEquals(0, $total);
    }

    public function testCreate()
    {
        $request = [
            "id" => "FOOD",
            "name" => "Food",
            "description" => "Food Category"
        ];

        $category = new Category($request);
        $category->save();

        $this->assertNotNull($category->id);
    }

    public function testCreateUsingQueryBuilder()
    {
        $request = [
            "id" => "FOOD",
            "name" => "Food",
            "description" => "Food Category"
        ];

        $category = Category::create($request);

        $this->assertNotNull($category->id);
    }

    public function testUpdateMass()
    {
        $this->seed(CategorySeeder::class);
        $request = [
            "name" => "Food Updated",
            "description" => "Food Category Updated"
        ];
        $category = Category::find("FOOD");
        $category->fill($request);
        $category->save();

        $this->assertNotNull($category->id);
    }

    public function testGlobalScope()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = false;
        $category->save();

        $category = Category::find("FOOD");
        $this->assertNull($category);

        $category = Category::withoutGlobalScopes([IsActiveScope::class])->find("FOOD");
        $this->assertNotNull($category);
    }

    public function testOneToMany()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $category = Category::find("FOOD");
        $this->assertNotNull($category);
        $products = $category->products;
        $this->assertNotNull($products);
        $this->assertCount(2, $products);
    }

    public function testOneToManyQuery()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = true;
        $category->save();
        $this->assertNotNull($category);

        $product = new Product();
        $product->id = "1";
        $product->name = "Product 1";
        $product->description = "Description 1";
        $category->products()->save($product);

        $this->assertNotNull($product->category_id);
    }

    public function testRelationshipQuery()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $category = Category::find("FOOD");
        $products = $category->products;
        $this->assertCount(2, $products);
        $outOfStockProducts = $category->products()->where("stock", "<=", 0)->get();
        $this->assertCount(2, $outOfStockProducts);
    }

    public function testHasManyThrough()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, CustomerSeeder::class, ReviewSeeder::class]);

        $category = Category::find("FOOD");
        $this->assertNotNull($category);

        $reviews = $category->reviews;
        $this->assertNotNull($reviews);
        $this->assertCount(2, $reviews);
    }

    public function testQueryingRelations()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find("FOOD");
        $products = $category->products()->where("price", "=", 200)->get();
        $this->assertCount(1, $products);
        $this->assertEquals("2", $products[0]->id);
    }

    public function testAggregatingRelations()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find("FOOD");
        $total = $category->products()->count();
        $this->assertEquals(2, $total);
        $total = $category->products()->where("price", 200)->count();
        $this->assertEquals(1, $total);
    }
}
