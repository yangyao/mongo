<?php
namespace Yangyao\Mongo\Db\Parser;
use Yangyao\Mongo\Db\Loader;
use Yangyao\Mongo\Exception;
use Illuminate\Filesystem\Filesystem;
class Schema {

    private $schema = '';

    private $errPre = '';

    private $fields = [];

    public static function writeAll() {
        $folder = Loader::metaFileFolder();
        $fs = new Filesystem();
        $fs->cleanDirectory($folder);
        $xmlFolder = Loader::schemaFileFolder();
        foreach($fs->files($xmlFolder) as $v) {
            if (pathinfo($v, PATHINFO_EXTENSION) != 'xml') {
                continue;
            }
            $schema = basename($v, '.xml');
            $writer = new self($schema);
            $writer->write();
        }
    }

    public function __construct($schema) {
        $this->schema = $schema;
        $this->errPre = 'Meta:'.$this->schema.'|';
    }

    private function parseData() {
        $xml = $this->load();
        $this->fields = [];
        foreach($xml->fields[0] as $v) {
            $f = array();
            $f['name'] = trim((string)$v['name']);
            $f['type'] = trim((string)$v['type']);
            $f['default'] = trim((string)$v);

            switch($f['type']) {
                case "STRING":
                    break;
                case "INT":
                    if (strlen($f['default'])>0 && !isIntNum($f['default'])) {
                        throw new Exception\System(__METHOD__, $this->errPre.'wrong field default: '.$f['name']);
                    }
                    break;
                case "FLOAT":
                    if (strlen($f['default'])>0 && !isNumber($f['default'])) {
                        throw new Exception\System(__METHOD__, $this->errPre.'wrong field default: '.$f['name']);
                    }
                    break;
                case "ARRAY":
                    if (strlen($f['default'])>0) {
                        throw new Exception\System(__METHOD__, $this->errPre.'field default should be empty: '.$f['name']);
                    }
                    break;
                case "BINARY":
                    if (strlen($f['default'])>0) {
                        throw new Exception\System(__METHOD__, $this->errPre.'field default should be empty: '.$f['name']);
                    }
                    break;
                default;
                    throw new Exception\System(__METHOD__, $this->errPre.'wrong field data type: '.$f['name']);
            }

            $this->fields[] = $f;
        }
        //var_dump($this->fields);
    }

    public function write() {
        $this->parseData();

        $fields = $this->fields;
        $classShortName = Loader::metaClassShortName($this->schema);
        $namespace = Loader::metaClassNamespace($this->schema);

        $content = "<?php \n";
        ob_start();
        require __DIR__ . '/meta.html';
        $content .= ob_get_contents();
        ob_end_clean();

        $metaFile = Loader::metaFile($this->schema);
        umask(0002);
        @mkdir(dirname($metaFile), 0775, true);
        $res = file_put_contents($metaFile, $content);
        if ($res === false) {
            throw new Exception\System(__METHOD__, $this->errPre.'write file failed: '.$metaFile);
        }
        chmod($metaFile, 0775);
    }

    private function load() {
        return simplexml_load_file(Loader::metaXml($this->schema));
    }

}