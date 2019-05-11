<?php

// 应用公共文件 

/**
 * @desc 获得 最近七日的 pv,uv,ip
 * @return array
 */
function getPvUvIp()
{
    $end = strtotime(date('Y-m-d'));
    $start = strtotime(date('Y-m-d', time()-86400*6));
    for ($i = $end; $i >= $start ; $i-=86400) {
        $day = date('Y-m-d', $i);
        $data[] = [
            'day' => $day,
            'pv_count' => getPvCount($day),
            'uv_count' => getUvCount($day),
            'ip_count' => getIpCount($day)
        ];
    }
    return $data;
}

/**
 * @desc 获得指定日期的PV数
 * @param $day
 * @return float|int
 */
function getPvCount($day)
{
    $arr = think\Db::name('pv')->where([
        'day' => $day
    ])->select();
    $pv_arr = array_column($arr, 'page_view');
    $pv_count = array_sum($pv_arr);
    return $pv_count;
}

/**
 * @desc 获得指定日期的UV数,若当日无访客则为零
 * @param $day
 * @return int|string
 */
function getUvCount($day)
{
    $arr = think\Db::name('uv')
        ->where('day', '=', $day)
        ->find();
    if (!empty($arr)) {
        $uv_count = $arr['uv'];
    } else {
        $uv_count = 0;
    }
    return $uv_count;
}

/**
 * @desc 获得指定日期的IP数
 * @param $day
 * @return int|string
 */
function getIpCount($day)
{
    $count = think\Db::name('pv')
        ->where('day', '=', $day)
        ->count();
    return $count;
}

/**
 * @desc 百度定位接口
 * @param $ip
 * @return array
 */
function getIpInfo($ip)
{
    $ak = 'bM7KqM7xHjiLKRBFIjR1f1uybfRGdocx'; //访问应用AK
    $url = file_get_contents("http://api.map.baidu.com/location/ip?ip=$ip&ak=$ak");
    $res1 = json_decode($url,true);
    $data =$res1;
    if ($data) {
        return array("status" => 1, "msg" => "查询成功", "result" => $data);
    } else {
        return array("status" => -1, "msg" => "查询失败");
    }
}

/**
 * @desc 根据城市获得天气信息
 * @param $city
 * @return mixed
 */
function getTodayWeather($city)
{
    $url = 'http://wthrcdn.etouch.cn/weather_mini?city='.urlencode($city);
    $html = file_get_contents($url);
    $json_data = gzdecode($html); //解码gzip压缩字符串，返回已解码的字符串
    $data = json_decode($json_data, true);
    $arr = $data['data']['forecast'][0];
    $arr['ganmao'] = $data['data']['ganmao'];
    $arr['wendu'] = $data['data']['wendu'];
    $arr['city'] = $data['data']['city'];
    return $arr;
}

/**
 * @desc 根据导航数组获得导航条对应栏目
 * @param $nav
 * @return mixed
 */
function getNavInfo($nav)
{
    foreach ($nav as $value) {
        $nav_info[$value['title']] = \think\Db::name('nav_info')->where('nav_id', '=', $value['id'])->select();
    }
    return $nav_info;
}

/**
 * @desc 根据轮播标识获得对应的轮播图或背景图
 * @param $name
 * @return false|PDOStatement|string|\think\Collection
 */
function getSlideInfo($name)
{
    $slide = \think\Db::name('slide')->where(['name' => $name, 'status' => 1])->find();
    $slide_id = $slide['id'];
    $slide_info = \think\Db::name('slide_info')->where(['slide_id' => $slide_id, 'status' => 1])->select();
    return $slide_info;
}

/**
 * @desc 插入搜索数据
 * @param $param
 * @param $cid
 * @param $day
 * @param $cate
 * @return array
 */
function insertSearchData($param, $cid, $day, $cate)
{
    return [
        'title' => isset($param['title']) ? $param['title'] : $param['name'],
        'cate' => $cate,
        'cid' => $cid,
        'publish_date' => $day,
        'created_at' => $param['created_at'],
        'updated_at' => $param['updated_at']
    ];
}

/**
 * @desc 更新搜索数据
 * @param $param
 * @param $tid
 * @param $day
 * @return array
 */
function updateSearchData($param, $tid, $day)
{
    return [
        'id' => $tid,
        'title' => isset($param['title']) ? $param['title'] : $param['name'],
        'publish_date' => $day,
        'updated_at' => $param['updated_at']
    ];
}

/**
 * @desc 获取前后文章id
 * @param $all_id
 * @param $id
 * @return array
 */
function getPreNextData($all_id, $id, $cate)
{
    foreach ($all_id as $key => $value) {
        if ($value['id'] == $id) {
            //定位当前，获取下一篇
            if (isset($all_id[$key+1])) {
                $next_id = $all_id[$key+1]['id'];
            } else {
                $next_id = '';
            }

            if (isset($all_id[$key-1])) {
                $pre_id = $all_id[$key-1]['id'];
            } else {
                $pre_id = '';
            }
        }
    }

    if ($pre_id != '') {
        $data_pre = \think\Db::name($cate)->find(['id' => $pre_id]);
    } else {
        $data_pre = '';
    }

    if ($next_id != '') {
        $data_next = \think\Db::name($cate)->find(['id' => $next_id]);
    } else {
        $data_next = '';
    }

    $data = [
        'data_next' => $data_next,
        'data_pre' => $data_pre
    ];

    return $data;
}

/**
 * @desc 邮箱发送验证码，一次发一条
 * @param array $data
 * @return bool
 * @throws \phpmailer\phpmailerException
 */
function sendEmail($data = [])
{
    Vendor('phpmailer.phpmailer');
    $mail = new \phpmailer\PHPMailer(); //实例化

    $mail->SMTPDebug = 1; // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host = 'smtp.qq.com'; //SMTP服务器 以QQ邮箱为例子
    $mail->Port = 465;  //邮件发送端口
    $mail->SMTPAuth = true;  //启用SMTP认证
    $mail->SMTPSecure = "ssl";   // 设置安全验证方式为ssl

    $mail->CharSet = "UTF-8"; //字符集
    $mail->Encoding = "base64"; //编码方式

    $mail->Username = '948360675@qq.com';  //你的邮箱
    $mail->Password = 'gvqidxsbinxabdjc';  //你的授权码
    $mail->Subject = '网站注册验证码'; //邮件标题

    $mail->From = '948360675@qq.com';  //发件人地址（也就是你的邮箱）
    $mail->FromName = 'LED照明驱动与控制应用研究中心';  //发件人姓名

    if($data && is_array($data)){
        foreach ($data as $k=>$v){
            $mail->AddAddress($v['user_email'], "亲"); //添加收件人（地址，昵称）
            $mail->IsHTML(true); //支持html格式内容
            $mail->Body = $v['content']; //邮件主体内容

            //发送成功就删除
            if ($mail->Send()) {
                return true;
            }else{
                echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息
                return false;
            }
        }
    }
}

//遍历文件夹下所有文件夹和子文件夹
function my_dir($dir) {
    $files = array();
    if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
        while(($file = readdir($handle)) !== false) {
            if($file != ".." && $file != ".") { //排除根目录；
                if(is_dir($dir."/".$file)) { //如果是子文件夹，就进行递归
                    $files[$file] = my_dir($dir."/".$file);
                } else { //不然就将文件的名字存入数组；
                    $files[] = $file;
                }

            }
        }
        closedir($handle);
        return $files;
    }
}
