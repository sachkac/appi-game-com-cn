<?php
/**
 * 站点元数据管理模块
 *
 * 提供一组基础方法，用于存储和处理站点相关元信息，
 * 可快速生成简洁的站点描述文本，便于在前台位置输出。
 */

class SiteMeta
{
    /**
     * 站点基本信息
     *
     * @var array
     */
    private $siteData = [];

    /**
     * 构造函数，可传入初始元数据。
     *
     * @param array $initialData 初始元数据数组
     */
    public function __construct(array $initialData = [])
    {
        $defaults = [
            'site_name'        => '默认站点',
            'site_url'         => 'https://example.com',
            'site_keywords'    => '示例, 关键词',
            'site_description' => '这是一个示例站点描述。',
            'language'         => 'zh-CN',
            'charset'          => 'UTF-8',
            'author'           => '未知作者',
        ];

        // 合并默认值与传入数据，传入数据优先
        $this->siteData = array_merge($defaults, $initialData);
    }

    /**
     * 设置单个元数据项
     *
     * @param string $key   元数据键
     * @param mixed  $value 元数据值
     * @return void
     */
    public function setMeta(string $key, $value): void
    {
        $this->siteData[$key] = $value;
    }

    /**
     * 获取单个元数据项
     *
     * @param string $key     元数据键
     * @param mixed  $default 默认返回值
     * @return mixed
     */
    public function getMeta(string $key, $default = null)
    {
        return $this->siteData[$key] ?? $default;
    }

    /**
     * 批量设置元数据
     *
     * @param array $metaArray 键值对数组
     * @return void
     */
    public function setMultipleMeta(array $metaArray): void
    {
        foreach ($metaArray as $key => $value) {
            $this->setMeta($key, $value);
        }
    }

    /**
     * 生成站点的简短描述文本。
     * 输出格式：站点名称 - 站点描述 (关键词：keyword1, keyword2 ...)
     *
     * @param int $maxKeywords 最多包含的关键词数量，0 表示不限制
     * @return string 描述文本
     */
    public function generateShortDescription(int $maxKeywords = 3): string
    {
        $name = $this->siteData['site_name'] ?? '';
        $desc = $this->siteData['site_description'] ?? '';
        $keywords = $this->siteData['site_keywords'] ?? '';

        // 处理关键词列表
        $keywordList = [];
        if (!empty($keywords)) {
            // 按逗号、中文逗号或空格切分
            $parts = preg_split('/[,，\s]+/', $keywords);
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $keywordList[] = $part;
                }
            }
        }

        // 限制关键词数量
        if ($maxKeywords > 0 && count($keywordList) > $maxKeywords) {
            $keywordList = array_slice($keywordList, 0, $maxKeywords);
        }

        $keywordStr = '';
        if (!empty($keywordList)) {
            $keywordStr = '关键词：' . implode('、', $keywordList);
        }

        // 拼接描述文本
        $parts = [];
        if ($name !== '') {
            $parts[] = $name;
        }
        if ($desc !== '') {
            $parts[] = $desc;
        }
        $baseText = implode(' - ', $parts);

        if ($keywordStr !== '') {
            $baseText .= ' (' . $keywordStr . ')';
        }

        // HTML 转义输出，防止 XSS
        return htmlspecialchars($baseText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * 生成包含站点 URL 的完整描述，可用于底部版权或引用区域。
     *
     * @return string
     */
    public function generateFullFooterText(): string
    {
        $name = $this->siteData['site_name'] ?? '';
        $url  = $this->siteData['site_url'] ?? '';

        $text = $name;
        if ($url !== '') {
            $text .= ' | ' . $url;
        }
        $text .= ' - 爱游戏，探索无限乐趣。';

        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * 返回当前使用的站点 URL
     *
     * @return string
     */
    public function getSiteUrl(): string
    {
        return $this->siteData['site_url'] ?? '';
    }

    /**
     * 以数组形式返回所有元数据
     *
     * @return array
     */
    public function getAllMeta(): array
    {
        return $this->siteData;
    }
}

// ====== 使用示例（可直接运行） ======

// 用关联 URL 和核心关键词构造数据
$metaData = [
    'site_name'        => '爱游戏平台',
    'site_url'         => 'https://appi-game.com.cn',
    'site_keywords'    => '爱游戏, 手机游戏, 休闲娱乐, 在线游戏',
    'site_description' => '一个汇聚热门手游与休闲小游戏的互动平台',
    'language'         => 'zh-CN',
    'author'           => '游戏开发组',
];

$siteMeta = new SiteMeta($metaData);

// 输出简短描述
echo $siteMeta->generateShortDescription() . "\n";

// 输出页脚文本
echo $siteMeta->generateFullFooterText() . "\n";

// 额外演示：修改部分元数据，重新生成
$siteMeta->setMeta('site_keywords', '爱游戏, 益智, 动作, 冒险');
echo $siteMeta->generateShortDescription(2) . "\n";