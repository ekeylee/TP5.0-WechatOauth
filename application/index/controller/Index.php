<?php
/*
 * (c) U.E Dream Development Studio
 *
 * Author: 李益达 - Ekey.Lee <ekey.lee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\index\controller;

use app\common\controller\Wechat;

class Index extends Wechat
{
    public function index()
    { 
        //$openid =get_openid();
       //dump($openid);
       //echo '333';
       $openid = get_openid();
       $this->assign('openid',$openid);
       return view();
 
    }
}
