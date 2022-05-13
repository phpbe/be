<?php

namespace Be\Util\Time;

class Timezone
{

    /**
     * 获取时区键值对列表
     * @return string[]
     */
    public static function getKeyValues(): array
    {
        return [
            'Etc/GMT+12' => '(GMT-12:00) 国际日期变更线西边',
            'Pacific/Pago_Pago' => '(GMT-11:00) 美属萨摩亚',
            'Pacific/Midway' => '(GMT-11:00) 中途岛',
            'Pacific/Honolulu' => '(GMT-10:00) 夏威夷',
            'America/Juneau' => '(GMT-09:00) 阿拉斯加',
            'America/Los_Angeles' => '(GMT-08:00) 太平洋时间（美国和加拿大）',
            'America/Tijuana' => '(GMT-08:00) 蒂华纳',
            'America/Phoenix' => '(GMT-07:00) 亚利桑那',
            'America/Chihuahua' => '(GMT-07:00) 奇瓦瓦',
            'America/Mazatlan' => '(GMT-07:00) 马萨特兰',
            'America/Denver' => '(GMT-07:00) 山区时间（美国和加拿大）',
            'America/Guatemala' => '(GMT-06:00) 中美洲',
            'America/Chicago' => '(GMT-06:00) 中部时间（美国和加拿大）',
            'America/Mexico_City' => '(GMT-06:00) 瓜达拉哈拉、墨西哥城',
            'America/Monterrey' => '(GMT-06:00) 蒙特雷',
            'America/Regina' => '(GMT-06:00) 萨斯喀彻温',
            'America/Bogota' => '(GMT-05:00) 波哥大',
            'America/New_York' => '(GMT-05:00) 东部时间（美国和加拿大）',
            'America/Indiana/Indianapolis' => '(GMT-05:00) 印第安纳（东部）',
            'America/Lima' => '(GMT-05:00) 利马、基多',
            'America/Halifax' => '(GMT-04:00) 大西洋时间（加拿大）',
            'America/Caracas' => '(GMT-04:00) 加拉加斯',
            'America/Guyana' => '(GMT-04:00) 乔治敦',
            'America/La_Paz' => '(GMT-04:00) 拉巴斯',
            'America/Puerto_Rico' => '(GMT-04:00) 波多黎各',
            'America/Santiago' => '(GMT-04:00) 圣地亚哥',
            'America/St_Johns' => '(GMT-03:30) 纽芬兰',
            'America/Sao_Paulo' => '(GMT-03:00) 巴西利亚',
            'America/Argentina/Buenos_Aires' => '(GMT-03:00) 布宜诺斯艾利斯',
            'America/Godthab' => '(GMT-03:00) 格陵兰岛',
            'America/Montevideo' => '(GMT-03:00) 蒙得维的亚',
            'Atlantic/South_Georgia' => '(GMT-02:00) 大西洋中部',
            'Atlantic/Azores' => '(GMT-01:00) 亚速尔群岛',
            'Atlantic/Cape_Verde' => '(GMT-01:00) 佛得角群岛',
            'Europe/London' => '(GMT+00:00) 爱丁堡、伦敦',
            'Europe/Lisbon' => '(GMT+00:00) 里斯本',
            'Africa/Monrovia' => '(GMT+00:00) 蒙罗维亚',
            'Etc/UTC' => '(GMT+00:00) UTC',
            'Europe/Amsterdam' => '(GMT+01:00) 阿姆斯特丹',
            'Europe/Belgrade' => '(GMT+01:00) 贝尔格莱德',
            'Europe/Berlin' => '(GMT+01:00) 柏林',
            'Europe/Zurich' => '(GMT+01:00) 伯尔尼、苏黎世',
            'Europe/Bratislava' => '(GMT+01:00) 布拉迪斯拉发',
            'Europe/Brussels' => '(GMT+01:00) 布鲁塞尔',
            'Europe/Budapest' => '(GMT+01:00) 布达佩斯',
            'Africa/Casablanca' => '(GMT+01:00) 卡萨布兰卡',
            'Europe/Copenhagen' => '(GMT+01:00) 哥本哈根',
            'Europe/Dublin' => '(GMT+00:00) 都柏林',
            'Europe/Ljubljana' => '(GMT+01:00) 卢布尔雅那',
            'Europe/Madrid' => '(GMT+01:00) 马德里',
            'Europe/Paris' => '(GMT+01:00) 巴黎',
            'Europe/Prague' => '(GMT+01:00) 布拉格',
            'Europe/Rome' => '(GMT+01:00) 罗马',
            'Europe/Sarajevo' => '(GMT+01:00) 萨拉热窝',
            'Europe/Skopje' => '(GMT+01:00) 斯科普里',
            'Europe/Stockholm' => '(GMT+01:00) 斯德哥尔摩',
            'Europe/Vienna' => '(GMT+01:00) 维也纳',
            'Europe/Warsaw' => '(GMT+01:00) 华沙',
            'Africa/Algiers' => '(GMT+01:00) 中非西部',
            'Europe/Zagreb' => '(GMT+01:00) 萨格勒布',
            'Europe/Athens' => '(GMT+02:00) 雅典',
            'Europe/Bucharest' => '(GMT+02:00) 布加勒斯特',
            'Africa/Cairo' => '(GMT+02:00) 开罗',
            'Africa/Harare' => '(GMT+02:00) 哈拉雷',
            'Europe/Helsinki' => '(GMT+02:00) 赫尔辛基',
            'Asia/Jerusalem' => '(GMT+02:00) 耶路撒冷',
            'Europe/Kaliningrad' => '(GMT+02:00) 加里宁格勒',
            'Europe/Kiev' => '(GMT+02:00) 基辅',
            'Africa/Johannesburg' => '(GMT+02:00) 比勒陀利亚',
            'Europe/Riga' => '(GMT+02:00) 里加',
            'Europe/Sofia' => '(GMT+02:00) 索菲亚',
            'Europe/Tallinn' => '(GMT+02:00) 塔林',
            'Europe/Vilnius' => '(GMT+02:00) 维尔纽斯',
            'Asia/Baghdad' => '(GMT+03:00) 巴格达',
            'Europe/Istanbul' => '(GMT+03:00) 伊斯坦布尔',
            'Asia/Kuwait' => '(GMT+03:00) 科威特',
            'Europe/Minsk' => '(GMT+03:00) 明斯克',
            'Europe/Moscow' => '(GMT+03:00) 莫斯科、圣彼得堡',
            'Africa/Nairobi' => '(GMT+03:00) 内罗毕',
            'Asia/Riyadh' => '(GMT+03:00) 利雅得',
            'Europe/Volgograd' => '(GMT+03:00) 伏尔加格勒',
            'Asia/Tehran' => '(GMT+03:30) 德黑兰',
            'Asia/Muscat' => '(GMT+04:00) 阿布扎比、马斯喀特',
            'Asia/Baku' => '(GMT+04:00) 巴库',
            'Europe/Samara' => '(GMT+04:00) 萨马拉',
            'Asia/Tbilisi' => '(GMT+04:00) 第比利斯',
            'Asia/Yerevan' => '(GMT+04:00) 埃里温',
            'Asia/Kabul' => '(GMT+04:30) 喀布尔',
            'Asia/Yekaterinburg' => '(GMT+05:00) 叶卡捷林堡',
            'Asia/Karachi' => '(GMT+05:00) 伊斯兰堡、卡拉奇',
            'Asia/Tashkent' => '(GMT+05:00) 塔什干',
            'Asia/Kolkata' => '(GMT+05:30) 金奈、加尔各答、孟买、新德里',
            'Asia/Colombo' => '(GMT+05:30) 斯里哈亚华登尼普拉',
            'Asia/Kathmandu' => '(GMT+05:45) 加德满都',
            'Asia/Almaty' => '(GMT+06:00) 阿拉木图',
            'Asia/Dhaka' => '(GMT+06:00) 阿斯塔纳、达卡',
            'Asia/Urumqi' => '(GMT+06:00) 乌鲁木齐',
            'Asia/Rangoon' => '(GMT+06:30) 仰光',
            'Asia/Bangkok' => '(GMT+07:00) 曼谷、河内',
            'Asia/Jakarta' => '(GMT+07:00) 雅加达',
            'Asia/Krasnoyarsk' => '(GMT+07:00) 克拉斯诺亚尔斯克',
            'Asia/Novosibirsk' => '(GMT+07:00) 新西伯利亚',
            'Asia/Shanghai' => '(GMT+08:00) 北京',
            'Asia/Chongqing' => '(GMT+08:00) 重庆',
            'Asia/Hong_Kong' => '(GMT+08:00) 香港',
            'Asia/Irkutsk' => '(GMT+08:00) 伊尔库次克',
            'Asia/Kuala_Lumpur' => '(GMT+08:00) 吉隆坡',
            'Australia/Perth' => '(GMT+08:00) 珀斯',
            'Asia/Singapore' => '(GMT+08:00) 新加坡',
            'Asia/Taipei' => '(GMT+08:00) 台北',
            'Asia/Ulaanbaatar' => '(GMT+08:00) 乌兰巴托',
            'Asia/Tokyo' => '(GMT+09:00) 大阪、札幌、东京',
            'Asia/Seoul' => '(GMT+09:00) 首尔',
            'Asia/Yakutsk' => '(GMT+09:00) 雅库茨克',
            'Australia/Adelaide' => '(GMT+09:30) 阿德莱德',
            'Australia/Darwin' => '(GMT+09:30) 达尔文',
            'Australia/Brisbane' => '(GMT+10:00) 布里斯班',
            'Australia/Melbourne' => '(GMT+10:00) 堪培拉、墨尔本',
            'Pacific/Guam' => '(GMT+10:00) 关岛',
            'Australia/Hobart' => '(GMT+10:00) 霍巴特',
            'Pacific/Port_Moresby' => '(GMT+10:00) 莫尔兹比港',
            'Australia/Sydney' => '(GMT+10:00) 悉尼',
            'Asia/Vladivostok' => '(GMT+10:00) 符拉迪沃斯托克',
            'Asia/Magadan' => '(GMT+11:00) 马加丹',
            'Pacific/Noumea' => '(GMT+11:00) 新喀里多尼亚',
            'Pacific/Guadalcanal' => '(GMT+11:00) 所罗门群岛',
            'Asia/Srednekolymsk' => '(GMT+11:00) 中科雷姆斯克',
            'Pacific/Auckland' => '(GMT+12:00) 奥克兰、惠灵顿',
            'Pacific/Fiji' => '(GMT+12:00) 斐济',
            'Asia/Kamchatka' => '(GMT+12:00) 堪察加',
            'Pacific/Majuro' => '(GMT+12:00) 马绍尔群岛',
            'Pacific/Chatham' => '(GMT+12:45) 查塔姆群岛',
            'Pacific/Tongatapu' => '(GMT+13:00) 努库阿洛法',
            'Pacific/Apia' => '(GMT+13:00) 萨摩亚',
            'Pacific/Fakaofo' => '(GMT+13:00) 托克劳群岛',
        ];
    }

    /**
     * 将指定的时间，转换时区
     *
     * @param string $toFormat
     * @param string $toTimezone
     * @param string $fromDatetime
     * @param string $fromTimezone
     * @return string
     */
    public static function convert(string $toFormat, string $toTimezone, string $fromDatetime = 'now', string $fromTimezone = null): string
    {
        $toDatetime = new \DateTime($fromDatetime, $fromTimezone);
        $toDatetime->setTimezone(new \DateTimeZone($toTimezone));
        return $toDatetime->format($toFormat);
    }


}
