<?php
/**
 * Project: cdn.mtgbracket.
 * User: Brandin
 * Date: 8/28/2016
 * Time: 7:24 PM
 */

namespace MisfitPixel\Command;


use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CardCompressionCommand
 * @package MisfitPixel\Command
 */
class CardCompressionCommand extends Command
{
    public function configure()
    {
        $this->setName('mtgbracket:cards:compress')
            ->setDescription('Compresses card images');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path = __DIR__ . '/../../../images/cards/compressed';

        $output->writeln('Starting card image compression');

        if(!file_exists($path)){
            $output->writeln('<comment>Creating compression directory</comment>');
            mkdir($path);
        }

        foreach(scandir(str_replace('compressed', 'uncompressed', $path)) as $expansion){
            $output->writeln(sprintf('Starting: <info>%s</info>', $expansion));

            foreach(scandir(sprintf('%s/%s', str_replace('compressed', 'uncompressed', $path), $expansion)) as $card){
                $output->write(sprintf('%s ... ', $card));

                /**
                 * TODO: compress the image and save.
                 * TODO: remove .full from file name.
                 * TODO: add gd library
                 */
                imagejpeg(
                    imagecreatefromstring(file_get_contents(sprintf('%s/%s/%s', str_replace('compressed', 'uncompressed', $path), $expansion, $card))),
                    imagecreatefromstring(file_get_contents(sprintf('%s/%s/%s', $path, $expansion, str_replace(['.full'], '', $card)))),
                    60
                );

                $output->writeln('done');
            }

            $output->writeln(sprintf('<info>%s</info> completed', $expansion));
        }

        $output->writeln('Card image compression completed');
    }
}