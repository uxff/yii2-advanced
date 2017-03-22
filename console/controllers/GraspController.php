<?php

namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\caching\Cache;
use yii\helpers\Console;
use yii\console\Exception;

class GraspController extends \yii\console\Controller {
        public $myacct = '1436715401';
        public $mytid = 'shefields';
        // 挖他们的微博
        public $listen_list = array(
                '搞笑皮哥',
                '环球搞笑趣闻',
                '第一笑话王',
                '我的前任是个极品',
                '博露齿一笑',
                '趣闻搞笑',
                '搞笑图片会',
                '眼睛长在屁股上',
                '我的同事是个婊子',
        );
        // 想过滤掉的关键词
        public $unlike_word = array(
                //'http:',
                '微信号',
                'QQ号',
                '请关注',
                '转发微博',
                '直接关注',
                '猛戳',
                '点击关注',
                //'减肥',
                '品牌',
                '性价比',
                '优惠',
                '防晒',
                '号：',
                '号:',
        );
        // 自动评论内容
        public $cmt_list = array(
                '有意思',
                '很好很强大',
                '楼主真聪明',
                '有道理',
                '我也来评论一下',
                '世界之大，无奇不有',
                '转发微博',
                '非诚 勿扰',
                '这好像和别人不一样',
                '聊天诚可贵,网费价更高。若为睡觉故，二者皆可抛！:－0Zzzz',
                '我总是手太软，心更软，没有话题还陪你侃。',
                '时间难以抗拒！',
                '同志，您辛苦了，请继续！',
                //'对不起，网路不通，请重新发表…… ',
                '围观ing',
                '路过ing',
                '这是什么',
                '踩一踩',
                '有梦想，有追求的人',
                '人生就像弈棋',
                '学会爱自己',
                '活得糊涂的人，容易幸福',
                '走走停停',
                '能为别人设想的人，永远不寂寞。',
                '上天只保佑那些肯帮助自己的人。',
                '你要感谢那些告诉你缺点的人。',
                '能像看别人的缺点一样，准确发现自己的缺点，你的生命将会不平凡。',
                //'服务器系统崩溃，请稍后再试！',
                '表这样',
                '你真逗',
                '你更逗',
                '你太逗',
                '就喜欢看你了开颜的样子！',
                '生活愉快！',
                '艾特你吧',
                '祝你生活愉快！',
                '艾特你吧',
                '祝你生活愉快！',
                '祝你笑口常开！',
                '祝你幸福快乐！',
                '听懂了',
                '你懂的挺多',
                '[微笑]',
                '[调皮]',
                '[阳光]',
                '[偷笑]',
                '[跳舞]',
                '[加油]',
                '[傻笑]',
                '[流汗]',
                '你不能十全十美，但你可以独一无二。',
                '有一个女玩家在论坛上发贴问：“光棍节想向心仪的男生表白，送点什么礼物好。” 神回复：First Blood！',
                '有人问：五四运动的导火索是什么？ 神回复：五一只放三天假。。。。',
                '今天看到朋友个性签名：need just Word，Word has Word。不懂，于是谦虚请教。她给了我神一般的回复：你的就是我的，我的还是我的。。 ',
                'Boy的复数形式是？ 神回复：Gay。',
                '问：“杨过为什么要跳崖？” 神回复：“父是康！”',
                '为什么贫乳妹子一般都是吃货？ 神回复:因为穷胸极饿！ ',
                '楼主：“本人女，为什么我和bf拥抱的时候能够强烈感受到彼此的心跳？是因为我们爱的深，心相连？” 【神回复：“不，因为你平胸”】',
                '问：“为什么CCAV新闻放完了总是要播出他们在收拾稿子的片段?” 神回复：“为了告诉你，我们吹牛是打了草稿的！” ',
                '“你不约会不谈恋爱不出去玩不喝酒不逛街不疯不闹不叛逆不追星不暗恋不表白不聚会不k歌不撒野因为你要学习请问你的青春被狗吃了么！！” 神回复：“你整天约会谈恋爱出去玩喝酒逛街疯闹叛逆追星
暗恋表白聚会k歌撒野，就是不学习，那麼你的青春狗愿意吃么？”',
                '日本和中国的区别？神回复：“雅灭爹，雅灭爹！”“干爹~干爹~” ',
                '熄灯后，某女和寝室几个妹子聊星座。我疑惑的问了句：“为什么只有处女座，没有处男座？” 神回复————然后上铺彪悍的神回复：“怎么没有，只不过人家后来改名了，叫做射手座。。。”',
                '我有个中国通的美国朋友，今天换了个QQ签名“you don‘t know love far high”。 我猜可能他失恋了，觉得对方对爱的含义理解不够深。问他到底什么意思？神回复：“法海你不懂爱。”',
                '大学相恋四年，始终没有一句承诺。回家的列车上，他有32站，她有21站。她说，到站叫我，便倒头睡去。不知过了多久，她被叫醒，车以过了好几站。他温柔地笑着说，跟我回家吧。她扑哧一笑，眼泪跟
着滑了下来。',
                '她割破了手指，他替她去买创可贴，但他是个哑巴，比划了一阵子售货员也不知道他要买什么。后来他索性把手指割破，售货员才恍然大悟，原来是需要创可贴啊。随后售货员讪笑，只是买个创可贴嘛，至
于把手指弄伤吗？她在他身后看得热泪盈眶。神回复：第二天，她来大姨妈了。',
                '今天才知道，原来“卫生巾”的另一个可爱的称呼叫“妹纸”  神回复：普通叫法:大创可贴、可爱叫法：妹纸、文艺叫法:藏经阁、2B叫法:日本国旗 ',
                '楼主：为什么领导访问日本，日本方面比较冷淡，甚至机场连欢迎标语都没挂？神回复：怎么挂？热烈欢迎老朋友来日？',
                '有人发帖《以前上学时课文里学过的最伤感的一句话是什么》。很多人提名“庭有枇杷树，吾妻死时所 植，今已亭亭如盖矣”，还有人说出师表、木兰辞、十年生死两茫茫、雨巷等等。  不过最神的回复>是：“背诵全文 ” ',
                '楼主：该死的理发店把我头剪坏了！大家出点损招，要求破坏性越大越好，动静越小越好，因为是我一个人去。 神回复：半夜三更，月黑风高，静静地、轻轻地，一个人吊死在理发店门口。',
                '上司要我们提供了生日信息，我估计是要根据星座来安排工作方向了，我是狮子座，你们觉得我会被安排干什么？ 有网友回复：在门口拿着一只球趴着。',
                '为什么白头发拔一根会长十根？神回复：因为周围的黑头发看见亲人被人连根拔走，脸都吓白了。。。 ',
                '媳妇儿是路。朋友是牛。人生只有一条路。路上会有好多牛。有钱的时候别走错路。缺钱得时候别卖牛。 神回复：牛上路了咋办。',
                '清晨，她对他说：”老公，我昨晚把货都搬到车里了，累死。。你去处理下就完事啦！” 看着熬了一整夜的妻子，他心疼地说：”老婆你真好，把粗活累活都自己干了，轻松的事却留给我。。”说完泪水>已经决堤，他默默在右上角的购物车栏点下了”全部付款”。',
                '八戒说：“师兄，你快去医院看看吧，听说医院专门为你开了一个科室。” 悟空：“哦？什么科室？” “二逼猴科！”',
                '楼主姓袁，刚刚有了孩子，想给孩子起名，求网友想个好名字。楼下神回复:袁芳，你怎么看？',
                '八戒说：“师兄，你快去医院看看吧，听说医院专门为你开了一个科室。” 悟空：“哦？什么科室？” “二逼猴科！”',
                '楼主姓袁，刚刚有了孩子，想给孩子起名，求网友想个好名字。楼下神回复:袁芳，你怎么看？',
                '上地理课老师提问：在地球外面那一层是什么。班里有很多人举手，连平时考试不及格的我也举了手，老师和同学都很惊讶。老师跟同学们说我难得一次举手，由我来回答问题，也让他们给了我掌声，我站
起来后回答：香飘飘奶茶。',
                '美国裸体男子当街啃食人脸被击毙。一楼：确定不是丧尸？真击毙了？二楼：我艹.我赶紧种点豌豆。',
                '女球迷死抓林丹不放手 超级丹表情不悦 一楼：这帮娘们是不是想干死林丹啊 二楼：我认为应该用坐这个动词比较恰当 三楼：应该压?比较妥当 四楼：应该用淹 五楼：个人感觉吞没要好点！',
                '中国乳协：目前国产乳粉质量为历史最好。一楼：洋奶粉要钱，国产奶要命啊。二楼：能說髒***嗎？',
                '“肺癌”患者被切肺后死亡 检查无癌细胞。一楼：看来任督二脉还没有完全打通。二楼：前几天刚看完《心术》，这部科幻剧还不错。。。',
                '网传点一盘蚊香对人体伤害等同抽6包烟。一楼：中国人的抗体可以直接食用蚊香。二楼：已经达到蚊咬蚊死，蛇咬蛇亡的境界了！三楼：烟瘾大的朋友还是点盘蚊香吧，又过瘾又省钱又驱蚊。',
                '据说大地震前有三个明显征兆：①井水异常；②牲畜反应异常；③专家出来辟谣。神回复：第二条和第三条重复了。',
                '怎样才能看清女朋友的心？神回复：日久见人心。',
                'CCTV又采访了，记者问“作为一个中国人你能为祖国做些什么？”神回复：“移民，不给祖国添乱。”',
                '记者又问：“你认为爱国主义的表现是什么？”神回复：“移民，给资本主义添乱。”',
                '网友：和女友第一次开房就遇到pol.ice查房。神回复：pol.ice对女友来了一句，怎么老是你？',
                '为什么我们会围在一起讨论高考作文，而不是数学或物理？网友：因为，这是我们现在唯一还看得懂的东西。',
                '网友提问：男朋友和我闹矛盾了，是不是我逼太紧了？神回复：是太松了。',
                '我们看的什么电视，到最后男女主角结婚了，电视就大结局了，这说明啥？神回复：只要一结婚，后面就没戏了。',
                '为什么每次铲猫屎时，猫猫都喜欢盯观跟，它们格外在乎自己的便便吗？神回复：你上完马桶看见一个外星人把你的屎捞走，不会也很想跟过去看看，并且适时绊他一下，让他把屎扔自己脸上吗？',
                '平时去超五星酒店里吃饭，管服务员叫服务员还是小姐？神回复：吃饭时叫服务员，吃完叫小姐。',
                '听到一特好听的歌，歌词只记得是“一个芝麻糕，不如一针细”，求歌名啊！神回复：你可知Macau，不是我真姓。',
                '让我们文艺起来~~~',
                //'永远不要低估那些无聊人的爆发力',
                '后面的评论一定很新鲜',
                '全都是泡沫。゜○。○゜。Ｏ°ｏ○。ｏ゜○。゜Ｏ○。°゜Ｏ○。°○ｏ°○ｏ○゜ｏ。Ｏ゜○。゜。゜○。○゜。Ｏ°ｏ○。ｏ゜゜○。○。Ｏ°ｏ○。ｏ゜○。゜Ｏ○。°゜',
                '咕噜咕噜',
                '下面的名字中，肯定有你曾经的同学。1.张伟 2. 王伟 3.王芳 4.李娜 5.刘伟 6.张敏 7.李静 8．张丽 9. 王静 10. 王丽 11.李强 12.张静 13. 李敏 14.王敏 15.王磊 16.王勇 17. 王艳 18.张磊 19 .>黄东 20.刘东 21.刘洋。如果有请自觉回复',
                '一个武士手里拿一条活鱼问禅师：我跟你打一个赌，你猜我手里这个鱼是活的还是死的？禅师心想：如果说是活的，武士就会把鱼捏死。但鱼命和原则哪个更重要？禅师沉思了半个小时，说道：是死的。武
士看了看手中的鱼，说道：麻鄙的，半个小时前还是活的。',
                '鼻孔已经一干二净了。。。',
                '腿毛昨天是三七，今天该中分。。。',
                '神评论：11点以后就不要发微博了，不然全世界都知道你没有性生活。。。',
                '楼主：听说你支持AC米兰，每次英语考试，我就没选过BD。回复：听说你钟情曼联，所以每次我总是过了收卷时间五分钟才交卷。回复：听说你喜欢皇马，每次画画我都用黄色。回复：听说你中意国足，6>年来我都没洗过脚。',
                '问：一学生，成绩年年倒数第一，常与人打架，按领导要求，老师想给学生好听一点的期末评语，应该怎么写啊？ 神回复：该学生成绩稳定，动手能力强。',
                '-老夫年薪2万，老子有钱！-寡人后宫佳丽无数，寡人性福。-孤名车百辆，出行阔气。-哥什么都没有，就生了三个儿子，一个年薪2万，一个后宫佳丽无数，一个名车百辆。',
                '为什么广州叫羊城? 神回复：两个广州人吵架：“你讲咩啊!”“咩啊!”“咩咩啊!”',
                '情人节没情人怎么办？神回复：家里没死人的难道还得在清明节前费劲弄死一个两个的么？没有就不过。',
                '发现男朋友有个微博小号，关注了前女友和若干公司的美女，怎么办？神回复：分了吧，这男人的智商让人捉急，这都能被发现！',
                '问：常见的男厕所涂鸦多是谩骂和性暗示，而女厕所涂鸦却是以谜语及小故事为主，如何看待二者间的差别？答：你怎么能男女厕所都这么了解，你是男还是女！！！……',
                '问：卖萌究竟是褒义词还是贬义词？神回复：全凭长相。',
                '把自己的属相和星座结合在一起，形成自己的守护兽，一定很带感吧！我的守护兽是蝎尾猴。神回复：处女鸡不开心。',
                '最新消息：到花果山游览，属猴的游客凭身份证免票。神回复：俺属猪的，是师弟，能不能给个半价？',
                '为什么男生吵架总是吵不过女生呢?神回复：男生吵架吵赢的，最后他们都分手了。',
                '最新消息：到花果山游览，属猴的游客凭身份证免票。神回复：俺属猪的，是师弟，能不能给个半价？',
                '为什么男生吵架总是吵不过女生呢?神回复：男生吵架吵赢的，最后他们都分手了。',
                '一个文盲，一个字也不认识，那么他看到汉字的时候是什么感觉？神回答：懻懽慑戁戂戃戄戅戆懯戨戬戫戭戱，明白了吗？',
                '女朋友总是觉得别人的男朋友好，怎么办？-神回复：成为别人的男朋友。',
                '昨晚夜观星象，紫微星东移，掐指一算，有一极品微博出土，寻迹于此，本欲观此微博，一看之下，乃吐血三尺三丈，血染万寸白绫，数千年修为尽毁于此，无奈呼，此乃神微博，非我辈凡夫俗子可以涉猎
。',
                '当时觉得楼主很2，我不屑一笑就离开了。许多年后，当我发现那条非常2的微博被海量转发后，我才追悔莫及，于是，每当我能遇到前排的时候，不管这微博是多么的2，我都会复制粘贴这段话上去，告诫>楼下的粉丝，你们不回，以后会后悔的。不为别的，只为了一句：万一火了呢？',
                '问君能有几多愁,恰似我来打酱油',
                //'世上本没有路，打酱油的多了，于是便有了路',
                '老师：小新，请用‘左右为难’来造句  
　　小新：我考试时左右为难  
　　老师：是题目不会答，让你左右为难？  
　　小新：不，是左右同学答案不一样，让我左右为难',
                'o',
                '两个黄鹂鸣翠柳，土豪我们做朋友。蓬门今始为君开，小伙伴们都惊呆。姑苏城外寒山寺，高端大气上档次。赤橙黄绿青蓝紫，不作死就不会死。云鬓花颜金步摇，为何放弃了治疗？少年不知愁滋味，卧槽
给跪也是醉。钟山风雨起仓皇，挖掘技术哪家强？',
                '高端大气上档次，低调奢华有内涵，奔放洋气有深度，简约时尚国际范，低端粗俗甩节操，土憋矫情无下限，装模作样绿茶婊，外猛内柔女汉子，卖萌嘟嘴剪刀手，忧郁深沉无所谓，狂拽帅气吊炸天，冷艳
高贵接地气，时尚亮丽小清新，可爱乡村非主流，贵族王朝杀马特。',
                '再长的路，一步步也能走完；再短的路，不迈开双脚也无法到达。不要让太多的昨天占据你的今天。重复别人走过的路，是因为忽视了自己的双脚。贪婪是最真实的贫穷，满足是最真实的财富。无论失去什
么，都不要失去好心情。把握住自己的心，让心境清净，洁白，安静。',
                '世界那么大，爱上一个人那么容易，被爱也那么容易，但要互相相爱，竟这么难。',
                '别人永远对，我永远错，这样子比较没烦恼。',
        );
        // 从时间线抓取有用微博
        public function mining()
        {
                $uid;
                $uid = $this->myacct;
                // 从数据库查找 myacct 的 token
                // 获取 uid 的 accessToken
                $mysql = new SaeMysql();
                try {
                        $u = $mysql->getData('SELECT wid,token FROM sae_wid WHERE wid='.$uid);
                } catch (Exception $e) {
                        $this->error($e->getMessage());
                }
                if (empty($u))
                {
                        $this->error('cannot get data from Sae Mysql');
                }
                $token = $u[0]['token'];
                // 将时间线的有名微博抓出来
                $c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token);
                // 将时间线的有名微博抓出来
                $c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token);
                $ms = $c->home_timeline();
                if (isset($ms['error']))
                {
                        $this->error($ms['error']);
                }
                //dump(count($ms['statuses']));
                $line = $ms['statuses'];
                $houxuan = json_decode(F('weibohouxuan'), true);
                for ($i=0; $i<count($line); ++$i)
                {
                        // 条件不符合的去掉
                        if (isset($line[$i]['retweeted_status']))
                        {
                                //print_r('转播的微博:['.$line[$i]['text'].']自:['.$line[$i]['retweeted_status']['text'].']<br/>');
                                continue;
                        }
                        if (!in_array($line[$i]['user']['name'], $this->listen_list))
                        {
                                //print_r('非专业微博:['.$line[$i]['text'].']<br/>');
                                continue;
                        }
                        $hasIntent = false;
                        foreach ($this->unlike_word as $kw)
                        {
                                if (strpos($line[$i]['text'], $kw))
                                {
                                        //print_r('过滤的微博:['.$line[$i]['text'].']含有屏蔽词汇:['.$kw.']<br/>');
                                        $hasIntent = true;
                                        break;
                                }
                        }
                        if ($hasIntent)
                        {
                                continue;
                        }
                        // 条件符合，追加到缓存变量
                        echo('合适微博：【'.$line[$i]['text'].'】<br/>');
                        $houxuan[] = $line[$i];
                }
                // 存入缓存
                F('weibohouxuan', json_encode($houxuan));
//echo '<pre>F(weibohouxuan)='.time();print_r($houxuan);echo '</pre>';
        }
        // 将缓存的候选内容发表到微博，并删除
        public function put()
        {
                // 获取 weibo token
                $wid = $this->myacct;
                $mysql = new SaeMysql();
                try {
                        $u = $mysql->getData('SELECT wid,token FROM sae_wid WHERE wid='.$wid);
                } catch (Exception $e) {
                        $this->error($e->getMessage());
                }
                if (empty($u))
                {
                        $this->error('cannot get data from table: sae_wid');
                }
                $wtoken = $u[0]['token'];
                // 获取缓存内容列表
                $houxuan = json_decode(F('weibohouxuan'), true);
                // 取出一条 并从缓存列表删除
                $one = array_shift($houxuan);
                //dump($one);return false;
                if (empty($one))
                {
                        $this->error('No candicate weibo content!');
                }
                $text = $one['text'];
                //dump($text);return false;

                // 新浪微博发布
                $wc = new SaeTClientV2(WB_AKEY , WB_SKEY , $wtoken);
                // 如果有图片，使用图片发布接口
                if (isset($one['original_pic']) && !empty($one['original_pic']))
                {
                        //$ret = $c->upload_url_text($text, $one['original_pic']);
                        $ret = $wc->upload($text, $one['original_pic']);
                        print_r('weibo put pic :<br/>');
                        print_r($ret);
                }
                else // 无图片 使用无图片接口
                {
                        $ret = $wc->update($text);
                        print_r('weibo put :<br/>');
                        dump($ret);
                }

                // 获取腾讯 token
                $tid = $this->mytid;
                try {
                        $u = $mysql->getData("SELECT tid,token,openid FROM sae_tid WHERE tid='".$tid."'");
                } catch (Exception $e) {
                        $this->error($e->getMessage());
                }
                dump($u);
                if (empty($u))
                {
                        $this->error('cannot get data from table: sae_tid');
                }
                $ttoken = $u[0]['token'];
                $openid = $u[0]['openid'];

                // 腾讯微博发布
                $tc = new TxClient(TX_CLIENT_ID, $ttoken, $openid);
                // 格式 api($command, $params = array(), $method = 'GET', $multi = false)
                $tparams = array(
                        'format' => 'json',
                        'content' => $text,
                        'longitude' => '',
                        'latitude' => '',
                        'syncflag' => '1',
                        //'pic' => '',
                );
                // 如果有图片，使用图片发布接口
                if (isset($one['original_pic']) && !empty($one['original_pic']))
                {
                        if (count($one['pic_urls']) > 1)
                        {
                                $uploadedPics = array();
                                // 腾讯微博图片，先上传
                                foreach ($one['pic_urls'] as $pKey=>$pic)
                                {
                                        $upPicParams = array('format'=>'json','pic_type'=>1);
                                        $upPicParams['pic_url'] = str_replace('/thumbnail/', '/large/', $pic['thumbnail_pic']);
                                        $upRet = $tc->api('t/upload_pic', $upPicParams, 'POST');
                                        $upPicParams['pic_url'] = str_replace('/thumbnail/', '/large/', $pic['thumbnail_pic']);
                                        $upRet = $tc->api('t/upload_pic', $upPicParams, 'POST');
                                        $upRet = json_decode($upRet, true);
                                        if (isset($upRet['data']))
                                        {
                                                $uploadedPics[] = $upRet['data']['imgurl'];
                                        }
                                        else
                                        {
                                                // 上次失败
                                                echo '第'.$pKey.'张图片： '.$pic['thumbnail_pic'].' 上传失败！<br/>'.chr(10);
                                                sleep(3);
                                                $upRet = $tc->api('t/upload_pic', $upPicParams, 'POST');
                                                $upRet = json_decode($upRet, true);
                                                if (isset($upRet['data']))
                                                {
                                                        $uploadedPics[] = $upRet['data']['imgurl'];
                                                }
                                                else
                                                {
                                                        echo '第'.$pKey.'张图片： '.$pic['thumbnail_pic'].' 第二次上传失败！放弃上传。<br/>'.chr(10);
                                                }
                                                //dump($upRet);
                                        }
                                }
                                $pics = implode(',', $uploadedPics);
                        }
                        else
                        {
                                /*
                                $upPicParams = array('format'=>'json','pic_type'=>1);
                                $upPicParams['pic_url'] = str_replace('/thumbnail/', '/large/', $pic['thumbnail_pic']);
                                $upRet = $tc->api('t/upload_pic', $upPicParams, 'POST');
                                $upRet = json_decode($upRet, true);
                                if (isset($upRet['data']))
                                {
                                        $uploadedPics[] = $upRet['data']['imgurl'];
                                }
                                else
                                {
                                        // 上次失败
                                        echo '一张图片： '.$pic['thumbnail_pic'].' 也居然上传失败！<br/>';
                                        sleep(3);
                                        // 重新尝试
                                        sleep(3);
                                        // 重新尝试
                                }
                                */
                                $pics = $one['original_pic'];
                        }
                        $tparams['pic_url'] = $pics;
                        //dump($tparams);exit(0);
                        $ret = $tc->api('t/add_pic_url', $tparams, 'POST');
                        print_r("tx put pic :<br/>\n");
                        dump($ret);
                }
                else // 无图片 使用无图片接口
                {
                        $ret = $tc->api('t/add', $tparams, 'POST');
                        print_r("tx put :<br/>\n");
                        dump($ret);
                }
                F('weibohouxuan', json_encode($houxuan));
        }

        // 将缓存的候选内容显示
        public function show()
        {
                // 获取缓存内容列表
                $houxuan = json_decode(F('weibohouxuan'), true);
                dump($houxuan);
                //return true;
                //echo json_encode($houxuan);
        }

        // 删除缓存微博第一条
        public function shift()
        {
                // 获取缓存内容列表
                $houxuan = json_decode(F('weibohouxuan'), true);
                array_shift($houxuan);
                F('weibohouxuan', json_encode($houxuan));
                dump($houxuan);
                //return true;
                //echo json_encode($houxuan);
        }

        // 获取好友的随机微e
        public function cmtwb()
        {
                $uid = $this->myacct;
                // 从数据库查找 myacct 的 token
                // 获取 uid 的 accessToken
                $mysql = new SaeMysql();
                try {
                        $u = $mysql->getData('SELECT wid,token FROM sae_wid WHERE wid='.$uid);
                } catch (Exception $e) {
                        $this->error($e->getMessage());
                }
                if (empty($u))
                {
                        $this->error('cannot get data from Sae Mysql');
                }
                $token = $u[0]['token'];
                // 将时间线的有名微博抓出来
                $c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token);
                $ms = $c->home_timeline();
                if (isset($ms['error']))
                {
                        $this->error($ms['error']);
                }
                //dump(count($ms['statuses']));
                $line = $ms['statuses'];
                //dump($line);
                if (0 == count($line))
                {
                        echo '你的好友居然没有说话的！';
                        return false;
                }
                $mid = $line[(rand()*1999)%count($line)];
                if ($mid['user']['idstr'] == $uid)
                {
                        echo 'I do not want to comment myself!<br/>';
                        return $this->cmtwb();
                }
                $text = $this->cmt_list[(rand()*499)%count($this->cmt_list)];
                //$text .= ' @搞笑热资讯 ';
                dump($text);
                $ret = $c->send_comment($mid['mid'], $text);
                print_r('cmt: <br/>');
                dump($ret);
        }
        // 获取好友的随机微博
        public function cmttx()
        {
                $uid = $this->mytid;
                // 从数据库查找 myacct 的 token
                // 获取 uid 的 accessToken
                $mysql = new SaeMysql();
                try {
                        $u = $mysql->getData("SELECT tid,token,openid FROM sae_tid WHERE tid='{$uid}';");
                } catch (Exception $e) {
                        $this->error($e->getMessage());
                }
                if (empty($u))
                {
                        $this->error('cannot get data from Sae Mysql');
                }
                $ttoken = $u[0]['token'];
                $openid = $u[0]['openid'];

                // 腾讯微博发布
                $tc = new TxClient(TX_CLIENT_ID, $ttoken, $openid);
                // 将时间线的有名微博抓出来
                //$c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token);
                // 格式 api($command, $params = array(), $method = 'GET', $multi = false)
                $tparams = array(
                        'format' => 'json',
                        'pageflage' => 0,
                        'pagetime' => 0,
                        'reqnum' => 50,
                        'type' => 3,
                        'contenttype' => 0,
                );
                $ret = $tc->api('statuses/home_timeline', $tparams, 'GET');
                $ret = json_decode($ret, true);
                //dump($ret);
                if (0 != $ret['ret'])
                {
                        $this->error($ret['msg']);
                }
                $line = $ret['data']['info'];
                //dump($line);
                if (0 == count($line))
                {
                        echo '你的好友居然没有说话的！';
                        return false;
                }
                // 抽取一条，对其评论
                $mid = $line[(rand()*1999)%count($line)];
                if ($mid['name'] == $uid)
                {
                        echo 'I do not want to comment myself!<br/>';
                        return $this->cmttx();
                }
                $text = $this->cmt_list[(rand()*499)%count($this->cmt_list)];
                //$text .= ' @搞笑热资讯 ';
                dump($text);
                $tparams = array(
                        'format' => 'json',
                        'content' => $text,
                        'clientip' => $_SERVER['REMOTE_ADDR'],
                        'reid' => $mid['id'],
                        'type' => 3,
                );
                $ret = $tc->api('t/comment', $tparams, 'POST');
                print_r("cmt: <br/>\n");
                dump($ret);
        }
        // 获取微博的评论列表
        public function cmtshow()
        {
                if (empty($_REQUEST['mid']))
                {
                        echo 'empty mid!';
                        return false;
                }
                dump($_REQUEST['mid']);
                $uid = $this->myacct;
                // 从数据库查找 myacct 的 token
                // 获取 uid 的 accessToken
                $mysql = new SaeMysql();
                try {
                        $u = $mysql->getData('SELECT wid,token FROM sae_wid WHERE wid='.$uid);
                } catch (Exception $e) {
                        $this->error($e->getMessage());
                }
                if (empty($u))
                {
                        $this->error('cannot get data from Sae Mysql');
                }
                $token = $u[0]['token'];
                // 将时间线的有名微博抓出来
                $c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token);
                $ret = $c->get_comments_by_sid($_REQUEST['mid']);
                //print_r('cmt show:<br/>');
                //dump($ret);
                //echo json_encode($ret);
        }
















	public function actionHello() {
		echo 'hello :'.__METHOD__;
		$ret = Yii::$app->djdb->createCommand('show tables;')->queryAll();
		print_r($ret);
	}
}
