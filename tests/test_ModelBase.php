<?php

use GroupThr3e\AJExchange\Models\ModelBase;
use GroupThr3e\AJExchange\Util\DatasetBase;

require_once '../vendor/autoload.php';

class Test1 extends ModelBase
{


    public int $test1Id;
    public string $test1Name;
    public int $test2Id;
    public ?Test2 $test2;

    /**
     * @param int $test1Id
     * @param string $test1Name
     * @param int $test2Id
     * @param Test2|null $test2
     */
    public function __construct(int $test1Id, string $test1Name, int $test2Id, ?Test2 &$test2)
    {
        $this->test1Id = $test1Id;
        $this->test1Name = $test1Name;
        $this->test2Id = $test2Id;
        $this->test2 = &$test2;
    }
}

class Test2 extends ModelBase
{


    public int $test2Id;
    public string $test2Name;
    public int $test1Id;
    public ?Test1 $test1;

    /**
     * @param int $test2Id
     * @param string $test2Name
     * @param int $test1Id
     * @param Test1|null $test1
     */
    public function __construct(int $test2Id, string $test2Name, int $test1Id, ?Test1 &$test1)
    {
        $this->test2Id = $test2Id;
        $this->test2Name = $test2Name;
        $this->test1Id = $test1Id;
        $this->test1 = &$test1;
    }
}

class dataset extends DatasetBase
{
    public function __construct()
    {
        $data = [
            'test1Id' => 1,
            'test1Name' => 'test1',
            'test2Id' => 2,
            'test2Name' => 'test2'
        ];

        $result = $this->createModel(Test1::class, $data);
        var_dump($result);
    }
}

new dataset();