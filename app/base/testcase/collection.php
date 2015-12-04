<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use base_support_arr as Arr;

class collection extends PHPUnit_Framework_TestCase
{
    protected $conn = null;

    public function setUp()
    {
    }

	/**
	 * test
	 *
	 * @return void
	 */
    public function testAll()
    {
        // all
        $collection = collect([1, 2, 3, 4, 5, 6, 7]);
        $this->assertEquals($collection->all(), [1, 2, 3, 4, 5, 6, 7]);
    }

    public function testChunk()
    {
        // chunk
        $collection = collect([1, 2, 3, 4, 5, 6, 7]);
        $chunks = $collection->chunk(4);
        $this->assertEquals($chunks->toArray(), [[1, 2, 3, 4], [5, 6, 7]]);
    }

    public function testCollapse()
    {
        // collapse
        /*
        $collection = collect(/[[1, 2, 3], [4, 5, 6], [7, 8, 9]]);
        $collapsed = $collection->collapse();
        $this->assertEquals($collapsed->all(), [1, 2, 3, 4, 5, 6, 7, 8, 9]);
        */

        // testing
        $collection = collect([collect('base_middleware_test')]);
        dd($collection->collapse());
    }

    public function testContains1()
    {
        // contains
        $collection = collect(['name' => 'Desk', 'price' => 100, 'a' => 'aaa']);
        $this->assertTrue($collection->contains('Desk'));
        $this->assertFalse($collection->contains('Desk York'));
    }

    public function testContains2()
    {
        // contains
        $collection = collect([['name' => 'Desk', 'price' => 100, 'a' => 'aaa']]);
        $this->assertTrue($collection->contains('name', 'Desk'));
    }

    public function testContains3()
    {
        // contains
        $collection = collect([1, 2, 3, 4, 5]);
        $this->assertFalse($collection->contains(function($key, $value) {
            return $value>5;
        }));
    }

    public function testCount()
    {
        $collection = collect([1, 2, 3, 4]);
        $this->assertEquals($collection->count(), 4);
    }

    public function testDiff()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $diff = $collection->diff([2, 4, 6, 8]);
        $this->assertEquals($diff->all(), [0 => 1, 2 => 3, 4 => 5]);
    }

    public function testEach()
    {
        $collection = collect([1, 2, 3, 4, 7]);
        $collection->each(function($item, $key) use (&$res) {
            $res[] = $item;
        });
        $this->assertEquals($res, [1, 2, 3, 4, 7]);
    }

    /**
	 * The filter method filters the collection by a given callback, keeping only those
	 * items that pass a given truth test
	 */
    public function testFilter()
    {
        $collection = collect([1, 2, 3, 4]);
        $filtered = $collection->filter(function ($item)
                                        {
            return $item > 2;
        });
        $this->assertEquals($filtered->values()->all(), [3, 4]);
    }

    /**
	 * The first method returns the first element in the collection that passes a
	 * given truth test
	 */
    public function testFirst()
    {
        $this->assertEquals(collect([1, 2, 3, 4])->first(function ($key, $value) {
            return $value > 2;
        }), 3);

        $this->assertEquals(collect([1, 2, 3, 4])->first(), 1);
    }

    /**
	 * 
	 */
    public function testFlatten()
    {
        $collection = collect(['name' => 'taylor', 'languages' => ['php', 'javascript' => ['javascrip1', 'javascrip2']]]);
        $flattened = $collection->flatten();
        $this->assertEquals($flattened->all(), ['taylor', 'php', 'javascrip1', 'javascrip2']);
    }

    /**
	 * The flip method swaps the collection's keys with their corresponding values
	 */
    public function testFlip()
    {
        $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);
        $flipped = $collection->flip();
        $this->assertEquals($flipped->all(), ['taylor' => 'name', 'laravel' => 'framework']);
    }

    /**
	 * The forget method removes an item from the collection by its key
	 */
    public function testForget()
    {
        $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);
        $collection->forget('name');
        $this->assertEquals($collection->all(), ['framework' => 'laravel']);
        // [framework' => 'laravel']        
    }

    /**
	 * The forPage method returns a new collection containing the items that would be
	 * present on a given page number
	 */
    public function testForPage()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9])->forPage(2, 3);
        $this->assertEquals($collection->all(), [4, 5, 6]);
    }

    /**
	 * The get method returns the item at a given key. If the key does not exist, null
	 * is returned
	 */
    public function testGet()
    {
        $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);
        $value = $collection->get('name');
        $this->assertEquals($value, 'taylor');

        // You may optionally pass a default value as the second argument
        $collection = collect(['name' => 'taylor', 'framework' => 'laravel']);
        $value = $collection->get('foo', 'default-value');
        $this->assertEquals($value, 'default-value');
    }

    /**
	 * You may even pass a callback as the default value. The result of the callback will
	 * be returned if the specified key does not exist:
	 */
    public function testGroupBy()
    {
        $collection = collect([
            ['account_id' => 'account-x10', 'product' => 'Chair', 'category' => 'x1'],
            ['account_id' => 'account-x10', 'product' => 'Bookcase', 'category' => 'x2'],
            ['account_id' => 'account-x11', 'product' => 'Desk', 'category' => 'x1'],
        ]);

        $grouped = $collection->groupBy('category');

        
        $this->assertEquals($grouped->toArray(), [
            'x1' => [
                ['account_id' => 'account-x10', 'product' => 'Chair', 'category' => 'x1'],
                ['account_id' => 'account-x11', 'product' => 'Desk', 'category' => 'x1'],                
            ],
            'x2' => [
                ['account_id' => 'account-x10', 'product' => 'Bookcase', 'category' => 'x2'],
            ],
        ]);
    }

    /**
	 * The has method determines if a given key exists in the collection
	 */
    public function testHas()
    {
        $collection = collect(['account_id' => 1, 'product' => 'Desk']);
        $this->assertFalse($collection->has('email'));
        $this->assertTrue($collection->has('account_id'));
    }

    /**
	 * The implode method joins the items in a collection. Its arguments depend on the type
	 * of items in the collection
	 */
    public function testImplode()
    {
        $collection = collect([
            ['account_id' => 1, 'product' => 'Desk'],
            ['account_id' => 2, 'product' => 'Chair'],
        ]);

        $this->assertEquals($collection->implode('product', ', '), 'Desk, Chair');

        // If the collection contains simple strings or numeric values, simply pass the "glue"
        // as the only argument to the method
        $this->assertEquals(collect([1, 2, 3, 4, 5])->implode('-'), '1-2-3-4-5');
    }

    /**
	 * The intersect method removes any values that are not present in the given array or
	 * collection
	 */
    public function testIntersect()
    {
        $collection = collect(['Desk', 'Sofa', 'Chair']);
        $intersect = $collection->intersect(['Desk', 'Chair', 'Bookcase']);
        $this->assertEquals($intersect->values()->all(), ['Desk', 'Chair']);
    }

    /**
	 * The isEmpty method returns true if the collection is empty; otherwise, false is returned
	 */
    public function testIsEmpty()
    {
        $this->assertEquals(collect([])->isEmpty(), true);
    }

    /**
	 * Keys the collection by the given key
	 */
    public function testKeyBy()
    {
        $collection = collect([
            ['product_id' => 'prod-100', 'name' => 'Desk'],
            ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);

        $keyed = $collection->keyBy('product_id');

        $this->assertEquals($keyed->all(), [
            'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);

        // You may also pass your own callback, which should return the value to key the
        // collection by
        $keyed = $collection->keyBy(function ($item) {
            return strtoupper($item['product_id']);
        });

        $this->assertEquals($keyed->all(), [
            'PROD-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'PROD-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);
    }

    /**
	 * The keys method returns all of the collection's keys
	 */
    public function testKeys()
    {
        $collection = collect([
            'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);
        $keys = $collection->keys();
        $this->assertEquals($keys->all(), ['prod-100', 'prod-200']);
    }

    /**
	 * The last method returns the last element in the collection that passes a given
	 * truth test
	 */
    public function testLast()
    {
        $this->assertEquals(collect([1, 2, 3, 4])->last(function ($key, $value) {
            return $value < 3;
        }), 2);

        // You may also call the last method with no arguments to get the last element
        // in the collection. If the collection is empty, null is returned
        $this->assertEquals(collect([1, 2, 3, 4])->last(), 4);
    }

    /**
	 * The map method iterates through the collection and passes each value to the given
	 * callback. The callback is free to modify the item and return it, thus forming a
	 * new collection of modified items
	 */
    public function testMap()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $multiplied = $collection->map(function ($item, $key) {
            return $item * 2;
        });
        $this->assertEquals($multiplied->all(), [2, 4, 6, 8, 10]);
    }

    /**
	 * The merge method merges the given array into the collection. Any string key in
	 * the array matching a string key in the collection will overwrite the value in the
	 * collection
	 */
    public function testMerge()
    {
        $collection = collect(['product_id' => 1, 'name' => 'Desk']);
        $merged = $collection->merge(['price' => 100, 'discount' => false]);
        $merged->all();
        $this->assertEquals($merged->all(), ['product_id' => 1, 'name' => 'Desk', 'price' => 100, 'discount' => false]);

        // If the given array's keys are numeric, the values will be appended to the end
        // of the collection
        $collection = collect(['Desk', 'Chair']);
        $merged = $collection->merge(['Bookcase', 'Door']);
        $this->assertEquals($merged->all(), ['Desk', 'Chair', 'Bookcase', 'Door']);
    }

    public function testPluck()
    {
        $collection = collect([
            ['product_id' => 'prod-100', 'name' => 'Desk'],
            ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);
        $plucked = $collection->pluck('name', 'product_id');
        $this->assertEquals($plucked->all(), ['prod-100' => 'Desk', 'prod-200' => 'Chair']);
    }

    public function testPop()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        $this->assertEquals($collection->pop(), 5);

        $this->assertEquals($collection->all(), [1, 2, 3, 4]);
        
    }

    public function testPrepend()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $collection->prepend(0);
        $this->assertEquals($collection->all(), [0, 1, 2, 3, 4, 5]);
    }

	/**
	 * The pull method removes and returns an item from the collection by its key
	 */
    public function testPull()
    {
        $collection = collect(['product_id' => 'prod-100', 'name' => 'Desk']);
        $this->assertEquals($collection->pull('name'), 'Desk');
        $this->assertEquals($collection->all(), ['product_id' => 'prod-100']);
    }

	/**
	 * The push method appends an item to the end of the collection
	 */
    public function testPush()
    {
        $collection = collect([1, 2, 3, 4]);
        $collection->push(5);
        $this->assertEquals($collection->all(), [1, 2, 3, 4, 5]);
    }

	/**
	 * The put method sets the given key and value in the collection
	 */
    public function testPut()
    {
        $collection = collect(['product_id' => 1, 'name' => 'Desk']);
        $collection->put('price', 100);
        $this->assertEquals($collection->all(), ['product_id' => 1, 'name' => 'Desk', 'price' => 100]);
    }

	/**
	 * The random method returns a random item from the collection
	 */
    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $random = $collection->random();
        $this->assertGreaterThanOrEqual($random, 5);
        $this->assertLessThanOrEqual($random, 1);
    }

	/**
	 * The reduce method reduces the collection to a single value,
	 * passing the result of each iteration into the subsequent iteration
	 */    
    public function testReduce()
    {
        $collection = collect([1, 2, 3]);

        $total = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        $this->assertEquals($total, 6);

        // 设置初始值
        $total = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        }, 4);
        $this->assertEquals($total, 10);
    }

	/**
	 * The reject method filters the collection using the given callback.
	 * The callback should return true for any items it wishes to remove from the resulting collection
	 * 根据callback, 过滤掉不需要的数据
	 */
    public function testReject()
    {
        $collection = collect([1, 2, 3, 4]);

        $filtered = $collection->reject(function ($item) {
            return $item > 2;
        });

        $this->assertEquals($filtered->all(), [1, 2]);
    }
   
	/**
	 * The reverse method reverses the order of the collection's items
	 */
    public function testReverse()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $reversed = $collection->reverse();
        $this->assertEquals($reversed->all(), [5, 4, 3, 2, 1]);
    }

	/**
	 * The search method searches the collection for the given value and returns its key if found.
	 * If the item is not found, false is returned
	 */
    public function testSearch()
    {
        $collection = collect(['a' => 2, 'b' => 4, 4, 6, 8]);

        $this->assertEquals($collection->search(4), 'b');

        $collection = collect(['a' => 2, 'b' => 4, 4, 'w' => 6, 8]);

        $this->assertEquals($collection->search(function ($item, $key) {
            return $item > 5;
        }), 'w');
    }

	/**
	 * The shift method removes and returns the first item from the collection
	 */
    public function testShift()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        $this->assertEquals($collection->shift(), 1);
        $this->assertEquals($collection->all(), [2, 3, 4, 5]);
    }

	/**
	 * The shuffle method randomly shuffles the items in the collection
	 */    
    public function testShuffle()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $shuffled = $collection->shuffle();
        $this->assertEquals($collection->reduce(function ($carry, $item) {
            return $item >= 1 && $item <= 5;
        }, false), true);
    }

	/**
	 * The slice method returns a slice of the collection starting at the given index
	 */    
    public function testSlice()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $slice = $collection->slice(4);
        $this->assertEquals($slice->all(), [5, 6, 7, 8, 9, 10]);

        $slice = $collection->slice(4, 2);
        $this->assertEquals($slice->all(), [5, 6]);
    }

	/**
	 * The sort method sorts the collection
	 */
    public function testSort()
    {
        
        $collection = collect([5, 3, 1, 2, 4]);
        $sorted = $collection->sort();
        $this->assertEquals($sorted->values()->all(), [1, 2, 3, 4, 5]);
    }

    /**
     * The sortBy method sorts the collection by the given key
     */
    public function testSortBy()
    {
        $collection = collect([
            ['name' => 'Desk', 'price' => 200],
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
        ]);

        $sorted = $collection->sortBy('price');
        $this->assertEquals($sorted->values()->all(), [
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
            ['name' => 'Desk', 'price' => 200],
        ]);

        // 使用匿名函数
        $collection = collect([
            ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
            ['name' => 'Chair', 'colors' => ['Black']],
            ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
        ]);

        $sorted = $collection->sortBy(function ($product, $key) {
            return count($product['colors']);
        });

        $this->assertEquals($sorted->values()->all(), [
            ['name' => 'Chair', 'colors' => ['Black']],
            ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
            ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
        ]);
    }

    /**
	 * This method has the same signature as the sortBy method,
	 * but will sort the collection in the opposite order
	 */
    public function testSortByDesc()
    {
        $collection = collect([1, 2, 3, 4]);
        $this->assertEquals($collection->sortByDesc()->values()->all(), [4, 3, 2, 1]);
    }

    /**
	 * The splice method removes and returns a slice of items starting at the specified index
	 */
    public function testSplice()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $chunk = $collection->splice(2);
        $this->assertEquals($chunk->all(), [3, 4, 5]);
        $this->assertEquals($collection->all(), [1, 2]);

        // You may pass a second argument to limit the size of the resulting chunk
        $collection = collect([1, 2, 3, 4, 5]);

        $chunk = $collection->splice(2, 1);

        $this->assertEquals($chunk->all(), [3]);
        $this->assertEquals($collection->all(), [1, 2, 4, 5]);

        // in addition, you can pass a third argument containing the new items to replace
        // the items removed from the collection
        $collection = collect([1, 2, 3, 4, 5]);
        $chunk = $collection->splice(2, 1, [10, 11]);
        $this->assertEquals($chunk->all(), [3]);
        $this->assertEquals($collection->all(), [1, 2, 10, 11, 4, 5]);
    }

    /**
	 * If the collection contains nested arrays or objects, you should pass a key
	 * to use for determining which values to sum
	 */
    public function testSum()
    {
        $collection = collect([
            ['name' => 'JavaScript: The Good Parts', 'pages' => 176],
            ['name' => 'JavaScript: The Definitive Guide', 'pages' => 1096],
        ]);

        $this->assertEquals($collection->sum('pages'), 1272);
        
        // In addition, you may pass your own callback to determine which values of the collection to sum
        $collection = collect([
            ['name' => 'Chair', 'colors' => ['Black']],
            ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
            ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
        ]);

        $this->assertEquals($collection->sum(function ($product) {
            return count($product['colors']);
        }), 6);
    }

    /**
	 * The take method returns a new collection with the specified number of items:
	 */
    public function testTake()
    {
        $collection = collect([0, 1, 2, 3, 4, 5]);
        $chunk = $collection->take(3);
        $this->assertEquals($chunk->all(), [0, 1, 2]);

        // You may also pass a negative integer to take the specified amount of
        // items from the end of the collection:
        $collection = collect([0, 1, 2, 3, 4, 5]);
        $chunk = $collection->take(-2);
        $this->assertEquals($chunk->all(), [4, 5]);
    }

    /**
	 * The toArray method converts the collection into a plain PHP array
	 */
    public function testToArray()
    {
        $collection = collect(['name' => 'Desk', 'price' => 200]);
        $this->assertEquals($collection->toArray(), ['name' => 'Desk', 'price' => 200]);
    }

    /**
	 * The toJson method converts the collection into JSON
	 */
    public function testTojson()
    {
        $collection = collect(['name' => 'Desk', 'price' => 200]);
        $this->assertEquals($collection->toJson(), '{"name":"Desk","price":200}');
    }

    /**
	 * The transform method iterates over the collection and calls the given callback with
	 * each item in the collection. The items in the collection will be replaced by the
	 * values returned by the callback
	 */
    public function testTransform()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $collection->transform(function ($item, $key) {
            return $item * 2;
        });
        $this->assertEquals($collection->all(), [2, 4, 6, 8, 10]);
    }

    /**
	 * The unique method returns all of the unique items in the collection
	 */
    public function testUnique()
    {
        $collection = collect([1, 1, 2, 2, 3, 4, 2]);
        $unique = $collection->unique();
        $this->assertEquals($unique->values()->all(), [1, 2, 3, 4,]);

        // When dealing with nested arrays or objects, you may specify the key used to
        // determine uniqueness
        $collection = collect([
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'iPhone 5', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
            ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
        ]);
        $unique = $collection->unique('brand');
        $this->assertEquals($unique->values()->all(), [
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
        ]);

        // You may also pass your own callback to determine item uniqueness
        $unique = $collection->unique(function ($item) {
            return $item['brand'].$item['type'];
        });

        $this->assertEquals($unique->values()->all(), [
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
            ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
        ]);
    }

    /**
	 * The values method returns a new collection with the keys reset to consecutive integers
	 */
    public function testValues()
    {
        $collection = collect([
            10 => ['product' => 'Desk', 'price' => 200],
            11 => ['product' => 'Desk', 'price' => 200]
        ]);
        $values = $collection->values();
        $this->assertEquals($values->all(), [
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Desk', 'price' => 200],
        ]);
    }

    /**
	 * The where method filters the collection by a given key / value pair
	 */
    public function testWhere()
    {
        $collection = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ]);

        $filtered = $collection->where('price', 100);
        $this->assertEquals($filtered->values()->all(), [
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ]);
    }

    /**
	 * This method has the same signature as the where method; however, all
	 * values are compared using "loose" comparisons
	 */
    public function testZip()
    {
        $collection = collect(['Chair', 'Desk']);
        $zipped = $collection->zip([100, 200]);
        $this->assertEquals($zipped->toArray(), [
            ['Chair', 100],
            ['Desk', 200],
        ]);
    }

}

