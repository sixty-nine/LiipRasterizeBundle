<?php

namespace Liip\RasterizeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Helper\DialogHelper;

class ClearCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('rasterize:cache:clear')
            ->setDescription('Clear the liip_rasterize cached files')
            ->setHelp('Clear the liip_rasterize cached files. Optionally can remove only the cached files for a given hostname.')
            ->addArgument('host', InputArgument::OPTIONAL, 'Set to a hostname (without http:// prefix) to clear only the files for that hostname')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getArgument('host');
        $files = $this->getFilesForHost($host);
        $fs = $this->getContainer()->get('filesystem');
        $dialog = new DialogHelper();

        if (!count($files)) {
            $output->writeln('<error>No matching files found</error>');
            return 1;
        }

        $question = 'Do you want to remove ' . count($files) . (count($files) == 1 ? ' file' : ' files') . '? [yes|no]: ';
        if ($dialog->ask($output, $question, 'no') !== 'yes') {
            $output->writeln('<error>Command aborted</error>');
            return 1;
        }

        foreach($files as $file) {
            $fs->remove($this->getCachePath() . '/' . $file);
        }

        $output->writeln('<info>Done</info>');
    }

    protected function getCachePath()
    {
        // TODO: ideally the real path for cached files should come from the CacheResolver
        return realpath(
            $this->getContainer()->getParameter('liip_imagine.web_root') .
            $this->getContainer()->getParameter('liip_imagine.cache_prefix') .
            '/liip_rasterize'
        );
    }

    protected function getFilesForHost($host = null)
    {
        $filePrefix = 'liip_rasterize';
        $prefixLen = strlen($filePrefix);
        $hostSha = sha1($host);
        $files = array();

        $dir = dir($this->getCachePath());
        while (false !== ($file = $dir->read())) {
            if (substr($file, 0, $prefixLen) === $filePrefix) {
                if (!$host) {
                    $files[] = $file;
                } else {
                    $parts = explode('.', $file);
                    if($parts && $parts[1] === $hostSha) {
                        $files[] = $file;
                    }
                }
            }
        }
        return $files;
    }

}