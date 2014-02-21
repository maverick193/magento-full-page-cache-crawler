#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
//use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\DialogHelper;
use Mage_Core_Model_Factory as MageFactory;

require_once (dirname(__FILE__) . '/../../../autoload.php');

class Crawl extends Command
{
    /**
     * Magento Root path
     *
     * @var string
     */
    protected $_rootPath;

    /**
     * Initialize application with code (store, website code)
     *
     * @var string
     */
    protected $_appCode     = 'admin';

    /**
     * Initialize application code type (store, website, store_group)
     *
     * @var string
     */
    protected $_appType     = 'store';

    /**
     * Input arguments
     *
     * @var array
     */
    protected $_args        = array();

    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    protected $_progress = null;

    const URL_OPTION = 1;
    const ID_OPTTION = 2;

    /**
     * Initialize application and parse input parameters
     *
     */
    public function __construct()
    {
        parent::__construct();

        require_once $this->_getRootPath() . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
        Mage::app($this->_appCode, $this->_appType);

        $this->_applyPhpVariables();
        $this->_validate();
    }

    /**
     * Interacts with the user.
     *
     * @param InputInterface $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @param bool $displayWelcome
     */
    public function interact(InputInterface $input, OutputInterface $output, $displayWelcome = true)
    {
        $helper = $this->_getHelper();
        if ($displayWelcome) {
            $welcome = '
    __________  ______   ______                    __
   / ____/ __ \/ ____/  / ____/________ __      __/ /__  _____
  / /_  / /_/ / /      / /   / ___/ __ `| | /| / / / _ \/ ___/
 / __/ / ____/ /___   / /___/ /  / /_/ /| |/ |/ / /  __/ /
/_/   /_/    \____/   \____/_/   \__,_/ |__/|__/_/\___/_/
        ';
            $output->writeln('<info>' . $welcome . '</info>');
        }

        if (!$input->getOption('url') && !$input->getOption('id')) {
            $dialog     = $this->getHelperSet()->get('dialog');
            $commands   = array(self::URL_OPTION => $helper->__('Warm Up Urls by typing them'),
                self::ID_OPTTION => $helper->__('Execute crawlers by typing their IDs')
            );
            $exitMsg    = $helper->__('Exit');
            $commands[] = $exitMsg;
            $exitIndex  = array_search($exitMsg, $commands);

            $command    = $dialog->select($output,
                '<fg=green;options=bold>Please, select a crawling mode</fg=green;options=bold>',
                $commands,
                0
            );

            //If user choose exit
            if ($command == $exitIndex) {
                $output->writeln($helper->__('Bye'));
                return;
            }

            //if user wants to warm up a url
            if ($command == self::URL_OPTION) {
                $urls = $dialog->askAndValidate(
                    $output,
                    $helper->__(
                        'Please, Enter Urls you want warm up <comment>(urls must be separated by spaces)</comment>'
                        . "\n"
                    ),
                    function($urls, $output){ return $this->_validateUrls(explode(' ', $urls), $output); },
                    false,
                    null
                );

                $this->_crawl($urls, $output);
            }

            if ($command == self::ID_OPTTION) {
                $secureBaseUrl      = Mage::getStoreConfig('web/secure/base_url');
                $unsecureBaseUrl    = Mage::getStoreConfig('web/unsecure/base_url');
                $output->writeln(
                    $helper->__('<fg=cyan>According to your configuration</fg=cyan> [system -> configuration -> web]')
                );
                $output->writeln(
                    $helper->__('      <fg=cyan>Your Secure Base Url    :</fg=cyan> %s', $secureBaseUrl)
                );
                $output->writeln(
                    $helper->__('      <fg=cyan>Your Unsecure Base Url  :</fg=cyan> %s', $unsecureBaseUrl)
                );

                $crawlerIds = $dialog->askAndValidate(
                    $output,
                    $helper->__(
                        'If your base urls are correct, enter crawler entities IDs <comment>(IDs must be separated by spaces)</comment>'
                        . "\n"
                    ),
                    function($crawlerIds, $output){ return $this->_validateIDs(explode(' ', $crawlerIds), $output); },
                    false,
                    null
                );

                $this->runCrawlers($crawlerIds, $output);
            }

            $this->interact($input, $output, false);
        }
    }

    /**
     * Run crawler entities
     *
     * @param $crawlerIds
     * @param $output
     * @throws RunTimeException
     */
    public function runCrawlers($crawlerIds, $output)
    {
        $crawlerHelper  = $this->_getHelper();
        $crawler        = Mage::getModel('maverick_crawler/crawler');

        foreach ($crawlerIds as $crawlerId) {
            $output->writeln($crawlerHelper->__('<fg=cyan>Loading Crawler ID %s</fg=cyan>', $crawlerId));
            $crawler->load($crawlerId);
            if (!$crawler->getId()) {
                throw new \RunTimeException($crawlerHelper->__('Cannot load Crawler (ID %s)' . "\n", $crawlerId));
            }

            $factory    = $crawler->getFactory();
            $urls       = $factory->getUrls();
            if (empty($urls)) {
                $output->writeln($crawlerHelper->__(
                        '  <fg=red>Unable to find Urls to crawl for Crawler ID %s</fg=red>', $crawlerId) . "\n"
                );
            }

            $scan = $crawler->getScan() ? true : false;
            $this->_crawl($urls, $output, $scan);
            $crawler->reset();
        }
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('crawl')
            ->setDescription('Warming Up Urls')
            ->addOption('url', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Crawl a single Url, this Url must be valid'
            )
            ->addOption('id', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Crawl urls by executing a crawler instance created in Magento Backend, ID must be an existing one'
            )
            ->setHelp(<<<EOT
The <info>FPC Crawler Command</info> helps you crawling pages and stores them in Magento Full Page Cache.

By default, the command interacts with the user and asks about the crawling mode :
<comment>Direct Url</comment>, <comment>Crawler IDs</comment>, etc ...

If you want to disable any user interaction, use <comment>--no-interaction</comment>
but don't forget to pass all needed options:

<info>php crawler crawl --url http://url1.example/ http://url2.example/something.html</info>
OR
<info>php crawler crawl --id 2</info>

EOT
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($url = $input->getOption('url')) {
            if (!is_array($url)) {
                $url = (array) $url;
            }
            $url = $this->_validateUrls($url);
            $this->_crawl($url, $output);
        } elseif($crawlerId = $input->getOption('id')) {
            $crawlerId = $this->_validateIDs($crawlerId);
            $this->runCrawlers($crawlerId, $output);
        }
    }

    /**
     * Get Magento Root path (with last directory separator)
     *
     * @return string
     */
    protected function _getRootPath()
    {
        if (is_null($this->_rootPath)) {
            $this->_rootPath = '..' . DIRECTORY_SEPARATOR;
        }
        return $this->_rootPath;
    }

    protected function _getHelper()
    {
        return Mage::helper('maverick_crawler/crawler');
    }

    /**
     * Validate arguments
     *
     */
    protected function _validate()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            die('This script cannot be run from Browser. This is the shell script.');
        }
    }

    /**
     * Parse .htaccess file and apply php settings to shell script
     *
     */
    protected function _applyPhpVariables()
    {
        $htaccess = $this->_getRootPath() . '.htaccess';
        if (file_exists($htaccess)) {
            // parse htaccess file
            $data = file_get_contents($htaccess);
            $matches = array();
            preg_match_all('#^\s+?php_value\s+([a-z_]+)\s+(.+)$#siUm', $data, $matches, PREG_SET_ORDER);
            if ($matches) {
                foreach ($matches as $match) {
                    @ini_set($match[1], str_replace("\r", '', $match[2]));
                }
            }
            preg_match_all('#^\s+?php_flag\s+([a-z_]+)\s+(.+)$#siUm', $data, $matches, PREG_SET_ORDER);
            if ($matches) {
                foreach ($matches as $match) {
                    @ini_set($match[1], str_replace("\r", '', $match[2]));
                }
            }
        }
    }

    /**
     * Check if crawler IDs exists in DB
     *
     * @param $crawlerIds
     * @return array
     * @throws RunTimeException
     */
    protected function _validateIDs($crawlerIds)
    {
        $crawlerHelper = $this->_getHelper();
        if (empty($crawlerIds)) {
            throw new \RunTimeException($crawlerHelper->__('Please enter an existing Crawler IDs'));
        }
        $crawlerResource = Mage::getResourceModel('maverick_crawler/crawler');

        foreach ($crawlerIds as $crawlerId) {
            if (!$crawlerResource->crawlerExists(trim($crawlerId))) {
                throw new \RunTimeException($crawlerHelper->__('ID %s does not exist', $crawlerId));
            }
        }

        return $crawlerIds;
    }

    /**
     * Check if given Urls are valid
     *
     * @param $urls
     * @return array
     * @throws RunTimeException
     */
    protected function _validateUrls($urls)
    {
        $crawlerHelper = $this->_getHelper();
        if (empty($urls)) {
            throw new \RunTimeException($crawlerHelper->__('Please enter a valid url(s)'));
        }

        foreach ($urls as $url) {
            $url = trim($url);
            if (empty($url)) {
                unset($urls[array_search($url, $urls)]);
                continue;
            }
            if (!$crawlerHelper->validateUrl($url)) {
                throw new \RunTimeException($crawlerHelper->__('%s Is not a valid url', $url));
            }
        }

        return $urls;
    }

    /**
     * Crawl Urls
     *
     * @param $urls
     * @param $output
     * @param bool $scan
     */
    protected function _crawl($urls, $output, $scan = false)
    {
        $crawlerHelper  = $this->_getHelper();
        $output->writeln($crawlerHelper->__('Crawling Urls ...'));

        foreach ($urls as $url) {
            $output->writeln($crawlerHelper->__('    <fg=cyan>Warming Up :</fg=cyan> %s', $url));

            $time   = time();
            $result = $crawlerHelper->visit($url, 2);
            if (is_string($result)) {
                $output->writeln($crawlerHelper->__('    <fg=red>%s</fg=red>', $result) . "\n");
            } else {
                $output->writeln(
                    $crawlerHelper->__('    <fg=cyan>Took       :</fg=cyan> %d seconds', (int)(time() - $time))
                );
                if ($scan) {
                    $this->_scanCrawledUrls($result, $output);
                }
                $output->writeln("\n");
            }
        }

        $output->writeln($crawlerHelper->__('<fg=cyan>Done</fg=cyan>' . "\n"));
    }

    protected function _scanCrawledUrls($result, $output)
    {
        $progress   = $this->_initProgressBar();
        $helper     = $this->_getHelper();

        $output->writeln($helper->__('    <fg=cyan>Scaning This Page Urls</fg=cyan>'));

        $scanedUrls = $helper->getPageLinks($result);

        $output->writeln($helper->__('        <fg=red>Found :</fg=red> %s Urls', count($scanedUrls)));

        if (count($scanedUrls) > 0) {
            $progress->start($output, count($scanedUrls));
            $output->writeln($helper->__('        <fg=red>Crawling Them...</fg=red>'));
            $progress->display();
            foreach ($scanedUrls as $scanedUrl) {
                $helper->visit($scanedUrl, 2);
                $progress->advance();
            }
            $progress->finish();
        }
    }

    protected function _initProgressBar()
    {
        if (is_null($this->_progress)) {
            $progress = $this->getHelperSet()->get('progress');
            $progress->setFormat(
                \Symfony\Component\Console\Helper\ProgressHelper::FORMAT_VERBOSE
            );
            $progress->setBarCharacter('<comment>=</comment>');
            $progress->setEmptyBarCharacter('<fg=red>-</fg=red>');
            $progress->setProgressCharacter('>');
            $progress->setBarWidth(50);
            $this->_progress = $progress;
        }

        return $this->_progress;
    }
}

$application = new Application();
$application->add(new Crawl);
$application->run();