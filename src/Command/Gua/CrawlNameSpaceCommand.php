<?php
namespace App\Command\Gua;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use App\Repository\WordRepository;

/**
 * 爬取NameSpace
 */
class CrawlNameSpaceCommand extends Command
{
    const FROM_NAME_SPACE = 'name-space';

    protected static $defaultName = 'crawler:crawl-name-space';

    protected $wordRepository;

    public function __construct(WordRepository $wordRepository)
    {
        $this->wordRepository = $wordRepository;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('爬蟲：專爬NameSpace名稱')
            ->setHelp('這個command，可以帶你享受爬NameSpace的快感。');

        $this->addArgument('target-path', InputArgument::REQUIRED, '需要給予目標路徑。 ex: D:\wegames_projects\www-wg-v2\core');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetPath = $input->getArgument('target-path');
        $output->writeln([
            'Framework Crawler',
            '======開始======',
            '路徑：' . $targetPath,
        ]);

        $finder = new Finder();
        $finder->files()->in($targetPath)->name('/\.php$/');

        foreach ($finder as $file) {
            //讀取內容
            $content = file_get_contents($file->getRealPath());
            $output->writeln('檔案名稱：'.$file->getFilename());
            $nameSpace = $this->getNamespace($content);
            $content = null;

            if (empty($nameSpace)) {
                continue;
            }

            $array = $this->explodeNamespace($nameSpace);
            $data = [];

            foreach ($array as $value) {
                $data['value'] = $value;
                $data['from']  = self::FROM_NAME_SPACE;
                $this->wordRepository->createOrUpdate($data);
                $output->write('>>>>>>>>');
                $output->writeln('儲存成功。');
            }
        }

        $output->writeln('掃描完畢，'.date('Y-m-d H:i:s'));
    }

    /**
     * 取得name space
     *
     * @param string $content
     *
     * @return string
     */
    private function getNamespace($content)
    {
        preg_match('/namespace+[\s]+(\S*)[\;]/', $content, $match);
        $result = '';

        if (!empty($match)) {
            $result = (isset($match[1]) && !empty($match[1])) ? $match[1] : '' ;
        }

        $match = null;

        return $result;
    }

    /**
     * 把文字拆解
     *
     * @param string $namespace
     *
     * @return array
     */
    private function explodeNamespace($namespace)
    {
        $result = [];

        if (!empty($namespace)) {
            $result = explode("\\", strtolower($namespace));
        }

        $namespace = null;

        return $result;
    }
}
