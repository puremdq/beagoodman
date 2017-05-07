<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Model\Dynamic;
use App\Http\Model\Mood;
use App\Http\Model\Article;
use Illuminate\Support\Facades\Redis;
use App\Http\Model\File as FileTable;
use Illuminate\Support\Facades\DB;

use App\Lib\AliYunOss;
use Mockery\Exception;

class DynamicController extends Controller
{

    public function index(Request $request)
    {
        $page = intval($this->getValue($request->input('page'), 1));
        $offset = intval($this->getValue($request->input('offset'), 0));
        $type = $this->getValue($request->input('type'), '%');
        $userid = $this->getValue($request->input('userid'), '%');
        //$offset = $request->input('offset');
        $pageSize = 5;        /*TODO 将页面大小设为配置项*/

        /*TODO  验证当前用户是否有访问权限*/

        $currentUserId = session('user_id');


        $fields = '`dynamic`.*,`username`,`avatar_key`';


        $tables = '`user`,`dynamic`';

        if (!empty($currentUserId)) {

            $fields = '`dynamic`.*,`username`,`avatar_key`,`user_relation`.`state` as `follow_state`,`like_record`.`state` as `like_state`';

            $tables = '`user`,`dynamic`
         LEFT JOIN `user_relation` ON `user_relation`.`target_user_id`=`dynamic`.`user_id` AND `user_relation`.`source_user_id` = ' . $currentUserId .
                ' LEFT JOIN  `like_record` ON `dynamic`.`dynamic_id`=`like_record`.`target_id` AND `like_record`.`user_id` = ' . $currentUserId . ' AND `like_record`.`target_type`=0';
        }

        $where = [
            '`dynamic`.`user_id`=`user`.`user_id`',
            '`dynamic` . `state` = 0',
            '`user` . `state` = 0'
        ];
        $orderBy = '`published_time` desc';
        $limit = ($pageSize * ($page - 1) + $offset) . ',' . $pageSize;
        $values = [];


        if ($type != '%' && is_numeric($type)) {
            $where[] = '`dynamic`.`dynamic_type` = ?';
            $values[] = intval($type);
        } else if ($type == 'zan') {
            $where[] = '`like_record`.`state` = 0';
        }

        if ($userid != '%') {
            $where[] = '`user`.`user_id` = ?';
            $values[] = intval($userid);
        }


        $results = $this->ls($fields, $tables, $where, $values, $orderBy, $limit);

        $fields = 'count(`dynamic` . `dynamic_id`) as `count`';
        $count = $this->ls($fields, $tables, $where, $values);
        $count = $count[0]->count;

        return json_encode(
            [
                'state' => 0,
                'dynamicList' => $results,
                'isAll' => $pageSize * $page + $offset >= $count ? 1 : 0

            ]
        );

    }

    /*显示 某一条动态*/
    public function show($id, Request $request)
    {
        //dd(Dynamic::count());
        /* DB::enableQueryLog();
          dd(DB::getQueryLog());
        */

        $dynamic = Dynamic::find($id);

        if ($dynamic->dynamic_type == 1) {

            //article
            return view('app.dynamic.article')->with([
                'dynamic' => $dynamic,
                'article' => $dynamic->target,
            ]);

        }


    }

    /*存储新动态*/
    public function store(Request $request)
    {
        //  dd( Common::getDateDir());
        $data = $request->except('_token');
        //  dd($data);
        $dynamic = [];
        $dynamic['user_id'] = session('user_id');
        $user = session('user');


        if ($data['dynamic_type'] == 'mood') {

            $dynamic['dynamic_type'] = 0;
            //创建心情
            $target = $this->createMood($data);
            $dynamic['target_id'] = $target->mood_id;
            $dynamic['preview'] = $this->getPreview($target->mood_content, $target->img_keys);

            $user->mood_num = $user->mood_num + 1;

        } else if ($data['dynamic_type'] == 'article') {

            $dynamic['dynamic_type'] = 1;

            $target = $this->createArticle($data);

            $dynamic['target_id'] = $target->article_id;
            $dynamic['preview'] = $this->getPreview($target->article_content, $data['first_img_url'], $target->article_title, 'article');

            $user->article_num = $user->article_num + 1;

        } else if ($data['dynamic_type'] == 'forward') {

            $dynamic['dynamic_type'] = 2;

        } else {
            return back(405)->with('tips', '非法的操作');
        }

        $dynamic['published_time'] = time();
        Dynamic::create($dynamic);
        $user->save();
        session(['user' => $user]);
        session()->flash('tips', '发表成功');
        session()->flash('state', 1);
        return redirect(url('/'));
    }


    /*删除给定id的动态*/
    public function delete($id)
    {
        $state = 0;
        $msg = '';

        $dynamic = Dynamic::find($id);

        if ($dynamic == null) {
            $state = 1;
            $msg = '非法操作';
        } else {


            if ($dynamic->user_id != session('user_id')) {
                /*当前用户没有权限*/
                $state = 1;
                $msg = '你很机智嘛,不过我更加机智';
            } else {

                DB::beginTransaction();//开始事务

                try {

                    $dynamic->state = 1;

                    $user = session('user');

                    if ($dynamic->dynamic_type == 0) {

                        //心情
                        $user->mood_num = $user->mood_num - 1;

                    } else if ($dynamic->dynamic_type == 1) {
                        //文章
                        $user->article_num = $user->article_num - 1;

                    }

                    $user->save();
                    $dynamic->save();

                    session(['user' => $user]);

                    DB::commit();
                } catch (\Exception $e) {
                    $state = 1;
                    $msg = $e->getMessage();
                    DB::rollBack();

                }

            }

        }

        return json_encode(['state' => $state,
                'msg' => $msg]
        );

    }


    /*将给定id的动态浏览次数+1*/
    public function addViews($id, Request $request)
    {
        $redisKey = 'dynamic_' . $id . 'add_read_num';

        $threshold = 10;//设置大于等于多少时 同步到数据库/*TODO 可设为配置项*/


        try {

            $addNum = intval($request->input('each_add_num'));//要增加的浏览次数
            $alreadyHaveAddNum = Redis::exists($redisKey) ? intval(Redis::get($redisKey)) : 0;//已经有的要增加的次数
            $newAlreadyHaveAddNum = $alreadyHaveAddNum + $addNum;//新的要增加的次数


            if ($newAlreadyHaveAddNum >= $threshold) {

                $res = DB::update('update `dynamic` set `read_num` = `read_num`+? where `dynamic_id` = ?', [$threshold, intval($id)]);

                if ($res) {

                    /*存储成功 减去阈值*/
                    $newAlreadyHaveAddNum = $newAlreadyHaveAddNum - $threshold;
                }

            }

            Redis::set($redisKey, $newAlreadyHaveAddNum);

            return json_encode([

                'state' => 0

            ]);

        } catch (Exception $e) {

            return json_encode([

                'state' => 1,
                'msg' => $e->getMessage()

            ]);
        }

    }


    /**
     *创建心情
     * @param array $data 要被创建的数据
     * @return mixed 是否成功 成功则返回 创建的对象 不成功则返回false
     */
    private function createMood($data)
    {

        $mood = [];
        $mood['img_keys'] = '';


        if ($data['img_keys'] != null) {

            //有上传图片
            $imgKeys = explode('|', $data['img_keys']);

            $newImgKeys = $this->storeImg($imgKeys);

            if ($newImgKeys === false) {
                return false;
            } else {

                $mood['img_keys'] = $newImgKeys;

            }

        }

        $mood['mood_content'] = $data['mood_content'];
        $mood = Mood::create($mood);

        return $mood;
    }

    /*创建文章*/
    private function createArticle($data)
    {

        $article = [];

        if ($data['img_keys'] != null) {

            //有上传图片
            $imgKeys = explode('|', $data['img_keys']);

            $newImgKeys = $this->storeImg($imgKeys, true);

            if ($newImgKeys === false) {
                return false;
            }

        }

        $article['article_title'] = $data['article_title'];
        $article['article_content'] = $data['article_content'];
        $article = Article::create($article);

        return $article;

    }


    /**
     * 储存图片
     * @param array $imgKeys 在redis的key的数组
     * @param boolean $isCreateFile 是否创建 在file表中插入记录 建立对应关系
     * @return mixed
     * */
    private function storeImg($imgKeys, $isCreateFile = false)
    {
        //有上传图片

        $newImgKeys = '';

        $path = 'dynamic/';

        foreach ($imgKeys as $imgKey) {

            $img = Redis::lrange($imgKey, 0, -1);
            $imgName = Redis::lrange($imgKey . "_name", 0, -1);
            $img = $img[0];
            $imgName = $path . $imgName[0];

            $res = AliYunOss::putObject('puremdq', $imgName, $img);

            if (!$res) {
                return false;
            } else {

                if ($isCreateFile) {

                    $fileData = [
                        'file_key' => $imgKey,
                        'file_url' => env('imgUrl') . '/' . $imgName,
                    ];

                    if (!FileTable::create($fileData)) {
                        return false;
                    }
                }

                $newImgKeys = $newImgKeys . $imgName . '|';
            }
        }

        return substr($newImgKeys, 0, -1);
    }


    /**
     * 得到预览
     * @param string $content 要生成预览的内容
     * @param string $imgKeysOrUrl 可能包含的图片的key值或url
     * @param string $title 如果存在的标题
     * @param string $type 要创建Preview的类型  如('mood'  'article')
     * @return mixed serialize操作后的结果
     * */
    private function getPreview($content, $imgKeysOrUrl = null, $title = null, $type = 'mood')
    {

        $typeAllow = ['mood', 'article', 'forward'];


        if (!in_array($type, $typeAllow)) {
            return false;
        }


        $preview = array();
        $content = strip_tags($content);

        $previewLen = 100;        /*TODO　可以将截取长度设为配置信息*/

        if ($title != null) {
            $preview['title'] = $title;

        }


        if (!empty($imgKeysOrUrl)) {

            if ($type == 'mood') {
                $preview['img_keys'] = $imgKeysOrUrl;

            } else {
                $preview['first_img_url'] = $imgKeysOrUrl;
            }

        }

        if (strlen($content) <= $previewLen) {
            $preview['content'] = $content;
            $preview['isAll'] = 1;

        } else {

            $preview['content'] = mb_substr($content, 0, $previewLen) . '...';
            $preview['isAll'] = 0;

        }

        return serialize($preview);

    }


}
