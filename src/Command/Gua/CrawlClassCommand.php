<?php
namespace App\Command\Gua;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Finder\Finder;
use ReflectionClass;
use App\Repository\WordRepository;
use ReflectionException;

class CrawlClassCommand extends Command
{
// the name of the command (the part after "bin/console")
    protected static $defaultName = 'crawler:crawl-class';

    protected $wordRepository;

    public function __construct(WordRepository $wordRepository)
    {
        $this->wordRepository = $wordRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('爬蟲:專爬CLASS名稱')
            ->setHelp('這個command，可以帶你享受爬Method的快感。');

        $this->addArgument('target-path', InputArgument::REQUIRED, '需要給予目標路徑。 ex: D:\wegames_projects\www-wg-v2\core');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetPath = $input->getArgument('target-path');
        $output->writeln([
            'Framework Crawler',
            '======開始======',
            '',
        ]);
        $output->writeln('Whoa!');
        $output->writeln('路徑：' . $targetPath);

        $finder = new Finder();
        $finder->files()->in($targetPath)->name('/\.php$/');

        foreach ($finder as $file) {
            $output->writeln('檔案名稱：' . $file->getFilename());
            $fileName = $this->getFileName($file->getFilename());
            //取得拆解後的檔案名字
            $array = $this->explodeFileName($fileName);

            foreach ($array as $name) {
                $data = [];
                //檔名就等於是Class名稱
                $data['value'] = $name;
                $data['from'] = 'class-name';
                $this->wordRepository->createOrUpdate($data);
                $output->writeln('儲存成功。');
            }
        }
        $output->writeln('...........................                              
            ░░░▐▀▀▄█▀▀▀▀▀▒▄▒▀▌░░░░
            ░░░▐▒█▀▒▒▒▒▒▒▒▒▀█░░░░░
            ░░░░█▒▒▒▒▒▒▒▒▒▒▒▀▌░░░░
            ░░░░▌▒██▒▒▒▒██▒▒▒▐░░░░
            ░░░░▌▒▒▄▒██▒▄▄▒▒▒▐░░░░
            ░░░▐▒▒▒▀▄█▀█▄▀▒▒▒▒█▄░░
            ░░░▀█▄▒▒▐▐▄▌▌▒▒▄▐▄▐░░░
            ░░▄▀▒▒▄▒▒▀▀▀▒▒▒▒▀▒▀▄░░
            ░░█▒▀█▀▌▒▒▒▒▒▄▄▄▐▒▒▐░░
            ░░░▀▄▄▌▌▒▒▒▒▐▒▒▒▀▒▒▐░░
            ░░░░░░░▐▌▒▒▒▒▀▄▄▄▄▄▀░░
            ░░░░░░░░▐▄▒▒▒▒▒▒▒▒▐░░░
            ░░░░░░░░▌▒▒▒▒▄▄▒▒▒▐░░░
        ---------------------------------結束囉。---------------------');
    }

    /**
     * 取得檔案名稱，並去除.php字眼
     *
     * @param string $fileName
     *
     * @return string
     */
    private function getFileName($fileName)
    {
        $result = '';

        if (!empty($fileName)) {
            $result = str_replace('.php', '', $fileName);
        }

        return $result;
    }

    /**
     * 拆解檔案名稱
     *
     * @param string $fileName
     *
     * @return array
     */
    private function explodeFileName($fileName)
    {
        preg_match_all('/(?:^|[A-Z])[a-z]+/', $fileName, $matches);
        $result = [];

        if (!empty($matches) && !empty($matches[0])) {
            foreach ($matches[0] as $match) {
                $result[] = strtolower($match);
            }

            $matches = null;
        }

        return $result;
    }
}
