<?php
/**
 * Created by IntelliJ IDEA.
 * User: lunasoftPC35
 * Date: 2018-10-01
 * Time: 오후 5:34
 */

namespace GoogleApiProc;


class TestController
{
   public function test()
   {
       echo request('testMsg');
   }
}