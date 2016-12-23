<?php 
namespace Yangyao\Mongo\Db\Meta\News;
use MongoBinData;
class Entry {

    public $fields = [
                'title' => 'STRING',
                'content' => 'STRING',
                'keywords' => 'STRING',
            ];

    protected $id;
    protected $v;
    protected $ct;
    protected $mt;

        public $title;
        public $content;
        public $keywords;
    
    public function __construct(){
        $this->ct = null;
        $this->mt = null;
        $this->v = 0;
                        $this->title = '';
                                $this->content = '';
                                $this->keywords = '';
                    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getCt(){
        return $this->ct;
    }

    public function setCt($cTime){
        $this->ct = $cTime;
        return $this;
    }

    public function getMt(){
        return $this->mt;
    }

    public function setMt($mTime){
        $this->mt = $mTime;
        return $this;
    }

    public function getV(){
        return $this->v;
    }

    public function setV($ver){
        $this->v = $ver;
        return $this;
    }

            public function getTitle(){
        return $this->title;
    }

    public function setTitle($val){
                $this->title = $val;
                return $this;
    }
            public function getContent(){
        return $this->content;
    }

    public function setContent($val){
                $this->content = $val;
                return $this;
    }
            public function getKeywords(){
        return $this->keywords;
    }

    public function setKeywords($val){
                $this->keywords = $val;
                return $this;
    }
    
    public function fromArray(& $data) {
        isset($data['ct']) && $this->setCt($data['ct']);
        isset($data['mt']) && $this->setMt($data['mt']);
        isset($data['v']) && $this->setV($data['v']);

                isset($data['title']) && $this->setTitle($data['title']);
                isset($data['content']) && $this->setContent($data['content']);
                isset($data['keywords']) && $this->setKeywords($data['keywords']);
            }

    public function toArray($dbType=false){
        return array(
            'ct' => $this->ct,
            'mt' => $this->mt,
            'v' => $this->v,
                                    'title' => $this->title,
                                                'content' => $this->content,
                                                'keywords' => $this->keywords,
                                );
    }

}
