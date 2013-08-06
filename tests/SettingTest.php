<?php

use Philf\Setting\Setting;

/**
 * Class SettingTest
 */
class SettingTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Setting our settings wrapper
     */
    public $setting;

    /**
     * @var string The file name of the config
     */
    private $file = 'test.json';

    /**
     * Set Up
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setting = new Setting('',$this->file);
    }

    /**
     *  Delete test files
     */
    public function tearDown()
    {
        if(file_exists($this->file))
            unlink($this->file);
    }


    /**
     * Test array_get
     */
    public function testArray_get()
    {
        //Test array_get
        $array = array(
            'a' => 'b',
            'c' => array(
                'd' => 'e',
            ),
        );

        $this->assertEquals('b', $this->setting->array_get($array, 'a'));
        $this->assertEquals($array, $this->setting->array_get($array, null));
        $this->assertEquals('e', $this->setting->array_get($array, 'c.d'));
        $this->assertEquals(array('d' => 'e'), $this->setting->array_get($array, 'c'));
        $this->assertEquals($array, $this->setting->array_get($array,''));
        $this->assertEquals('b', $this->setting->array_get($array,'a.b.c.d'));
    }

    /**
     * Test array_set
     */
    public function testArray_set()
    {
        $array = array();
        //set
        $this->setting->array_set($array,null,1);
        $this->assertEquals(array(),$array);

        $this->setting->array_set($array,'a',null);
        $this->assertEquals(array(),$array);

        $this->setting->array_set($array,'a',array());
        $this->assertEquals(array('a'=>array()),$array);

        $this->setting->array_set($array,'a',1);
        $this->assertEquals(array('a'=>1),$array);

        $this->setting->array_set($array,'a.b.c',10);
        $this->assertEquals(array(
            'a'=>array(
                'b'=>array(
                        'c'=>10
                    ),
                ),
            ),$array);

        $this->setting->array_set($array,'a.d.c',10);
        $this->assertEquals(array(
            'a'=>array(
                'b'=>array(
                    'c'=>10
                ),
                'd'=>array(
                    'c'=>10
                ),
            ),
        ),$array);

        $test = $this->setting->array_set($array,'a.b.c.d.e',10);
        $test2 = $this->setting->array_set($array,'a.b.c.d',array('e' => 10));
        $this->assertEquals($test,$test2);

    }

}
