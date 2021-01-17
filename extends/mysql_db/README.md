# mysql_db
mysql db操作类

使用方法

$db = new Db();

//查询操作

var_dump($db->table('user')->where('id > 2')->order('id desc')->limit('2,4')->select());

//插入操作

var_dump($db->table('user')->insert(array('username'=>'user','password'=>'pwd')));

//更新操作

var_dump($db->table('user')->where('id = 1')->update(array('username'=>'user1','password'=>'pwd1')));

//删除操作

var_dump($db->table('user')->where('id = 1')->delete());
