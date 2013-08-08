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
        $this->assertEquals($array, $this->setting->array_get($array,'a.b.c.d'));
    }

    /**
     * Test array_set
     */
    public function testArray_set()
    {
        $array = array();
        $this->setting->array_set($array,null,1);
        $this->assertEquals(array(),$array);

        $array = array();
        $this->setting->array_set($array,'','a');
        $this->assertEquals(array(),$array);

        $array = array();
        $this->setting->array_set($array,'.','a');
        $this->assertEquals(array(),$array);

        $array = array();
        $this->setting->array_set($array,'a',null);
        $this->assertEquals(array(),$array);

        $array = array();
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

    public function testSet()
    {
        $this->setting->set('testCase.foo', 'bar');
        $this->assertTrue($this->setting->has('testCase.foo'));
        $this->assertEquals('bar', $this->setting->get('testCase.foo'));
        $this->assertEquals(array('foo' => 'bar'), $this->setting->get('testCase'));

        $this->setting->set('a.b', 'c');
        $this->assertTrue($this->setting->has('a'));
        $this->assertEquals(array('b' => 'c'), $this->setting->get('a'));

        $this->setting->set('', 'FOOBAR');
        $this->assertNull($this->setting->get(''));

        $this->setting->set('1.2.3.4.5.6.7.8', 'f');
        $this->assertTrue($this->setting->has('1.2.3.4'));

        $this->setting->set('1.2.3.4.5.6.7.8.', 'f');
        $this->assertTrue($this->setting->has('1.2.3.4.5.6.7.8.'));
        $this->assertEquals('f',$this->setting->get('1.2.3.4.5.6.7.8.'));
    }

    public function testForget()
    {
        $this->setting->set('a.b.c.d.e', 'f');
        $this->setting->forget('a.b.c');
        $this->assertFalse($this->setting->has('a.b.c'));

        $this->setting->set('1.2.3.4.5.6', 'f');
        $this->setting->forget('1.2.3.4.5');
        $this->assertFalse($this->setting->has('1.2.3.4.5.6'));
        $this->assertTrue($this->setting->has('1.2.3.4'));

        $this->setting->set('1.2.3.4.5.6.', 'f');
        $this->setting->forget('1.2.3.4.5.6.');
        $this->assertFalse($this->setting->has('1.2.3.4.5.6.'));
        $this->assertTrue($this->setting->has('1.2.3.4.5'));
    }
}
