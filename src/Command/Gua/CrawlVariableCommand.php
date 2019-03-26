<?php
namespace App\Command\Gua;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Finder\Finder;
use App\Repository\WordRepository;

class CrawlVariableCommand extends Command
{
// the name of the command (the part after "bin/console")
    protected static $defaultName = 'crawler:crawl-variable';

    protected $wordRepository;

    public function __construct(WordRepository $wordRepository)
    {
        $this->wordRepository = $wordRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('爬蟲:專爬變數名稱')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('這個command，可以帶你享受爬Method的快感。')
        ;

        // configure an argument
        $this->addArgument('target-path', InputArgument::REQUIRED, '需要給予目標路徑。 ex: D:\wegames_projects\www-wg-v2\core');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetPath = $input->getArgument('target-path');
        $output->writeln([
            'Framework Crawler',
            '======開始======',
            '
__        __   _                          
\ \      / /__| | ___ ___  _ __ ___   ___ 
 \ \ /\ / / _ \ |/ __/ _ \| \'_ ` _ \ / _ \
  \ V  V /  __/ | (_| (_) | | | | | |  __/
   \_/\_/ \___|_|\___\___/|_| |_| |_|\___|
  
            '
        ]);
        $output->writeln('Whoa!');
        $output->writeln('路徑：' . $targetPath);

        $finder = new Finder();
        $finder->files()->in($targetPath)->name('/\.php$/');
        $count = 0;

        try {
            foreach ($finder as $file) {
                $content = file_get_contents($file->getRealPath());
                $output->writeln('檔案名稱：'.$file->getFilename());
                $variables = $this->getVariable($content);
                $content = null;

                foreach ($variables as $variable) {
                    try {
                        preg_match_all('/[^\_]+/', $variable, $specialWord);

                        if (!empty($specialWord[0])) {
                            foreach ($specialWord[0] as $key => $simpleWord) {
                                if ($key != 0) {
                                    $specialWord[0][$key] = ucwords(strtolower($simpleWord));
                                } else {
                                    $specialWord[0][$key] = strtolower($simpleWord);
                                }
                            }
                            $name = implode("", $specialWord[0]);
                            $inputName = $name;

                        } else {
                            $inputName = $variable;
                        }

                        $array = $this->formatToArray($inputName);

                        if (!empty($array)) {
                            foreach ($array as $value) {
                                $tempInput['value'] = $value;
                                $tempInput['from'] = 'variable';
                                $output->writeln([
                                    '--------',
                                    '檔案位置：'.$file->getRealPath()
                                ]);
                                $this->wordRepository->createOrUpdate($tempInput);
                                $output->writeln([
                                    '儲存成功'.$value,
                                    '--------'
                                ]);

                                unset($tempInput['value']);
                                unset($tempInput['from']);
                            }

                            $array = null;
                            unset($array);
                        }
                    } catch (\Exception $e) {
                        $output->writeln([
                            '錯誤：'.$e->getMessage(),
                            '路徑：'.$file->getRealPath()
                        ]);
                    }
                }

                $variables = null;
                unset($variables);

                $count ++;
                $output->writeln('目前完成：'.$count);
            }

            // Just in case PHP would choose not to run garbage collection,
            // we run it manually at the end of each batch so that memory is
            // regularly released
            gc_collect_cycles();

            $finder = null;
            unset($finder);
        } catch (\Exception $exception) {
            $output->writeln('發生錯誤：'.$exception->getMessage());
        }

        $output->writeln([
            'Framework Crawler',
            '======結束======',
            date('Y-m-d H:i:s')
        ]);

        $output->writeln('...........................
        
        ---------------------------------------------------------');
    }

    /**
     * 取得檔案內所有的變數
     *
     * @param string $content
     *
     * @return array
     */
    private function getVariable($content)
    {
        preg_match_all('/\$[\w]*\b/', $content, $matches, PREG_SET_ORDER);

        $result = [];

        if (!empty($matches)) {
            foreach ($matches as $match) {
                if (!empty($match[0])) {
                    $result[] = $this->removeDollarIcon($match[0]);
                }
            }
            $matches = null;
            unset($matches);
        }

        return $result;
    }

    /**
     * 去除錢字號
     *
     * @param string $string
     *
     * @return string
     */
    private function removeDollarIcon($string)
    {
        $result = '';

        if (!empty($string)) {
            $array = explode('$', $string);
            $result = (!empty($array[0])) ? $array[0] : $array[1];
            $string = null;
            unset($string);
        }

        return $result;
    }

    /**
     * 拆解文字
     *
     * @param string $string
     *
     * @return array
     */
    private function formatToArray($string)
    {
        //拆解駝峰式
        $matches = preg_split('/((?:^|[A-Z])[a-z]+)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = [];

        if (!empty($string)) {
            foreach ($matches as $match) {
                if (!empty($match)) {
                    $result[] = strtolower($match);
                }
            }

            $string = null;
            unset($string);
        }

        $matches = null;
        unset($matches);

        return $result;
    }
}
