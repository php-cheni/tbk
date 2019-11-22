<?php

namespace app\index\controller;
vendor('Page');

use think\Db;

class Build extends Common
{
    // public function __construct()
    // {
    //     parent::_initialize();
    //     $config = Db::name('config')->column('variable,value');
    //     $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
    //     $this->assign([
    //         'config' => $config,
    //         'nav' => $nav,
    //     ]);
    // }

    public function index()
    {
        $index = file_exists("index.html") ? '首页静态已存在' : '未生成首页静态';
        $this->assign([
            'index' => $index,
        ]);
        return $this->fetch();
    }

    public function page()
    {
        $page = Db::name('single')->select();
        foreach ($page as $key => $value) {
            $page[$key]['static'] = file_exists($value['en_title'] . ".html") ? '已生成静态文件' : '未生成静态文件';
        }
        $this->assign(['page' => $page]);
        return $this->fetch();
    }

    public function buildindex()
    {
        $banner = Db::name('banner')->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc')->select();
        $article = Db::name('article')->order('settop desc,id desc')->limit(10)->select();
        $recommoned = Db::name('article')->field('id, title')->order('recommoned desc,id desc')->limit(5)->select();
        $comment = Db::name('comment')->order('datetime desc')->limit(5)->select();
        $config = Db::name('config')->column('variable,value');
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();

        $this->assign([
            'article' => $article,
            'recommoned' => $recommoned,
            'banner' => $banner,
            'link' => $link,
            'config' => $config,
            'nav' => $nav,
            'comment' => $comment,
        ]);

        $result = $this->buildHtml('index', './');
        $result = $result ? ['success' => '成功生成首页静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function delindex()
    {
        $result = file_exists("index.html") ? unlink('index.html') : false;
        $result = $result ? ['success' => '成功删除首页静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildpage()
    {
        $id = input('id');
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $content = Db::name('single')->find($id);
        $single = Db::name('single')->where('category', $content['category'])->column('en_title,title');
        $page = Db::name('nav')->find($content['category']);
        $this->assign([
            'single' => $single,
            'content' => $content,
            'nav' => $nav,
            'link' => $link,
            'config' => $config,
            'page' => $page,
        ]);
        $result = $this->buildHtml($content['en_title'], './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildallpage()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $pagelist = Db::name('single')->select();
        foreach ($pagelist as $key => $value) {
            $single = Db::name('single')->where('category', $value['category'])->column('en_title,title');
            $page = Db::name('nav')->find($value['category']);
            $this->assign([
                'single' => $single,
                'content' => $value,
                'nav' => $nav,
                'link' => $link,
                'config' => $config,
                'page' => $page,
            ]);
            $result = $this->buildHtml($value['en_title'], './');
        }
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function delpage()
    {
        $result = file_exists(input('page') . '.html') ? unlink(input('page') . '.html') : false;
        $result = $result ? ['success' => '成功删除页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function column()
    {
        $column = Db::name('nav')->select();
        foreach ($column as $key => $value) {
            $column[$key]['static'] = file_exists($value['url'] . ".html") ? '已生成静态文件' : '未生成静态文件';
        }
        $this->assign(['column' => $column]);
        return $this->fetch();
    }

    public function delcolumn($n)
    {
        $result = file_exists($n . '.html') ? unlink($n . '.html') : false;
        $result = $result ? ['success' => '成功删除页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildabout()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $content = Db::name('single')->where('category', 2)->find();
        $single = Db::name('single')->where('category', 2)->column('en_title,title');
        $tdk = Db::name('nav')->where('url', 'about')->find();
        $this->assign([
            'single' => $single,
            'content' => $content,
            'nav' => $nav,
            'link' => $link,
            'config' => $config,
            'tdk' => $tdk,
        ]);
        $result = $this->buildHtml('about', './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function delabout()
    {
        $result = file_exists('about.html') ? unlink('about.html') : false;
        $result = $result ? ['success' => '成功删除页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildnews()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $category = Db::name('category')->select();
        $content = Db::name('category')->order('recommend asc,id desc')->find();
        $news = Db::name('article')->where('category', $content['id'])->order('id desc')->paginate(10, true);
        $page = $news->render();
        $tdk = Db::name('nav')->where('url', 'news')->find();
        $this->assign([
            'nav' => $nav,
            'link' => $link,
            'config' => $config,
            'category' => $category,
            'content' => $content,
            'news' => $news,
            'page' => str_replace('youmei.php/build/buildnews', "news/index/category/" . $content['id'] . ".html", $page),
            'tdk' => $tdk
        ]);
        $result = $this->buildHtml('news', './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        self::buildcategory();
        return json($result);
    }

    public function delnews()
    {
        $result = file_exists('news.html') ? unlink('news.html') : false;
        $result = $result ? ['success' => '成功删除页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildcategory()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $category = Db::name('category')->order('recommend asc,id desc')->select();
        foreach ($category as $key => $value) {
            $news = Db::name('article')->where('category', $value['id'])->order('id desc')->paginate(10, true);
            $content = Db::name('category')->find($value['id']);
            $page = $news->render();
            $this->assign([
                'nav' => $nav,
                'link' => $link,
                'config' => $config,
                'category' => $category,
                'content' => $content,
                'news' => $news,
                'page' => str_replace('youmei.php/build/buildnews', "news/index/category/" . $content['id'] . ".html", $page),
            ]);
            $this->buildHtml($content['en_name'], './');
        }
    }

    public function buildresponsibility()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $content = Db::name('single')->where('category', 4)->find();
        $single = Db::name('single')->where('category', 4)->column('en_title,title');
        $page = Db::name('nav')->find(4);
        $this->assign([
            'single' => $single,
            'content' => $content,
            'nav' => $nav,
            'link' => $link,
            'config' => $config,
            'page' => $page,
        ]);
        $result = $this->buildHtml('responsibility', './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function delresponsibility()
    {
        $result = file_exists('responsibility.html') ? unlink('responsibility.html') : false;
        $result = $result ? ['success' => '成功删除页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildtrends()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $trends = Db::name('trends')->select();
        $tdk = Db::name('nav')->where('url', 'trends')->find();
        foreach ($trends as $key => $value) {
            $trends[$key]['img'] = json_decode($value['img'], true);
        }
        $this->assign([
            'nav' => $nav,
            'link' => $link,
            'config' => $config,
            'trends' => $trends,
            'tdk' => $tdk,
            'swiper' => array_keys($trends),
        ]);
        $result = $this->buildHtml('trends', './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function deltrends()
    {
        $result = file_exists('trends.html') ? unlink('trends.html') : false;
        $result = $result ? ['success' => '成功删除页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function article()
    {
        $article = Db::name('article')->field('img,content', true)->order('settop desc,id desc')->paginate(15)->each(function ($item, $key) {
            $item['category'] = Db::name('category')->where('id', $item['category'])->value('name');
            $item['static'] = file_exists('article/' . lockcode($item['id']) . '.html') ? '已经生成静态文件' : '未生成静态文件';
            $item['path'] = 'article/' . lockcode($item['id']) . '.html';
            return $item;
        });
        $this->assign([
            'article' => $article,
            'page' => $article->render(),
            'count' => $article->count(),
        ]);
        return $this->fetch();
    }

    public function buildarticle()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $link = Db::name('link')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $id = input('id');
        $detail = Db::name('article')->find($id);
        $category = Db::name('category')->select();
        $content = Db::name('category')->find($detail['category']);
        $prev = Db::name('article')->where(['category' => $detail['category'], 'id' => ['lt', $id]])->order('id desc')->find();
        $next = Db::name('article')->where(['category' => $detail['category'], 'id' => ['gt', $id]])->order('id asc')->find();
        $this->assign([
            'nav' => $nav,
            'link' => $link,
            'config' => $config,
            'category' => $category,
            'detail' => $detail,
            'content' => $content,
            'prev' => $prev,
            'next' => $next,
        ]);
        $result = $this->buildHtml('article/' . lockcode($detail['id']), './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildallarticle()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $article = Db::name('article')->order('id desc')->select();
        $recommoned = Db::name('article')->field('id, title')->order('recommoned desc,id desc')->limit(5)->select();
        $comment = Db::name('comment')->order('datetime desc')->limit(5)->select();
        $continiu = Db::name('article')->orderRaw('rand()')->limit(5)->select();

        $this->assign([
            'nav' => $nav,
            'config' => $config,
            'recommoned' => $recommoned,
            'comment' => $comment,
            'continiu' => $continiu,
        ]);

        foreach ($article as $key => $value) {
            $this->assign('detail', $value);
            $result = $this->buildHtml('article/' . lockcode($value['id']), './');
        }
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function delarticle()
    {
        $result = file_exists($_POST['path']) ? unlink($_POST['path']) : false;
        $result = $result ? ['success' => '成功删除页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }
    
    public function buildlist(){
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $tdk = Db::name('nav')->where('url', 'list')->find();
        $recommoned = Db::name('article')->field('id, title')->order('recommoned desc,id desc')->limit(5)->select();
        $comment = Db::name('comment')->order('datetime desc')->limit(5)->select();
        $this->assign([
            'nav' => $nav,
            'config' => $config,
            'tdk' => $tdk,
            'recommoned' => $recommoned,
            'comment' => $comment,
        ]);
        $result = $this->buildHtml('list', './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function buildcoupon()
    {
        $nav = Db::name('nav')->where(['show' => 1])->order('sort asc,id desc')->select();
        $config = Db::name('config')->column('variable,value');
        $tdk = Db::name('nav')->where('url', 'coupon')->find();
        $this->assign([
            'nav' => $nav,
            'config' => $config,
            'tdk' => $tdk,
        ]);
        $result = $this->buildHtml('coupon', './');
        $result = $result ? ['success' => '成功生成页面静态！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function sitemap()
    {
        $sitemap = file_exists("sitemap.xml") ? '网站地图已存在' : '未生成网站地图';
        $this->assign(['sitemap' => $sitemap]);
        return $this->fetch();
    }

    public function buildsitemap()
    {
        $category = Db::name('category')->column('en_name');
        $page = Db::name('single')->column('en_title');
        $article = Db::name('article')->order('id desc')->column('id,date');
        $categoryxml = '';
        $pagexml = '';
        $articlexml = '';
        $date = date('Y-m-d');
        $site = $_SERVER['HTTP_ORIGIN'];
        foreach ($category as $key => $value) {
            $categoryxml .= "<url>
    <loc>$site/$value.html</loc>
    <priority>0.9</priority>
    <lastmod>" . $date . "T9:00:00+00:00</lastmod>
    <changefreq>Always</changefreq>
</url>\n";
        }
        foreach ($page as $key => $value) {
            $pagexml .= "<url>
    <loc>$site/$value.html</loc>
    <priority>0.8</priority>
    <lastmod>" . $date . "T9:00:00+00:00</lastmod>
    <changefreq>Always</changefreq>
</url>\n";
        }
        foreach ($article as $key => $value) {
            $articlexml .= "<url>
    <loc>$site/article/" . lockcode($key) . ".html</loc>
    <priority>0.6</priority>
    <lastmod>" . $value . "T9:00:00+00:00</lastmod>
    <changefreq>Always</changefreq>
</url>\n";
        }
        $sitemapxml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
    <loc>' . $site . '</loc>
    <priority>1.0</priority>
    <lastmod>' . $date . 'T9:00:00+00:00</lastmod>
    <changefreq>Always</changefreq>
</url>
' . $categoryxml . $pagexml . $articlexml . '</urlset>';
        $sitemap = fopen("sitemap.xml", "w") or die("Unable to open file!");
        $result = fwrite($sitemap, $sitemapxml);
        fclose($sitemap);
        $result = $result ? ['success' => '成功生成网站地图！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }

    public function delsitemap()
    {
        $result = file_exists("sitemap.xml") ? unlink("sitemap.xml") : false;
        $result = $result ? ['success' => '成功删除网站地图！'] : ['msg' => '额...操作失败了！'];
        return json($result);
    }
}
