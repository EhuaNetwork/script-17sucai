<?php

namespace DB;

/**
 *php操作mysql的工具类
 */
class Db
{
    private $_db = null;//数据库连接句柄
    private $_table = null;//表名
    private $_where = null;//where条件
    private $_order = null;//order排序
    private $_limit = null;//limit限定查询
    private $_group = null;//group分组
    private $_configs = array(
        'hostname' => "localhost",
        'dbname' => "caiji_htmlsucai",
        'username' => "ehua",
        'password' => "150638",
    );//数据库配置

    /**
     * 构造函数，连接数据库
     */
    public function __construct()
    {
        $link = $this->_db;
        if (!$link) {
            $db = mysqli_connect($this->_configs['hostname'], $this->_configs['username'], $this->_configs['password'], $this->_configs['dbname']);
            mysqli_query($db, "set names utf8");
            if (!$db) {
                $this->ShowException("错误信息" . mysqli_connect_error());
            }
            $this->_db = $db;
        }
    }

    /**
     * 获取所有数据
     *
     * @param <type> $table The table
     *
     * @return   boolean All.
     */
    public function getAll($table = null)
    {
        $link = $this->_db;
        if (!$link) return false;
        $sql = "SELECT * FROM {$table}";
        $data = mysqli_fetch_all($this->execute($sql), MYSQLI_ASSOC);
        return $data;
    }

    public function table($table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * 实现查询操作
     *
     * @param string $fields The fields
     *
     * @return   boolean ( description_of_the_return_value )
     */
    public function select($fields = "*")
    {
        $fieldsStr = '';
        $link = $this->_db;
        if (!$link) return false;
        if (is_array($fields)) {
            $fieldsStr = implode(',', $fields);
        } elseif (is_string($fields) && !empty($fields)) {
            $fieldsStr = $fields;
        }
        $sql = "SELECT {$fields} FROM {$this->_table} {$this->_where} {$this->_order} {$this->_limit}";
        $data = mysqli_fetch_all($this->execute($sql), MYSQLI_ASSOC);
        return $data;
    }

    /**
     * order排序
     *
     * @param string $order The order
     *
     * @return   boolean ( description_of_the_return_value )
     */
    public function order($order = '')
    {
        $orderStr = '';
        $link = $this->_db;
        if (!$link) return false;
        if (is_string($order) && !empty($order)) {
            $orderStr = "ORDER BY " . $order;
        }
        $this->_order = $orderStr;
        return $this;
    }

    /**
     * where条件
     *
     * @param string $where The where
     *
     * @return   <type> ( description_of_the_return_value )
     */
    public function where($where = '')
    {
        $whereStr = '';
        $link = $this->_db;
        if (!$link) return $link;
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                if ($value == end($where)) {
                    $whereStr .= "`" . $key . "` = '" . $value . "'";
                } else {
                    $whereStr .= "`" . $key . "` = '" . $value . "' AND ";
                }
            }
            $whereStr = "WHERE " . $whereStr;
        } elseif (is_string($where) && !empty($where)) {
            $whereStr = "WHERE " . $where;
        }
        $this->_where = $whereStr;
        return $this;
    }

    /**
     * group分组
     *
     * @param string $group The group
     *
     * @return   boolean ( description_of_the_return_value )
     */
    public function group($group = '')
    {
        $groupStr = '';
        $link = $this->_db;
        if (!$link) return false;
        if (is_array($group)) {
            $groupStr = "GROUP BY " . implode(',', $group);
        } elseif (is_string($group) && !empty($group)) {
            $groupStr = "GROUP BY " . $group;
        }
        $this->_group = $groupStr;
        return $this;
    }

    /**
     * limit限定查询
     *
     * @param string $limit The limit
     *
     * @return   <type> ( description_of_the_return_value )
     */
    public function limit($limit = '')
    {
        $limitStr = '';
        $link = $this->_db;
        if (!$link) return $link;
        if (is_string($limit) || !empty($limit)) {
            $limitStr = "LIMIT " . $limit;
        } elseif (is_numeric($limit)) {
            $limitStr = "LIMIT " . $limit;
        }
        $this->_limit = $limitStr;
        return $this;
    }

    /**
     * 执行sql语句
     *
     * @param <type> $sql The sql
     *
     * @return   boolean ( description_of_the_return_value )
     */
    public function execute($sql = null)
    {
        $link = $this->_db;
        if (!$link) return false;
        $res = mysqli_query($this->_db, $sql);
        if (!$res) {
            $errors = mysqli_error_list($this->_db);
            $this->ShowException("报错啦！<br/>错误号：" . $errors[0]['errno'] . "<br/>SQL错误状态：" . $errors[0]['sqlstate'] . "<br/>错误信息：" . $errors[0]['error']);
            die();
        }
        return $res;
    }

    /**
     * 执行sql语句  有数据则返回数据 没有返回结果0 1
     *
     * @param <type> $sql The sql
     *
     * @return   boolean ( description_of_the_return_value )
     */
    public function query($sql = null)
    {
        $res = $this->execute($sql);
        $data = mysqli_fetch_all($res, MYSQLI_ASSOC);

        return $data ? $data : $res;
    }

    /**
     * 插入数据
     *
     * @param <type> $data The data
     *
     * @return   boolean ( description_of_the_return_value )
     */
    public function insert($data)
    {
        $link = $this->_db;
        if (!$link) return false;
        if (is_array($data)) {
            $keys = '';
            $values = '';
            foreach ($data as $key => $value) {
                $keys .= "`" . $key . "`,";
                $values .= "'" . $value . "',";
            }
            $keys = rtrim($keys, ',');
            $values = rtrim($values, ',');
        }
        $sql = "INSERT INTO `{$this->_table}`({$keys}) VALUES({$values})";
        dd($sql);
        mysqli_query($this->_db, $sql);
        $insertId = mysqli_insert_id($this->_db);
        return $insertId;
    }

    /**
     * 更新数据
     *
     * @param <type> $data The data
     *
     * @return   <type> ( description_of_the_return_value )
     */
    public function update($data)
    {
        $link = $this->_db;
        if (!$link) return $link;
        if (is_array($data)) {
            $dataStr = '';
            foreach ($data as $key => $value) {
                $dataStr .= "`" . $key . "`='" . $value . "',";
            }
            $dataStr = rtrim($dataStr, ',');
        }
        $sql = "UPDATE `{$this->_table}` SET {$dataStr} {$this->_where} {$this->_order} {$this->_limit}";
        $res = $this->execute($sql);
        return $res;
    }

    /**
     * 删除数据
     *
     * @return   <type> ( description_of_the_return_value )
     */
    public function delete()
    {
        $link = $this->_db;
        if (!$link) return $link;
        $sql = "DELETE FROM `{$this->_table}` {$this->_where}";
        $res = $this->execute($sql);
        return $res;
    }

    /**
     * 异常信息输出
     *
     * @param <type> $var The variable
     */
    private function ShowException($var)
    {
        if (is_bool($var)) {
            var_dump($var);
        } else if (is_null($var)) {
            var_dump(NULL);
        } else {
            echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($var, true) . "</pre>";
        }
    }



// //查询操作
// var_dump($db->table('yio_orders')->where('id > 2')->order('id desc')->limit('2,4')->select());
// //插入操作
// var_dump($db->table('yio_orders')->insert(array('order_id'=>'1235','type'=>'alipay')));
// //更新操作
// var_dump($db->table('yio_orders')->where('order_id = 1')->update(array('type'=>'wechat','order_price'=>'1.00')));
// //删除操作
// var_dump($db->table('yio_orders')->where('order_id = 1235')->delete());
//执行SQL

//print_r($db->table('yio_orders')->query("select * from  yio_orders where order_id like '%1235%' "));


//print_r($db->table('yio_orders')->query("delete  from  yio_orders where id= 445"));


//查询操作
//$orders = $db->table('yio_orders')->where('id > 2')->order('id desc')->limit('5')->select();

//插入操作
//var_dump($db->table('yio_orders')->insert(array('order_id'=>'1235s','order_type'=>'alipay')));
//更新操作
//var_dump($db->table('yio_orders')->where("order_id = '1235s'")->update(array('order_type'=>'wechat','order_price'=>'1.00')));
//删除操作
//var_dump($db->table('yio_orders')->where("order_id = '1235s'")->delete());
//执行SQL

//print_r($db->table('yio_orders')->query("select * from  yio_orders where order_id like '%1235%' "));


//print_r($db->table('yio_orders')->query("delete  from  yio_orders where id= 445"));


//$orders = $db->table('yio_orders')->where('id > 2')->order('id desc')->limit('10')->select();
//
//print_r($orders[0]);
//
//
//echo "<style>td{border-left:1px solid #ccc;border-top:1px solid #ccc}</style><table>";
//
//
//echo '<table><tr><td>[id]</td><td>[order_id]</td><td>[order_type]</td><td>[pay_status]</td><td>[qr_price]</td><td>[create_at]</td><td>[update_at]</td></tr>';
//
//
//foreach ($orders as $k => $v) {
//
//echo "<tr>";
//
//
//$array = array($v['id'],$v['order_id'],$v['order_type'],$v['pay_status'],$v['qr_price'],$v['created_at'],$v['updated_at']);
//
//
//$table = table_show($array);
//
//echo $table;
//
//
//echo "</tr>";
//
//}


//function table_show($array)
//
//{
//
//
//$str ='';
//
//foreach ($array as $k => $v) {
//
//$str.= "<td>".$v."</td>";
//
//}
//
//return $str;
//
//
//}


//
//echo "</table>";
//
//$id = $_REQUEST['id']?$_REQUEST['id']:1;
//
//if($id>1){
//
//
//var_dump($db->table('yio_orders')->where("id = $id")->update(array('pay_status'=>'0')));
//
//echo  $_REQUEST['id'].'已更新';
//

}
