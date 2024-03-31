<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Wallet;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\VirtualAccountSeeder;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function testOneToOne()
    {
        $this->seed([CustomerSeeder::class, WalletSeeder::class]);

        $customer = Customer::find("RAHMAT");
        $this->assertNotNull($customer);

        // mengambil data dengan query where tidak perlu di lakukan lagi karena sudah dihandle relasi
        // $wallet = Wallet::where("customer_id", $customer->id)->first();

        $wallet = $customer->wallet;
        $this->assertNotNull($wallet);

        $this->assertEquals(1000000, $wallet->amount);
    }

    public function testOneToOneQuery()
    {
        $customer = new Customer();
        $customer->id = "RAHMAT";
        $customer->name = "Rahmat";
        $customer->email = "rahmat@test.com";
        $customer->save();

        // karena relationship extends dengan class builder, sehingga relationship bisa menggunakan fungsi-fungsi builder
        $wallet = new Wallet();
        $wallet->amount = 5000000;
        $customer->wallet()->save($wallet);

        $this->assertNotNull($wallet->customer_id);
    }

    public function testHasOneThrough()
    {
        $this->seed([CustomerSeeder::class, WalletSeeder::class, VirtualAccountSeeder::class]);

        $customer = Customer::find("RAHMAT");
        $this->assertNotNull($customer);

        $virtualAccount = $customer->virtualAccount;
        $this->assertNotNull($virtualAccount);
        $this->assertEquals("BCA", $virtualAccount->bank);
    }

    public function testManyToMany()
    {
        $this->seed([CustomerSeeder::class, CategorySeeder::class, ProductSeeder::class]);

        $customer = Customer::find("RAHMAT");
        $this->assertNotNull($customer);

        $customer->likeProducts()->attach("1");

        $products = $customer->likeProducts;
        $this->assertCount(1, $products);
        $this->assertEquals("1", $products[0]->id);

        // $products = Product::find("1");
        // $this->assertCount(1, $products->likedByCustomers);
        // $this->assertEquals("RAHMAT", $products->likedByCustomers[0]->id);
    }

    public function testManyToManyDetach()
    {
        $this->testManyToMany();

        $customer = Customer::find("RAHMAT");
        $customer->likeProducts()->detach("1");

        $products = $customer->likeProducts;
        $this->assertCount(0, $products);
    }

    public function testPivotAttribute()
    {
        $this->testManyToMany();

        $customer = Customer::find("RAHMAT");
        $products = $customer->likeProducts;

        foreach ($products as $product) {
            $pivot = $product->pivot;
            $this->assertNotNull($pivot);
            $this->assertNotNull($pivot->customer_id);
            $this->assertNotNull($pivot->product_id);
            $this->assertNotNull($pivot->created_at);
        }
    }

    public function testPivotAttributeCondition()
    {
        $this->testManyToMany();

        $customer = Customer::find("RAHMAT");
        $products = $customer->likeProductsLastWeek;

        foreach ($products as $product) {
            $pivot = $product->pivot;
            $this->assertNotNull($pivot);
            $this->assertNotNull($pivot->customer_id);
            $this->assertNotNull($pivot->product_id);
            $this->assertNotNull($pivot->created_at);
        }
    }

    public function testPivotModel()
    {
        $this->testManyToMany();

        $customer = Customer::find("RAHMAT");
        $products = $customer->likeProductsLastWeek;

        foreach ($products as $product) {
            $pivot = $product->pivot; // object model Like
            $this->assertNotNull($pivot);
            $this->assertNotNull($pivot->customer_id);
            $this->assertNotNull($pivot->product_id);
            $this->assertNotNull($pivot->created_at);

            $this->assertNotNull($pivot->customer);
            $this->assertNotNull($pivot->product);
        }
    }

    public function testOneToOnePolymorphic()
    {
        $this->seed([CustomerSeeder::class, ImageSeeder::class]);
        $customer = Customer::find("RAHMAT");
        $this->assertNotNull($customer);

        $image = $customer->image;
        $this->assertNotNull($image);

        $this->assertEquals("https://www.test.com/image/1.jpg", $image->url);
    }

    public function testEager(){
        $this->seed([CustomerSeeder::class,WalletSeeder::class,ImageSeeder::class]);
        $customer = Customer::with(["wallet","image"])->find("RAHMAT");
        $this->assertNotNull($customer);
    }

    public function testEagerModel(){
        $this->seed([CustomerSeeder::class,WalletSeeder::class,ImageSeeder::class]);
        $customer = Customer::find("RAHMAT");
        $this->assertNotNull($customer);
    }
}
