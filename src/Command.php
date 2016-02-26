<?php

namespace Jehaby\Quest;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;

/**
 * Class Command
 * @package Jehaby\Quest
 */
class Command extends SymfonyCommand {

    /**
     * @var Quest
     */
    private $quest;

    /**
     * @param Quest $quest
     */
    public function __construct(Quest $quest)
    {
        parent::__construct();
        $this->quest = $quest;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('statistic')
            ->setDescription('Show statistics')
            ->setAliases(['s'])
            ->addOption(
                'without-documents',
                null,
                InputOption::VALUE_NONE,
                'Payments with documents'
            )
            ->addOption(
                'with-documents',
                null,
                InputOption::VALUE_NONE,
                'Payments without documents'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['count', 'amount']);

        $withDocuments = $input->getOption('with-documents');
        $withoutDocuments = $input->getOption('without-documents');

        if (! ($withDocuments || $withoutDocuments)) {
            $withDocuments = $withoutDocuments = true;
        }

        $startDate = $this->getDateFromUser($input, $output, new Question('Please enter start date:', '2015-01-01'));
        $endDate = $this->getDateFromUser($input, $output, new Question('Please enter end date:', '2015-12-31'));

        if ($withDocuments) {
            $table->addRow($this->quest->getStatisticsWithDocuments($startDate, $endDate));
        }

        if ($withoutDocuments) {
            $table->addRow($this->quest->getStatisticsWithoutDocuments($startDate, $endDate));
        }

        $table->render();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Question $question
     * @return string
     */
    private function getDateFromUser($input, $output, $question)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        while (true) {
            $date = $helper->ask($input, $output, $question);
            if ($this->dateIsValid($date)) {
                return $date;
            }
            $output->writeln("It's not a valid date!");
        }
    }

    /**
     * @param string $date
     * @return bool
     */
    private function dateIsValid($date)
    {
        $date = date_parse($date);
        return ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"]));
    }

}