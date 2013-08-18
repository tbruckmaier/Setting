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
        $this->setting->clear();
    }

    /**
     *  Delete test files
     */
    public function tearDown()
    {
        if(file_exists($this->file))
            unlink($this->file);
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

        $this->setting->clear();
        $this->setting->set('', 'FOOBAR');
        $this->assertEquals(array('' =>'FOOBAR'),$this->setting->get(''));

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

    public function testSetArray(){
        $array = [
            'id' => "foo",
            'user_info' => [
                'username' => "bar",
                'recently_viewed' => false,
            ]
        ];
        $this->setting->setArray($array);
        $this->assertEquals($array, $this->setting->get());

    }
}
