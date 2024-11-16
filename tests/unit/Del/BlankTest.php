<?php

namespace Del;

class BlankTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var EnlistPackage
     */
    protected $blank;

    protected function _before()
    {
        // create a fresh blank class before each test
        $this->blank = new EnlistPackage();
    }

    protected function _after()
    {
        // unset the blank class after each test
        unset($this->blank);
    }

    /**
     * Check tests are working
     */
    public function testBlah()
    {
        $this->assertEquals('Ready to start building tests', $this->blank->blah());
    }


}
