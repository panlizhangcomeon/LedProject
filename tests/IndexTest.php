<?php
/**
 * @author panlizhang
 * @version Release:
 * Date: 2019-04-23
 */
namespace tests;

class IndexTest extends TestCase
{
    //测试添加一个Session
    public function testAddOneValue(){
        $response=$this->visit('/index/index/addOneValue');
        $response->assertSessionHas('value1');
        $response->assertSessionHas('value1',1);
    }

    //测试添加多个Session
    public function testAddTwoValue(){
        $sessData=array(
            'value2'=>2,
            'value3'=>3
        );
        $response=$this->visit('/index/index/addTwoValue');
        $response->assertSessionHas($sessData);
    }

    //测试响应
    public function testResponse()
    {
        $this->visit('/index/index/Response')->assertResponseOk();
    }

}