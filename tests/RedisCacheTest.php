<?php

use SimpleS3\Components\Cache\RedisCache;

class RedisCacheTest extends PHPUnit_Framework_TestCase
{
    const BUCKET_NAME = 'bucket';

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @throws Exception
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cache = new RedisCache(new Predis\Client());
    }

    /**
     * @test
     */
    public function set_and_retrieve_from_cache()
    {
        $this->cache->set(self::BUCKET_NAME, 'folder/to/file.txt', 'lorem ipsum');
        $this->cache->set(self::BUCKET_NAME, 'folder/to/file2.txt', 'lorem ipsum fdfdsf');
        $this->cache->set(self::BUCKET_NAME, 'file.txt', 'lorem ipsum fdsfsdfsd fsdf dsf sdds fsd');

        $this->assertEquals('lorem ipsum', $this->cache->get(self::BUCKET_NAME, 'folder/to/file.txt'));

        $search = $this->cache->search(self::BUCKET_NAME, 'folder/to/');
        $expected = [
            'folder/to/file.txt',
            'folder/to/file2.txt',
        ];

        $this->assertEquals($search, $expected);

        $this->cache->set(self::BUCKET_NAME, 'folder/to/file2.txt', 'updated lorem ipsum');

        $this->assertEquals('updated lorem ipsum', $this->cache->get(self::BUCKET_NAME, 'folder/to/file2.txt'));
    }

    /**
     * @test
     */
    public function remove_from_cache()
    {
        $this->cache->remove(self::BUCKET_NAME, 'folder/to/file.txt');
        $this->cache->remove(self::BUCKET_NAME, 'folder/to/file2.txt');
        $this->cache->remove(self::BUCKET_NAME, 'file.txt');

        $search = $this->cache->search(self::BUCKET_NAME, 'folder/to/');

        $this->assertCount(0, $search);
    }
}