<?php
namespace Yangyao\Mongo\Exception;
class System extends Abs {

    const E_DEFAULT = 100000;

    const E_MAINTANCE = 100001; //系统维护

    //const E_BUSY = 100002;  //系统繁忙（上锁失败）

    const E_NOTHING = 100003;   //出错但没有任何处理(往往伴随cmd)

    const E_CLIENT_USER_WRONG = 100008; 

    const E_VERSION_LOW = 100010;   //客户端版本过低

    //const E_INPUT = 100100;

    const E_VALIDATION = 100105;

    //{{{ auth
    const E_AUTH_EMAIL_FAIL = 100201; //该邮箱未注册
    const E_AUTH_CRED_WRONG = 100202;   //用户登录验证错误（一般情况下即密码错误）

    const E_AUTH_PWD_TOKEN_FAIL = 100205;   //修改密码的token验证错误
    const E_AUTH_PWD_TOKEN_EXPIRED = 100206;   //修改密码的token过期

    const E_AUTH_IS_ANON = 100210;  //这是一个匿名账号
    const E_AUTH_ANONID_FAIL = 100211;  //匿名账号ID未注册

    const E_AUTH_TOKEN_EXPIRED = 100220;  //authToken过时
    const E_AUTH_TOKEN_EMAIL_WRONG = 100221;  //authToken的email不匹配
    const E_AUTH_TOKEN_PWD_EMPTY = 100222;  //authToken的pwd为空

    const E_AUTH_MTK_EXPIRED = 100240;  //mobileToken过时
    const E_AUTH_MTK_SEED_FAIL = 100241;  //mobileToken的seed验证错误
    //}}}

    //{{{ phone
    const E_PHONE_UNIQUE = 100271; //该手机号码不是唯一，已经被人使用
    const E_PHONE_FORMAT = 100272; //该手机号码格式错误
    const E_PHONE_CODE_WRONG = 100273; //该手机号码校验码错误 
    //}}}

    //{{{ comment
    const E_COMMENT_FREQUENCY = 100300;  //评论太频繁
    //}}}

    //{{{ gym
    const E_GYM_NO_ORDER_RIGHT = 100400;  //没有下订单的权限
    const E_GYM_ORDER_RIGHT_EXPIRED = 100401;  //下订单的权限已过期
    //const E_GYM_ORDER_TODAY_MAKED = 100402;  //今天已经下过订单
    const E_GYM_ORDER_RIGHT_FROZEN = 100403;  //下订单的权限已冻结
    const E_GYM_ORDER_MAKING_EXHAUSTED_DAILY = 100404;  //每日（下、修改）订单的次数已用完


    const E_GYM_USER_NOT_GYM = 100421;  //这个用户不是健身房
    //const E_GYM_CODE_NOT_MATCH = 100422;  //gym code不匹配
    const E_GYM_ORDER_TOKEN_FAILED = 100423;  //无法根据该order token找到订单
    const E_GYM_ORDER_EXPIRED = 100424;  //该gym order已经过期
    const E_GYM_ORDER_CONSUME_STATUS_FAILED = 100425;  //该gym order不能消费
    const E_GYM_ORDER_CONSUME_GYM_MONTH_LIMIT = 100426;  //该gym order本月在这个gym的消费次数受到限制
    const E_GYM_ORDER_CONSUME_GYM_WRONG = 100427;  //该gym order不能在这个gym消费（gym id不匹配）

    const E_GYM_FROZEN = 100450; //该健身房被冻结
    const E_GYM_FAILED_LIMIT = 100451; //该健身房当天验证消费码失败次数太多（防刷）
    //}}}

    //{{{ PAYMENT
    const E_PAYMENT_ORDER_CODE_FORMAT = 100500;  //该orderCode格式错误
    const E_PAYMENT_ORDER_CODE_UNIQUE = 100501;  //orderCode必须是全表唯一
    const E_PAYMENT_ORDER_EXISTED = 100502;  //order已经存在
    const E_PAYMENT_ORDER_TIMES_LIMIT = 100503;  //用户不能再下订单啦
    const E_PAYMENT_ORDER_CONSUMED = 100504;  //order已经激活
    //const E_PAYMENT_CARD_POOL_CONSUMED = 100505;  //激活码已经输入
    const E_PAYMENT_ORDER_GYM_LIMIT = 100506;  //健身房管理员不能下订单
    const E_PAYMENT_ORDER_CODE_EXPIRED = 100507;  //orderCode过期
    const E_PAYMENT_ORDER_ONE_YEAR_CARD_ONLY = 100508;  //只能买一张年卡
    const E_PAYMENT_ORDER_MUTEX_CARD = 100509;  //互斥的卡
    //}}}

    //{{{ FEED
    const E_FEED_PERMISSION_DENIED = 100600;  //私信权限不足
    const E_FEED_REPLY_DENIED = 100601;  //私信不存在，不能回复。
    //}}}

    //{{{
    const E_ACTI_ORDER_CONSUME_STATUS_FAILED = 100650;  //该activity order不能消费
    //}}}

    //{{{ frequency
    const E_FREQUENCY = 100700;  //评论太频繁
    //}}}

    //{{{ COUPON
    const E_COUPON_CONSUMED= 100800;  //优惠码已使用
    const E_COUPON_WRONG= 100801;  //错误的优惠码
    const E_COUPON_EXPIRED= 100802;  //优惠码已过期
    const E_COUPON_CARD_WRONG= 100803;  //优惠码不适用该卡
    const E_COUPON_PENDING= 100804;  //优惠码已经被使用。
    //}}}

    //{{{ WALLET
    const E_WALLET_COIN_LACK = 100900;  //钱包的钱不够
    //}}}

    //{{{ FILM
    const E_FILM_NUM_MAX = 101000;  //上传自拍视频满了
    //}}}

    //{{{ EVENT '注意!!!!!!!!！这种类型的exception要从120000开始，其它类型不要插入进来
    const E_EVENT_ONCE_LIMIT = 120000;  //该活动只能参加一次
    const E_EVENT_1YUANTIYAN_QUAL = 120001;  //用户没有参与1元体验的资格
    //}}}

    //{{{ LIVE
    const E_LIVE_PLAYING_OR_END = 121000;  //直播正在进行中或者已经结束
    const E_LIVE_TIME_CONFLICT = 121001; //直播时间冲突
    //}}}

    //{{{ COMMUNITY
    const E_COMMUNITY_USER_NOT_GYM = 121100;  // 这个用户不是健身房
    const E_COMMUNITY_GYM_FROZEN = 121101;  // 这个用户对应的健身房被冻结
    const E_COMMUNITY_STAMP_FAILED = 121102;  // 团购券错误
    const E_COMMUNITY_STAMP_EXPIRED = 121103;  // 团购券过期
    const E_COMMUNITY_STAMP_CONSUME_STATUS_FAILED = 121104;  // 已经用过或着过期了，无法再使用了。
    const E_COMMUNITY_STAMP_CONSUME_GYM_WRONG = 121105;  // 不能再别的健身房消费

    //}}}

    //{{{ PLATFORM
    const E_PLATFORM = 150000;  //第三方通讯通用错误
    //}}}

    public function __construct($method, $message, $code=self::E_DEFAULT, $data = null){
        !empty($method) && $message = '['.$method . ']' . $message;
        parent::__construct($message, $code, $data);
    }
}