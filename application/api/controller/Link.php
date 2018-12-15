<?php

namespace app\api\controller;

use think\Controller;
class Link extends Controller
{
    public function test()
    {
        $request[] = input();
        return json($request); 
    }
    public function ss()
    {
        echo '444'; 
    }
    public function getIndex(){
        $id = input('mpid');
        $type = db('shop_type')->where(['mpid'=>$id])->select();
        $info = db('shop_list')->where(['mpid'=>$id])->order('sort desc')->select();
        foreach($info as $k=>$v){
            $info[$k]['url'] = url('show/index/index',['id'=>$v['id']]);
        }
       
        $return = [
            'type'=>$type,
            'info'=>$info
        ];
        //$ss[]=$type;
        return json($return);
    }
}
