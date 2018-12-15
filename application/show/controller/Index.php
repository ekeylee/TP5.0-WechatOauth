<?php 
/*
 * (c) U.E Dream Development Studio
 *
 * Author: 李益达 - Ekey.Lee <ekey.lee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

namespace app\show\controller;

use app\common\controller\Wechat;

class index extends Wechat
{
    //产品内页
    public function index(){
        $id = input('id');
        $info = db('shop_info')->where(['pid'=>$id])->find();
     
        $this->assign('info',$info);
        return view();
    }
   
}
