<?php
// Code not yet tested!!! Might not work as expected

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Footballclub;
use Pimcore\Model\DataObject\Clubplayer;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\DataObject\Folder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFootballClubsCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('app:import-footballclubs')
            ->setDescription('Imports Football Clubs and Club Players from an Excel file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = PIMCORE_PROJECT_ROOT . '/var/import/footballclubs.xlsx';
        
        if (!file_exists($filePath)) {
            $output->writeln('<error>File not found: ' . $filePath . '</error>');
            return self::FAILURE;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $clubFolder = Folder::getByPath('/Footballclubs') ?: new Folder();
        if (!$clubFolder->getId()) {
            $clubFolder->setKey('Footballclubs');
            $clubFolder->setParent(Folder::getByPath('/'));
            $clubFolder->save();
        }

        $clubs = [];

        foreach ($rows as $index => $row) {
            if ($index === 1) continue; // Skip header row

            [$clubName, $trainer, $logoPath, $lat, $lng, $year, $website, $playerName, $number, $age, $position] = array_values($row);

            if (!isset($clubs[$clubName])) {
                $club = new Footballclub();
                $club->setKey(
                    preg_replace('/[^a-z0-9]+/i', '-', strtolower($clubName))
                );
                $club->setParent($clubFolder);
                $club->setName($clubName);
                $club->setTrainer($trainer);
                $club->setYearEstablished((int) $year);
                $club->setWebsite($website);
                if ($lat && $lng) {
                    $geoCoordinates = new \Pimcore\Model\DataObject\Data\GeoCoordinates();
                    $geoCoordinates->setLatitude((float) $lat);
                    $geoCoordinates->setLongitude((float) $lng);
                    $club->setLocation($geoCoordinates);
                }

                if ($logoPath && file_exists(PIMCORE_PROJECT_ROOT . '/var/import/' . $logoPath)) {
                    $logo = new \Pimcore\Model\Asset\Image();
                    $logo->setFilename(basename($logoPath));
                    $logo->setParent(Folder::getByPath('/FootballclubLogos'));
                    $logo->setData(file_get_contents(PIMCORE_PROJECT_ROOT . '/var/import/' . $logoPath));
                    $logo->save();
                    $club->setFcLogo($logo);
                }

                $club->save();
                $clubs[$clubName] = $club;
                $output->writeln("Created club: $clubName");
            }

            $player = new Clubplayer();
            $player->setKey(preg_replace('/[^a-z0-9]+/i', '-', strtolower($playerName)));
            $player->setParent($clubFolder);
            $player->setName($playerName);
            $player->setNumber((int) $number);
            $player->setAge((int) $age);
            $player->setPosition($position);
            $player->setFootballclub($clubs[$clubName]);
            $player->save();

            $output->writeln(" - Added player: $playerName to $clubName");
        }

        $output->writeln('<info>Import completed successfully!</info>');
        return self::SUCCESS;
    }
}
